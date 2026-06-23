<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;


Auth::routes();

Route::get('/', function () {
    return redirect()->route('login');
});

// Public Kiosk Routes (no login required)
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::post('/kiosk', [KioskController::class, 'store'])->name('kiosk.store');

// Handle form submissions to save new items
Route::post('/inventory/store', [HomeController::class, 'store'])->middleware(['auth', 'role:admin'])->name('inventory.store');
Route::put('/inventory/update/{id}', [HomeController::class, 'update'])->middleware(['auth', 'role:admin'])->name('inventory.update');
Route::delete('/inventory/delete/{id}', [HomeController::class, 'destroy'])->middleware(['auth', 'role:admin'])->name('inventory.destroy');
Route::get('/inventory/export/pdf', [ExportController::class, 'exportPDF'])->middleware(['auth', 'role:admin'])->name('inventory.export.pdf');
Route::get('/inventory/export/excel', [ExportController::class, 'exportExcel'])->middleware(['auth', 'role:admin'])->name('inventory.export.excel');

// Stock Management
Route::post('/stock/store', [\App\Http\Controllers\StockController::class, 'store'])->middleware(['auth', 'role:admin'])->name('stock.store');
Route::put('/stock/{id}', [\App\Http\Controllers\StockController::class, 'update'])->middleware(['auth', 'role:admin'])->name('stock.update');
Route::delete('/stock/{id}', [\App\Http\Controllers\StockController::class, 'destroy'])->middleware(['auth', 'role:admin'])->name('stock.destroy');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware(['auth', 'role:admin'])->name('notifications.read');
Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');



// Profile Route
Route::post('/profile', [ProfileController::class, 'update'])->middleware(['auth'])->name('profile.update');

// Dashboard Route
Route::get('/dashboard/{page?}', [HomeController::class, 'index'])->middleware(['auth', 'role:admin'])->name('dashboard');
