<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
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
            margin-bottom: 10px;
        }

        h3 {
            color: #2c3e50;
            font-weight: normal;
            margin-bottom: 20px;
        }

        p {
            color: #555555;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        a.button {
            display: inline-block;
            padding: 12px 24px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            margin-top: 15px;
            transition: background 0.3s ease;
        }

        a.button:hover {
            background: #1e4ed8;
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
        <h3>Halo {{ $name }},</h3>
        <p>Terima kasih telah menambahkan email. Klik tombol di bawah untuk memverifikasi email Anda:</p>
        <a href="{{ $link }}" class="button">Verifikasi Email</a>
        <p>Jika Anda tidak merasa menambahkan email ini, abaikan pesan ini.</p>

        <div class="footer">
            &copy; Smart Portal {{ date('Y') }}
        </div>
    </div>
</body>

</html>
