@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Fund Cluster</h2>
    <form method="POST" action="{{ route('fund_clusters.update', $fund_cluster) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label for="name">Fund Cluster Name</label>
            <input type="text" name="name" value="{{ $fund_cluster->name }}" class="form-control" required>
        </div>
        <button class="btn btn-primary mt-2">Update</button>
    </form>
</div>
@endsection
