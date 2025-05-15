@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Branches</h2>
    <a href="{{ route('branches.create') }}" class="btn btn-primary mb-3">Add Branch</a>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        @foreach($branches as $branch)
        <tr>
            <td>{{ $branch->branch_id }}</td>
            <td>{{ $branch->branch_name }}</td>
            <td>
                <a href="{{ route('branches.edit', $branch->branch_id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form action="{{ route('branches.destroy', $branch->branch_id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Delete this branch?')" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
