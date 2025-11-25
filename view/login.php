<?php
session_start();
require_once __DIR__ . '../../config/config.php';
require_once __DIR__ . '../../config/function.php';
require __DIR__ . "/../vendor/autoload.php";

//Google API login
$client = new Google\Client;

$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirecturi(GOOGLE_REDIRECT_URI);

$client->addScope("email");
$client->addScope("profile");

$url = $client->createAuthUrl();

//Create
if (isset($_POST['signup'])) {
    $nama = $_POST['name'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    $password = md5($_POST['password']); // Hash MD5

    //UIDGENERARTOR
    $query_id = mysqli_query($conn, "SELECT MAX(id_pengguna) AS maxID FROM pengguna_user");
    $data = mysqli_fetch_assoc($query_id);
    $lastID = $data['maxID'] ?? null;

    if ($lastID && preg_match('/USR(\d+)/', $lastID, $matches)) {
        $num = (int)$matches[1] + 1;
    } else {
        $num = 1;
    }
    $newID = "USR" . str_pad($num, 3, "0", STR_PAD_LEFT); //GENERATE UNIQUE ID

    $query = "INSERT INTO pengguna_user (id_pengguna, nama_pengguna, email, no_telp, password_pengguna) VALUES ('$newID', '$nama', '$email', '$no_telp',  '$password')";

    if (mysqli_query($conn, $query)) {
        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Pendaftaran akun Berhasil!',
                    text: 'Silakan login untuk melanjutkan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'login.php';
                });
            });
        </script>";
        exit();
    }
}

//Read
$error = "";
if (isset($_POST['masuk'])) {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $password = md5($_POST['password']);

    // Deteksi input login: email atau nomor telepon
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        // Kalau login pakai email
        $query = "SELECT * FROM pengguna_user WHERE email = '$login' AND password_pengguna = '$password'";
    } else {
        // Kalau login pakai no_telp
        $query = "SELECT * FROM pengguna_user WHERE no_telp = '$login' AND password_pengguna = '$password'";
    }

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);

        // Set session
        $_SESSION['log'] = true;
        $_SESSION['login'] = $login; // bisa email / no_telp
        $_SESSION['id_pengguna'] = $user_data['id_pengguna'];
        $_SESSION['nama_pengguna'] = $user_data['nama_pengguna'];

        header('Location: index.php');
        exit();
    } else {
        $error = "Login gagal. Periksa kembali data Anda.";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nano Komputer - Login & Register</title>
    <link rel="icon" href="../assets/img/logo-nano-transparant.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#882426',
                        'primary-dark': '#8B3A3A',
                        'primary-light': '#d32f2f',
                    },
                    animation: {
                        'slide-in-right': 'slideInRight 0.5s ease-out',
                        'slide-in-left': 'slideInLeft 0.5s ease-out',
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                    },
                    keyframes: {
                        slideInRight: {
                            '0%': {
                                transform: 'translateX(100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            },
                        },
                        slideInLeft: {
                            '0%': {
                                transform: 'translateX(-100%)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateX(0)',
                                opacity: '1'
                            },
                        },
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            },
                        },
                        scaleIn: {
                            '0%': {
                                transform: 'scale(0.9)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            },
                        },
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: #FAF7F3;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .password-toggle {
            cursor: pointer;
            transition: all 0.2s;
        }

        .password-toggle:hover {
            color: #b90000;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-6xl animate-scale-in">

        <!-- Desktop & Tablet View -->
        <div class="hidden md:flex glass-effect rounded-3xl shadow-xl overflow-hidden min-h-[650px]">

            <!-- Left Panel - Sign In -->
            <div id="signInPanel" class="w-1/2 p-8 lg:p-12 flex flex-col justify-center transition-all duration-500">
                <div class="max-w-md mx-auto w-full animate-fade-in">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">Masuk</h1>
                    <p class="text-gray-600 mb-6">Selamat datang kembali!</p>

                    <!-- Google Sign In Button -->
                    <a href="<?= $url ?>" class="flex items-center justify-center gap-3 w-full border-2 border-gray-200 rounded-xl px-6 py-3 mb-4 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="font-semibold text-gray-700 group-hover:text-gray-900">Masuk dengan Google</span>
                    </a>

                    <div class="flex items-center my-6">
                        <div class="flex-1 border-t border-gray-300"></div>
                        <span class="px-4 text-gray-500 text-sm">atau</span>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>

                    <!-- Sign In Form -->
                    <form method="POST" action="" class="space-y-4">
                        <?php if (!empty($error)): ?>
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm animate-fade-in">
                                <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email atau Nomor Telepon</label>
                            <input type="text" name="login" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
                                placeholder="Masukkan email atau nomor telepon">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="signinPassword" name="password" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none pr-12"
                                    placeholder="Masukkan password">
                                <i class="fas fa-eye-slash password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"
                                    onclick="togglePassword('signinPassword', this)"></i>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                                <span class="text-gray-700">Ingat saya</span>
                            </label>
                            <a href="#" class="text-primary hover:text-primary-dark font-medium">Lupa Password?</a>
                        </div>

                        <button type="submit" name="masuk"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg hover:shadow-xl">
                            Masuk
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-600 mt-6">
                        Butuh bantuan? <a href="#" class="text-primary hover:text-primary-dark font-medium">Hubungi Nano Komputer</a>
                    </p>
                </div>
            </div>

            <!-- Right Panel - Sign Up -->
            <div id="signUpPanel" class="w-1/2 p-8 lg:p-12 flex flex-col justify-center bg-gradient-to-br from-primary to-primary-light text-white transition-all duration-500">
                <div class="max-w-md mx-auto w-full text-center animate-fade-in">
                    <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-48 mx-auto mb-6 drop-shadow-lg">
                    <h2 class="text-3xl font-bold mb-4">Halo, Teman!</h2>
                    <p class="mb-8 text-white/90">Belum punya akun? Daftar sekarang dan nikmati berbagai penawaran menarik!</p>
                    <button onclick="toggleForms()"
                        class="bg-white text-primary font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg">
                        Daftar Sekarang
                    </button>
                </div>
            </div>

        </div>

        <!-- Mobile View -->
        <div class="md:hidden glass-effect rounded-3xl shadow-2xl p-6">

            <!-- Mobile Sign In -->
            <div id="mobileSignIn" class="animate-fade-in">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">Masuk</h1>
                    <p class="text-gray-600 text-sm">Selamat datang kembali!</p>
                </div>

                <!-- Google Sign In Button -->
                <a href="<?= $url ?>" class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 rounded-xl px-4 py-3 mb-4 hover:bg-gray-50 transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    <span class="font-semibold text-gray-700 text-sm">Masuk dengan Google</span>
                </a>

                <div class="flex items-center my-4">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-3 text-gray-500 text-xs">atau</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <!-- Sign In Form -->
                <form method="POST" action="" class="space-y-3">
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs">
                            <i class="fas fa-exclamation-circle mr-1"></i><?= $error ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email atau Nomor Telepon</label>
                        <input type="text" name="login" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none"
                            placeholder="Masukkan email atau nomor telepon">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="mobileSigninPassword" name="password" required
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none pr-10"
                                placeholder="Masukkan password">
                            <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                onclick="togglePassword('mobileSigninPassword', this)"></i>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="text-gray-700">Ingat saya</span>
                        </label>
                        <a href="#" class="text-primary hover:text-primary-dark font-medium">Lupa Password?</a>
                    </div>

                    <button type="submit" name="masuk"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg text-sm">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-600">
                        Belum punya akun?
                        <button onclick="toggleMobileForms()" class="text-primary hover:text-primary-dark font-semibold">
                            Daftar Sekarang
                        </button>
                    </p>
                    <p class="text-xs text-gray-600 mt-3">
                        Butuh bantuan? <a href="#" class="text-primary hover:text-primary-dark font-medium">Hubungi Kami</a>
                    </p>
                </div>
            </div>

            <!-- Mobile Sign Up -->
            <div id="mobileSignUp" class="hidden animate-fade-in">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">Daftar</h1>
                    <p class="text-gray-600 text-sm">Buat akun baru Anda</p>
                </div>

                <!-- Google Sign Up Button -->
                <a href="<?= $url ?>" class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 rounded-xl px-4 py-3 mb-4 hover:bg-gray-50 transition-all duration-200">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    <span class="font-semibold text-gray-700 text-sm">Daftar dengan Google</span>
                </a>

                <div class="flex items-center my-4">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-3 text-gray-500 text-xs">atau</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <!-- Sign Up Form -->
                <form method="POST" action="" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none"
                            placeholder="nama@email.com">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="number" name="no_telp" maxlength="15" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="mobileSignupPassword" name="password" required
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none pr-10"
                                placeholder="Buat password">
                            <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                onclick="togglePassword('mobileSignupPassword', this)"></i>
                        </div>
                    </div>

                    <button type="submit" name="signup"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg text-sm mt-4">
                        Daftar
                    </button>

                    <p class="text-xs text-gray-600 text-center mt-3 leading-relaxed">
                        Dengan mendaftar, saya menyetujui <br>
                        <a href="#" class="text-primary hover:text-primary-dark font-medium">Syarat & Ketentuan</a>
                        serta
                        <a href="#" class="text-primary hover:text-primary-dark font-medium">Kebijakan Privasi</a>
                    </p>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-600">
                        Sudah punya akun?
                        <button onclick="toggleMobileForms()" class="text-primary hover:text-primary-dark font-semibold">
                            Masuk
                        </button>
                    </p>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Toggle between Sign In and Sign Up (Desktop/Tablet)
        let isSignUp = false;

        function toggleForms() {
            const signInPanel = document.getElementById('signInPanel');
            const signUpPanel = document.getElementById('signUpPanel');

            isSignUp = !isSignUp;

            if (isSignUp) {
                // Show Sign Up form
                signInPanel.innerHTML = `
                    <div class="max-w-md mx-auto w-full animate-slide-in-left">
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">Daftar Sekarang</h1>
                        <p class="text-gray-600 mb-6">Buat akun baru Anda!</p>
                        
                        <!-- Google Sign Up Button -->
                        <a href="<?= $url ?>" class="flex items-center justify-center gap-3 w-full border-2 border-gray-200 rounded-xl px-6 py-3 mb-4 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="font-semibold text-gray-700 group-hover:text-gray-900">Daftar dengan Google</span>
                        </a>
                        
                        <div class="flex items-center my-6">
                            <div class="flex-1 border-t border-gray-300"></div>
                            <span class="px-4 text-gray-500 text-sm">atau</span>
                            <div class="flex-1 border-t border-gray-300"></div>
                        </div>
                        
                        <!-- Sign Up Form -->
                        <form method="POST" action="" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
                                    placeholder="Masukkan nama lengkap">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
                                    placeholder="nama@email.com">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="number" name="no_telp" maxlength="15" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <input type="password" id="signupPassword" name="password" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none pr-12"
                                        placeholder="Buat password">
                                    <i class="fas fa-eye-slash password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-gray-400" 
                                        onclick="togglePassword('signupPassword', this)"></i>
                                </div>
                            </div>
                            
                            <button type="submit" name="signup"
                                class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg hover:shadow-xl">
                                Daftar
                            </button>
                            
                            <p class="text-center text-xs text-gray-600 mt-4 leading-relaxed">
                                Dengan mendaftar, saya menyetujui <br>
                                <a href="#" class="text-primary hover:text-primary-dark font-medium">Syarat & Ketentuan</a>
                                serta
                                <a href="#" class="text-primary hover:text-primary-dark font-medium">Kebijakan Privasi</a>
                            </p>
                        </form>
                    </div>
                `;

                signUpPanel.innerHTML = `
                    <div class="max-w-md mx-auto w-full text-center animate-slide-in-right">
                        <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-48 mx-auto mb-6 drop-shadow-lg">
                        <h2 class="text-3xl font-bold mb-4">Selamat Datang!</h2>
                        <p class="mb-8 text-white/90">Sudah punya akun? Masuk dan lanjutkan pengalaman belanja Anda!</p>
                        <button onclick="toggleForms()" 
                            class="bg-white text-primary font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg">
                            Masuk
                        </button>
                    </div>
                `;
            } else {
                // Show Sign In form (reload page to get original PHP form)
                window.location.reload();
            }
        }

        // Toggle between Sign In and Sign Up (Mobile)
        function toggleMobileForms() {
            const mobileSignIn = document.getElementById('mobileSignIn');
            const mobileSignUp = document.getElementById('mobileSignUp');

            mobileSignIn.classList.toggle('hidden');
            mobileSignUp.classList.toggle('hidden');
        }

        // Toggle Password Visibility
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye-slash');
            icon.classList.toggle('fa-eye');
        }
    </script>

</body>

</html>