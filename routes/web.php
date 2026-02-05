<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController; 

// Default home page
Route::get('/', function () {
    return view('welcome');
});

// Using 'forms' as the resource name to match your controller
Route::resource('forms', FormController::class);

// Dashboard route
Route::get('/dashboard', [FormController::class, 'index'])->name('dashboard');

// Catch-all route (needed for SPAs/Frontends to handle routing)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');