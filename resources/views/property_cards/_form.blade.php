@csrf

<div class="mb-3">
    <label for="entity_id" class="form-label">Entity</label>
    <select name="entity_id" id="entity_id" class="form-select" required>
        <option value="" disabled {{ old('entity_id', $property_card->entity_id ?? '') ? '' : 'selected' }}>-- Select Entity --</option>
        @foreach($entities as $entity)
            <option value="{{ $entity->entity_id }}" {{ old('entity_id', $property_card->entity_id ?? '') == $entity->entity_id ? 'selected' : '' }}>
                {{ $entity->entity_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="property_number" class="form-label">Property Number</label>
    <input type="text" name="property_number" id="property_number" class="form-control" value="{{ old('property_number', $property_card->property_number ?? '') }}" required>
</div>

<div class="mb-4">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $property_card->description ?? '') }}</textarea>
</div>

<h3 class="mb-3">Movement Records</h3>

<div class="table-responsive">
    <table class="table table-bordered align-middle" id="movement-table" style="min-width: 900px;">
        <thead class="table-light text-center align-middle">
            <tr>
                <th rowspan="2">Date</th>
                <th rowspan="2">PAR</th>
                <th rowspan="2">Receipt Qty</th>
                <th colspan="2">Issue / Transfer / Disposal</th>
                <th rowspan="2">Balance</th>
                <th rowspan="2">Amount</th>
                <th rowspan="2">Remarks</th>
                <th rowspan="2">Action</th>
            </tr>
            <tr>
                <th>Qty</th>
                <th>Officer</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($property_card) && $property_card->movements->count())
                @foreach($property_card->movements as $i => $movement)
                    <tr>@include('property_cards._movement_row', ['movement' => $movement, 'i' => $i])</tr>
                @endforeach
            @else
                <tr>@include('property_cards._movement_row', ['movement' => null, 'i' => 0])</tr>
            @endif
        </tbody>
    </table>
</div>

<div class="mb-4">
    <button type="button" class="btn btn-outline-secondary" id="add-row">Add Row</button>
</div>

<div>
    <button type="submit" class="btn btn-primary">Save</button>
</div>
