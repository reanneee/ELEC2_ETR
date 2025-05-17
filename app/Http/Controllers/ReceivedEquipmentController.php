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
        $equipment = ReceivedEquipment::findOrFail($id);
        $entities = Entity::all();
        return view('received_equipment.edit', compact('equipment', 'entities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'entity_id' => 'required|exists:entities,entity_id',
            'date_acquired' => 'required|date',
            'amount' => 'required|numeric',
            'received_by_name' => 'required|string',
            'received_by_designation' => 'required|string',
            'verified_by_name' => 'required|string',
            'verified_by_designation' => 'required|string',
            'receipt_date' => 'required|date',
        ]);

        $equipment = ReceivedEquipment::findOrFail($id);
        $equipment->update($request->all());

        return redirect()->route('received_equipment.index')->with('success', 'Equipment updated successfully!');
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
}