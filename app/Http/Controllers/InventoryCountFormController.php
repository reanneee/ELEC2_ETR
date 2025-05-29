<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryCountForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReceivedEquipmentDescription;
class InventoryCountFormController extends Controller
{
  
    public function index()
    {
        // Get descriptions with their items using Eloquent relationships
        $descriptions = ReceivedEquipmentDescription::with(['equipment', 'items'])
            ->orderByDesc('description_id')
            ->paginate(10);
    
        // Get fund matches using the correct join logic (without fund_id)
        $fundMatches = DB::table('received_equipment_item as rei')
            ->join('funds as f', function($join) {
                // Your existing join logic using property_no and account_code
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
        // Check if we have processed descriptions from the previous step
        $processedDescriptions = session('processedDescriptions');
        $fundMatches = session('fundMatches', collect());
        $equipmentItems = session('equipmentItems', collect());
        $linkedItems = session('linkedItems', collect());
        $quantities = session('quantities', []);
        
        // If no processed descriptions in session, redirect back to descriptions index
        if (!$processedDescriptions || $processedDescriptions->isEmpty()) {
            return redirect()->route('descriptions.index')
                ->with('error', 'No equipment selected for inventory. Please select equipment first.');
        }
        
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
        
        return view('inventory_count_form.create', compact(
            'processedDescriptions', 
            'fundMatches', 
            'quantities', 
            'equipmentItems', 
            'locations', 
            'entities',
            'linkedItems'
        ));
    }
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
        $fundMatches = DB::table('received_equipment_item as rei')
            ->join('funds as f', function($join) {
                // Extract MM-DD from property_no (positions 5-9) and compare with 4th-7th digits of account_code
                $join->on(DB::raw("REPLACE(SUBSTRING(rei.property_no, 6, 5), '-', '')"), '=', DB::raw("SUBSTRING(f.account_code, 4, 4)"));
            })
            ->select('rei.item_id', 'rei.property_no', 'f.account_code', 'f.account_title', 'f.id as fund_id')
            ->get()
            ->keyBy('item_id');
        
        // Get existing linked equipment items with constructed new property numbers
        $linkedItems = DB::table('linked_equipment_items')
            ->select(
                'original_property_no', 
                'reference_mmdd', 
                'new_property_no', 
                'location', 
                'id',
                'created_at',
                // Construct the full new property number: YEAR-reference_mmdd-new_property_no-location
                DB::raw("CONCAT(YEAR(created_at), '-', reference_mmdd, '-', new_property_no, '-', location) as full_new_property_no")
            )
            ->get()
            ->keyBy('original_property_no');
        
        // Get equipment items for location information
        $equipmentItems = DB::table('equipment_items')
            ->select('property_no', 'location_id', 'status')
            ->get();
        
        // Store data in session for the GET create method
        session([
            'processedDescriptions' => $descriptions,
            'fundMatches' => $fundMatches,
            'quantities' => $quantities,
            'equipmentItems' => $equipmentItems,
            'linkedItems' => $linkedItems
        ]);
        
        // Redirect to the GET create route
        return redirect()->route('inventory.create');
    }
    
    // Add this method to save/update linked equipment items via AJAX

/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    try {
        // Start database transaction
        DB::beginTransaction();
        
        // Validate the request
        $request->validate([
            'entity_id' => 'required|exists:entities,entity_id',
            'inventory_date' => 'required|date',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.article_item' => 'required|string',
            'inventory_items.*.description' => 'required|string',
            'inventory_items.*.old_property_no' => 'required|string',
            'inventory_items.*.new_property_no' => 'nullable|string',
            'inventory_items.*.unit' => 'required|string',
            'inventory_items.*.unit_value' => 'required|numeric|min:0',
            'inventory_items.*.qty_card' => 'required|integer|min:0',
            'inventory_items.*.qty_physical' => 'required|integer|min:0',
            'inventory_items.*.location' => 'required|string',
            'inventory_items.*.condition' => 'required|string',
            'inventory_items.*.remarks' => 'nullable|string',
            'prepared_by_name' => 'nullable|string|max:255',
            'prepared_by_position' => 'nullable|string|max:255',
            'reviewed_by_name' => 'nullable|string|max:255',
            'reviewed_by_position' => 'nullable|string|max:255',
        ]);

        // Create the main inventory count form record
        $inventoryCountForm = InventoryCountForm::create([
            'entity_id' => $request->entity_id,
            'inventory_date' => $request->inventory_date,
            'prepared_by_name' => $request->prepared_by_name,
            'prepared_by_position' => $request->prepared_by_position,
            'reviewed_by_name' => $request->reviewed_by_name,
            'reviewed_by_position' => $request->reviewed_by_position,
        ]);

        // Store each inventory item in property_cards table
        foreach ($request->inventory_items as $item) {
            // First, get the received_equipment_item_id based on old_property_no
            $receivedEquipmentItem = DB::table('received_equipment_item')
                ->where('property_no', $item['old_property_no'])
                ->first();

            if ($receivedEquipmentItem) {
                // Find the location_id based on location name
                $locationParts = explode(' - ', $item['location']);
                $buildingName = $locationParts[0] ?? '';
                $officeName = $locationParts[1] ?? '';
                
                $location = DB::table('locations')
                    ->where('building_name', $buildingName)
                    ->where('office_name', $officeName)
                    ->first();

                DB::table('property_cards')->insert([
                    'received_equipment_item_id' => $receivedEquipmentItem->item_id,
                    'qty_physical' => $item['qty_physical'],
                    'condition' => $item['condition'],
                    'remarks' => $item['remarks'] ?? '',
                    'issue_transfer_disposal' => '', // Default value, adjust as needed
                    'received_by_name' => '',
                    'article' => $item['article_item'],
                    'locations_id' => $location ? $location->id : 1, // Default to 1 if location not found
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Log the successful creation
        Log::info('Inventory Count Form created successfully', [
            'form_id' => $inventoryCountForm->id,
            'entity_id' => $request->entity_id,
            'total_items' => count($request->inventory_items),
            'inventory_date' => $request->inventory_date
        ]);

        // Commit the transaction
        DB::commit();

        // Clear the session data
        session()->forget(['processedDescriptions', 'fundMatches', 'quantities', 'equipmentItems', 'linkedItems']);

        // Redirect with success message
        return redirect()->route('inventory.index')
            ->with('success', 'Inventory Count Form created successfully! Total items: ' . count($request->inventory_items));

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        Log::error('Validation failed for inventory count form', [
            'errors' => $e->errors(),
            'request_data' => $request->all()
        ]);
        
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error creating inventory count form: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->all()
        ]);

        return redirect()->back()
            ->with('error', 'An error occurred while saving the inventory count form. Please try again.')
            ->withInput();
    }
}
    public function saveLinkedEquipmentItem(Request $request)
    {
        try {
            $request->validate([
                'original_property_no' => 'required|string',
                'reference_mmdd' => 'required|string',
                'new_property_no' => 'required|string',
                'location' => 'required|string',
            ]);
    
            // Check if record exists
            $existingRecord = DB::table('linked_equipment_items')
                ->where('original_property_no', $request->original_property_no)
                ->first();
    
            if ($existingRecord) {
                // Update existing record
                DB::table('linked_equipment_items')
                    ->where('id', $existingRecord->id)
                    ->update([
                        'reference_mmdd' => $request->reference_mmdd,
                        'new_property_no' => $request->new_property_no,
                        'location' => $request->location,
                        'updated_at' => now(),
                    ]);
            } else {
                // Create new record
                DB::table('linked_equipment_items')->insert([
                    'original_property_no' => $request->original_property_no,
                    'reference_mmdd' => $request->reference_mmdd,
                    'new_property_no' => $request->new_property_no,
                    'location' => $request->location,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
