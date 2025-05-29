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
                // Generate new property number for this specific fund and reference_mmdd
                $newPropertyNo = $this->generateNewPropertyNo($fund->id, $referenceMmdd);
                
                // Create linked equipment item with default location of "00"
                $linkedItem = LinkedEquipmentItem::create([
                    'fund_id' => $fund->id,
                    'original_property_no' => $propertyNo,
                    'reference_mmdd' => $referenceMmdd,
                    'new_property_no' => $newPropertyNo,
                    'year' => now()->year,
                    'location' => '00', // Default location representing "00"
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
 * Generate new property number for specific fund and reference_mmdd
 * Format: 0001, 0002, 0003, etc. (separate sequence per fund/reference combination)
 */
private function generateNewPropertyNo($fundId, $referenceMmdd)
{
    // Get the highest existing new_property_no for this specific fund_id and reference_mmdd
    $lastItem = LinkedEquipmentItem::where('fund_id', $fundId)
        ->where('reference_mmdd', $referenceMmdd)
        ->orderBy('new_property_no', 'desc')
        ->first();
    
    if ($lastItem) {
        // Extract the number part and increment
        $lastNumber = intval($lastItem->new_property_no);
        $nextNumber = $lastNumber + 1;
    } else {
        // This is the first item for this fund/reference combination
        $nextNumber = 1;
    }
    
    // Format as 4-digit number: 0001, 0002, 0003, etc.
    return sprintf('%04d', $nextNumber);
}
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
        $entity = $receivedEquipment->entity; 
    
        return view('received_equipment.edit', compact('receivedEquipment', 'entity'));
    }
    
    

 public function update(Request $request, $id)
{
    Log::info('Updating Received Equipment Form Data:', $request->all());
    
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

    $totalAmount = 0;
    foreach ($request->equipments as $equipment) {
        foreach ($equipment['items'] as $item) {
            $totalAmount += floatval($item['amount']);
        }
    }

    $earliestDate = now()->format('Y-m-d');
    foreach ($request->equipments as $equipment) {
        foreach ($equipment['items'] as $item) {
            if (isset($item['date_acquired']) && $item['date_acquired'] < $earliestDate) {
                $earliestDate = $item['date_acquired'];
            }
        }
    }

    DB::beginTransaction();
    try {
        $receivedEquipment = ReceivedEquipment::with('descriptions.items')->findOrFail($id);
        
        // Update the main equipment record
        $receivedEquipment->update([
            'entity_id' => $request->entity_id,
            'date_acquired' => $earliestDate,
            'amount' => $totalAmount,
            'received_by_name' => $request->received_by_name,
            'received_by_designation' => $request->received_by_designation,
            'verified_by_name' => $request->verified_by_name,
            'verified_by_designation' => $request->verified_by_designation,
            'receipt_date' => $request->receipt_date,
        ]);

        // Clear existing descriptions and items to rebuild them
        foreach ($receivedEquipment->descriptions as $description) {
            $description->items()->delete();
        }
        $receivedEquipment->descriptions()->delete();
        
        // Create new descriptions and items
        foreach ($request->equipments as $equipmentData) {
            $description = new ReceivedEquipmentDescription([
                'description' => $equipmentData['description'],
                'quantity' => $equipmentData['quantity'],
                'unit' => $equipmentData['unit']
            ]);
            $receivedEquipment->descriptions()->save($description);
            
            // Create items for this description
            foreach ($equipmentData['items'] as $itemData) {
                $item = new ReceivedEquipmentItem([
                    'property_no' => $itemData['property_no'],
                    'serial_no' => $itemData['serial_no'] ?? null,
                    'date_acquired' => $itemData['date_acquired'],
                    'amount' => $itemData['amount']
                ]);
                $description->items()->save($item);
            }
        }
        
        DB::commit();
        
        // Add session flash message for success feedback
        session()->flash('success', 'Equipment updated successfully!');
        
        return redirect()->route('received_equipment.index');
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating received equipment: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        // Return with error message and input data
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Error updating equipment: ' . $e->getMessage()]);
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