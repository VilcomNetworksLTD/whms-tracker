<?php

<<<<<<< HEAD
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\DomainController;

Route::apiResource('forms', FormController::class);
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;       
use App\Http\Controllers\TrackerFormController; 


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);


>>>>>>> 447465f2ca67def0c1b7b07f9758a60e292d952b
