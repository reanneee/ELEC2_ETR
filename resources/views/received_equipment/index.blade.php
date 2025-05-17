@extends('layouts.app')

@section('content')
<h1>Received Equipment List</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>PAR No</th>
            <th>Entity</th>
            <th>Date Acquired</th>
            <th>Amount</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($equipments as $equipment)
        <tr>
            <td>{{ $equipment->par_no }}</td>
            <td>{{ $equipment->entity->entity_name ?? 'N/A' }}</td>
            <td>{{ $equipment->date_acquired->format('Y-m-d') }}</td>
            <td>₱{{ number_format($equipment->amount, 2) }}</td>
            <td><a href="{{ route('received_equipment.generate_pdf', $equipment->equipment_id) }}" class="btn btn-danger">
    Generate PDF
</a>

                <a href="{{ route('received_equipment.show', $equipment->equipment_id) }}">Show</a> |
                <a href="{{ route('received_equipment.edit', $equipment->equipment_id) }}">Edit</a> |
                <form action="{{ route('received_equipment.destroy', $equipment->equipment_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this equipment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
