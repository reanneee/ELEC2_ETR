@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Fund Cluster</h2>
    <form method="POST" action="{{ route('fund_clusters.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Fund Cluster Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button class="btn btn-success mt-2">Save</button>
    </form>
</div>
@endsection
