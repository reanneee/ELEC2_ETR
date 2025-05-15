<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\Fund;
use App\Models\ReceivedEquipment;

class ReceivedEquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Entity $entity)
    {
       
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

    public function createWithEntity($entityId)
    {
        $entity = Entity::with('branch', 'fundCluster')->findOrFail($entityId);
        $par_no = $this->getNextParNo();
        $funds = Fund::all();

        return view('equipment.create', compact('entity', 'par_no','funds'));
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

    


}
