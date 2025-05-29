<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceivedEquipmentDescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PropertyNumberService;

class ReceivedEquipmentDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get descriptions first
        $descriptions = DB::table('received_equipment_description as red')
            ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
            ->select('red.*', 're.par_no')
            ->orderByDesc('red.description_id')
            ->paginate(10);
    
        // Get all items for these descriptions
        $descriptionIds = $descriptions->pluck('description_id');
        
        $items = DB::table('received_equipment_item')
            ->whereIn('description_id', $descriptionIds)
            ->get()
            ->groupBy('description_id');
    
        // Attach items to descriptions
        foreach ($descriptions as $description) {
            $description->items = $items->get($description->description_id, collect());
        }
    
        // Get fund matches
        $fundMatches = DB::table('received_equipment_item as rei')
            ->join('funds as f', function($join) {
                $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
            })
            ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title')
            ->get()
            ->keyBy('item_id');
    
        return view('descriptions.index', compact('descriptions', 'fundMatches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get available equipment for the dropdown
        $equipments = DB::table('received_equipment')
            ->select('equipment_id', 'par_no', 'description')
            ->orderBy('par_no')
            ->get();

        return view('descriptions.create', compact('equipments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
            'equipment_id' => 'required|exists:received_equipment,equipment_id',
            'total_quantity' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.old_property_no' => 'required|string|max:50|distinct',
            'items.*.new_property_no' => 'nullable|string|max:50',
            'items.*.unit_of_measure' => 'required|string|max:20',
            'items.*.unit_value' => 'nullable|numeric|min:0',
            'items.*.qty_physical_count' => 'required|integer|min:1',
            'items.*.location' => 'nullable|string|max:255',
            'items.*.condition' => 'required|string|max:50',
            'items.*.remarks' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Create the description
            $descriptionId = DB::table('received_equipment_description')->insertGetId([
                'equipment_id' => $request->equipment_id,
                'description' => $request->description,
                'quantity' => $request->total_quantity,
                'unit' => $request->items[array_key_first($request->items)]['unit_of_measure'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create the items
            foreach ($request->items as $itemData) {
                DB::table('received_equipment_item')->insert([
                    'description_id' => $descriptionId,
                    'name' => $request->description, // Use description as name
                    'property_no' => $itemData['old_property_no'],
                    'new_property_no' => $itemData['new_property_no'],
                    'unit_of_measure' => $itemData['unit_of_measure'],
                    'unit_value' => $itemData['unit_value'],
                    'qty_physical_count' => $itemData['qty_physical_count'],
                    'location' => $itemData['location'],
                    'condition' => $itemData['condition'],
                    'remarks' => $itemData['remarks'],
                    'serial_no' => null, // Can be added later if needed
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('descriptions.index')
                ->with('success', 'Equipment description created successfully with ' . count($request->items) . ' items!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create equipment description: ' . $e->getMessage());
        }
    }

    /**
     * Create inventory count form
     */
//     public function createInventory(Request $request)
//     {
//         $request->validate([
//             'selected_items' => 'required|array',
//             'selected_items.*' => 'exists:received_equipment_description,description_id',
//             'quantities' => 'required|array',
//             'quantities.*' => 'integer|min:1'
//         ]);
        
//         $selectedDescriptionIds = $request->selected_items;
//         $quantities = $request->quantities;
        
//         // Get selected descriptions with their items and equipment details
//         $descriptions = DB::table('received_equipment_description as red')
//             ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
//             ->whereIn('red.description_id', $selectedDescriptionIds)
//             ->select('red.*', 're.par_no', 're.amount')
//             ->get();
        
//         // Get items for selected descriptions with additional details
//         $items = DB::table('received_equipment_item')
//             ->whereIn('description_id', $selectedDescriptionIds)
//             ->get()
//             ->groupBy('description_id');
        
//         // Attach items to descriptions
//         foreach ($descriptions as $description) {
//             $description->items = $items->get($description->description_id, collect());
//             $description->inventory_quantity = $quantities[$description->description_id] ?? $description->quantity;
//         }
        
//         // Get fund matches for article/item classification
//         // Updated logic to match 4th-7th digits of fund account code with property number pattern
//         $fundMatches = DB::table('received_equipment_item as rei')
//             ->join('funds as f', function($join) {
//                 // Extract MM-DD from property_no (positions 5-9) and compare with 4th-7th digits of account_code
//                 $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
//             })
//             ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title', 'f.id as fund_id')
//             ->get()
//             ->keyBy('item_id');
        
//         // Get existing linked equipment items (new property numbers)
//         $linkedItems = DB::table('linked_equipment_items')
//             ->select('original_property_no', 'new_property_no', 'id')
//             ->get()
//             ->keyBy('original_property_no');
        
//         // Get equipment items for location information
//         $equipmentItems = DB::table('equipment_items')
//             ->select('property_no', 'location_id', 'status')
//             ->get();
        
//         // Get all locations for dropdown
//         $locations = DB::table('locations')
//             ->select('id', 'building_name', 'office_name', 'officer_name')
//             ->orderBy('building_name')
//             ->orderBy('office_name')
//             ->get();
        
//         // Get entities for dropdown
//         $entities = DB::table('entities')
//             ->select('entity_id', 'entity_name')
//             ->orderBy('entity_name')
//             ->get();
        
//         return view('inventory_count_form.create', compact(
//             'descriptions', 
//             'fundMatches', 
//             'quantities', 
//             'equipmentItems', 
//             'locations', 
//             'entities',
//             'linkedItems'
//         ));
//     }

//     public function generatePropertyNumber(Request $request)
// {
//     $request->validate([
//         'old_property_no' => 'required|string',
//         'fund_account_code' => 'required|string',
//         'location_id' => 'nullable|integer'
//     ]);
    
//     try {
//         $newPropertyNo = PropertyNumberService::generateForEquipmentItem(
//             $request->old_property_no,
//             $request->fund_account_code,
//             $request->location_id
//         );
        
//         return response()->json([
//             'success' => true,
//             'new_property_no' => $newPropertyNo
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }
public function createInventory(Request $request)
{
    // Debug: Log the incoming request data
    Log::info('Received request data:', [
        'selected_items' => $request->selected_items,
        'quantities' => $request->quantities,
        'all_input' => $request->all()
    ]);
    
    $request->validate([
        'selected_items' => 'required|array',
        'selected_items.*' => 'exists:received_equipment_description,description_id',
        'quantities' => 'required|array',
        'quantities.*' => 'integer|min:1'
    ]);
    
    $selectedDescriptionIds = $request->selected_items;
    $quantities = $request->quantities;
    
    // Debug: Log the processed data
    Log::info('Processed data:', [
        'selectedDescriptionIds' => $selectedDescriptionIds,
        'quantities' => $quantities
    ]);
    
    // Get selected descriptions with their items and equipment details
    $descriptions = DB::table('received_equipment_description as red')
        ->join('received_equipment as re', 'red.equipment_id', '=', 're.equipment_id')
        ->whereIn('red.description_id', $selectedDescriptionIds)
        ->select('red.*', 're.par_no', 're.amount')
        ->get();
    
    // Get items for selected descriptions with additional details
    $items = DB::table('received_equipment_item')
        ->whereIn('description_id', $selectedDescriptionIds)
        ->get()
        ->groupBy('description_id');
    
    // Attach items to descriptions and prepare inventory rows
    foreach ($descriptions as $description) {
        $allItems = $items->get($description->description_id, collect());
        $inventoryQuantity = $quantities[$description->description_id] ?? $description->quantity;
        
        // Take only the number of items specified in inventory_quantity
        $description->items = $allItems->take($inventoryQuantity);
        $description->inventory_quantity = $inventoryQuantity;
        $description->total_available = $allItems->count(); // Total available items
        
        // Calculate unit value if not set (total amount divided by total quantity)
        if ($description->amount && $description->quantity > 0) {
            $description->unit_value = $description->amount / $description->quantity;
        } else {
            $description->unit_value = 0;
        }
        
        // Debug: Log each description's quantity assignment
        Log::info("Description {$description->description_id}: total_available={$description->total_available}, inventory_quantity={$description->inventory_quantity}, actual_rows={$description->items->count()}, unit_value={$description->unit_value}");
    }
    
    // Get fund matches for article/item classification
    // Updated logic to match 4th-7th digits of fund account code with property number pattern
    $fundMatches = DB::table('received_equipment_item as rei')
        ->join('funds as f', function($join) {
            // Extract MM-DD from property_no (positions 5-9) and compare with 4th-7th digits of account_code
            $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
        })
        ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title', 'f.id as fund_id')
        ->get()
        ->keyBy('item_id');
    
    // Get existing linked equipment items (new property numbers)
    $linkedItems = DB::table('linked_equipment_items')
        ->select('original_property_no', 'new_property_no', 'id')
        ->get()
        ->keyBy('original_property_no');
    
    // Get equipment items for location information
    $equipmentItems = DB::table('equipment_items')
        ->select('property_no', 'location_id', 'status')
        ->get();
    
    // Get all locations for dropdown
    $locations = DB::table('locations')
        ->select('id', 'building_name', 'office_name', 'officer_name')
        ->orderBy('building_name')
        ->orderBy('office_name')
        ->get();
    
    // Get entities for dropdown
    $entities = DB::table('entities')
        ->select('entity_id', 'entity_name')
        ->orderBy('entity_name')
        ->get();
    
    // Fix: Change $descriptions to $processedDescriptions to match the Blade template
    return view('inventory_count_form.create', compact(
        'descriptions', 
        'fundMatches', 
        'quantities', 
        'equipmentItems', 
        'locations', 
        'entities',
        'linkedItems'
    ))->with('processedDescriptions', $descriptions); // Add this line
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}