<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

Auth::routes();

// Root URL -> Login page
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (accessible after login)
Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');