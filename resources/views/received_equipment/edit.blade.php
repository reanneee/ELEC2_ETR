@extends('layouts.app')

@section('content')
<h1>Edit Equipment - PAR #{{ $equipment->par_no }}</h1>

@if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('received_equipment.update', $equipment->equipment_id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Entity:</label>
    <select name="entity_id">
        @foreach($entities as $entity)
            <option value="{{ $entity->entity_id }}" {{ $equipment->entity_id == $entity->entity_id ? 'selected' : '' }}>
                {{ $entity->entity_name }}
            </option>
        @endforeach
    </select><br><br>

    <label>Date Acquired:</label>
    <input type="date" name="date_acquired" value="{{ $equipment->date_acquired->format('Y-m-d') }}"><br><br>

    <label>Amount:</label>
    <input type="number" name="amount" step="0.01" value="{{ $equipment->amount }}"><br><br>

    <label>Received By Name:</label>
    <input type="text" name="received_by_name" value="{{ $equipment->received_by_name }}"><br><br>

    <label>Received By Designation:</label>
    <input type="text" name="received_by_designation" value="{{ $equipment->received_by_designation }}"><br><br>

    <label>Verified By Name:</label>
    <input type="text" name="verified_by_name" value="{{ $equipment->verified_by_name }}"><br><br>

    <label>Verified By Designation:</label>
    <input type="text" name="verified_by_designation" value="{{ $equipment->verified_by_designation }}"><br><br>

    <label>Receipt Date:</label>
    <input type="date" name="receipt_date" value="{{ $equipment->receipt_date->format('Y-m-d') }}"><br><br>

    <button type="submit">Update Equipment</button>
</form>

<a href="{{ route('received_equipment.index') }}">Back</a>
@endsection
