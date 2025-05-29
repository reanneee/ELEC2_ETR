<!-- descriptions.index -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Received Equipment Descriptions</h1>

    @if ($descriptions->count())
        <form id="inventoryForm" action="{{ route('inventory.create') }}" method="POST">
            @csrf
            <div class="mb-3">
                <button type="button" id="selectAll" class="btn btn-sm btn-secondary">Select All</button>
                <button type="button" id="deselectAll" class="btn btn-sm btn-secondary">Deselect All</button>
                <button type="submit" id="createInventory" class="btn btn-primary" disabled>
                    Create Inventory Count Form
                </button>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAllCheckbox">
                        </th>
                        <th>ID</th>
                        <th>PAR No</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Items</th>
                        <th>Fund</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($descriptions as $desc)
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       name="selected_items[]" 
                                       value="{{ $desc->description_id }}" 
                                       class="item-checkbox"
                                       data-description="{{ $desc->description }}"
                                       data-quantity="{{ $desc->quantity }}"
                                       data-unit="{{ $desc->unit }}">
                            </td>
                            <td>{{ $desc->description_id }}</td>
                            <td>{{ $desc->equipment->par_no ?? 'N/A' }}</td>
                            <td>{{ $desc->description }}</td>
                            <td>{{ $desc->quantity }}</td>
                            <td>{{ $desc->unit }}</td>
                            <td>
                                <ul>
                                    @forelse ($desc->items as $item)
                                        <li>
                                            <strong>{{ $item->name ?? 'No name available' }}</strong><br>
                                            Property No: {{ $item->property_no ?? 'N/A' }}<br>
                                            @if (!empty($item->serial_no))
                                                Serial: {{ $item->serial_no }}
                                            @endif
                                        </li>
                                    @empty
                                        <li>No items found</li>
                                    @endforelse
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @forelse ($desc->items as $item)
                                        <li>
                                            @if (isset($fundMatches[$item->item_id]))
                                                <em>{{ $fundMatches[$item->item_id]->account_title }}</em>
                                            @else
                                                <span class="text-danger">No match</span>
                                            @endif
                                        </li>
                                    @empty
                                        <li>No items found</li>
                                    @endforelse
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        {{-- Pagination links --}}
        {{ $descriptions->links() }}
    @else
        <p>No descriptions found.</p>
    @endif
</div>

<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quantityModalLabel">Set Inventory Count Quantities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="quantityInputs"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmCreate" class="btn btn-primary">Create Inventory Count</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const createInventoryBtn = document.getElementById('createInventory');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const quantityModal = new bootstrap.Modal(document.getElementById('quantityModal'));
    const quantityInputs = document.getElementById('quantityInputs');
    const confirmCreateBtn = document.getElementById('confirmCreate');

    // Update create button state
    function updateCreateButton() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        createInventoryBtn.disabled = checkedBoxes.length === 0;
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateCreateButton();
    });

    selectAllBtn.addEventListener('click', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        selectAllCheckbox.checked = true;
        updateCreateButton();
    });

    deselectAllBtn.addEventListener('click', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
        updateCreateButton();
    });

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateCreateButton();
            
            // Update select all checkbox
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            const totalCount = itemCheckboxes.length;
            
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
            selectAllCheckbox.checked = checkedCount === totalCount;
        });
    });

    // Create inventory form submission
    createInventoryBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        let quantityHTML = '';
        
        checkedBoxes.forEach(checkbox => {
            const description = checkbox.dataset.description;
            const maxQuantity = parseInt(checkbox.dataset.quantity);
            const unit = checkbox.dataset.unit;
            const descriptionId = checkbox.value;
            
            quantityHTML += `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><strong>${description}</strong></label>
                        <small class="text-muted d-block">Available: ${maxQuantity} ${unit}</small>
                    </div>
                    <div class="col-md-6">
                        <input type="number" 
                               class="form-control quantity-input" 
                               name="quantities[${descriptionId}]"
                               data-description-id="${descriptionId}"
                               min="1" 
                               max="${maxQuantity}" 
                               value="${maxQuantity}"
                               required>
                        <small class="text-muted">Max: ${maxQuantity}</small>
                    </div>
                </div>
            `;
        });
        
        quantityInputs.innerHTML = quantityHTML;
        quantityModal.show();
    });

    // Confirm create inventory
    confirmCreateBtn.addEventListener('click', function() {
        const form = document.getElementById('inventoryForm');
        const quantityInputsElements = document.querySelectorAll('.quantity-input');
        
        // Remove any existing quantity hidden inputs to prevent duplicates
        const existingQuantityInputs = form.querySelectorAll('input[name^="quantities["]');
        existingQuantityInputs.forEach(input => input.remove());
        
        // Debug: Log the quantities being processed
        console.log('Processing quantities:');
        quantityInputsElements.forEach(input => {
            console.log(`Description ID: ${input.dataset.descriptionId}, Quantity: ${input.value}`);
        });
        
        // Add current quantity inputs to the main form
        quantityInputsElements.forEach(input => {
            // Validate the input has a value
            if (input.value && input.dataset.descriptionId) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `quantities[${input.dataset.descriptionId}]`;
                hiddenInput.value = parseInt(input.value);
                form.appendChild(hiddenInput);
                
                // Debug: Log what's being added
                console.log(`Added hidden input: ${hiddenInput.name} = ${hiddenInput.value}`);
            }
        });
        
        // Debug: Log all form data before submission
        const formData = new FormData(form);
        console.log('Final form data:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        
        // Hide the modal
        quantityModal.hide();
        
        // Submit the form
        form.submit();
    });

    // Initialize button state
    updateCreateButton();
});
</script>
@endsection