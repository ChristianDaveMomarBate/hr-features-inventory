<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagementController;

Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
});

// Handle form submissions to save new items
Route::post('/inventory/store', [HomeController::class, 'store'])->middleware(['auth', 'role:admin'])->name('inventory.store');
Route::put('/inventory/update/{id}', [HomeController::class, 'update'])->middleware(['auth', 'role:admin'])->name('inventory.update');
Route::delete('/inventory/delete/{id}', [HomeController::class, 'destroy'])->middleware(['auth', 'role:admin'])->name('inventory.destroy');
Route::get('/inventory/export/pdf', [ExportController::class, 'exportPDF'])->middleware('auth')->name('inventory.export.pdf');
Route::get('/inventory/export/excel', [ExportController::class, 'exportExcel'])->middleware('auth')->name('inventory.export.excel');

// Stock Management
Route::post('/stock/store', [\App\Http\Controllers\StockController::class, 'store'])->middleware(['auth', 'role:admin,staff'])->name('stock.store');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth')->name('notifications.read');
Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');

// Dashboard Route
Route::get('/dashboard/{page?}', [HomeController::class, 'index'])->middleware('auth')->name('dashboard');
