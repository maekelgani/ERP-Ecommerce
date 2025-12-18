<?php
require_once __DIR__ . '/../config/config.php';

use App\Auth\CustomerRepository;
use App\Auth\GoogleOAuthHandler;
use App\Auth\SessionManager;

$error = '';
$success = '';
$customerRepo = new CustomerRepository();
$googleOAuth = new GoogleOAuthHandler(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI);

if (isset($_SESSION['oauth_error'])) {
    $error = $_SESSION['oauth_error'];
    unset($_SESSION['oauth_error']);
}

if (SessionManager::isCustomerLoggedIn()) {
    header('Location: users/landingPage.php');
    exit;
}

if (SessionManager::tryRestoreCustomerSessionFromRemember($customerRepo)) {
    header('Location: users/landingPage.php');
    exit;
}

$url = $googleOAuth->getAuthorizationUrl();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masuk'])) {
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
                $error = 'Akun Anda telah dinonaktifkan. Hubungi support untuk informasi lebih lanjut.';
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
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
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nano Komputer - Login & Register</title>
    <link rel="icon" href="../assets/img/logo-nano-transparant.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> -->

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    // fontFamily: {
                    //     sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    // },
                    colors: {
                        primary: {
                            DEFAULT: 'hsl(359, 60%, 33%)',
                            dark: 'hsl(359, 65%, 28%)',
                            light: 'hsl(359, 55%, 45%)',
                            foreground: 'hsl(0, 0%, 100%)',
                        },
                        background: 'hsl(30, 33%, 97%)',
                        foreground: 'hsl(0, 0%, 15%)',
                        card: 'hsl(0, 0%, 100%)',
                        border: 'hsl(30, 15%, 88%)',
                        muted: {
                            DEFAULT: 'hsl(30, 15%, 92%)',
                            foreground: 'hsl(0, 0%, 45%)',
                        },
                        secondary: 'hsl(30, 20%, 93%)',
                        destructive: 'hsl(0, 84%, 60%)',
                        success: 'hsl(142, 76%, 36%)',
                    },
                },
            },
        }
    </script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .gradient-primary {
            background: linear-gradient(135deg, hsl(359, 60%, 33%) 0%, hsl(359, 55%, 45%) 100%);
        }

        /* ============ ANIMASI UTAMA ============ */

        /* Container utama dengan overflow hidden */
        .auth-container {
            position: relative;
            overflow: hidden;
        }

        /* Panel wrapper untuk sliding effect */
        .panels-wrapper {
            display: flex;
            width: 200%;
            transition: transform 0.6s cubic-bezier(0.68, -0.15, 0.32, 1.15);
        }

        .panels-wrapper.show-register {
            transform: translateX(-50%);
        }

        /* Setiap panel */
        .auth-panel {
            width: 50%;
            display: flex;
            min-height: 620px;
        }

        /* Form panel animations */
        .form-content {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) 0.2s;
        }

        .form-content.hidden-form {
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }

        /* Brand panel dengan animasi berbeda */
        .brand-content {
            opacity: 1;
            transform: scale(1);
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1) 0.15s;
        }

        .brand-content.hidden-brand {
            opacity: 0;
            transform: scale(0.95);
        }

        /* ============ ANIMASI MOBILE ============ */

        .mobile-form {
            position: absolute;
            width: 100%;
            transition: all 0.5s cubic-bezier(0.68, -0.15, 0.32, 1.15);
        }

        .mobile-form.slide-left {
            transform: translateX(-110%);
            opacity: 0;
        }

        .mobile-form.slide-right {
            transform: translateX(110%);
            opacity: 0;
        }

        .mobile-form.active {
            transform: translateX(0);
            opacity: 1;
        }

        /* Mobile container */
        .mobile-forms-container {
            position: relative;
            min-height: 580px;
        }

        /* ============ ANIMASI ELEMEN ============ */

        @keyframes scaleIn {
            from {
                transform: scale(0.92);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(25px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInLeft {
            from {
                transform: translateX(-30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 0 0 hsla(359, 60%, 33%, 0.4);
            }

            50% {
                box-shadow: 0 0 20px 5px hsla(359, 60%, 33%, 0.2);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-scale-in {
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .animate-slide-left {
            animation: slideInLeft 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .animate-slide-right {
            animation: slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Staggered animation delays */
        .stagger-1 {
            animation-delay: 0.1s;
        }

        .stagger-2 {
            animation-delay: 0.2s;
        }

        .stagger-3 {
            animation-delay: 0.3s;
        }

        .stagger-4 {
            animation-delay: 0.4s;
        }

        .stagger-5 {
            animation-delay: 0.5s;
        }

        /* ============ INPUT STYLES ============ */

        input:focus {
            outline: none;
            border-color: hsl(359, 60%, 33%);
            box-shadow: 0 0 0 4px hsla(359, 60%, 33%, 0.12);
        }

        input {
            transition: all 0.25s ease;
        }

        input:hover {
            border-color: hsl(359, 55%, 45%);
        }

        /* ============ BUTTON STYLES ============ */

        .btn-primary {
            background: linear-gradient(135deg, hsl(359, 60%, 33%) 0%, hsl(359, 55%, 40%) 100%);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, hsl(359, 65%, 28%) 0%, hsl(359, 60%, 35%) 100%);
            box-shadow: 0 10px 30px -8px hsla(359, 60%, 33%, 0.5);
            transform: translateY(-2px);
        }

        .btn-primary:active {
            transform: scale(0.98) translateY(0);
        }

        .btn-hero {
            background: white;
            color: hsl(359, 60%, 33%);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, hsla(359, 60%, 33%, 0.05), hsla(359, 60%, 33%, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-hero:hover::after {
            opacity: 1;
        }

        .btn-hero:hover {
            box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .btn-hero:active {
            transform: scale(0.98) translateY(0);
        }

        .btn-google {
            border: 2px solid hsl(30, 15%, 88%);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .btn-google:hover {
            background: hsl(30, 20%, 96%);
            border-color: hsl(0, 0%, 75%);
            box-shadow: 0 8px 25px -8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .btn-google:active {
            transform: scale(0.98) translateY(0);
        }

        /* ============ BACK BUTTON ============ */

        .back-btn {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .back-btn:hover {
            box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.2);
        }

        .back-btn:hover svg {
            transform: translateX(-3px);
        }

        .back-btn svg {
            transition: transform 0.3s ease;
        }

        /* ============ CHECKBOX STYLES ============ */

        input[type="checkbox"] {
            accent-color: hsl(359, 60%, 33%);
        }

        /* ============ ALERT ANIMATIONS ============ */

        .alert-enter {
            animation: slideInLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* ============ RESPONSIVE TABLET ============ */

        @media (min-width: 768px) and (max-width: 1023px) {
            .auth-panel {
                min-height: 550px;
            }

            .panels-wrapper {
                flex-direction: column;
                width: 100%;
                height: auto;
            }

            .panels-wrapper.show-register {
                transform: translateX(0);
            }

            .auth-panel {
                width: 100%;
            }

            .tablet-login-panel,
            .tablet-register-panel {
                transition: all 0.5s cubic-bezier(0.68, -0.15, 0.32, 1.15);
            }

            .tablet-login-panel.hidden-tablet {
                display: none !important;
            }

            .tablet-register-panel.hidden-tablet {
                display: none !important;
            }
        }

        /* ============ LOGO HOVER EFFECT ============ */

        .logo-hover {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .logo-hover:hover {
            transform: scale(1.05) rotate(2deg);
            filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.15));
        }

        /* ============ LINK HOVER ============ */

        .link-hover {
            position: relative;
        }

        .link-hover::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: currentColor;
            transition: width 0.3s ease;
        }

        .link-hover:hover::after {
            width: 100%;
        }

        /* ============ DECORATIVE ELEMENTS ============ */

        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        .deco-circle-1 {
            width: 200px;
            height: 200px;
            top: -50px;
            right: -50px;
        }

        .deco-circle-2 {
            width: 150px;
            height: 150px;
            bottom: -30px;
            left: -30px;
        }

        .deco-dots {
            position: absolute;
            width: 100px;
            height: 100px;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
            background-size: 15px 15px;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-background min-h-screen flex items-center justify-center p-4 lg:p-6">

    <!-- Main Container -->
    <div class="w-full max-w-5xl animate-scale-in">

        <!-- ============ DESKTOP VIEW (lg+) ============ -->
        <div class="hidden lg:block auth-container glass-card rounded-3xl shadow-2xl">
            <div class="panels-wrapper" id="desktopPanels">

                <!-- Panel 1: Login Form + Register CTA -->
                <div class="auth-panel" id="loginPanel">
                    <!-- Left: Login Form -->
                    <div class="w-1/2 p-8 xl:p-12 flex flex-col justify-center bg-card relative rounded-l-3xl">
                        <!-- Back Button -->
                        <a href="users/landingPage.php" class="back-btn absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary hover:bg-primary hover:text-white text-foreground shadow-sm text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </a>

                        <div class="form-content max-w-md mx-auto w-full" id="loginFormContent">
                            <h1 class="text-3xl xl:text-4xl font-bold text-foreground mb-2 animate-fade-in-up">Masuk</h1>
                            <p class="text-muted-foreground mb-6 animate-fade-in-up stagger-1">Selamat datang kembali!</p>

                            <!-- Google Sign In -->
                            <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-3 w-full rounded-xl px-6 py-3.5 mb-4 bg-card animate-fade-in-up stagger-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                </svg>
                                <span class="font-semibold text-foreground">Masuk dengan Google</span>
                            </a>

                            <div class="flex items-center my-6 animate-fade-in-up stagger-2">
                                <div class="flex-1 border-t border-border"></div>
                                <span class="px-4 text-muted-foreground text-sm">atau</span>
                                <div class="flex-1 border-t border-border"></div>
                            </div>

                            <!-- Login Form -->
                            <form method="POST" action="" class="space-y-4">
                                <?php if (!empty($error)): ?>
                                    <div class="alert-enter flex items-center gap-2 px-4 py-3 rounded-xl text-sm bg-red-50 border border-red-200 text-red-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($success)): ?>
                                    <div class="alert-enter flex items-center gap-2 px-4 py-3 rounded-xl text-sm bg-green-50 border border-green-200 text-green-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= htmlspecialchars($success) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="space-y-2 animate-fade-in-up stagger-3">
                                    <label class="block text-sm font-medium text-foreground">Email atau Nomor Telepon</label>
                                    <input type="text" name="login" required
                                        class="w-full h-12 px-4 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                        placeholder="Masukkan email atau nomor telepon">
                                </div>

                                <div class="space-y-2 animate-fade-in-up stagger-4">
                                    <label class="block text-sm font-medium text-foreground">Password</label>
                                    <div class="relative">
                                        <input type="password" id="signinPassword" name="password" required
                                            class="w-full h-12 px-4 pr-12 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="Masukkan password">
                                        <button type="button" onclick="togglePassword('signinPassword', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                            <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                            <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-sm animate-fade-in-up stagger-4">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border">
                                        <span class="text-foreground group-hover:text-primary transition-colors">Ingat saya</span>
                                    </label>
                                    <a href="#" class="link-hover text-primary hover:text-primary-dark font-medium transition-colors">Lupa Password?</a>
                                </div>

                                <button type="submit" name="masuk"
                                    class="btn-primary w-full h-12 text-white font-semibold rounded-xl shadow-lg animate-fade-in-up stagger-5">
                                    Masuk
                                </button>
                            </form>

                            <p class="text-center text-sm text-muted-foreground mt-6 animate-fade-in-up stagger-5">
                                Butuh bantuan? <a href="#" class="link-hover text-primary hover:text-primary-dark font-medium transition-colors">Hubungi Nano Komputer</a>
                            </p>
                        </div>
                    </div>

                    <!-- Right: Register CTA -->
                    <div class="w-1/2 p-8 xl:p-12 flex flex-col justify-center gradient-primary text-white relative overflow-hidden rounded-r-3xl">
                        <!-- Decorative elements -->
                        <div class="deco-circle deco-circle-1"></div>
                        <div class="deco-circle deco-circle-2"></div>
                        <div class="deco-dots top-10 left-10 opacity-50"></div>

                        <div class="brand-content max-w-md mx-auto w-full text-center relative z-10" id="registerCtaContent">
                            <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="logo-hover h-28 xl:h-40 mx-auto mb-6 drop-shadow-2xl animate-float">
                            <h2 class="text-3xl xl:text-4xl font-bold mb-4 animate-fade-in-up stagger-1">Halo, Teman!</h2>
                            <p class="mb-8 text-white/90 text-sm animate-fade-in-up stagger-2">Belum punya akun? Daftar sekarang dan nikmati berbagai penawaran menarik!</p>
                            <button onclick="toggleDesktopForms()"
                                class="btn-hero font-semibold px-10 py-3.5 rounded-xl shadow-xl animate-fade-in-up stagger-3">
                                Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Register Form + Login CTA -->
                <div class="auth-panel" id="registerPanel">
                    <!-- Left: Login CTA -->
                    <div class="w-1/2 p-8 xl:p-12 flex flex-col justify-center gradient-primary text-white relative overflow-hidden rounded-l-3xl">
                        <!-- Decorative elements -->
                        <div class="deco-circle deco-circle-1"></div>
                        <div class="deco-circle deco-circle-2"></div>
                        <div class="deco-dots bottom-10 right-10 opacity-50"></div>

                        <div class="brand-content max-w-md mx-auto w-full text-center relative z-10" id="loginCtaContent">
                            <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="logo-hover h-28 xl:h-40 mx-auto mb-6 drop-shadow-2xl animate-float">
                            <h2 class="text-3xl xl:text-4xl font-bold mb-4">Selamat Datang!</h2>
                            <p class="mb-8 text-white/90 text-sm">Sudah punya akun? Masuk sekarang untuk mengakses berbagai fitur menarik!</p>
                            <button onclick="toggleDesktopForms()"
                                class="btn-hero font-semibold px-10 py-3.5 rounded-xl shadow-xl">
                                Masuk
                            </button>
                        </div>
                    </div>

                    <!-- Right: Register Form -->
                    <div class="w-1/2 p-8 xl:p-12 flex flex-col justify-center bg-card relative rounded-r-3xl">
                        <!-- Back Button -->
                        <a href="users/landingPage.php" class="back-btn absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary hover:bg-primary hover:text-white text-foreground shadow-sm text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </a>

                        <div class="form-content max-w-md mx-auto w-full" id="registerFormContent">
                            <h1 class="text-3xl xl:text-4xl font-bold text-foreground mb-2">Daftar</h1>
                            <p class="text-muted-foreground mb-5">Buat akun baru Anda</p>

                            <!-- Google Sign Up -->
                            <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-3 w-full rounded-xl px-6 py-3 mb-4 bg-card">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                </svg>
                                <span class="font-semibold text-foreground">Daftar dengan Google</span>
                            </a>

                            <div class="flex items-center my-4">
                                <div class="flex-1 border-t border-border"></div>
                                <span class="px-4 text-muted-foreground text-sm">atau</span>
                                <div class="flex-1 border-t border-border"></div>
                            </div>

                            <!-- Register Form -->
                            <form method="POST" action="" class="space-y-3">
                                <?php if (!empty($error)): ?>
                                    <div class="alert-enter flex items-center gap-2 px-4 py-3 rounded-xl text-sm bg-red-50 border border-red-200 text-red-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-foreground">Nama Lengkap</label>
                                    <input type="text" name="name" required
                                        class="w-full h-11 px-4 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                        placeholder="Masukkan nama lengkap">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-foreground">Email</label>
                                        <input type="email" name="email" required
                                            class="w-full h-11 px-4 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="Email">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-foreground">Nomor Telepon</label>
                                        <input type="tel" name="no_telp" maxlength="15" required
                                            class="w-full h-11 px-4 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-foreground">Password</label>
                                        <div class="relative">
                                            <input type="password" id="signupPassword" name="password" required minlength="8"
                                                class="w-full h-11 px-4 pr-11 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                                placeholder="Min 8 karakter">
                                            <button type="button" onclick="togglePassword('signupPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-sm font-medium text-foreground">Konfirmasi</label>
                                        <div class="relative">
                                            <input type="password" id="confirmPassword" name="confirm_password" required
                                                class="w-full h-11 px-4 pr-11 border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                                placeholder="Ulangi password">
                                            <button type="button" onclick="togglePassword('confirmPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="daftar"
                                    class="btn-primary w-full h-11 text-white font-semibold rounded-xl shadow-lg mt-2">
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
                </div>

            </div>
        </div>

        <!-- ============ TABLET VIEW (md) ============ -->
        <div class="hidden md:block lg:hidden glass-card rounded-3xl shadow-2xl overflow-hidden">
            <!-- Login Panel for Tablet -->
            <div class="tablet-login-panel" id="tabletLoginPanel">
                <div class="flex flex-col">
                    <!-- Top: Brand -->
                    <div class="p-8 gradient-primary text-white relative overflow-hidden">
                        <div class="deco-circle deco-circle-1"></div>
                        <div class="max-w-md mx-auto text-center relative z-10 mb-4">
                            <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="logo-hover h-36 mx-auto mb-2 drop-shadow-xl">
                            <h2 class="text-2xl font-bold mb-2">Selamat Datang!</h2>
                            <p class="text-white/90 mb-4 text-sm">Belum punya akun?</p>
                            <button onclick="toggleTabletForms()"
                                class="btn-hero font-semibold px-8 py-2.5 rounded-xl shadow-lg text-sm">
                                Daftar Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Bottom: Form -->
                    <div class="p-8 bg-card relative">
                        <a href="users/landingPage.php" class="back-btn absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-secondary hover:bg-primary hover:text-white text-foreground shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="max-w-md mx-auto">
                            <h1 class="text-2xl font-bold text-foreground mb-1">Masuk</h1>
                            <p class="text-muted-foreground text-sm mb-5">Masuk ke akun Anda</p>

                            <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-2 w-full rounded-xl px-4 py-3 mb-4 bg-card">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                </svg>
                                <span class="font-semibold text-foreground text-sm">Masuk dengan Google</span>
                            </a>

                            <div class="flex items-center my-4">
                                <div class="flex-1 border-t border-border"></div>
                                <span class="px-3 text-muted-foreground text-xs">atau</span>
                                <div class="flex-1 border-t border-border"></div>
                            </div>

                            <form method="POST" action="" class="space-y-3">
                                <?php if (!empty($error)): ?>
                                    <div class="alert-enter flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-red-50 border border-red-200 text-red-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($success)): ?>
                                    <div class="alert-enter flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-green-50 border border-green-200 text-green-600">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <?= htmlspecialchars($success) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5 col-span-2">
                                        <label class="block text-xs font-medium text-foreground">Email atau Nomor Telepon</label>
                                        <input type="text" name="login" required
                                            class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="Masukkan email atau nomor telepon">
                                    </div>
                                    <div class="space-y-1.5 col-span-2">
                                        <label class="block text-xs font-medium text-foreground">Password</label>
                                        <div class="relative">
                                            <input type="password" id="tabletSigninPassword" name="password" required
                                                class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                                placeholder="Masukkan password">
                                            <button type="button" onclick="togglePassword('tabletSigninPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-border">
                                        <span class="text-foreground">Ingat saya</span>
                                    </label>
                                    <a href="#" class="link-hover text-primary hover:text-primary-dark font-medium transition-colors">Lupa Password?</a>
                                </div>

                                <button type="submit" name="masuk"
                                    class="btn-primary w-full h-11 text-white font-semibold rounded-xl shadow-lg text-sm">
                                    Masuk
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Register Panel for Tablet -->
            <div class="tablet-register-panel hidden-tablet" id="tabletRegisterPanel">
                <div class="flex flex-col">
                    <!-- Top: Brand -->
                    <div class="p-8 gradient-primary text-white relative overflow-hidden">
                        <div class="deco-circle deco-circle-1"></div>
                        <div class="max-w-md mx-auto text-center relative z-10 mb-4">
                            <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="logo-hover h-36 mx-auto mb-2 drop-shadow-xl">
                            <h2 class="text-2xl font-bold mb-2">Sudah Punya Akun?</h2>
                            <p class="text-white/90 mb-4 text-sm">Masuk untuk melanjutkan</p>
                            <button onclick="toggleTabletForms()"
                                class="btn-hero font-semibold px-8 py-2.5 rounded-xl shadow-lg text-sm">
                                Masuk
                            </button>
                        </div>
                    </div>

                    <!-- Bottom: Form -->
                    <div class="p-8 bg-card relative">
                        <a href="users/landingPage.php" class="back-btn absolute top-4 right-4 inline-flex items-center justify-center w-10 h-10 rounded-xl bg-secondary hover:bg-primary hover:text-white text-foreground shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <div class="max-w-md mx-auto">
                            <h1 class="text-2xl font-bold text-foreground mb-1">Daftar</h1>
                            <p class="text-muted-foreground text-sm mb-5">Buat akun baru Anda</p>

                            <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-2 w-full rounded-xl px-4 py-3 mb-4 bg-card">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                </svg>
                                <span class="font-semibold text-foreground text-sm">Daftar dengan Google</span>
                            </a>

                            <div class="flex items-center my-4">
                                <div class="flex-1 border-t border-border"></div>
                                <span class="px-3 text-muted-foreground text-xs">atau</span>
                                <div class="flex-1 border-t border-border"></div>
                            </div>

                            <form method="POST" action="" class="space-y-3">
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-medium text-foreground">Nama Lengkap</label>
                                    <input type="text" name="name" required
                                        class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                        placeholder="Masukkan nama lengkap">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-foreground">Email</label>
                                        <input type="email" name="email" required
                                            class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="Email">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-foreground">Nomor Telepon</label>
                                        <input type="tel" name="no_telp" maxlength="15" required
                                            class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                            placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-foreground">Password</label>
                                        <div class="relative">
                                            <input type="password" id="tabletSignupPassword" name="password" required minlength="8"
                                                class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                                placeholder="Min 8 karakter">
                                            <button type="button" onclick="togglePassword('tabletSignupPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-medium text-foreground">Konfirmasi</label>
                                        <div class="relative">
                                            <input type="password" id="tabletConfirmPassword" name="confirm_password" required
                                                class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                                placeholder="Ulangi password">
                                            <button type="button" onclick="togglePassword('tabletConfirmPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                                <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                </svg>
                                                <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" name="daftar"
                                    class="btn-primary w-full h-11 text-white font-semibold rounded-xl shadow-lg text-sm">
                                    Daftar Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ MOBILE VIEW (sm) ============ -->
        <div class="md:hidden glass-card rounded-3xl shadow-2xl p-6 relative overflow-hidden">
            <!-- Mobile Back Button -->
            <a href="users/landingPage.php" class="back-btn absolute top-4 left-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-secondary hover:bg-primary hover:text-white text-foreground shadow-sm z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Mobile Forms Container -->
            <div class="mobile-forms-container pt-8">

                <!-- Mobile Sign In -->
                <div id="mobileSignIn" class="mobile-form active">
                    <div class="text-center mb-6">
                        <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="logo-hover h-16 mx-auto mb-4">
                        <h1 class="text-2xl font-bold text-foreground">Masuk</h1>
                        <p class="text-muted-foreground text-sm">Selamat datang kembali!</p>
                    </div>

                    <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-2 w-full rounded-xl px-4 py-3 mb-4 bg-card">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="font-semibold text-foreground text-sm">Masuk dengan Google</span>
                    </a>

                    <div class="flex items-center my-4">
                        <div class="flex-1 border-t border-border"></div>
                        <span class="px-3 text-muted-foreground text-xs">atau</span>
                        <div class="flex-1 border-t border-border"></div>
                    </div>

                    <form method="POST" action="" class="space-y-3">
                        <?php if (!empty($error)): ?>
                            <div class="alert-enter flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-red-50 border border-red-200 text-red-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="alert-enter flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-green-50 border border-green-200 text-green-600">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Email atau Nomor Telepon</label>
                            <input type="text" name="login" required
                                class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                placeholder="Masukkan email atau nomor telepon">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Password</label>
                            <div class="relative">
                                <input type="password" id="mobileSigninPassword" name="password" required
                                    class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                    placeholder="Masukkan password">
                                <button type="button" onclick="togglePassword('mobileSigninPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                    <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-border">
                                <span class="text-foreground">Ingat saya</span>
                            </label>
                            <a href="#" class="link-hover text-primary hover:text-primary-dark font-medium transition-colors">Lupa Password?</a>
                        </div>

                        <button type="submit" name="masuk"
                            class="btn-primary w-full h-11 text-white font-semibold rounded-xl shadow-lg text-sm">
                            Masuk
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-xs text-muted-foreground">
                            Belum punya akun?
                            <button onclick="toggleMobileForms()" class="link-hover text-primary hover:text-primary-dark font-semibold transition-colors ml-1">
                                Daftar Sekarang
                            </button>
                        </p>
                        <p class="text-xs text-muted-foreground mt-3">
                            Butuh bantuan? <a href="#" class="link-hover text-primary hover:text-primary-dark font-medium transition-colors">Hubungi Kami</a>
                        </p>
                    </div>
                </div>

                <!-- Mobile Sign Up -->
                <div id="mobileSignUp" class="mobile-form slide-right">
                    <div class="text-center mb-6">
                        <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="logo-hover h-16 mx-auto mb-4">
                        <h1 class="text-2xl font-bold text-foreground">Daftar</h1>
                        <p class="text-muted-foreground text-sm">Buat akun baru Anda</p>
                    </div>

                    <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-2 w-full rounded-xl px-4 py-3 mb-4 bg-card">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="font-semibold text-foreground text-sm">Daftar dengan Google</span>
                    </a>

                    <div class="flex items-center my-4">
                        <div class="flex-1 border-t border-border"></div>
                        <span class="px-3 text-muted-foreground text-xs">atau</span>
                        <div class="flex-1 border-t border-border"></div>
                    </div>

                    <form method="POST" action="" class="space-y-3">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Nama Lengkap</label>
                            <input type="text" name="name" required
                                class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Email</label>
                            <input type="email" name="email" required
                                class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                placeholder="Masukkan email">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Nomor Telepon</label>
                            <input type="tel" name="no_telp" maxlength="15" required
                                class="w-full h-11 px-4 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                placeholder="Masukkan nomor telepon">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Password</label>
                            <div class="relative">
                                <input type="password" id="mobileSignupPassword" name="password" required minlength="8"
                                    class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" onclick="togglePassword('mobileSignupPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                    <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-medium text-foreground">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="mobileConfirmPassword" name="confirm_password" required
                                    class="w-full h-11 px-4 pr-11 text-sm border-2 border-border rounded-xl bg-card text-foreground placeholder:text-muted-foreground"
                                    placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('mobileConfirmPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                    <svg class="w-5 h-5 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    <svg class="w-5 h-5 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="daftar"
                            class="btn-primary w-full h-11 text-white font-semibold rounded-xl shadow-lg text-sm">
                            Daftar
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-xs text-muted-foreground">
                            Sudah punya akun?
                            <button onclick="toggleMobileForms()" class="link-hover text-primary hover:text-primary-dark font-semibold transition-colors ml-1">
                                Masuk Sekarang
                            </button>
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        // ============ PASSWORD TOGGLE ============
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeOff = button.querySelector('.eye-off');
            const eye = button.querySelector('.eye');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOff.classList.add('hidden');
                eye.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOff.classList.remove('hidden');
                eye.classList.add('hidden');
            }
        }

        // ============ DESKTOP TOGGLE (Slider Animation) ============
        let isRegisterDesktop = false;

        function toggleDesktopForms() {
            const panelsWrapper = document.getElementById('desktopPanels');
            isRegisterDesktop = !isRegisterDesktop;

            if (isRegisterDesktop) {
                panelsWrapper.classList.add('show-register');
            } else {
                panelsWrapper.classList.remove('show-register');
            }
        }

        // ============ TABLET TOGGLE ============
        let isRegisterTablet = false;

        function toggleTabletForms() {
            const loginPanel = document.getElementById('tabletLoginPanel');
            const registerPanel = document.getElementById('tabletRegisterPanel');
            isRegisterTablet = !isRegisterTablet;

            if (isRegisterTablet) {
                loginPanel.classList.add('hidden-tablet');
                loginPanel.style.animation = 'fadeInUp 0.5s ease reverse';
                setTimeout(() => {
                    registerPanel.classList.remove('hidden-tablet');
                    registerPanel.style.animation = 'fadeInUp 0.5s ease';
                }, 150);
            } else {
                registerPanel.classList.add('hidden-tablet');
                registerPanel.style.animation = 'fadeInUp 0.5s ease reverse';
                setTimeout(() => {
                    loginPanel.classList.remove('hidden-tablet');
                    loginPanel.style.animation = 'fadeInUp 0.5s ease';
                }, 150);
            }
        }

        // ============ MOBILE TOGGLE (Slide Animation) ============
        let isRegisterMobile = false;

        function toggleMobileForms() {
            const mobileSignIn = document.getElementById('mobileSignIn');
            const mobileSignUp = document.getElementById('mobileSignUp');
            isRegisterMobile = !isRegisterMobile;

            if (isRegisterMobile) {
                // Sign In slides left, Sign Up slides in from right
                mobileSignIn.classList.remove('active');
                mobileSignIn.classList.add('slide-left');

                setTimeout(() => {
                    mobileSignUp.classList.remove('slide-right');
                    mobileSignUp.classList.add('active');
                }, 100);
            } else {
                // Sign Up slides right, Sign In slides in from left
                mobileSignUp.classList.remove('active');
                mobileSignUp.classList.add('slide-right');

                setTimeout(() => {
                    mobileSignIn.classList.remove('slide-left');
                    mobileSignIn.classList.add('active');
                }, 100);
            }
        }

        // ============ SMOOTH SCROLL TO TOP ON FORM SWITCH ============
        document.querySelectorAll('button[onclick*="toggle"]').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(() => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 300);
            });
        });
    </script>

</body>

</html>