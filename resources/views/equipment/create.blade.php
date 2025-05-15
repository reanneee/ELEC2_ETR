@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Received Equipment for Entity: {{ $entity->entity_name }}</h2>

    <form action="{{ route('received_equipment.store') }}" method="POST">
        @csrf

        <input type="hidden" name="entity_id" value="{{ $entity->entity_id }}">

        <div class="mb-3">
            <label><strong>Branch:</strong></label>
            <p>{{ $entity->branch->branch_name }}</p>
        </div>

        <div class="mb-3">
            <label><strong>Entity Name:</strong></label>
            <p>{{ $entity->entity_name }}</p>
        </div>

        {{-- Fund Cluster Dropdown --}}
        <div class="mb-3">
            <label for="fund_id"><strong>Fund Cluster:</strong></label>
            <select id="fundSelect" name="fund_id" class="form-control" required>
                <option value="" disabled selected>Select Fund Cluster</option>
                @foreach($funds as $fund)
                    <option 
                        value="{{ $fund->fund_id }}" 
                        data-account-code="{{ $fund->account_code }}">
                        {{ $fund->account_title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="par_no">PAR Number</label>
            <input type="text" name="par_no" class="form-control" value="{{ $par_no }}" readonly>
        </div>

        {{-- Input Fields for Generating Equipment --}}
        <div class="row mb-3">
            <div class="col">
                <label>Quantity</label>
                <input type="number" id="quantity" class="form-control" required>
            </div>
            <div class="col">
                <label>Unit</label>
                <input type="text" id="unit" class="form-control" required>
            </div>
            <div class="col">
                <label>Description</label>
                <input type="text" id="description" class="form-control" required>
            </div>
            <div class="col">
                <label>Date Acquired</label>
                <input type="date" id="date_acquired" class="form-control" required>
            </div>
            <div class="col">
                <label>Amount</label>
                <input type="number" step="0.01" id="amount" class="form-control" required>
            </div>
            <div class="col">
                <label>Starting Property No</label>
                <input type="text" id="start_property_no" class="form-control" placeholder="e.g., 2024-001" required>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" onclick="generateRows()">Generate Equipment Rows</button>

        <table class="table table-bordered" id="equipmentTable">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Description</th>
                    <th>Property No</th>
                    <th>Date Acquired</th>
                    <th>Amount</th>
                    <th>Total</th>
                    <th>Serial Number</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be generated here -->
            </tbody>
        </table>

        {{-- Received & Verified By --}}
        <div class="mb-3">
            <label for="received_by_name">Received By (Name)</label>
            <input type="text" name="received_by_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="received_by_designation">Received By (Designation)</label>
            <input type="text" name="received_by_designation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="verified_by_name">Verified By (Name)</label>
            <input type="text" name="verified_by_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="verified_by_designation">Verified By (Designation)</label>
            <input type="text" name="verified_by_designation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="receipt_date">Receipt Date</label>
            <input type="date" name="receipt_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Received Equipment</button>
    </form>
</div>

<script>
    function generateRows() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const unit = document.getElementById('unit').value;
        const description = document.getElementById('description').value;
        const dateAcquired = document.getElementById('date_acquired').value;
        const amount = parseFloat(document.getElementById('amount').value);
        const startPropertyNo = document.getElementById('start_property_no').value;

        if (!quantity || !unit || !description || !dateAcquired || !amount || !startPropertyNo) {
            alert("Please fill out all fields before generating.");
            return;
        }

        const tbody = document.querySelector('#equipmentTable tbody');
        tbody.innerHTML = ''; // Clear previous rows

        let [year, number] = startPropertyNo.split('-');
        number = parseInt(number);

        for (let i = 0; i < quantity; i++) {
            const propertyNo = `${year}-${String(number + i).padStart(3, '0')}`;
            const total = (amount).toFixed(2);

            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="equipments[${i}][unit]" class="form-control" value="${unit}" readonly></td>
                <td><input type="text" name="equipments[${i}][description]" class="form-control" value="${description}" readonly></td>
                <td><input type="text" name="equipments[${i}][property_no]" class="form-control" value="${propertyNo}" readonly></td>
                <td><input type="date" name="equipments[${i}][date_acquired]" class="form-control" value="${dateAcquired}" readonly></td>
                <td><input type="number" name="equipments[${i}][amount]" class="form-control" value="${amount}" readonly></td>
                <td><input type="text" name="equipments[${i}][total]" class="form-control" value="${total}" readonly></td>
                <td><input type="text" name="equipments[${i}][serial_no]" class="form-control"></td>
            `;
            tbody.appendChild(row);
        }
    }
</script>
@endsection
