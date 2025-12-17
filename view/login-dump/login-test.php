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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
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
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .gradient-primary {
            background: linear-gradient(135deg, hsl(359, 60%, 33%) 0%, hsl(359, 55%, 45%) 100%);
        }
        
        @keyframes scaleIn {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-scale-in {
            animation: scaleIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        input:focus {
            outline: none;
            border-color: hsl(359, 60%, 33%);
            box-shadow: 0 0 0 3px hsla(359, 60%, 33%, 0.15);
        }
        
        .btn-primary {
            background: hsl(359, 60%, 33%);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: hsl(359, 65%, 28%);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary:active {
            transform: scale(0.98);
        }
        
        .btn-hero {
            background: white;
            color: hsl(359, 60%, 33%);
            transition: all 0.2s ease;
        }
        
        .btn-hero:hover {
            background: hsl(30, 20%, 93%);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }
        
        .btn-hero:active {
            transform: scale(0.98);
        }
        
        .btn-google {
            border: 2px solid hsl(30, 15%, 88%);
            transition: all 0.2s ease;
        }
        
        .btn-google:hover {
            background: hsl(30, 20%, 93%);
            border-color: hsl(0, 0%, 70%);
        }
    </style>
</head>

<body class="bg-background min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-5xl animate-scale-in">

        <!-- Desktop View -->
        <div class="hidden md:flex glass-card rounded-3xl shadow-2xl overflow-hidden min-h-[600px]" id="mainContainer">
            
            <!-- Left Panel - Sign In -->
            <div id="signInPanel" class="w-1/2 p-8 lg:p-12 flex flex-col justify-center bg-card relative">
                <!-- Back Button -->
                <a href="users/landingPage.php" class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-lg bg-secondary hover:bg-primary hover:text-white text-foreground transition-all duration-300 group shadow-sm hover:shadow-md text-sm font-semibold">
                    <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>

                <div class="max-w-md mx-auto w-full animate-fade-in-up">
                    <h1 class="text-3xl lg:text-4xl font-bold text-foreground mb-2">Masuk</h1>
                    <p class="text-muted-foreground mb-6">Selamat datang kembali!</p>

                    <!-- Google Sign In Button -->
                    <a href="<?= htmlspecialchars($url) ?>" class="btn-google flex items-center justify-center gap-3 w-full rounded-xl px-6 py-3 mb-4 bg-card">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="font-semibold text-foreground">Masuk dengan Google</span>
                    </a>

                    <div class="flex items-center my-6">
                        <div class="flex-1 border-t border-border"></div>
                        <span class="px-4 text-muted-foreground text-sm">atau</span>
                        <div class="flex-1 border-t border-border"></div>
                    </div>

                    <!-- Sign In Form -->
                    <form method="POST" action="" class="space-y-4">
                        <?php if (!empty($error)): ?>
                            <div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm bg-red-50 border border-red-200 text-red-600 animate-fade-in-up">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($success)): ?>
                            <div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm bg-green-50 border border-green-200 text-green-600 animate-fade-in-up">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-foreground">Email atau Nomor Telepon</label>
                            <input type="text" name="login" required
                                class="w-full h-11 px-4 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                placeholder="Masukkan email atau nomor telepon">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-foreground">Password</label>
                            <div class="relative">
                                <input type="password" id="signinPassword" name="password" required
                                    class="w-full h-11 px-4 pr-11 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                    placeholder="Masukkan password">
                                <button type="button" onclick="togglePassword('signinPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
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

                        <div class="flex items-center justify-between text-sm">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border text-primary focus:ring-primary focus:ring-offset-0">
                                <span class="text-foreground">Ingat saya</span>
                            </label>
                            <a href="#" class="text-primary hover:text-primary-dark font-medium transition-colors">Lupa Password?</a>
                        </div>

                        <button type="submit" name="masuk"
                            class="btn-primary w-full h-11 text-white font-semibold rounded-lg shadow-lg">
                            Masuk
                        </button>
                    </form>

                    <p class="text-center text-sm text-muted-foreground mt-6">
                        Butuh bantuan? <a href="#" class="text-primary hover:text-primary-dark font-medium transition-colors">Hubungi Nano Komputer</a>
                    </p>
                </div>
            </div>

            <!-- Right Panel - CTA Sign Up -->
            <div id="signUpPanel" class="w-1/2 p-8 lg:p-12 flex flex-col justify-center gradient-primary text-white">
                <div class="max-w-md mx-auto w-full text-center animate-fade-in-up">
                    <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-32 mx-auto mb-6 drop-shadow-lg">
                    <h2 class="text-3xl font-bold mb-4">Halo, Teman!</h2>
                    <p class="mb-8 text-white/90">Belum punya akun? Daftar sekarang dan nikmati berbagai penawaran menarik!</p>
                    <button onclick="toggleForms()"
                        class="btn-hero font-semibold px-8 py-3 rounded-lg shadow-lg">
                        Daftar Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Desktop Register Form (Hidden by default) -->
        <div id="registerFormDesktop" class="hidden glass-card rounded-3xl shadow-2xl overflow-hidden min-h-[600px]">
            <div class="flex">
                <!-- Left Panel - Brand -->
                <div class="w-1/2 p-8 lg:p-12 flex flex-col justify-center gradient-primary text-white">
                    <div class="max-w-md mx-auto w-full text-center animate-fade-in-up">
                        <img src="../assets/img/logo_nano.png" alt="Nano Komputer" class="h-32 mx-auto mb-6 drop-shadow-lg">
                        <h2 class="text-3xl font-bold mb-4">Selamat Datang!</h2>
                        <p class="mb-8 text-white/90">Sudah punya akun? Masuk sekarang untuk mengakses berbagai fitur menarik!</p>
                        <button onclick="toggleForms()"
                            class="btn-hero font-semibold px-8 py-3 rounded-lg shadow-lg">
                            Masuk
                        </button>
                    </div>
                </div>

                <!-- Right Panel - Register Form -->
                <div class="w-1/2 p-8 lg:p-12 flex flex-col justify-center bg-card relative">
                    <a href="users/landingPage.php" class="absolute top-6 right-6 flex items-center gap-2 px-4 py-2 rounded-lg bg-secondary hover:bg-primary hover:text-white text-foreground transition-all duration-300 group shadow-sm hover:shadow-md text-sm font-semibold">
                        <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali</span>
                    </a>

                    <div class="max-w-md mx-auto w-full animate-fade-in-up">
                        <h1 class="text-3xl lg:text-4xl font-bold text-foreground mb-2">Daftar</h1>
                        <p class="text-muted-foreground mb-6">Buat akun baru Anda</p>

                        <!-- Google Sign Up Button -->
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
                                <div class="flex items-center gap-2 px-4 py-3 rounded-lg text-sm bg-red-50 border border-red-200 text-red-600 animate-fade-in-up">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-medium text-foreground">Nama Lengkap</label>
                                <input type="text" name="name" required
                                    class="w-full h-11 px-4 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                    placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-foreground">Email</label>
                                    <input type="email" name="email" required
                                        class="w-full h-11 px-4 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                        placeholder="Email">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-foreground">Nomor Telepon</label>
                                    <input type="tel" name="no_telp" maxlength="15" required
                                        class="w-full h-11 px-4 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-medium text-foreground">Password</label>
                                    <div class="relative">
                                        <input type="password" id="signupPassword" name="password" required minlength="8"
                                            class="w-full h-11 px-4 pr-11 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
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
                                            class="w-full h-11 px-4 pr-11 border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
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
                                class="btn-primary w-full h-11 text-white font-semibold rounded-lg shadow-lg mt-4">
                                Daftar Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden glass-card rounded-3xl shadow-2xl p-6 relative">
            <!-- Mobile Back Button -->
            <a href="users/landingPage.php" class="absolute top-4 left-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-secondary hover:bg-primary hover:text-white text-foreground transition-all duration-300 shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Mobile Sign In -->
            <div id="mobileSignIn" class="animate-fade-in-up pt-8">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
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
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-red-50 border border-red-200 text-red-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs bg-green-50 border border-green-200 text-green-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-foreground">Email atau Nomor Telepon</label>
                        <input type="text" name="login" required
                            class="w-full h-10 px-3 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                            placeholder="Masukkan email atau nomor telepon">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-foreground">Password</label>
                        <div class="relative">
                            <input type="password" id="mobileSigninPassword" name="password" required
                                class="w-full h-10 px-3 pr-10 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                placeholder="Masukkan password">
                            <button type="button" onclick="togglePassword('mobileSigninPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg class="w-4 h-4 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-border text-primary focus:ring-primary focus:ring-offset-0">
                            <span class="text-foreground">Ingat saya</span>
                        </label>
                        <a href="#" class="text-primary hover:text-primary-dark font-medium transition-colors">Lupa Password?</a>
                    </div>

                    <button type="submit" name="masuk"
                        class="btn-primary w-full h-10 text-white font-semibold rounded-lg shadow-lg text-sm">
                        Masuk
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-muted-foreground">
                        Belum punya akun?
                        <button onclick="toggleMobileForms()" class="text-primary hover:text-primary-dark font-semibold transition-colors">
                            Daftar Sekarang
                        </button>
                    </p>
                    <p class="text-xs text-muted-foreground mt-3">
                        Butuh bantuan? <a href="#" class="text-primary hover:text-primary-dark font-medium transition-colors">Hubungi Kami</a>
                    </p>
                </div>
            </div>

            <!-- Mobile Sign Up -->
            <div id="mobileSignUp" class="hidden animate-fade-in-up pt-8">
                <div class="text-center mb-6">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-16 mx-auto mb-4">
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
                            class="w-full h-10 px-3 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                            placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-foreground">Email</label>
                        <input type="email" name="email" required
                            class="w-full h-10 px-3 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                            placeholder="Masukkan email">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-foreground">Nomor Telepon</label>
                        <input type="tel" name="no_telp" maxlength="15" required
                            class="w-full h-10 px-3 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                            placeholder="Masukkan nomor telepon">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-foreground">Password</label>
                        <div class="relative">
                            <input type="password" id="mobileSignupPassword" name="password" required minlength="8"
                                class="w-full h-10 px-3 pr-10 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('mobileSignupPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg class="w-4 h-4 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="w-full h-10 px-3 pr-10 text-sm border-2 border-border rounded-lg bg-card text-foreground placeholder:text-muted-foreground transition-all duration-200"
                                placeholder="Ulangi password">
                            <button type="button" onclick="togglePassword('mobileConfirmPassword', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-primary transition-colors">
                                <svg class="w-4 h-4 eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <svg class="w-4 h-4 eye hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="daftar"
                        class="btn-primary w-full h-10 text-white font-semibold rounded-lg shadow-lg text-sm">
                        Daftar
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-muted-foreground">
                        Sudah punya akun?
                        <button onclick="toggleMobileForms()" class="text-primary hover:text-primary-dark font-semibold transition-colors">
                            Masuk Sekarang
                        </button>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <script>
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

        function toggleForms() {
            const mainContainer = document.getElementById('mainContainer');
            const registerForm = document.getElementById('registerFormDesktop');

            if (registerForm.classList.contains('hidden')) {
                mainContainer.classList.add('hidden');
                mainContainer.classList.remove('md:flex');
                registerForm.classList.remove('hidden');
                registerForm.classList.add('md:block');
            } else {
                registerForm.classList.add('hidden');
                registerForm.classList.remove('md:block');
                mainContainer.classList.remove('hidden');
                mainContainer.classList.add('md:flex');
            }
        }

        function toggleMobileForms() {
            const mobileSignIn = document.getElementById('mobileSignIn');
            const mobileSignUp = document.getElementById('mobileSignUp');

            if (mobileSignIn.classList.contains('hidden')) {
                mobileSignIn.classList.remove('hidden');
                mobileSignUp.classList.add('hidden');
            } else {
                mobileSignIn.classList.add('hidden');
                mobileSignUp.classList.remove('hidden');
            }
        }
    </script>

</body>
</html>
