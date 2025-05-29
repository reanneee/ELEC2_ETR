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