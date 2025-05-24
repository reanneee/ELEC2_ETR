@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Property Cards</h1>
        <a href="{{ route('property_cards.create') }}" class="btn btn-primary">Create New</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Entity</th>
                    <th>Property Number</th>
                    <th>Description</th>
                    <th class="text-center" style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cards as $card)
                    <tr>
                        <td>{{ $card->entity->entity_name }}</td>
                        <td>{{ $card->property_number }}</td>
                        <td>{{ $card->description }}</td>
                        <td class="text-center">
                            <a href="{{ route('property_cards.edit', $card) }}" class="btn btn-sm btn-outline-secondary me-1">Edit</a>
                            <a href="{{ route('property_cards.pdf', $card) }}" target="_blank" class="btn btn-sm btn-outline-info me-1">PDF</a>
                            <form action="{{ route('property_cards.destroy', $card) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this property card?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No property cards found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
