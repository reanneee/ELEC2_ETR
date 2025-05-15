@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Entity</h2>
    <form action="{{ route('entities.update', $entity->entity_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="entity_name">Entity Name</label>
            <input type="text" name="entity_name" value="{{ $entity->entity_name }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="branch_id">Branch</label>
            <select name="branch_id" class="form-control" required>
                <option value="">-- Select Branch --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->branch_id }}"
                        {{ old('branch_id', $entity->branch_id) == $branch->branch_id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="fund_cluster_id">Fund Cluster</label>
            <select name="fund_cluster_id" class="form-control" required>
                <option value="">-- Select Fund Cluster --</option>
                @foreach($fundClusters as $cluster)
                    <option value="{{ $cluster->id }}"
                        {{ old('fund_cluster_id', $entity->fund_cluster_id) == $cluster->id ? 'selected' : '' }}>
                        {{ $cluster->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Update</button>
    </form>
</div>
@endsection
