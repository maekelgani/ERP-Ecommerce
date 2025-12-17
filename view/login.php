<?php
require_once __DIR__ . '/../config/config.php';

use App\Auth\CustomerRepository;
use App\Auth\GoogleOAuthHandler;
use App\Auth\SessionManager;

$error = '';
$success = '';
$dbConnected = true;
$customerRepo = null;

try {
    $customerRepo = new CustomerRepository();
} catch (Exception $e) {
    $dbConnected = false;
    $error = 'Sistem sedang dalam pemeliharaan. Silakan coba lagi dalam beberapa saat.';
}

$googleOAuth = new GoogleOAuthHandler(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI);

if (isset($_SESSION['oauth_error'])) {
    $error = $_SESSION['oauth_error'];
    unset($_SESSION['oauth_error']);
}

if ($dbConnected && SessionManager::isCustomerLoggedIn()) {
    header('Location: users/landingPage.php');
    exit;
}

if ($dbConnected && $customerRepo && SessionManager::tryRestoreCustomerSessionFromRemember($customerRepo)) {
    header('Location: users/landingPage.php');
    exit;
}

$url = $googleOAuth->getAuthorizationUrl();

if ($dbConnected && $customerRepo && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masuk'])) {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    if (empty($login) || empty($password)) {
        $error = 'Email/Nomor Telepon dan Password harus diisi!';
    } else {
        $customer = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? $customerRepo->findByEmail($login)
            : $customerRepo->findByPhone($login);

        if ($customer && $customer['password_hash'] && $customerRepo->verifyPassword($password, $customer['password_hash'])) {
            if (!$customer['is_active']) {
                $_SESSION['deactivated_account'] = true;
                $error = 'Akun Anda telah dinonaktifkan. Anda tidak dapat login sampai akun diaktifkan kembali. Hubungi tim support kami di support@nanokomputer.com atau WhatsApp 0812-3456-7890 untuk informasi lebih lanjut dan untuk meminta pengaktifan kembali akun Anda.';
            } else {
                SessionManager::createCustomerSession($customer, $remember);

                if ($remember) {
                    SessionManager::setCustomerRememberMe($customer['id_customer'], $customerRepo);
                }

                $_SESSION['toast_success'] = 'Selamat datang kembali, ' . htmlspecialchars($customer['nama_lengkap']) . '!';
                header('Location: users/landingPage.php');
                exit;
            }
        } else {
            $error = 'Email atau Nomor Telepon atau Password salah!';
        }
    }
}

if ($dbConnected && $customerRepo && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $nama = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['no_telp'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($nama) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($phone) < 10 || strlen($phone) > 15 || !ctype_digit($phone)) {
        $error = 'Nomor telepon harus 10-15 digit!';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter!';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password dan konfirmasi password tidak cocok!';
    } elseif ($customerRepo->emailExists($email)) {
        $error = 'Email sudah terdaftar!';
    } elseif ($customerRepo->phoneExists($phone)) {
        $error = 'Nomor telepon sudah terdaftar!';
    } else {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $customerId = $customerRepo->createRegularCustomer([
                'nama_lengkap' => $nama,
                'email' => $email,
                'no_telp' => $phone,
                'password_hash' => $passwordHash
            ]);

            $success = 'Pendaftaran berhasil! Silakan login dengan email dan password Anda.';
        } catch (\Exception $e) {
            $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
            if (APP_DEBUG) {
                $error .= ' (' . $e->getMessage() . ')';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

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

        .auth-container {
            position: relative;
            width: 100%;
            min-height: 520px;
            overflow: hidden;
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            width: 50%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-container {
            left: 0;
            z-index: 2;
        }

        .sign-up-container {
            left: 0;
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .overlay {
            background: linear-gradient(to bottom right, #882426, #b33b3d);
            color: #ffffff;
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transition: transform 0.6s ease-in-out;
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 2rem 2.5rem;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transition: transform 0.6s ease-in-out;
            box-sizing: border-box;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .auth-container.right-panel-active .sign-in-container {
            transform: translateX(100%);
            opacity: 0;
        }

        .auth-container.right-panel-active .sign-up-container {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            pointer-events: auto;
        }

        .auth-container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .auth-container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .auth-container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .auth-container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-6xl animate-scale-in">

        <!-- Desktop & Tablet View -->
        <div id="authContainer" class="hidden md:block glass-effect rounded-3xl shadow-xl auth-container">

            <!-- Sign In Form Container -->
            <div class="form-container sign-in-container p-8 lg:p-12 flex flex-col justify-center relative">
                <a href="users/landingPage.php" class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-50 hover:bg-primary hover:text-white text-gray-700 transition-all duration-300 group shadow-sm hover:shadow-md text-sm font-medium">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <div class="max-w-md mx-auto w-full animate-fade-in">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">Masuk</h1>
                    <p class="text-gray-600 mb-6">Selamat datang kembali!</p>

                    <a href="<?= htmlspecialchars($url) ?>" class="flex items-center justify-center gap-3 w-full border-2 border-gray-200 rounded-xl px-6 py-3 mb-4 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
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

                    <form method="POST" action="" class="space-y-4">
                        <?php if (!empty($error)): ?>
                            <?php if (isset($_SESSION['deactivated_account']) && $_SESSION['deactivated_account']): ?>
                                <?php unset($_SESSION['deactivated_account']); ?>
                                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-xl animate-fade-in">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-ban text-red-600"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-red-800 mb-1">Akun Dinonaktifkan</p>
                                            <p class="text-sm text-red-700 mb-3">Akun Anda telah dinonaktifkan. Anda tidak dapat login sampai akun diaktifkan kembali.</p>
                                            <div class="bg-white/60 rounded-lg p-3 text-sm">
                                                <p class="font-medium text-red-800 mb-2">Hubungi support untuk informasi lebih lanjut:</p>
                                                <div class="space-y-1.5">
                                                    <p class="flex items-center gap-2 text-red-700">
                                                        <i class="fas fa-envelope w-4"></i>
                                                        <span>support@nanokomputer.com</span>
                                                    </p>
                                                    <p class="flex items-center gap-2 text-red-700">
                                                        <i class="fab fa-whatsapp w-4"></i>
                                                        <span>0812-3456-7890</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm animate-fade-in">
                                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm animate-fade-in">
                                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
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
                        Butuh bantuan? <a href="../view/users/aboutContact.php#contact" class="text-primary hover:text-primary-dark font-medium">Hubungi Nano Komputer</a>
                    </p>
                </div>
            </div>

            <!-- Sign Up Form Container -->
            <div class="form-container sign-up-container p-6 lg:p-8 flex flex-col justify-start relative">
                <a href="users/landingPage.php" class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-50 hover:bg-primary hover:text-white text-gray-700 transition-all duration-300 group shadow-sm hover:shadow-md text-sm font-medium">
                    <svg class="w-4 h-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <div class="max-w-md mx-auto w-full mt-8">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">Daftar</h1>
                    <p class="text-gray-600 mb-4">Buat akun baru Anda</p>

                    <a href="<?= htmlspecialchars($url) ?>" class="flex items-center justify-center gap-3 w-full border-2 border-gray-200 rounded-xl px-6 py-3 mb-4 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 group">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="font-semibold text-gray-700 group-hover:text-gray-900">Daftar dengan Google</span>
                    </a>

                    <div class="flex items-center my-4">
                        <div class="flex-1 border-t border-gray-300"></div>
                        <span class="px-4 text-gray-500 text-sm">atau</span>
                        <div class="flex-1 border-t border-gray-300"></div>
                    </div>

                    <form method="POST" action="" class="space-y-3">
                        <?php if (!empty($error)): ?>
                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="Email">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <input type="tel" name="no_telp" maxlength="15" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <div class="relative">
                                    <input type="password" id="signupPasswordNew" name="password" required minlength="8"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none pr-10"
                                        placeholder="Min 8 karakter">
                                    <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                        onclick="togglePassword('signupPasswordNew', this)"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                <div class="relative">
                                    <input type="password" id="confirmPasswordNew" name="confirm_password" required
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none pr-10"
                                        placeholder="Ulangi password">
                                    <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                        onclick="togglePassword('confirmPasswordNew', this)"></i>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="daftar"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg hover:shadow-xl mt-2">
                            Daftar Sekarang
                        </button>
                    </form>

                    <p class="text-center text-sm text-gray-600 mt-4">
                        Dengan mendaftar, saya menyetujui<br>
                        <a href="users/termsConditions.php" class="text-primary hover:text-primary-dark font-semibold hover:underline">Syarat & Ketentuan</a>
                        serta
                        <a href="users/privacyPolicy.php" class="text-primary hover:text-primary-dark font-semibold hover:underline">Kebijakan Privasi</a>
                    </p>
                </div>
            </div>

            <!-- Overlay Container -->
            <div class="overlay-container">
                <div class="overlay">
                    <!-- Overlay Left Panel - Show when on Register Form -->
                    <div class="overlay-panel overlay-left">
                        <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-48 mx-auto mb-4 drop-shadow-lg">
                        <h2 class="text-2xl font-bold mb-3">Selamat Datang!</h2>
                        <p class="mb-6 text-white/90 text-sm">Sudah punya akun? Masuk sekarang untuk mengakses berbagai fitur menarik!</p>
                        <button onclick="toggleForms()"
                            class="bg-white text-primary font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg">
                            Masuk
                        </button>
                    </div>
                    <!-- Overlay Right Panel - Show when on Login Form -->
                    <div class="overlay-panel overlay-right">
                        <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-48 mx-auto mb-4 drop-shadow-lg">
                        <h2 class="text-2xl font-bold mb-3">Halo, Teman!</h2>
                        <p class="mb-6 text-white/90 text-sm">Belum punya akun? Daftar sekarang dan nikmati berbagai penawaran menarik!</p>
                        <button onclick="toggleForms()"
                            class="bg-white text-primary font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile View -->
        <div class="md:hidden glass-effect rounded-3xl shadow-2xl p-6 relative">

            <!-- Mobile Back Button -->
            <a href="users/landingPage.php" class="absolute top-4 left-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-50 hover:bg-primary hover:text-white text-gray-700 transition-all duration-300 shadow-sm hover:shadow-md hover:scale-110 transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Mobile Sign In -->
            <div id="mobileSignIn" class="animate-fade-in pt-4">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">Masuk</h1>
                    <p class="text-gray-600 text-sm">Selamat datang kembali!</p>
                </div>

                <a href="<?= htmlspecialchars($url) ?>" class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 rounded-xl px-4 py-3 mb-4 hover:bg-gray-50 transition-all duration-200">
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

                <form method="POST" action="" class="space-y-3">
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs">
                            <i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-xs">
                            <i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($success) ?>
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
                        Butuh bantuan? <a href="../view/users/aboutContact.php#contact" class="text-primary hover:text-primary-dark font-medium">Hubungi Kami</a>
                    </p>
                </div>
            </div>

            <!-- Mobile Sign Up -->
            <div id="mobileSignUp" class="hidden animate-fade-in pt-4">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">Daftar</h1>
                    <p class="text-gray-600 text-sm">Buat akun baru Anda</p>
                </div>

                <a href="<?= htmlspecialchars($url) ?>" class="flex items-center justify-center gap-2 w-full border-2 border-gray-200 rounded-xl px-4 py-3 mb-4 hover:bg-gray-50 transition-all duration-200">
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
                            placeholder="Masukkan email">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="tel" name="no_telp" maxlength="15" required
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="mobileSignupPassword" name="password" required minlength="8"
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none pr-10"
                                    placeholder="Min 8 karakter">
                                <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                    onclick="togglePassword('mobileSignupPassword', this)"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Konfirmasi</label>
                            <div class="relative">
                                <input type="password" id="mobileConfirmPassword" name="confirm_password" required
                                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-sm outline-none pr-10"
                                    placeholder="Ulangi">
                                <i class="fas fa-eye-slash password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                                    onclick="togglePassword('mobileConfirmPassword', this)"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="daftar"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2.5 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg text-sm">
                        Daftar Sekarang
                    </button>
                </form>

                <p class="text-center text-xs text-gray-600 mt-4">
                    Dengan mendaftar, saya menyetujui
                    <a href="users/termsConditions.php" class="text-primary hover:text-primary-dark font-semibold">Syarat & Ketentuan</a>
                    serta
                    <a href="users/privacyPolicy.php" class="text-primary hover:text-primary-dark font-semibold">Kebijakan Privasi</a>
                </p>

                <div class="mt-4 text-center">
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
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        function toggleForms() {
            const container = document.getElementById('authContainer');
            container.classList.toggle('right-panel-active');
        }

        function toggleMobileForms() {
            const signIn = document.getElementById('mobileSignIn');
            const signUp = document.getElementById('mobileSignUp');
            signIn.classList.toggle('hidden');
            signUp.classList.toggle('hidden');
        }
    </script>

</body>

</html>