<?php

use Illuminate\Support\Facades\Route;
// Update this path to include the "Api" folder where your controller lives
use App\Http\Controllers\Api\FormController; 

// The default home page
Route::get('/', function () {
    return view('welcome');
});

// Using 'forms' as the resource name to match your controller
Route::resource('forms', FormController::class);

// Dashboard route
Route::get('/dashboard', [FormController::class, 'index'])->name('dashboard');