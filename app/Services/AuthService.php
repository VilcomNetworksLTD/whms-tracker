<?php

namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Step 1: Register User & Send OTP
     */
    public function register(array $data)
    {
        // ---------------------------------------------------------
        // 0. DOMAIN CHECK (MUST BE FIRST)
        // ---------------------------------------------------------
        if (!str_ends_with($data['email'], '@vilcom.co.ke')) {
            throw ValidationException::withMessages([
                'email' => ['Registration is restricted to .vilcom.co.ke accounts only.']
            ]);
        }

        // 1. Create the User (Unverified by default)
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
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.']
            ]);
        }

        $user = Auth::user();

        // 2. Security Check: Is email verified?
        if (is_null($user->email_verified_at)) {
             throw ValidationException::withMessages([
                'email' => ['Please verify your email address first via the OTP sent to you.']
            ]);
        }
        
        // 3. Issue Token
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function forgotPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        // Security: If user doesn't exist, we usually don't want to tell the hacker.
        // But for internal apps, it's fine to throw an error.
        if (!$user) {
            throw ValidationException::withMessages(['email' => ['User not found.']]);
        }

        // Generate Code
        $code = rand(100000, 999999);
        
        // Save to DB (Update existing OTP if exists)
        Otp::updateOrCreate(
            ['email' => $data['email']],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10)
            ]
        );

        // Send Email
        try {
            Mail::to($data['email'])->send(new OtpMail($code));
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['email' => ['Failed to send email. Check SMTP.']]);
        }

        return true;}


        public function resetPassword(array $data)
    {
        // A. Verify the OTP
        $otpRecord = Otp::where('email', $data['email'])
                        ->where('code', $data['otp'])
                        ->first();

        if (!$otpRecord) {
            throw ValidationException::withMessages(['otp' => ['Invalid code.']]);
        }

        if ($otpRecord->expires_at->isPast()) {
            throw ValidationException::withMessages(['otp' => ['Code expired.']]);
        }

        // B. Update the Password
        $user = User::where('email', $data['email'])->firstOrFail();
        
        $user->forceFill([
            'password' => Hash::make($data['password'])
        ])->save();

        // C. Clean up (Delete the OTP so it can't be used again)
        $otpRecord->delete();

        return true;
    }
}