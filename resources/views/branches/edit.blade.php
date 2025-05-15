@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Branch</h2>
    <form action="{{ route('branches.update', $branch->branch_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Branch Name</label>
            <input type="text" name="branch_name" class="form-control" value="{{ $branch->branch_name }}" required>
        </div>
        <button class="btn btn-success mt-2">Update</button>
    </form>
</div>
@endsection
