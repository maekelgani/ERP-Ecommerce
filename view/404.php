<?php

/**
 * Halaman 404 - Page Not Found
 * Halaman error yang ditampilkan ketika URL tidak ditemukan
 * Branded dengan identitas Nano Komputer
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Nano Komputer</title>
    <link rel="icon" href="assets/img/logo-nano-transparant.png">
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
                        'float': 'float 6s ease-in-out infinite',
                        'bounce-slow': 'bounceSlow 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px)',
                            },
                            '50%': {
                                transform: 'translateY(-20px)',
                            },
                        },
                        bounceSlow: {
                            '0%, 100%': {
                                transform: 'translateY(0)',
                            },
                            '50%': {
                                transform: 'translateY(-10px)',
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
            background: linear-gradient(135deg, #FAF7F3 0%, #f3e5e0 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .error-code {
            font-size: 12rem;
            font-weight: 900;
            background: linear-gradient(135deg, #882426 0%, #d32f2f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(136, 36, 38, 0.1);
        }

        .floating-icon {
            animation: float 6s ease-in-out infinite;
        }

        .broken-server {
            position: relative;
            height: 200px;
            margin: 2rem 0;
        }

        .server-box {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #882426 0%, #b91c1c 100%);
            border-radius: 0.75rem;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(136, 36, 38, 0.2);
            animation: bounce-slow 3s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .crack {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #882426 0%, #b91c1c 100%);
            color: white;
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(136, 36, 38, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(136, 36, 38, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: transparent;
            color: #882426;
            text-decoration: none;
            border: 2px solid #882426;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #FAF7F3;
            transform: translateY(-3px);
        }

        .decorative-elements {
            position: fixed;
            z-index: -1;
            pointer-events: none;
        }

        .circle {
            position: fixed;
            border-radius: 50%;
            opacity: 0.05;
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            background: #882426;
            top: -100px;
            right: -100px;
        }

        .circle-2 {
            width: 250px;
            height: 250px;
            background: #d32f2f;
            bottom: -80px;
            left: -80px;
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 6rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Decorative Elements -->
    <div class="decorative-elements">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-2xl">

        <!-- Error Card -->
        <div class="glass-card rounded-3xl shadow-2xl p-8 lg:p-16 text-center">

            <!-- Logo -->
            <div class="mb-8">
                <img src="assets/img/logo-nano.png" alt="Nano Komputer" class="h-24 mx-auto mb-6 floating-icon">
            </div>

            <!-- Error Code -->
            <div class="error-code mb-6">404</div>

            <!-- Error Title -->
            <h1 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-3">
                Halaman Tidak Ditemukan
            </h1>

            <!-- Error Description -->
            <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin URL salah atau halaman telah dipindahkan.
                Tim kami akan membantu Anda kembali ke jalur yang benar.
            </p>

            <!-- Broken Server Animation -->
            <div class="broken-server mb-12">
                <div class="server-box">
                    <i class="fas fa-server text-white"></i>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group mb-12">
                <a href="/" class="btn-primary">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
                <a href="login-admin.php" class="btn-secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Admin
                </a>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-12 pt-8 border-t border-gray-200">
                <div class="p-4">
                    <i class="fas fa-search text-2xl text-primary mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Cek URL</h3>
                    <p class="text-sm text-gray-600">Pastikan URL yang Anda masukkan sudah benar</p>
                </div>
                <div class="p-4">
                    <i class="fas fa-sync text-2xl text-primary mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Refresh Halaman</h3>
                    <p class="text-sm text-gray-600">Coba refresh atau reload halaman ini</p>
                </div>
                <div class="p-4">
                    <i class="fas fa-headset text-2xl text-primary mb-2"></i>
                    <h3 class="font-semibold text-gray-800 mb-1">Hubungi Support</h3>
                    <p class="text-sm text-gray-600">Tim support siap membantu Anda</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-600 mb-4">
                    Kode Error: <span class="font-mono font-semibold text-primary">404</span> - Page Not Found
                </p>
                <p class="text-xs text-gray-500">
                    Nano Komputer © 2025 | Semua Hak Dilindungi
                </p>
            </div>

        </div>

    </div>

    <!-- Keyboard Shortcut Hint -->
    <script>
        // Tambahan: Shortcut keyboard untuk user experience yang lebih baik
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.history.back();
            } else if (event.key === 'h' || event.key === 'H') {
                window.location.href = '/';
            }
        });
    </script>

</body>

</html>