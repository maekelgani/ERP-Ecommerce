<?php
require_once __DIR__ . '/../config/config.php';

use App\Auth\CustomerRepository;
use App\Auth\GoogleOAuthHandler;
use App\Auth\SessionManager;

$error = '';
$success = '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .container {
      position: relative;
      width: 100%;
      height: 650px;
      overflow: hidden
    }

    .panel {
      position: absolute;
      top: 0;
      height: 100%;
      width: 50%;
      transition: transform .6s ease
    }

    .container.right-active .signin {
      transform: translateX(100%)
    }

    .container.right-active .signup {
      transform: translateX(100%)
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100">

  <div class="container bg-white rounded-xl shadow-xl flex" id="container">

    <div class="panel signin flex flex-col justify-center items-center p-10">
      <h2 class="text-2xl font-bold mb-4">Login</h2>
      <form method="post">
        <input type="email" name="email" class="border p-2 mb-3 w-64" placeholder="Email">
        <input type="password" name="password" class="border p-2 mb-3 w-64" placeholder="Password">
        <button type="submit" class="bg-red-700 text-white px-6 py-2 rounded">Masuk</button>
      </form>
      <button type="button" id="toRegister" class="mt-4 text-sm text-red-700">Daftar Sekarang</button>
    </div>

    <div class="panel signup flex flex-col justify-center items-center p-10 bg-red-700 text-white" style="transform:translateX(100%)">
      <h2 class="text-2xl font-bold mb-4">Register</h2>
      <form method="post">
        <input type="text" name="nama" class="border p-2 mb-3 w-64 text-black" placeholder="Nama Lengkap">
        <input type="email" name="email" class="border p-2 mb-3 w-64 text-black" placeholder="Email">
        <input type="password" name="password" class="border p-2 mb-3 w-64 text-black" placeholder="Password">
        <button type="submit" class="bg-white text-red-700 px-6 py-2 rounded">Daftar</button>
      </form>
      <button type="button" id="toLogin" class="mt-4 text-sm underline">Sudah punya akun?</button>
    </div>

  </div>

  <script>
    const container = document.getElementById('container');
    document.getElementById('toRegister').addEventListener('click', () => container.classList.add('right-active'));
    document.getElementById('toLogin').addEventListener('click', () => container.classList.remove('right-active'));
  </script>

</body>

</html>