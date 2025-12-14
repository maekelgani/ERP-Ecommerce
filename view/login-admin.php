<?php
require_once __DIR__ . '/../config/config.php';

use App\Auth\AdminRepository;
use App\Auth\SessionManager;

$error = '';
$success = '';
$adminRepo = new AdminRepository();

// Check if already logged in
if (SessionManager::isAdminLoggedIn()) {
    header('Location: admin/DashboardAdmin.php');
    exit;
}

// Try to restore from remember me token
$rememberAdmin = SessionManager::restoreAdminFromRememberToken($adminRepo);
if ($rememberAdmin) {
    SessionManager::createAdminSession($rememberAdmin);
    header('Location: admin/DashboardAdmin.php?login=success');
    exit;
}

// Handle Login Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masuk_admin'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);

    // Validation
    if (empty($email) || empty($password)) {
        $error = 'Email dan Password harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Find admin by email with role
        $admin = $adminRepo->getAdminByEmail($email);

        if ($admin && $adminRepo->verifyPassword($password, $admin['password_hash'])) {
            if (!$admin['is_active']) {
                $error = 'Akun admin Anda telah dinonaktifkan.';
            } else {
                // Create session dengan flag remember me
                SessionManager::createAdminSession($admin, $remember);

                // Update last login
                $adminRepo->updateLastLogin($admin['id_admin']);

                // Set remember me if checked
                if ($remember) {
                    SessionManager::setAdminRememberMe($admin['id_admin'], $adminRepo);
                }

                $success = 'Login berhasil! Redirecting...';
                header('Location: admin/DashboardAdmin.php?login=success');
                exit;
            }
        } else {
            $error = 'Email atau Password salah!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nano Komputer - Admin Login</title>
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
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                    },
                    keyframes: {
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
    <div class="w-full max-w-2xl animate-scale-in">

        <!-- Login Card -->
        <div class="glass-effect rounded-3xl shadow-xl overflow-hidden p-8 lg:p-12">

            <div class="max-w-md mx-auto w-full animate-fade-in">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <img src="../assets/img/logo-nano.png" alt="Nano Komputer" class="h-20 mx-auto mb-4">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-2">Admin Login</h1>
                    <p class="text-gray-600">Portal Administrator Nano Komputer</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="" class="space-y-4">
                    <?php if (!empty($error)): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm animate-fade-in">
                            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm animate-fade-in">
                            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Email Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Administrator</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="email" name="email" required
                                class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none"
                                placeholder="Masukkan email admin">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="password" id="adminPassword" name="password" required
                                class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 outline-none pr-12"
                                placeholder="Masukkan password">
                            <i class="fas fa-eye-slash password-toggle absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"
                                onclick="togglePassword('adminPassword', this)"></i>
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <span class="text-gray-700">Ingat saya</span>
                        </label>
                        <a href="#" class="text-primary hover:text-primary-dark font-medium">Lupa Password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" name="masuk_admin"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-95 shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk Admin
                    </button>
                </form>

                <!-- Info Box -->
                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-xs text-gray-700">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                        Ini adalah portal login khusus administrator. Jika Anda pelanggan, silakan
                        <a href="login.php" class="text-primary hover:text-primary-dark font-semibold">login di sini</a>.
                    </p>
                </div>

                <!-- Footer -->
                <p class="text-center text-sm text-gray-600 mt-6">
                    Butuh bantuan? <a href="#" class="text-primary hover:text-primary-dark font-medium">Hubungi Support</a>
                </p>
            </div>

        </div>

    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        // Check for logout success parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('logout') === 'success') {
                // Remove the parameter from URL without reload
                window.history.replaceState({}, document.title, window.location.pathname);

                // Show modern logout success popup
                Swal.fire({
                    icon: 'success',
                    title: '<span style="color: #1f2937; font-weight: 700;">Logout Berhasil!</span>',
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3); animation: bounceIn 0.6s ease;">
                                <i class="fas fa-sign-out-alt" style="font-size: 32px; color: white;"></i>
                            </div>
                            <p style="color: #4b5563; font-size: 15px; margin: 0; text-align: center; line-height: 1.6;">
                                Anda telah berhasil keluar dari sistem.<br>
                                <span style="color: #6b7280; font-size: 13px;">Terima kasih telah menggunakan Admin Panel</span>
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; background: #f0fdf4; padding: 8px 16px; border-radius: 20px; border: 1px solid #bbf7d0;">
                                <div style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                <span style="color: #15803d; font-size: 12px; font-weight: 500;">Sesi telah diakhiri dengan aman</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-sign-in-alt mr-2"></i>Login Kembali',
                    confirmButtonColor: '#10b981',
                    background: '#ffffff',
                    backdrop: `
                        rgba(0,0,0,0.5)
                        left top
                        no-repeat
                    `,
                    customClass: {
                        popup: 'logout-popup-custom',
                        confirmButton: 'logout-confirm-btn'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (popup) => {
                        // Add confetti effect
                        createLogoutConfetti();
                    }
                });
            }

            if (urlParams.get('logout') === 'error') {
                window.history.replaceState({}, document.title, window.location.pathname);

                Swal.fire({
                    icon: 'warning',
                    title: 'Logout Selesai',
                    text: 'Sesi Anda telah berakhir.',
                    confirmButtonColor: '#882426',
                    timer: 3000,
                    timerProgressBar: true
                });
            }

            // Check for session expired parameter
            if (urlParams.get('session') === 'expired') {
                window.history.replaceState({}, document.title, window.location.pathname);

                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color: #1f2937; font-weight: 700;">Sesi Telah Berakhir!</span>',
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 8px 0;">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(245, 158, 11, 0.3); animation: bounceIn 0.6s ease;">
                                <i class="fas fa-clock" style="font-size: 32px; color: white;"></i>
                            </div>
                            <p style="color: #4b5563; font-size: 15px; margin: 0; text-align: center; line-height: 1.6;">
                                Sesi login Anda telah habis masa berlakunya.<br>
                                <span style="color: #6b7280; font-size: 13px;">Silakan login kembali untuk melanjutkan</span>
                            </p>
                            <div style="display: flex; align-items: center; gap: 8px; background: #fffbeb; padding: 8px 16px; border-radius: 20px; border: 1px solid #fde68a;">
                                <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                <span style="color: #b45309; font-size: 12px; font-weight: 500;">Sesi otomatis berakhir untuk keamanan</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-sign-in-alt mr-2"></i>Login Kembali',
                    confirmButtonColor: '#f59e0b',
                    background: '#ffffff',
                    backdrop: `
                        rgba(0,0,0,0.5)
                        left top
                        no-repeat
                    `,
                    customClass: {
                        popup: 'session-expired-popup-custom',
                        confirmButton: 'session-expired-confirm-btn'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    },
                    timer: 8000,
                    timerProgressBar: true
                });
            }
        });

        // Confetti animation for logout success
        function createLogoutConfetti() {
            const colors = ['#10b981', '#059669', '#34d399', '#6ee7b7', '#a7f3d0'];
            const confettiContainer = document.createElement('div');
            confettiContainer.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999; overflow: hidden;';
            document.body.appendChild(confettiContainer);

            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    const size = Math.random() * 10 + 5;
                    const color = colors[Math.floor(Math.random() * colors.length)];

                    confetti.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        background: ${color};
                        border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                        left: ${Math.random() * 100}%;
                        top: -20px;
                        opacity: ${Math.random() * 0.5 + 0.5};
                        animation: confettiFall ${Math.random() * 2 + 2}s linear forwards;
                        transform: rotate(${Math.random() * 360}deg);
                    `;
                    confettiContainer.appendChild(confetti);

                    setTimeout(() => confetti.remove(), 4000);
                }, i * 30);
            }

            setTimeout(() => confettiContainer.remove(), 5000);
        }
    </script>

    <style>
        /* Logout Popup Custom Styles */
        .logout-popup-custom {
            border-radius: 20px !important;
            padding: 24px 24px 0 24px !important;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2) !important;
            overflow: hidden !important;
        }

        .logout-popup-custom .swal2-html-container {
            margin-bottom: 20px !important;
        }

        .logout-popup-custom .swal2-actions {
            margin-bottom: 24px !important;
        }

        .logout-popup-custom .swal2-timer-progress-bar-container {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 6px !important;
            background: rgba(16, 185, 129, 0.15) !important;
            border-radius: 0 !important;
            overflow: hidden !important;
        }

        .logout-popup-custom .swal2-timer-progress-bar {
            height: 100% !important;
            border-radius: 0 !important;
        }

        .logout-confirm-btn {
            border-radius: 10px !important;
            padding: 12px 28px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .logout-confirm-btn:hover {
            transform: scale(1.02) !important;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4) !important;
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.7;
                transform: scale(1.2);
            }
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* SweetAlert2 icon animation override */
        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: #10b981 !important;
        }

        /* Timer progress bar */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #10b981, #059669) !important;
        }

        /* Session Expired Popup Custom Styles */
        .session-expired-popup-custom {
            border-radius: 20px !important;
            padding: 24px 24px 0 24px !important;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.2) !important;
            overflow: hidden !important;
        }

        .session-expired-popup-custom .swal2-html-container {
            margin-bottom: 20px !important;
        }

        .session-expired-popup-custom .swal2-actions {
            margin-bottom: 24px !important;
        }

        .session-expired-popup-custom .swal2-timer-progress-bar-container {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 6px !important;
            background: rgba(245, 158, 11, 0.15) !important;
            border-radius: 0 !important;
            overflow: hidden !important;
        }

        .session-expired-popup-custom .swal2-timer-progress-bar {
            height: 100% !important;
            border-radius: 0 !important;
            background: linear-gradient(90deg, #f59e0b, #d97706) !important;
        }

        .session-expired-confirm-btn {
            border-radius: 10px !important;
            padding: 12px 28px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .session-expired-confirm-btn:hover {
            transform: scale(1.02) !important;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4) !important;
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
        }

        /* SweetAlert2 warning icon override for session expired */
        .session-expired-popup-custom .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }
    </style>

</body>

</html>