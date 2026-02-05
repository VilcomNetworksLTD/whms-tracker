<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; text-align: center;">

    <div style="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md" style="background: white; max-width: 400px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        
        <h2 style="color: #333; margin-top: 0;">Welcome to Vilcom!</h2>
        
        <p style="color: #666; font-size: 16px; line-height: 1.5;">
            Hello, please use the verification code below to activate your account.
        </p>

        <div style="margin: 25px 0;">
            <span style="display: inline-block; background-color: #e3f2fd; color: #0d47a1; font-size: 24px; font-weight: bold; padding: 12px 24px; letter-spacing: 5px; border-radius: 6px; border: 1px solid #bbdefb;">
                {{ $otpCode }}
            </span>
        </div>

        <p style="color: #999; font-size: 14px;">
            This code expires in 10 minutes.<br>
            If you did not request this, please ignore this email.
        </p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="color: #aaa; font-size: 12px;">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>

    </div>

</body>
</html>