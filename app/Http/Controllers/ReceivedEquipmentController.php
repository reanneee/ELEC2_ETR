<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Fund;
use App\Models\ReceivedEquipment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReceivedEquipmentDescription;
use App\Models\ReceivedEquipmentItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LinkedEquipmentItem;

class ReceivedEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipments = ReceivedEquipment::with('entity')->orderBy('created_at', 'desc')->get();
        return view('received_equipment.index', compact('equipments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Entity $entity)
    {
        $par_no = $this->getNextParNo();
        $funds = Fund::all();
        return view('received_equipment.create', compact('entity', 'par_no', 'funds'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
 
     public function store(Request $request)
     {
         // Log incoming data for debugging
         Log::info('Received Equipment Form Data:', $request->all());
         
         // Validate form data
         $request->validate([
             'entity_id' => 'required|exists:entities,entity_id',
             'par_no' => 'required|string',
             'received_by_name' => 'required|string',
             'received_by_designation' => 'required|string',
             'verified_by_name' => 'required|string',
             'verified_by_designation' => 'required|string',
             'receipt_date' => 'required|date',
             'equipments' => 'required|array|min:1',
             'equipments.*.description' => 'required|string',
             'equipments.*.quantity' => 'required|integer|min:1',
             'equipments.*.unit' => 'required|string',
             'equipments.*.items' => 'required|array|min:1',
             'equipments.*.items.*.property_no' => 'required|string',
             'equipments.*.items.*.serial_no' => 'nullable|string',
             'equipments.*.items.*.date_acquired' => 'required|date',
             'equipments.*.items.*.amount' => 'required|numeric|min:0',
         ]);
     
         // Calculate total amount across all items
         $totalAmount = 0;
         foreach ($request->equipments as $equipment) {
             foreach ($equipment['items'] as $item) {
                 $totalAmount += floatval($item['amount']);
             }
         }
     
         // Create received equipment (parent) within transaction
         DB::beginTransaction();
         try {
             // Find earliest acquisition date from items
             $earliestDate = now()->format('Y-m-d');
             foreach ($request->equipments as $equipment) {
                 foreach ($equipment['items'] as $item) {
                     if (isset($item['date_acquired']) && $item['date_acquired'] < $earliestDate) {
                         $earliestDate = $item['date_acquired'];
                     }
                 }
             }
     
             // Create main equipment record
             $receivedEquipment = ReceivedEquipment::create([
                 'entity_id' => $request->entity_id,
                 'date_acquired' => $earliestDate,
                 'amount' => $totalAmount,
                 'received_by_name' => $request->received_by_name,
                 'received_by_designation' => $request->received_by_designation,
                 'verified_by_name' => $request->verified_by_name,
                 'verified_by_designation' => $request->verified_by_designation,
                 'receipt_date' => $request->receipt_date,
                 'par_no' => $request->par_no,
             ]);
     
             // Process each equipment group
             foreach ($request->equipments as $equipment) {
                 // Create description record for this equipment
                 $description = new ReceivedEquipmentDescription([
                     'description' => $equipment['description'],
                     'quantity' => $equipment['quantity'],
                     'unit' => $equipment['unit']
                 ]);
                 
                 // Save description linked to parent equipment
                 $receivedEquipment->descriptions()->save($description);
                 
                 // Process items within this equipment group
                 foreach ($equipment['items'] as $itemData) {
                     // Create and save each item linked to this description
                     $item = new ReceivedEquipmentItem([
                         'property_no' => $itemData['property_no'],
                         'serial_no' => $itemData['serial_no'] ?? null,
                         'date_acquired' => $itemData['date_acquired'],
                         'amount' => $itemData['amount']
                     ]);
                     
                     $description->items()->save($item);
                     
                     // Create linked equipment item
                     $this->createLinkedEquipmentItem($itemData['property_no']);
                 }
             }
             
             DB::commit();
             return redirect()->route('received_equipment.index')
                 ->with('success', 'Received equipment saved successfully.');
         } 
         catch (\Exception $e) {
             DB::rollBack();
             Log::error('Error saving received equipment: ' . $e->getMessage());
             return back()->withInput()
                 ->with('error', 'Error saving equipment: ' . $e->getMessage());
         }
     }
     
     /**
      * Create linked equipment item based on property number
      */
     private function createLinkedEquipmentItem($propertyNo)
     {
         try {
         
             $parts = explode('-', $propertyNo);
             
             if (count($parts) >= 3) {
                 $mmdd = $parts[1] . $parts[2]; // "05" + "03" = "0503"
                 $referenceMmdd = $parts[1] . '-' . $parts[2]; // "05-03" for storage
                 
                 // Find fund by code
                 $fund = Fund::where('code', $mmdd)->first();
                 
                 if ($fund) {
                     // Generate new property number
                     $newPropertyNo = $this->generateNewPropertyNo();
                     
                     // Create linked equipment item with default location_id of 1 (representing "00")
                     $linkedItem = LinkedEquipmentItem::create([
                         'fund_id' => $fund->id,
                         'original_property_no' => $propertyNo,
                         'reference_mmdd' => $referenceMmdd,
                         'new_property_no' => $newPropertyNo,
                         'location' =>00, // Default location representing "00"
                     ]);
                     
                     return $linkedItem;
                 }
             }
         } catch (\Exception $e) {
             // Log error but don't break the main transaction
             Log::error('Error creating linked equipment item: ' . $e->getMessage());
         }
         
         return null;
     }
     
     /**
      * Generate new property number in format 0001-00, 0002-00, etc.
      */
     private function generateNewPropertyNo()
     {
         // Get the highest existing new_property_no
         $lastItem = LinkedEquipmentItem::orderBy('new_property_no', 'desc')->first();
         
         if ($lastItem) {
             // Extract the number part (before the dash)
             $parts = explode('-', $lastItem->new_property_no);
             $lastNumber = intval($parts[0]);
             $nextNumber = $lastNumber + 1;
         } else {
             $nextNumber = 1;
         }
         
         // Format as 4-digit number with -00 suffix
         return sprintf('%04d', $nextNumber);
     }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $equipment = ReceivedEquipment::with('entity', 'descriptions.items')->findOrFail($id);
        return view('received_equipment.show', compact('equipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $receivedEquipment = ReceivedEquipment::with('entity.branch', 'entity.fundCluster', 'descriptions.items')->findOrFail($id);
        $entity = $receivedEquipment->entity; // get entity from the equipment
    
        // Optionally, pass all entities if needed for dropdowns or selection
        // $entities = Entity::all();
    
        return view('received_equipment.edit', compact('receivedEquipment', 'entity'));
    }
    
    

    /**
     * Update the specified resource in storage.
     *//**
 * Update the specified resource in storage.
 */
public function update(Request $request, $id)
{
    // Log incoming data for debugging
    Log::info('Updating Received Equipment Form Data:', $request->all());
    
    // Validate form data
    $request->validate([
        'entity_id' => 'required|exists:entities,entity_id',
        'received_by_name' => 'required|string',
        'received_by_designation' => 'required|string',
        'verified_by_name' => 'required|string',
        'verified_by_designation' => 'required|string',
        'receipt_date' => 'required|date',
        'equipments' => 'required|array|min:1',
        'equipments.*.description' => 'required|string',
        'equipments.*.quantity' => 'required|integer|min:1',
        'equipments.*.unit' => 'required|string',
        'equipments.*.items' => 'required|array|min:1',
        'equipments.*.items.*.property_no' => 'required|string',
        'equipments.*.items.*.serial_no' => 'nullable|string',
        'equipments.*.items.*.date_acquired' => 'required|date',
        'equipments.*.items.*.amount' => 'required|numeric|min:0',
    ]);

    // Calculate total amount across all items
    $totalAmount = 0;
    foreach ($request->equipments as $equipment) {
        foreach ($equipment['items'] as $item) {
            $totalAmount += floatval($item['amount']);
        }
    }

    // Find earliest acquisition date from items
    $earliestDate = now()->format('Y-m-d');
    foreach ($request->equipments as $equipment) {
        foreach ($equipment['items'] as $item) {
            if (isset($item['date_acquired']) && $item['date_acquired'] < $earliestDate) {
                $earliestDate = $item['date_acquired'];
            }
        }
    }

    // Update in a transaction
    DB::beginTransaction();
    try {
        // Find the received equipment record
        $receivedEquipment = ReceivedEquipment::with('descriptions.items')->findOrFail($id);
        
        // Update main equipment record
        $receivedEquipment->update([
            'entity_id' => $request->entity_id,
            'date_acquired' => $earliestDate,
            'amount' => $totalAmount,
            'received_by_name' => $request->received_by_name,
            'received_by_designation' => $request->received_by_designation,
            'verified_by_name' => $request->verified_by_name,
            'verified_by_designation' => $request->verified_by_designation,
            'receipt_date' => $request->receipt_date,
            // PAR number shouldn't be changed in update
        ]);

        // Track which descriptions are still in the request
        $existingDescriptionIds = $receivedEquipment->descriptions->pluck('id')->toArray();
        $updatedDescriptionIds = [];
        
        // Process each equipment group in the request
        foreach ($request->equipments as $equipmentIndex => $equipmentData) {
            $descriptionId = null;
            
            // Check if this is an existing description (when editing via index)
            if ($equipmentIndex < count($existingDescriptionIds)) {
                $descriptionId = $existingDescriptionIds[$equipmentIndex];
                $description = ReceivedEquipmentDescription::find($descriptionId);
                
                if ($description) {
                    // Update existing description
                    $description->update([
                        'description' => $equipmentData['description'],
                        'quantity' => $equipmentData['quantity'],
                        'unit' => $equipmentData['unit']
                    ]);
                    $updatedDescriptionIds[] = $descriptionId;
                } else {
                    // Create new description if not found
                    $description = new ReceivedEquipmentDescription([
                        'description' => $equipmentData['description'],
                        'quantity' => $equipmentData['quantity'],
                        'unit' => $equipmentData['unit']
                    ]);
                    $receivedEquipment->descriptions()->save($description);
                }
            } else {
                // Create new description for new equipment groups
                $description = new ReceivedEquipmentDescription([
                    'description' => $equipmentData['description'],
                    'quantity' => $equipmentData['quantity'],
                    'unit' => $equipmentData['unit']
                ]);
                $receivedEquipment->descriptions()->save($description);
            }
            
            // Track which items are still in the request
            $existingItemIds = $description->items->pluck('id')->toArray();
            $updatedItemIds = [];
            
            // Process items for this equipment group
            foreach ($equipmentData['items'] as $itemIndex => $itemData) {
                // Check if this is an existing item (when editing via index)
                if ($itemIndex < count($existingItemIds)) {
                    $itemId = $existingItemIds[$itemIndex];
                    $item = ReceivedEquipmentItem::find($itemId);
                    
                    if ($item) {
                        // Update existing item
                        $item->update([
                            'property_no' => $itemData['property_no'],
                            'serial_no' => $itemData['serial_no'] ?? null,
                            'date_acquired' => $itemData['date_acquired'],
                            'amount' => $itemData['amount']
                        ]);
                        $updatedItemIds[] = $itemId;
                    } else {
                        // Create new item if not found
                        $item = new ReceivedEquipmentItem([
                            'property_no' => $itemData['property_no'],
                            'serial_no' => $itemData['serial_no'] ?? null,
                            'date_acquired' => $itemData['date_acquired'],
                            'amount' => $itemData['amount']
                        ]);
                        $description->items()->save($item);
                    }
                } else {
                    // Create new item for new entries
                    $item = new ReceivedEquipmentItem([
                        'property_no' => $itemData['property_no'],
                        'serial_no' => $itemData['serial_no'] ?? null,
                        'date_acquired' => $itemData['date_acquired'],
                        'amount' => $itemData['amount']
                    ]);
                    $description->items()->save($item);
                }
            }
            
            // Remove items that were deleted in the UI
            if (!empty($existingItemIds)) {
                ReceivedEquipmentItem::whereIn('id', array_diff($existingItemIds, $updatedItemIds))
                    ->delete();
            }
        }
        
        // Remove descriptions that were deleted in the UI
        if (!empty($existingDescriptionIds)) {
            ReceivedEquipmentDescription::whereIn('id', array_diff($existingDescriptionIds, $updatedDescriptionIds))
                ->delete();
        }
        
        DB::commit();
        return redirect()->route('received_equipment.index')
            ->with('success', 'Equipment updated successfully!');
    } 
    catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating received equipment: ' . $e->getMessage());
        return back()->withInput()
            ->with('error', 'Error updating equipment: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        $equipment = ReceivedEquipment::findOrFail($id);
        $equipment->delete();

        return redirect()->route('received_equipment.index')->with('success', 'Equipment deleted successfully!');
    }

    public function createWithEntity($entityId)
    {
        $entity = Entity::with('branch', 'fundCluster')->findOrFail($entityId);
        $par_no = $this->getNextParNo();
        $funds = Fund::all();

        return view('received_equipment.create', compact('entity', 'par_no', 'funds'));
    }

    public static function getNextParNo()
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $serial = ReceivedEquipment::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->count() + 1;

        $serialFormatted = str_pad($serial, 4, '0', STR_PAD_LEFT);

        return "{$year}-{$month}-{$serialFormatted}";
    }

    public function generatePdf($id)
    {
        // Load equipment with all related data including entity, branch, and fund cluster
        $equipment = ReceivedEquipment::with([
            'entity',
            'entity.branch',
            'entity.fundCluster',
            'descriptions.items'
        ])->findOrFail($id);
        
        // Set PDF options for better handling of text and layout
        $options = [
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ];
        
        $pdf = Pdf::loadView('received_equipment.pdf', compact('equipment'))
                  ->setOptions($options);
        
        // Set paper to A4
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download('PAR-' . $equipment->par_no . '.pdf');
    }

    /**
 * Delete an equipment item via AJAX
 * 
 * @param int $descriptionId
 * @param int $itemId
 * @return \Illuminate\Http\JsonResponse
 */
public function deleteEquipmentItem($descriptionId, $itemId)
{
    // Start a database transaction
    DB::beginTransaction();
    try {
        // Find the item
        $item = ReceivedEquipmentItem::findOrFail($itemId);
        
        // Store the amount to subtract from total
        $amountToSubtract = $item->amount;
        
        // Find the description
        $description = ReceivedEquipmentDescription::findOrFail($descriptionId);
        
        // Find the parent equipment
        $receivedEquipment = $description->receivedEquipment;
        
        // Delete the item
        $item->delete();
        
        // Update the equipment's total amount
        $receivedEquipment->amount = $receivedEquipment->amount - $amountToSubtract;
        $receivedEquipment->save();
        
        // If this was the last item for this description, you may want to delete the description too
        if ($description->items()->count() === 0) {
            $description->delete();
        }
        
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully']);
    } 
    catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting equipment item: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()], 500);
    }
}
}