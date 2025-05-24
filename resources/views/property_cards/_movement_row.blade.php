@php
    $i = $i ?? '__INDEX__';
@endphp

<td>
    <input 
        type="date" 
        name="movement_records[{{ $i }}][movement_date]" 
        value="{{ old("movement_records.$i.movement_date", $movement->movement_date ?? '') }}" 
        class="form-control"
        aria-label="Movement Date"
    />
</td>
<td>
    <input 
        type="text" 
        name="movement_records[{{ $i }}][par]" 
        value="{{ old("movement_records.$i.par", $movement->par ?? '') }}" 
        class="form-control"
        aria-label="PAR"
    />
</td>
<td>
    <input 
        type="number" 
        name="movement_records[{{ $i }}][qty]" 
        value="{{ old("movement_records.$i.qty", $movement->qty ?? '') }}" 
        class="form-control"
        aria-label="Quantity"
    />
</td>
<td>
    <input 
        type="number" 
        name="movement_records[{{ $i }}][movement_qty]" 
        value="{{ old("movement_records.$i.movement_qty", $movement->movement_qty ?? '') }}" 
        class="form-control"
        aria-label="Movement Quantity"
    />
</td>
<td>
    <input 
        type="text" 
        name="movement_records[{{ $i }}][office_officer]" 
        value="{{ old("movement_records.$i.office_officer", $movement->office_officer ?? '') }}" 
        class="form-control"
        aria-label="Office Officer"
    />
</td>
<td>
    <input 
        type="number" 
        name="movement_records[{{ $i }}][balance]" 
        value="{{ old("movement_records.$i.balance", $movement->balance ?? '') }}" 
        class="form-control"
        aria-label="Balance"
    />
</td>
<td>
    <input 
        type="number" 
        step="0.01" 
        name="movement_records[{{ $i }}][amount]" 
        value="{{ old("movement_records.$i.amount", $movement->amount ?? '') }}" 
        class="form-control"
        aria-label="Amount"
    />
</td>
<td>
    <input 
        type="text" 
        name="movement_records[{{ $i }}][remarks]" 
        value="{{ old("movement_records.$i.remarks", $movement->remarks ?? '') }}" 
        class="form-control"
        aria-label="Remarks"
    />
</td>

@if(isset($movement->movement_record_id))
    <input 
        type="hidden" 
        name="movement_records[{{ $i }}][id]" 
        value="{{ $movement->movement_record_id }}" 
    />
@endif

<td>
    <button 
        type="button" 
        class="remove-row btn btn-sm btn-danger" 
        aria-label="Remove row"
    >
        Remove
    </button>
</td>
