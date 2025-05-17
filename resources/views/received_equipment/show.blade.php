@extends('layouts.app')

@section('content')
<div class="container py-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Equipment Details</h2>
            <small class="text-muted">PAR No: {{ $equipment->par_no }}</small>
        </div>
        <a href="{{ route('received_equipment.index') }}" class="btn btn-outline-primary">← Back to List</a>
    </div>

    <!-- Main Details Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-body p-4">
            <table class="table table-borderless mb-0">
                <tr>
                    <th class="text-muted" style="width: 200px;">Entity</th>
                    <td>{{ $equipment->entity->entity_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Date Acquired</th>
                    <td>{{ $equipment->date_acquired->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th class="text-muted">Total Amount</th>
                    <td><strong>₱{{ number_format($equipment->amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Descriptions and Items Table -->
    @forelse($equipment->descriptions as $description)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $description->description }}</strong>
                    <span class="text-muted">({{ $description->quantity }} {{ $description->unit }})</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($description->items->count())
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Serial Number</th>
                                <th style="width: 30%">Property Number</th>
                                <th style="width: 20%">Amount (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($description->items as $item)
                            <tr>
                                <td>{{ $item->serial_no }}</td>
                                <td>{{ $item->property_no }}</td>
                                <td>{{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-3 text-muted">No items listed under this description.</div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center">No equipment descriptions found.</div>
    @endforelse

</div>
@endsection
