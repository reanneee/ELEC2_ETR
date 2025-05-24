<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;

class PropertyNumberService
{
    /**
     * Generate new property number based on fund account code and location
     */
    public static function generateNewPropertyNumber($fundAccountCode, $locationId = null)
    {
        // Extract 4th to 7th digits from fund account code and format as MM-DD
        // Example: 10601010-00 -> digits 4-7 are "0101" -> format as "01-01"
        $digits = substr($fundAccountCode, 3, 4); // Get 4th to 7th digit (0-indexed, so position 3)
        $mmdd = substr($digits, 0, 2) . '-' . substr($digits, 2, 2); // Format as MM-DD
        
        // Get current year
        $currentYear = date('Y');
        
        // Find the next sequence number for this MM-DD pattern
        $lastSequence = DB::table('linked_equipment_items')
            ->where('reference_mmdd', $mmdd)
            ->max('sequence_number') ?? 0;
        
        $nextSequence = $lastSequence + 1;
        
        // Format location suffix (default to 00 if no location)
        $locationSuffix = $locationId ? str_pad($locationId, 2, '0', STR_PAD_LEFT) : '00';
        
        // Generate new property number: YYYY-MM-DD-SSSS-LL
        $newPropertyNo = sprintf(
            '%s-%s-%04d-%s',
            $currentYear,
            $mmdd,
            $nextSequence,
            $locationSuffix
        );
        
        return [
            'new_property_no' => $newPropertyNo,
            'reference_mmdd' => $mmdd,
            'sequence_number' => $nextSequence,
            'location_suffix' => $locationSuffix
        ];
    }
    
    /**
     * Update location in existing property number
     */
    public static function updateLocationInPropertyNumber($propertyNo, $newLocationId)
    {
        // Extract all parts except the last 2 digits
        $basePart = substr($propertyNo, 0, -2); // Everything except last 2 digits
        
        // Format new location suffix
        $locationSuffix = $newLocationId ? str_pad($newLocationId, 2, '0', STR_PAD_LEFT) : '00';
        
        return $basePart . $locationSuffix;
    }
    
    /**
     * Check if property number already exists
     */
    public static function propertyNumberExists($propertyNo)
    {
        return DB::table('linked_equipment_items')
            ->where('new_property_no', $propertyNo)
            ->exists();
    }
    
    /**
     * Save linked equipment item based on fund account code
     */
    public static function saveLikedEquipmentItem($oldPropertyNo, $fundAccountCode, $locationId = null)
    {
        $propertyData = self::generateNewPropertyNumber($fundAccountCode, $locationId);
        
        // Check if this old property number is already linked
        $existing = DB::table('linked_equipment_items')
            ->where('original_property_no', $oldPropertyNo)
            ->first();
            
        if ($existing) {
            // Update existing record
            $newPropertyNo = self::updateLocationInPropertyNumber($existing->new_property_no, $locationId);
            
            DB::table('linked_equipment_items')
                ->where('id', $existing->id)
                ->update([
                    'new_property_no' => $newPropertyNo,
                    'location_id' => $locationId,
                    'updated_at' => now()
                ]);
                
            return $newPropertyNo;
        } else {
            // Get the fund_id for the account code
            $fund = DB::table('funds')->where('account_code', $fundAccountCode)->first();
            $fundId = $fund ? $fund->id : null;
            
            // Create new record
            DB::table('linked_equipment_items')->insert([
                'fund_id' => $fundId,
                'original_property_no' => $oldPropertyNo,
                'reference_mmdd' => $propertyData['reference_mmdd'],
                'new_property_no' => $propertyData['new_property_no'],
                'sequence_number' => $propertyData['sequence_number'],
                'location_id' => $locationId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return $propertyData['new_property_no'];
        }
    }
    
    /**
     * Generate new property number for existing equipment item (for the JavaScript function)
     */
    public static function generateForEquipmentItem($oldPropertyNo, $fundAccountCode, $locationId)
    {
        return self::saveLikedEquipmentItem($oldPropertyNo, $fundAccountCode, $locationId);
    }
}