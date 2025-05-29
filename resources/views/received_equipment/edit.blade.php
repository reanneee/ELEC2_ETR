@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold">Edit Received Equipment</h2>
    <!-- Entity Info -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Entity Information</h5>
            <div class="row">
                <div class="col-md-6"><strong>Branch:</strong> {{ $entity->branch->branch_name }}</div>
                <div class="col-md-6"><strong>Fund Cluster:</strong> {{ $entity->fundCluster->name }}</div>
                <div class="col-md-6"><strong>Entity Name:</strong> {{ $entity->entity_name }}</div>
            </div>
        </div>
    </div>

    <!-- Form Start -->
    <form action="{{ route('received_equipment.update', $receivedEquipment->equipment_id) }}" method="POST" id="equipmentForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="entity_id" value="{{ $entity->entity_id }}">

        <div class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label for="par_no" class="form-label fw-semibold">PAR Number</label>
                <input type="text" class="form-control" value="{{ $receivedEquipment->par_no }}" readonly>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Equipment Entry</h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="quantity" class="form-control" min="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <input type="text" id="unit" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <textarea id="description" class="form-control" rows="1" style="resize:none; overflow:hidden;" oninput="autoResizeTextarea(this)"></textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date Acquired</label>
                        <input type="date" id="date_acquired" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Amount</label>
                        <input type="number" id="amount" class="form-control" step="0.01" min="0">
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-success" onclick="generateRows()">Generate Rows</button>
                    <button type="button" class="btn btn-outline-primary" onclick="addManualRow()">Add One Row</button>
                </div>
            </div>
        </div>
        
        @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some errors with your input:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Equipment Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Equipment Details</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="equipmentTable">
                        <thead class="table-light">
                            <tr>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Property No</th>
                                <th>Serial No</th>
                                <th>Date Acquired</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receivedEquipment->descriptions as $descIndex => $description)
                                @foreach($description->items as $itemIndex => $item)
                                    <tr data-group="{{ $descIndex }}" @if($itemIndex > 0) class="subrow" @endif>
                                        @if($itemIndex === 0)
                                            <td rowspan="{{ $description->items->count() }}">
                                                <input type="number" name="equipments[{{ $descIndex }}][quantity]" class="form-control" value="{{ $description->quantity }}" readonly>
                                            </td>
                                            <td rowspan="{{ $description->items->count() }}">
                                                <input type="text" name="equipments[{{ $descIndex }}][unit]" class="form-control" value="{{ $description->unit }}" readonly>
                                            </td>
                                            <td rowspan="{{ $description->items->count() }}">
                                                <textarea 
                                                    name="equipments[{{ $descIndex }}][description]" 
                                                    class="form-control auto-resize-textarea" 
                                                    rows="1" 
                                                    style="resize:none; overflow:hidden;" 
                                                    oninput="autoResizeTextarea(this)"
                                                    readonly
                                                >{{ $description->description }}</textarea>
                                            </td>
                                        @endif
                                        
                                        <td>
                                            <input type="text" name="equipments[{{ $descIndex }}][items][{{ $itemIndex }}][property_no]" class="form-control" value="{{ $item->property_no }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="equipments[{{ $descIndex }}][items][{{ $itemIndex }}][serial_no]" class="form-control" value="{{ $item->serial_no }}">
                                        </td>
                                        <td>
                                            <input type="date" name="equipments[{{ $descIndex }}][items][{{ $itemIndex }}][date_acquired]" class="form-control" value="{{ $item->date_acquired }}" required>
                                        </td>
                                        <td>
                                            <input type="number" name="equipments[{{ $descIndex }}][items][{{ $itemIndex }}][amount]" class="form-control amount-field" value="{{ $item->amount }}" step="0.01" oninput="calculateTotal()" required>
                                        </td>
                                        
                                        @if($itemIndex === 0)
                                            <td rowspan="{{ $description->items->count() }}">
                                                <!-- <button type="button" class="btn btn-danger btn-sm" onclick="deleteRowGroup({{ $descIndex }})">Delete Group</button>  -->
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="6" class="text-end fw-semibold">Total</td>
                                <td><input type="text" id="totalAmount" class="form-control" value="{{ $receivedEquipment->amount }}" readonly></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Receiving Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Receiving & Verification</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Received By (Name)</label>
                        <input type="text" name="received_by_name" class="form-control" value="{{ $receivedEquipment->received_by_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Received By (Designation)</label>
                        <input type="text" name="received_by_designation" class="form-control" value="{{ $receivedEquipment->received_by_designation }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Verified By (Name)</label>
                        <input type="text" name="verified_by_name" class="form-control" value="{{ $receivedEquipment->verified_by_name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Verified By (Designation)</label>
                        <input type="text" name="verified_by_designation" class="form-control" value="{{ $receivedEquipment->verified_by_designation }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Receipt</label>
                    <input type="date" name="receipt_date" class="form-control" value="{{ $receivedEquipment->receipt_date }}" required>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <a href="{{ route('received_equipment.index') }}" class="btn btn-secondary btn-lg me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg" onclick="validateBeforeSubmit(event)">Update Equipment</button>
        </div>
    </form>
</div>


<script>
var equipmentIndex = {{ $receivedEquipment->descriptions->count() }};

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Initialize auto resize for existing textareas on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.auto-resize-textarea').forEach(autoResizeTextarea);
    calculateTotal(); // Calculate total on page load
});

function generateRows() {
    const qty = parseInt(document.getElementById('quantity').value);
    const unit = document.getElementById('unit').value.trim();
    const desc = document.getElementById('description').value.trim();
    const date = document.getElementById('date_acquired').value;
    const amount = parseFloat(document.getElementById('amount').value);

    if (!qty || !unit || !desc || !date || isNaN(amount)) {
        alert("Please fill all fields before generating.");
        return;
    }

    const tbody = document.querySelector('#equipmentTable tbody');

    for (let i = 0; i < qty; i++) {
        const tr = document.createElement('tr');
        tr.dataset.group = equipmentIndex;
        
        if (i === 0) {
            tr.innerHTML += `
                <td rowspan="${qty}">
                    <input type="number" name="equipments[${equipmentIndex}][quantity]" class="form-control" value="${qty}" readonly>
                </td>
                <td rowspan="${qty}">
                    <input type="text" name="equipments[${equipmentIndex}][unit]" class="form-control" value="${unit}" readonly>
                </td>
                <td rowspan="${qty}">
                    <textarea 
                        name="equipments[${equipmentIndex}][description]" 
                        class="form-control auto-resize-textarea" 
                        rows="1" 
                        style="resize:none; overflow:hidden;" 
                        oninput="autoResizeTextarea(this)"
                        readonly
                    >${desc}</textarea>
                </td>
            `;
        } else {
            tr.classList.add('subrow');
        }

        tr.innerHTML += `
            <td>
                <input type="text" name="equipments[${equipmentIndex}][items][${i}][property_no]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="equipments[${equipmentIndex}][items][${i}][serial_no]" class="form-control">
            </td>
            <td>
                <input type="date" name="equipments[${equipmentIndex}][items][${i}][date_acquired]" class="form-control" value="${date}" required>
            </td>
            <td>
                <input type="number" name="equipments[${equipmentIndex}][items][${i}][amount]" class="form-control amount-field" value="${amount}" step="0.01" oninput="calculateTotal()" required>
            </td>
        `;

        if (i === 0) {
            tr.innerHTML += `
                <td rowspan="${qty}">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRowGroup(${equipmentIndex})">Delete Group</button>
                </td>
            `;
        }

        tbody.appendChild(tr);
    }

    equipmentIndex++;
    calculateTotal();
    
    // Clear form fields
    document.getElementById('quantity').value = '';
    document.getElementById('unit').value = '';
    document.getElementById('description').value = '';
    document.getElementById('date_acquired').value = '';
    document.getElementById('amount').value = '';

    // Auto-resize newly created textareas
    document.querySelectorAll('.auto-resize-textarea').forEach(autoResizeTextarea);
}

function addManualRow() {
    const tbody = document.querySelector('#equipmentTable tbody');
    const tr = document.createElement('tr');
    tr.dataset.group = equipmentIndex;
    tr.innerHTML = `
        <td><input type="number" name="equipments[${equipmentIndex}][quantity]" class="form-control" value="1" required></td>
        <td><input type="text" name="equipments[${equipmentIndex}][unit]" class="form-control" required></td>
        <td><textarea name="equipments[${equipmentIndex}][description]" class="form-control auto-resize-textarea" rows="1" style="resize:none; overflow:hidden;" oninput="autoResizeTextarea(this)" required></textarea></td>
        <td><input type="text" name="equipments[${equipmentIndex}][items][0][property_no]" class="form-control" required></td>
        <td><input type="text" name="equipments[${equipmentIndex}][items][0][serial_no]" class="form-control"></td>
        <td><input type="date" name="equipments[${equipmentIndex}][items][0][date_acquired]" class="form-control" required></td>
        <td><input type="number" name="equipments[${equipmentIndex}][items][0][amount]" class="form-control amount-field" step="0.01" oninput="calculateTotal()" required></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="deleteRowGroup(${equipmentIndex})">Delete</button></td>
    `;
    tbody.appendChild(tr);
    equipmentIndex++;

    // Auto-resize textarea in new manual row
    document.querySelectorAll('.auto-resize-textarea').forEach(autoResizeTextarea);
    calculateTotal();
}

function deleteRowGroup(groupId) {
    document.querySelectorAll(`tr[data-group="${groupId}"]`).forEach(row => row.remove());
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.amount-field').forEach(input => {
        const val = parseFloat(input.value);
        if (!isNaN(val)) total += val;
    });
    document.getElementById('totalAmount').value = total.toFixed(2);
}

function validateBeforeSubmit(event) {
    // Check if we have equipment items
    const equipmentInputs = document.querySelectorAll('input[name*="equipments"][name*="property_no"]');
    if (equipmentInputs.length === 0) {
        event.preventDefault();
        alert('Please add at least one equipment item before saving.');
        return false;
    }
    
    // Check if all required fields are filled
    const form = document.getElementById('equipmentForm');
    const requiredFields = form.querySelectorAll('[required]');
    let hasEmptyFields = false;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            hasEmptyFields = true;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (hasEmptyFields) {
        event.preventDefault();
        alert('Please fill all required fields.');
        return false;
    }
    
    return true;
}

// Add CSS for invalid fields
const style = document.createElement('style');
style.textContent = `
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
`;
document.head.appendChild(style);
</script>
@endsection