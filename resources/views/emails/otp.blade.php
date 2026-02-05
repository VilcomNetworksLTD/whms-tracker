<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 20px;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            display: inline-block;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #3b82f6;
            background: #f0f9ff;
            padding: 20px 30px;
            border-radius: 8px;
            border: 2px dashed #93c5fd;
        }
        .instructions {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
        }
        .logo svg {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1>Web Tracker</h1>
            <p style="margin: 10px 0 0; opacity: 0.9;">Email Verification</p>
        </div>
        
        <div class="content">
            <h2 style="color: #1f2937; margin-top: 0;">Hello!</h2>
            <p>Thank you for registering with <strong>Web Tracker</strong>. Please use the following verification code to complete your registration:</p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="instructions">
                <p><strong>⚠️ Important:</strong></p>
                <ul style="text-align: left; padding-left: 20px; margin: 10px 0;">
                    <li>This code will expire in <strong>10 minutes</strong></li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this code, please ignore this email</li>
                </ul>
            </div>
            
            <p>Enter this code in the verification form to complete your registration.</p>
            <p>If you have any questions, please contact our support team.</p>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>The Web Tracker Team</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Web Tracker. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
            <p style="margin-top: 10px;">
                <small>
                    For security reasons, this email was sent to you because someone registered with this email address.
                    If this wasn't you, please disregard this email.
                </small>
            </p>
        </div>
    </div>
</body>
</html>