@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Create Inventory Count Form</h1>
        <a href="{{ route('descriptions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Descriptions
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST" id="inventoryForm">
        @csrf

        <!-- Form Header Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Inventory Count Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="entity_id" class="form-label">Entity <span class="text-danger">*</span></label>
                        <select class="form-select" id="entity_id" name="entity_id" required>
                            <option value="">Select Entity</option>
                            @foreach($entities as $entity)
                            <option value="{{ $entity->entity_id }}" {{ old('entity_id') == $entity->entity_id ? 'selected' : '' }}>
                                {{ $entity->entity_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="inventory_date" class="form-label">Inventory Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="inventory_date" name="inventory_date"
                            value="{{ old('inventory_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="total_items" class="form-label">Total Items</label>
                        <input type="text" class="form-control" id="total_items" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selected Equipment Items -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Selected Equipment for Inventory Count</h5>
                <small class="text-muted">Complete the inventory details for each selected item</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="inventoryTable">
                        <thead class="table-light">
                            <tr>
                                <th width="7%">Article/Item</th>
                                <th width="18%">Description</th>
                                <th width="10%">Old Property No.</th>
                                <th width="10%">New Property No.</th>
                                <th width="6%">Unit</th>
                                <th width="7%">Unit Value</th>
                                <th width="6%">Qty Card</th>
                                <th width="6%">Qty Physical</th>
                                <th width="10%">Location</th>
                                <th width="7%">Condition</th>
                                <th width="13%">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
  <!-- Fixed section for your Blade template -->
@php $itemIndex = 0; @endphp
@foreach($processedDescriptions as $description)
    @foreach($description->items as $item)
        @php
        $fundMatch = $fundMatches->get($item->item_id);
        $equipmentItem = $equipmentItems->firstWhere('property_no', $item->property_no);
        $linkedItem = $linkedItems->get($item->property_no);
        
        // Construct the full new property number from linked_equipment_items table
        $currentNewPropertyNo = '';
        if ($linkedItem) {
            // Use the constructed full_new_property_no from the SQL query
            $currentNewPropertyNo = $linkedItem->full_new_property_no;
        }
        @endphp
        <tr class="inventory-row" data-description-id="{{ $description->description_id }}">
            <!-- Article/Item Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][article_item]"
                    value="{{ $fundMatch->account_title ?? 'N/A' }}" readonly>
            </td>

            <!-- Description Column -->
            <td>
                <textarea class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][description]"
                    rows="2" readonly>{{ $description->description }}</textarea>
                <input type="hidden" name="inventory_items[{{ $itemIndex }}][entity_id]" value="">
                <small class="text-muted">Selected: {{ $description->inventory_quantity }} of {{ $description->total_available }} available</small>
            </td>

            <!-- Old Property No Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][old_property_no]"
                    value="{{ $item->property_no }}" readonly>
            </td>

            <!-- New Property No Column -->
            <td>
                <input type="text" class="form-control form-control-sm new-property-input"
                    name="inventory_items[{{ $itemIndex }}][new_property_no]"
                    value="{{ $currentNewPropertyNo }}"
                    data-old-property="{{ $item->property_no }}"
                    data-fund-account-code="{{ $fundMatch->account_code ?? '' }}"
                    data-linked-item-id="{{ $linkedItem->id ?? '' }}"
                    data-reference-mmdd="{{ $linkedItem->reference_mmdd ?? '' }}"
                    data-sequence="{{ $linkedItem->new_property_no ?? '' }}"
                    data-location-code="{{ $linkedItem->location ?? '' }}"
                    readonly>
            </td>

            <!-- Unit Column -->
            <td>
                <input type="text" class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][unit]"
                    value="{{ $description->unit }}" readonly>
            </td>

            <!-- Unit Value Column -->
            <td>
                <input type="number" class="form-control form-control-sm unit-value"
                    name="inventory_items[{{ $itemIndex }}][unit_value]"
                    value="{{ $item->unit_value ?? $description->unit_value ?? 0 }}"
                    step="0.01" min="0">
            </td>

            <!-- Qty Card Column - This represents 1 unit per individual item -->
            <td>
                <input type="number" class="form-control form-control-sm qty-card"
                    name="inventory_items[{{ $itemIndex }}][qty_card]"
                    value="1"
                    min="0" readonly>
            </td>

            <!-- Qty Physical Column - Default to 1 for physical count -->
            <td>
                <input type="number" class="form-control form-control-sm qty-physical"
                    name="inventory_items[{{ $itemIndex }}][qty_physical]"
                    value="1"
                    min="0" required>
            </td>

            <!-- Location Column -->
            <td>
                <select class="form-select form-select-sm location-select"
                    name="inventory_items[{{ $itemIndex }}][location]"
                    data-row-index="{{ $itemIndex }}" required>
                    <option value="">Select Location</option>
                    @foreach($locations as $location)
                    <option value="{{ $location->building_name }} - {{ $location->office_name }}"
                        data-location-id="{{ $location->id }}"
                        {{ (optional($equipmentItem)->location_id == $location->id) ? 'selected' : '' }}>
                        {{ $location->building_name }}
                        @if($location->office_name)
                        - {{ $location->office_name }}
                        @endif
                        @if($location->officer_name)
                        ({{ $location->officer_name }})
                        @endif
                    </option>
                    @endforeach
                </select>
            </td>

            <!-- Condition Column -->
            <td>
                <select class="form-select form-select-sm condition-select"
                    name="inventory_items[{{ $itemIndex }}][condition]" required>
                    <option value="">Select Condition</option>
                    <option value="Serviceable" {{ ($item->condition ?? '') == 'Serviceable' ? 'selected' : '' }}>Serviceable</option>
                    <option value="Unserviceable" {{ ($item->condition ?? '') == 'Unserviceable' ? 'selected' : '' }}>Unserviceable</option>
                    <option value="For Repair" {{ ($item->condition ?? '') == 'For Repair' ? 'selected' : '' }}>For Repair</option>
                    <option value="For Disposal" {{ ($item->condition ?? '') == 'For Disposal' ? 'selected' : '' }}>For Disposal</option>
                    <option value="Missing" {{ ($item->condition ?? '') == 'Missing' ? 'selected' : '' }}>Missing</option>
                    <option value="Damaged" {{ ($item->condition ?? '') == 'Damaged' ? 'selected' : '' }}>Damaged</option>
                    <option value="New" {{ ($item->condition ?? '') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Used - Good" {{ ($item->condition ?? '') == 'Used - Good' ? 'selected' : '' }}>Used - Good</option>
                    <option value="Used - Fair" {{ ($item->condition ?? '') == 'Used - Fair' ? 'selected' : '' }}>Used - Fair</option>
                    <option value="Obsolete" {{ ($item->condition ?? '') == 'Obsolete' ? 'selected' : '' }}>Obsolete</option>
                </select>
            </td>

            <!-- Remarks Column -->
            <td>
                <textarea class="form-control form-control-sm"
                    name="inventory_items[{{ $itemIndex }}][remarks]"
                    rows="2" placeholder="Enter remarks..."></textarea>
            </td>
        </tr>
        @php $itemIndex++; @endphp
    @endforeach
@endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Remarks Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Additional Information</h5>
            </div>
            <div class="card-body">
              

                <!-- Signature Fields -->
                <div class="row">
                    <div class="col-md-4">
                        <label for="prepared_by_name" class="form-label">Prepared By (Name)</label>
                        <input type="text" class="form-control" id="prepared_by_name"
                            name="prepared_by_name" value="{{ old('prepared_by_name') }}">
                        <label for="prepared_by_position" class="form-label mt-2">Position</label>
                        <input type="text" class="form-control" id="prepared_by_position"
                            name="prepared_by_position" value="{{ old('prepared_by_position') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="reviewed_by_name" class="form-label">Reviewed By (Name)</label>
                        <input type="text" class="form-control" id="reviewed_by_name"
                            name="reviewed_by_name" value="{{ old('reviewed_by_name') }}">
                        <label for="reviewed_by_position" class="form-label mt-2">Position</label>
                        <input type="text" class="form-control" id="reviewed_by_position"
                            name="reviewed_by_position" value="{{ old('reviewed_by_position') }}">
                    </div>
                   
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Summary</h6>
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 id="summaryTotalItems" class="text-primary">{{ $itemIndex }}</h4>
                                        <small>Total Items</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryCardQty" class="text-info">0</h4>
                                        <small>Card Quantity</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryPhysicalQty" class="text-success">0</h4>
                                        <small>Physical Count</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 id="summaryTotalValue" class="text-warning">₱0.00</h4>
                                        <small>Total Value</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save"></i> Save Inventory Count
                            </button>
                            <a href="{{ route('descriptions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .table td,
    .table th {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .form-control-sm,
    .form-select-sm {
        font-size: 0.775rem;
    }

    .inventory-row:hover {
        background-color: #f8f9fa;
    }

    .qty-mismatch {
        background-color: #fff3cd;
    }

    .qty-match {
        background-color: #d1e7dd;
    }
</style>

<script>
// Updated JavaScript section for the Blade template
document.addEventListener('DOMContentLoaded', function() {
    const entitySelect = document.getElementById('entity_id');
    const totalItemsInput = document.getElementById('total_items');
    const inventoryRows = document.querySelectorAll('.inventory-row');

    // Update entity_id in hidden inputs when entity is selected
    entitySelect.addEventListener('change', function() {
        const selectedEntityId = this.value;
        document.querySelectorAll('input[name*="[entity_id]"]').forEach(input => {
            input.value = selectedEntityId;
        });
    });

    // Function to generate new property number format: YEAR-reference_mmdd-new_property_no-location
    function generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, inputElement) {
        if (!fundAccountCode) {
            inputElement.value = '';
            return;
        }

        // Extract existing data from data attributes
        let referenceMmdd = inputElement.getAttribute('data-reference-mmdd');
        let sequence = inputElement.getAttribute('data-sequence');

        // If no existing data, generate new ones
        if (!referenceMmdd || !sequence) {
            // Extract 4th to 7th digits from fund account code and format as MM-DD
            const digits = fundAccountCode.substring(3, 7);
            referenceMmdd = digits.substring(0, 2) + '-' + digits.substring(2, 4);
            
            // Generate sequence number based on row index
            const rowIndex = inputElement.closest('tr').querySelector('.location-select').getAttribute('data-row-index');
            sequence = String(parseInt(rowIndex) + 1).padStart(4, '0');
        }

        // Format location code
        const locationCode = locationId ? String(locationId).padStart(2, '0') : '00';

        const currentYear = new Date().getFullYear();
        const newPropertyNo = `${currentYear}-${referenceMmdd}-${sequence}-${locationCode}`;
        
        inputElement.value = newPropertyNo;

        // Update data attributes for future reference
        inputElement.setAttribute('data-reference-mmdd', referenceMmdd);
        inputElement.setAttribute('data-sequence', sequence);
        inputElement.setAttribute('data-location-code', locationCode);

        // Save to database via AJAX
        if (locationId && oldPropertyNo) {
            saveOrUpdateLinkedEquipmentItem(oldPropertyNo, referenceMmdd, sequence, locationCode);
        }
    }

    // Generate initial property numbers on page load for items that don't have them
    inventoryRows.forEach((row, index) => {
        const newPropertyInput = row.querySelector('.new-property-input');
        const oldPropertyNo = newPropertyInput.getAttribute('data-old-property');
        const fundAccountCode = newPropertyInput.getAttribute('data-fund-account-code');
        const locationSelect = row.querySelector('.location-select');
        
        // Only generate if there's no existing new property number
        if (!newPropertyInput.value && oldPropertyNo && fundAccountCode) {
            // Get current location ID if selected
            let locationId = null;
            if (locationSelect && locationSelect.value) {
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                locationId = selectedOption.getAttribute('data-location-id');
            }

            generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, newPropertyInput);
        }
    });

    // Update summary statistics
    function updateSummary() {
        let totalCardQty = 0;
        let totalPhysicalQty = 0;
        let totalValue = 0;
        let totalItems = inventoryRows.length;

        inventoryRows.forEach(row => {
            const cardQty = parseInt(row.querySelector('.qty-card').value) || 0;
            const physicalQty = parseInt(row.querySelector('.qty-physical').value) || 0;
            const unitValue = parseFloat(row.querySelector('.unit-value').value) || 0;

            totalCardQty += cardQty;
            totalPhysicalQty += physicalQty;
            totalValue += (physicalQty * unitValue);

            // Highlight quantity mismatches
            row.classList.remove('qty-mismatch', 'qty-match');
            if (cardQty !== physicalQty) {
                row.classList.add('qty-mismatch');
            } else {
                row.classList.add('qty-match');
            }
        });

        document.getElementById('summaryTotalItems').textContent = totalItems;
        document.getElementById('summaryCardQty').textContent = totalCardQty;
        document.getElementById('summaryPhysicalQty').textContent = totalPhysicalQty;
        document.getElementById('summaryTotalValue').textContent = '₱' + totalValue.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        totalItemsInput.value = totalItems + ' items';
    }

    // Add event listeners for quantity and value changes
    inventoryRows.forEach(row => {
        const qtyPhysical = row.querySelector('.qty-physical');
        const unitValue = row.querySelector('.unit-value');
        const locationSelect = row.querySelector('.location-select');

        [qtyPhysical, unitValue].forEach(input => {
            input.addEventListener('input', updateSummary);
        });

        // Handle location change to update new property number and linked_equipment_items
        if (locationSelect) {
            locationSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const locationId = selectedOption.getAttribute('data-location-id');
                const newPropertyInput = row.querySelector('.new-property-input');
                const oldPropertyNo = newPropertyInput.getAttribute('data-old-property');
                const fundAccountCode = newPropertyInput.getAttribute('data-fund-account-code');

                if (oldPropertyNo && fundAccountCode && locationId) {
                    generateNewPropertyNumber(oldPropertyNo, fundAccountCode, locationId, newPropertyInput);
                }
            });
        }
    });

    // Function to save or update linked equipment item via AJAX
    function saveOrUpdateLinkedEquipmentItem(oldPropertyNo, referenceMmdd, sequence, locationCode) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }

        fetch('/api/save-linked-equipment-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({
                    original_property_no: oldPropertyNo,
                    reference_mmdd: referenceMmdd,
                    new_property_no: sequence,
                    location: locationCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Linked equipment item saved successfully');
                } else {
                    console.error('Error saving linked equipment item:', data.message);
                }
            })
            .catch(error => {
                console.error('Error saving linked equipment item:', error);
            });
    }

    // Form validation and submission
    document.getElementById('inventoryForm').addEventListener('submit', function(e) {
        const entityId = entitySelect.value;
        if (!entityId) {
            e.preventDefault();
            alert('Please select an entity before submitting.');
            entitySelect.focus();
            return false;
        }

        // Check if all required fields are filled
        const requiredFields = this.querySelectorAll('[required]');
        let hasEmptyFields = false;
        let firstEmptyField = null;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                hasEmptyFields = true;
                field.classList.add('is-invalid');
                if (!firstEmptyField) {
                    firstEmptyField = field;
                }
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (hasEmptyFields) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            if (firstEmptyField) {
                firstEmptyField.focus();
            }
            return false;
        }

        // Validate that all locations are selected
        let missingLocations = false;
        inventoryRows.forEach(row => {
            const locationSelect = row.querySelector('.location-select');
            if (!locationSelect.value) {
                missingLocations = true;
                locationSelect.classList.add('is-invalid');
            } else {
                locationSelect.classList.remove('is-invalid');
            }
        });

        if (missingLocations) {
            e.preventDefault();
            alert('Please select a location for all items.');
            return false;
        }

        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitButton.disabled = true;

        // Confirm submission
        if (!confirm('Are you sure you want to save this inventory count? This action cannot be undone.')) {
            e.preventDefault();
            // Restore button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            return false;
        }

        return true;
    });

    // Initialize summary
    updateSummary();

    // Set initial entity_id values
    if (entitySelect.value) {
        document.querySelectorAll('input[name*="[entity_id]"]').forEach(input => {
            input.value = entitySelect.value;
        });
    }
});
</script>
@endsection