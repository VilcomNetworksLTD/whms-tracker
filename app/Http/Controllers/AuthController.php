<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        Log::info('Register payload:', $request->all());
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = $this->authService->register($validated);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. OTP sent to email.',
                'email' => $validated['email'],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Registration error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
            ]);

            $token = $this->authService->verifyOtp($validated);
            $user = \App\Models\User::where('email', $validated['email'])->first();

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('OTP verification error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            // Generate new OTP
            $code = rand(100000, 999999);

            // Update or create OTP record
            $otp = \App\Models\Otp::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'code' => $code,
                    'expires_at' => \Carbon\Carbon::now()->addMinutes(10),
                ]
            );

            // Send email
            \Illuminate\Support\Facades\Mail::to($validated['email'])
                ->send(new \App\Mail\OtpMail($code));

            return response()->json([
                'success' => true,
                'message' => 'OTP has been resent to your email.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Resend OTP error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $token = $this->authService->login($credentials);
            $user = \App\Models\User::where('email', $credentials['email'])->first();

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        }
    }


    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $this->authService->forgotPassword($request->all());

        return response()->json(['message' => 'Reset code sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:8|confirmed' 
        ]);

        $this->authService->resetPassword($request->all());

        return response()->json(['message' => 'Password changed successfully.']);
    }
}

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                // Revoke all tokens for the user
                $user->tokens()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Logout error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to logout. Please try again.',
            ], 500);
        }
    }
}
