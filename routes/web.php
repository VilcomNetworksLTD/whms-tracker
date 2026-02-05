<?php

use Illuminate\Support\Facades\Route;
// Update this path to include the "Api" folder where your controller lives
use App\Http\Controllers\Api\FormController; 

<<<<<<< HEAD
// The default home page
=======

>>>>>>> 447465f2ca67def0c1b7b07f9758a60e292d952b
Route::get('/', function () {
    return view('welcome');
});

<<<<<<< HEAD
// Using 'forms' as the resource name to match your controller
Route::resource('forms', FormController::class);

// Dashboard route
Route::get('/dashboard', [FormController::class, 'index'])->name('dashboard');
=======

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
>>>>>>> 447465f2ca67def0c1b7b07f9758a60e292d952b
