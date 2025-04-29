<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #4285F4;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }

        .content {
            background-color: #ffffff;
            padding: 30px;
            border-left: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
        }

        .otp-container {
            background-color: #f5f5f5;
            border: 1px solid #dddddd;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            border-radius: 5px;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4285F4;
            letter-spacing: 5px;
        }

        .footer {
            background-color: #f5f5f5;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            border-radius: 0 0 5px 5px;
            border: 1px solid #dddddd;
            border-top: none;
        }

        .button {
            display: inline-block;
            background-color: #4285F4;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .expiry {
            font-style: italic;
            color: #ff0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hello <?= htmlspecialchars($user_name) ?>,</p>
            <p>We have received a request to reset the password for your account on <?= htmlspecialchars(APP_NAME) ?>. </p>
            <p>Your One-Time Password (OTP) is:</p>
            <div class="otp-container">
                <div class="otp-code"> <?= htmlspecialchars($otp) ?> </div>
            </div>
            <p>Please enter this code on the password reset page to proceed.</p>
            <p class="expiry">Note: This code is valid for <?= htmlspecialchars((OTP_EXPIRATION / 60)) ?> minutes only.</p>
            <p>If you did not request a password reset, please ignore this email. Your account remains secure.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>The <?= htmlspecialchars(APP_NAME) ?> Team</p>
        </div>
    </div>
</body>

</html>