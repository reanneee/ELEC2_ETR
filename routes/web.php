<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FundClusterController;
use App\Http\Controllers\InventoryCountFormController;
use App\Http\Controllers\ReceivedEquipmentController;
use App\Http\Controllers\PropertyCardController;
use App\Http\Controllers\ReceivedEquipmentDescriptionController;
use App\Models\ReceivedEquipmentItem;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');

    Route::resource('entities', EntityController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('fund_clusters', FundClusterController::class);
    Route::resource('equipment-list', ReceivedEquipmentDescriptionController::class);

    Route::resource('received_equipment', ReceivedEquipmentController::class);
    Route::resource('descriptions', InventoryCountFormController::class);
    // Special route for creating equipment for a specific entity
    Route::get('received_equipment/entity/{entityId}', [ReceivedEquipmentController::class, 'createWithEntity'])
        ->name('received_equipment.create_with_entity');
    Route::get('/received_equipment/{id}/generate-pdf', [ReceivedEquipmentController::class, 'generatePdf'])
        ->name('received_equipment.generate_pdf');
    Route::delete('received_equipment/descriptions/{descriptionId}/items/{itemId}', [ReceivedEquipmentController::class, 'deleteEquipmentItem'])->name('received_equipment.delete_item');


Route::get('/property_cards/create-row-template', function () {
    return view('property_cards._movement_row', [
        'movement' => null,
        'i' => '__INDEX__',
    ])->render();
})->name('property_cards.movement_row');

Route::get('/property-cards/{property_card}/pdf', [PropertyCardController::class, 'pdf'])->name('property_cards.pdf');

Route::get('property_cards', [PropertyCardController::class, 'index'])->name('property_cards.index');
Route::get('property_cards/create', [PropertyCardController::class, 'create'])->name('property_cards.create');
Route::post('property_cards', [PropertyCardController::class, 'store'])->name('property_cards.store');
Route::get('property_cards/{property_card}/edit', [PropertyCardController::class, 'edit'])->name('property_cards.edit');
Route::put('property_cards/{property_card}', [PropertyCardController::class, 'update'])->name('property_cards.update');
Route::delete('property_cards/{property_card}', [PropertyCardController::class, 'destroy'])->name('property_cards.destroy');


// Inventory routes
Route::get('/inventory/create', [InventoryCountFormController::class, 'create'])->name('inventory.create');
Route::post('/inventory/create', [InventoryCountFormController::class, 'createInventory'])->name('inventory.create.post');
Route::post('/inventory', [InventoryCountFormController::class, 'store'])->name('inventory.store');
Route::get('/inventory', [InventoryCountFormController::class, 'index'])->name('inventory.index');

// API routes
Route::post('/api/generate-property-number', [ReceivedEquipmentDescriptionController::class, 'generatePropertyNumber']);
Route::post('/api/save-linked-equipment-item', [InventoryCountFormController::class, 'saveLinkedEquipmentItem']);
});


