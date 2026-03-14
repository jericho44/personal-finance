<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333333;
        }

        p {
            color: #555555;
            font-size: 15px;
            line-height: 1.5;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #2c3e50;
            margin: 20px 0;
            padding: 15px;
            border: 2px dashed #3498db;
            display: inline-block;
            border-radius: 8px;
            background-color: #f0f8ff;
        }

        .footer {
            font-size: 12px;
            color: #888888;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>{{ $title }}</h2>
        <p>Kode verifikasi (OTP) Anda adalah:</p>

        <div class="otp-code">{{ $otp }}</div>

        <p>Kode ini berlaku selama <strong>{{ $expires_in }} menit</strong>.<br>
            Jangan berikan kode ini kepada siapa pun.</p>

        <div class="footer">
            &copy; Smart Portal {{ date('Y') }}
        </div>
    </div>
</body>

</html>
