<?php


namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\PropertyCard;
use App\Models\MovementRecord;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyCardController extends Controller
{
    public function index()
    {
        $cards = PropertyCard::with('entity')->get();
        return view('property_cards.index', compact('cards'));
    }

    public function create()
    {
        $entities = Entity::all();
        return view('property_cards.create', compact('entities'));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,entity_id',
            'property_number' => 'required|unique:property_cards,property_number',
            'description' => 'nullable|string',
            'movement_records.*.movement_date' => 'nullable|date',
            'movement_records.*.par' => 'nullable|string',
            'movement_records.*.receipt' => 'nullable|boolean',
            'movement_records.*.qty' => 'nullable|integer',
            'movement_records.*.movement_qty' => 'nullable|integer',
            'movement_records.*.issue_transfer_disposal' => 'nullable|string',
            'movement_records.*.balance' => 'nullable|integer',
            'movement_records.*.amount' => 'nullable|numeric',
            'movement_records.*.office_officer' => 'nullable|string',
            'movement_records.*.remarks' => 'nullable|string',
        ]);

        $card = PropertyCard::create($request->only(['entity_id', 'property_number', 'description']));

        foreach ($request->movement_records as $record) {
            $card->movements()->create($record);
        }

        return redirect()->route('property_cards.index')->with('success', 'Property Card created successfully.');
    }

        public function edit($id)
        {
            $card = PropertyCard::with('movements')->findOrFail($id);

            return view('property_cards.edit', [
                'entities' => Entity::all(),
                'property_card' => $card,
            ]);
        }

    public function update(Request $request, PropertyCard $property_card)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,entity_id',
            'property_number' => 'required|unique:property_cards,property_number,' . $property_card->property_card_id . ',property_card_id',
        ]);

        $property_card->update($request->only(['entity_id', 'property_number', 'description']));

        $property_card->movements()->delete();

        foreach ($request->movement_records as $record) {
            $property_card->movements()->create($record);
        }

        return redirect()->route('property_cards.index')->with('success', 'Property Card updated.');
    }

    public function destroy(PropertyCard $property_card)
    {
        $property_card->delete();
        return redirect()->route('property_cards.index')->with('success', 'Property Card deleted.');
    }

public function pdf(PropertyCard $property_card)
{
    $property_card->load('entity', 'movements');


    $pdf = PDF::loadView('property_cards.pdf', compact('property_card'));
    return $pdf->stream("property_card_{$property_card->property_card_id}.pdf");
}

}
