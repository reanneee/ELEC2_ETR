<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\FundClusterController;
use App\Http\Controllers\ReceivedEquipmentController;

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


    Route::resource('received_equipment', ReceivedEquipmentController::class);
 // Special route for creating equipment for a specific entity
Route::get('received_equipment/entity/{entityId}', [ReceivedEquipmentController::class, 'createWithEntity'])
->name('received_equipment.create_with_entity');



Route::get('/received_equipment/{id}/generate-pdf', [ReceivedEquipmentController::class, 'generatePdf'])
     ->name('received_equipment.generate_pdf');

});