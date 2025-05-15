@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add New Entity</h2>
    <form action="{{ route('entities.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="entity_name">Entity Name</label>
            <input type="text" name="entity_name" class="form-control" value="{{ old('entity_name') }}" required>
        </div>

        <!-- Branch Selection -->
        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" class="form-control" required>
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->branch_id }}"
                        {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Fund Cluster Selection -->
        <div class="form-group">
            <label for="fund_cluster_id">Fund Cluster</label>
            <select name="fund_cluster_id" class="form-control" required>
                <option value="">-- Select Fund Cluster --</option>
                @foreach($fundClusters as $cluster)
                    <option value="{{ $cluster->id }}"
                        {{ old('fund_cluster_id') == $cluster->id ? 'selected' : '' }}>
                        {{ $cluster->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-2">Save</button>
    </form>
</div>
@endsection
