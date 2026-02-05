<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService; 
class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->authService->register($validated);

        return response()->json([
            'message' => 'Registration successful. OTP sent to email.',
            'email' => $validated['email']
        ], 201);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $token = $this->authService->verifyOtp($validated);

        return response()->json([
            'message' => 'Email verified successfully.',
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $token = $this->authService->login($credentials);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ]);
    }

    public function resendOtp(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email'
    ]);

    $this->authService->resendOtp($validated);

    return response()->json([
        'message' => 'New code sent successfully.'
    ], 200);
}
}