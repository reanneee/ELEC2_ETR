@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Branch</h2>
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Branch Name</label>
            <input type="text" name="branch_name" class="form-control" required>
        </div>
        <button class="btn btn-success mt-2">Save</button>
    </form>
</div>
@endsection
