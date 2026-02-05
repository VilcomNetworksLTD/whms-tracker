<?php

namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Carbon\Carbon;

class AuthService
{
    /**
     * Step 1: Register User & Send OTP
     */
    public function register(array $data)
    {
        // 1. Domain Restriction Check
        if (!str_ends_with($data['email'], '@vilcom.co.ke')) {
            throw ValidationException::withMessages([
                'email' => ['Registration is restricted to .vilcom.co.ke accounts only.']
            ]);
        }

        // 2. Create the User (Unverified by default)
        // We use updateOrCreate in case they try to register again after failing verification
        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
            ]
        );

        // 3. Generate 6-digit OTP
        $code = rand(100000, 999999);
        
        // 4. Save OTP to Database
        Otp::updateOrCreate(
            ['email' => $data['email']],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        // 5. Send Email via SMTP
        try {
            Mail::to($data['email'])->send(new OtpMail($code));
        } catch (\Exception $e) {
            // Log error but throw message to frontend
            throw ValidationException::withMessages([
                'email' => ['User created, but failed to send email. Please check SMTP settings.']
            ]);
        }

        return $user;
    }

    /**
     * Step 2: Verify OTP & Issue Token
     */
    public function verifyOtp(array $data)
    {
        // 1. Find the OTP record
        $otpRecord = Otp::where('email', $data['email'])
                        ->where('code', $data['otp'])
                        ->first();

        // 2. Validate Existence
        if (!$otpRecord) {
            throw ValidationException::withMessages(['otp' => ['Invalid OTP code.']]);
        }

        // 3. Validate Expiry
        if ($otpRecord->expires_at->isPast()) {
            throw ValidationException::withMessages(['otp' => ['OTP has expired. Please register again.']]);
        }

        // 4. Verify the User
        $user = User::where('email', $data['email'])->firstOrFail();
        
        // Mark email as verified
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        // 5. Clean up (Delete the used OTP)
        $otpRecord->delete();

        // 6. Issue Token
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Step 3: Login (for returning users)
     */
    public function login(array $credentials)
    {
        // 1. Check Password
        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.']
            ]);
        }

        $user = auth()->user();

        // 2. Security Check: Is email verified?
        if (is_null($user->email_verified_at)) {
             throw ValidationException::withMessages([
                'email' => ['Please verify your email address first via the OTP sent to you.']
            ]);
        }
        
        // 3. Issue Token
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function resendOtp(array $data)
{
    // 1. Check if user exists
    $user = User::where('email', $data['email'])->first();

    if (!$user) {
        throw ValidationException::withMessages(['email' => ['User not found.']]);
    }

    if ($user->email_verified_at) {
        throw ValidationException::withMessages(['email' => ['Email is already verified. Please login.']]);
    }

    // 2. Generate NEW code
    $code = rand(100000, 999999);
    
    // 3. Update the existing OTP record (or create new)
    Otp::updateOrCreate(
        ['email' => $data['email']],
        [
            'code' => $code,
            'expires_at' => \Carbon\Carbon::now()->addMinutes(10)
        ]
    );

    // 4. Send Email
    try {
        Mail::to($data['email'])->send(new OtpMail($code));
    } catch (\Exception $e) {
        throw ValidationException::withMessages([
            'email' => ['Failed to resend email. Check SMTP.']
        ]);
    }

    return true;
}
}