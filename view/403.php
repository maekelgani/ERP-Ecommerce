<?php

/**
 * Forbidden Page (403)
 * Halaman yang ditampilkan ketika user tidak memiliki akses
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Nano Komputer</title>
    <link rel="icon" href="../assets/img/logo-nano-transparant.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">

        <div class="mb-6">
            <i class="fas fa-lock text-6xl text-red-500"></i>
        </div>

        <h1 class="text-4xl font-bold text-gray-800 mb-2">403</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Akses Ditolak</h2>

        <p class="text-gray-600 mb-8">
            Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
        </p>

        <div class="space-y-3">
            <a href="/view/users/landingPage.php"
                class="block w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-[1.02]">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
            <a href="/api/auth/logout.php"
                class="block w-full border-2 border-gray-300 text-gray-700 hover:text-red-600 hover:border-red-300 font-semibold py-3 rounded-lg transition-all duration-200">
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
        </div>

        <p class="text-sm text-gray-500 mt-6">
            Kode Error: 403 - Forbidden
        </p>

    </div>

</body>

</html>