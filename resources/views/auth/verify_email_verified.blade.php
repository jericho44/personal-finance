<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md w-full text-center">
        {{-- Icon Check --}}
        <div class="flex justify-center mb-6">
            <svg class="w-20 h-20 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12l2 2l4 -4m6 2a9 9 0 1 1 -18 0a9 9 0 0 1 18 0" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2">Email Terverifikasi</h1>
        <p class="text-gray-600 mb-6">
            Selamat! Email Anda telah berhasil diverifikasi.
            Sekarang akun Anda lebih aman dan siap digunakan.
        </p>

        <a href="{{ url('/') }}"
            class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg shadow-md transition">
            Lanjut ke Beranda
        </a>
    </div>
</body>

</html>
