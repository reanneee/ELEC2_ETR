@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Fund Clusters</h2>
    <a href="{{ route('fund_clusters.create') }}" class="btn btn-success mb-2">Add New</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fundClusters as $cluster)
                <tr>
                    <td>{{ $cluster->name }}</td>
                    <td>
                        <a href="{{ route('fund_clusters.edit', $cluster) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('fund_clusters.destroy', $cluster) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
