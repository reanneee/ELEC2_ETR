@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Entities</h2>
    <a href="{{ route('entities.create') }}" class="btn btn-success mb-3">Add New Entity</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Branch</th>
                <th>Fund Cluster</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $entity)
                <tr>
                    <td>{{ $entity->entity_id }}</td>
                    <td>{{ $entity->entity_name }}</td>
                    <td>{{ $entity->branch->branch_name ?? 'N/A' }}</td>
                    <td>{{ $entity->fundCluster->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('entities.edit', $entity->entity_id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('entities.destroy', $entity->entity_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
