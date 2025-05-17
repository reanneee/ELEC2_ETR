@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center">Received Equipment List</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>PAR No</th>
                    <th>Entity</th>
                    <th>Date Acquired</th>
                    <th>Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipments as $equipment)
                    <tr>
                        <td>{{ $equipment->par_no }}</td>
                        <td>{{ $equipment->entity->entity_name ?? 'N/A' }}</td>
                        <td>{{ $equipment->date_acquired->format('Y-m-d') }}</td>
                        <td>₱{{ number_format($equipment->amount, 2) }}</td>
                        <td>
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                <a href="{{ route('received_equipment.generate_pdf', $equipment->equipment_id) }}" class="btn btn-sm btn-outline-danger">
                                    PDF
                                </a>
                                <a href="{{ route('received_equipment.show', $equipment->equipment_id) }}" class="btn btn-sm btn-outline-primary">
                                    Show
                                </a>
                                <a href="{{ route('received_equipment.edit', $equipment->equipment_id) }}" class="btn btn-sm btn-outline-warning">
                                    Edit
                                </a>
                                <form action="{{ route('received_equipment.destroy', $equipment->equipment_id) }}" method="POST" onsubmit="return confirm('Delete this equipment?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted">No equipment found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
