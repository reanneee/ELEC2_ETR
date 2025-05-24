<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryCountForm;
use Illuminate\Support\Facades\DB;
use App\Models\ReceivedEquipmentDescription;
class InventoryCountFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'old_property_no' => 'required|string|max:100',
            // validate other fields
        ]);
    
        // Optional: lookup data from item/fund
        $item = DB::table('received_equipment_item')->where('property_no', $request->old_property_no)->first();
    
        $inventory = new InventoryCountForm();
        $inventory->old_property_no = $request->old_property_no;
        $inventory->unit = $request->unit ?? $item->unit ?? null;
        $inventory->unit_value = $request->unit_value ?? $item->amount ?? null;
        // Fill in more fields as needed
        $inventory->save();
    
        return redirect()->route('inventory.index')->with('success', 'Inventory saved successfully.');
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
