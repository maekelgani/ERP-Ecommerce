<?php

namespace App\Auth;

use App\Auth\SessionManager;
use App\Auth\CustomerRepository;

class CustomerAuthMiddleware
{
    private const GUEST_ALLOWED_PAGES = [
        'landingPage.php',
        'productCollection.php',
        'productDetail.php',
        'promoCampaign.php',
        'PromoPage.php',
        'articleTemplate.php'
    ];

    private const LOGIN_REQUIRED_PAGES = [
        'cart.php',
        'productCheckout.php',
        'myOrder.php',
        'detailOrder.php',
        'processPayment.php',
        'usersSetting.php',
        'simpan.php'
    ];

    private const PAGE_MESSAGES = [
        'cart.php' => [
            'title' => 'Keranjang Belanja',
            'message' => 'Silakan masuk ke akun Anda untuk melihat dan mengelola keranjang belanja.',
            'icon' => 'shopping_cart'
        ],
        'productCheckout.php' => [
            'title' => 'Checkout',
            'message' => 'Silakan masuk untuk melanjutkan proses checkout dan menyelesaikan pesanan Anda.',
            'icon' => 'credit_card'
        ],
        'myOrder.php' => [
            'title' => 'Pesanan Saya',
            'message' => 'Silakan masuk untuk melihat riwayat dan status pesanan Anda.',
            'icon' => 'receipt_long'
        ],
        'detailOrder.php' => [
            'title' => 'Detail Pesanan',
            'message' => 'Silakan masuk untuk melihat detail pesanan Anda.',
            'icon' => 'assignment'
        ],
        'processPayment.php' => [
            'title' => 'Pembayaran',
            'message' => 'Silakan masuk untuk melanjutkan proses pembayaran.',
            'icon' => 'payments'
        ],
        'usersSetting.php' => [
            'title' => 'Pengaturan Akun',
            'message' => 'Silakan masuk untuk mengakses pengaturan akun Anda.',
            'icon' => 'settings'
        ],
        'simpan.php' => [
            'title' => 'Wishlist',
            'message' => 'Silakan masuk untuk menyimpan produk ke wishlist Anda.',
            'icon' => 'favorite'
        ]
    ];

    public static function isLoggedIn(): bool
    {
        if (!SessionManager::isCustomerLoggedIn()) {
            return false;
        }

        if (SessionManager::isCustomerSessionExpired()) {
            if (!self::tryRestoreFromRememberToken()) {
                SessionManager::destroySession();
                return false;
            }
        }

        return true;
    }

    public static function checkSessionStatus(): string
    {
        return SessionManager::checkCustomerSessionStatus();
    }

    public static function getSessionRemainingTime(): int
    {
        return SessionManager::getCustomerSessionRemainingTime();
    }

    public static function isRememberMeActive(): bool
    {
        return SessionManager::isCustomerRememberMeActive();
    }

    public static function getCurrentCustomer(): ?array
    {
        return SessionManager::getCurrentCustomer();
    }

    public static function getCustomerId(): ?int
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return $_SESSION['customer_id'] ?? null;
    }

    public static function requireLogin(string $currentPage = ''): void
    {
        if (self::isLoggedIn()) {
            return;
        }

        if (self::tryRestoreFromRememberToken()) {
            return;
        }

        $pageInfo = self::getPageInfo($currentPage);

        header('Content-Type: text/html; charset=utf-8');
        self::renderLoginRequiredPage($pageInfo, $currentPage);
        exit;
    }

    public static function checkLoginForAjax(): array
    {
        if (!self::isLoggedIn()) {
            if (!self::tryRestoreFromRememberToken()) {
                return [
                    'success' => false,
                    'requireLogin' => true,
                    'message' => 'Silakan login terlebih dahulu'
                ];
            }
        }
        return ['success' => true, 'loggedIn' => true];
    }

    public static function isGuestAllowed(string $page): bool
    {
        return in_array($page, self::GUEST_ALLOWED_PAGES);
    }

    public static function isLoginRequired(string $page): bool
    {
        return in_array($page, self::LOGIN_REQUIRED_PAGES);
    }

    public static function getPageInfo(string $page): array
    {
        return self::PAGE_MESSAGES[$page] ?? [
            'title' => 'Akses Terbatas',
            'message' => 'Silakan masuk ke akun Anda untuk mengakses halaman ini.',
            'icon' => 'lock'
        ];
    }

    private static function tryRestoreFromRememberToken(): bool
    {
        if (!isset($_COOKIE['remember_token'])) {
            return false;
        }

        try {
            $customerRepo = new CustomerRepository();
            return SessionManager::tryRestoreCustomerSessionFromRemember($customerRepo);
        } catch (\Exception $e) {
            error_log('CustomerAuthMiddleware: Failed to restore session - ' . $e->getMessage());
        }

        return false;
    }

    public static function getLoginUrl(): string
    {
        return '../../view/login.php';
    }

    public static function getRegisterUrl(): string
    {
        return '../../view/login.php#registerPanel';
    }

    private static function renderLoginRequiredPage(array $pageInfo, string $returnPage): void
    {
        $loginUrl = self::getLoginUrl();
        $registerUrl = self::getRegisterUrl();
        $returnUrl = urlencode($_SERVER['REQUEST_URI'] ?? '');

?>
        <!DOCTYPE html>
        <html lang="id">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($pageInfo['title']) ?> - Login Diperlukan | Nano Komputer</title>
            <link rel="icon" href="../../assets/img/logo-nano-transparant.png" type="image/x-icon">
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: '#882426',
                                'primary-dark': '#6a1c1e',
                                'primary-light': '#a42d30'
                            }
                        }
                    }
                }
            </script>
            <style>
                @keyframes float {

                    0%,
                    100% {
                        transform: translateY(0px);
                    }

                    50% {
                        transform: translateY(-10px);
                    }
                }

                @keyframes pulse-ring {
                    0% {
                        transform: scale(0.8);
                        opacity: 1;
                    }

                    100% {
                        transform: scale(1.3);
                        opacity: 0;
                    }
                }

                .float-animation {
                    animation: float 3s ease-in-out infinite;
                }

                .pulse-ring {
                    animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
                }

                .glass-effect {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                }
            </style>
        </head>

        <body class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 flex items-center justify-center p-4">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-primary/10 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-radial from-primary/5 to-transparent rounded-full"></div>
            </div>

            <div class="relative z-10 max-w-lg w-full">
                <div class="glass-effect rounded-3xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-primary to-primary-dark p-8 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-black/10"></div>
                        <div class="relative">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4 float-animation">
                                <div class="absolute inset-0 rounded-full bg-white/30 pulse-ring"></div>
                                <span class="material-symbols-outlined text-white text-4xl">
                                    <?= htmlspecialchars($pageInfo['icon']) ?>
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-white mb-2"><?= htmlspecialchars($pageInfo['title']) ?></h1>
                            <p class="text-white/80 text-sm">Diperlukan akun untuk melanjutkan</p>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
                                <span class="material-symbols-outlined text-primary text-3xl">account_circle</span>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">Masuk ke Akun Anda</h2>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <?= htmlspecialchars($pageInfo['message']) ?>
                            </p>
                        </div>

                        <div class="space-y-4">
                            <a href="<?= $loginUrl ?>?redirect=<?= $returnUrl ?>"
                                class="flex items-center justify-center gap-2 w-full py-4 px-6 bg-gradient-to-r from-primary to-primary-dark text-white font-semibold rounded-xl hover:from-primary-dark hover:to-primary transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                <span class="material-symbols-outlined">login</span>
                                <span>Masuk Sekarang</span>
                            </a>

                            <div class="relative flex items-center justify-center my-4">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <span class="relative px-4 text-sm text-gray-500 bg-white">atau</span>
                            </div>

                            <a href="<?= $registerUrl ?>"
                                class="flex items-center justify-center gap-2 w-full py-4 px-6 bg-white border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary hover:text-white transition-all duration-300">
                                <span class="material-symbols-outlined">person_add</span>
                                <span>Daftar Akun Baru</span>
                            </a>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="flex flex-wrap items-center justify-center gap-6 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-green-500 text-lg">verified_user</span>
                                    <span>Aman & Terpercaya</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-500 text-lg">speed</span>
                                    <span>Proses Cepat</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <a href="../../view/users/landingPage.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-primary transition-colors text-sm">
                                <span class="material-symbols-outlined text-lg">arrow_back</span>
                                <span>Kembali ke Beranda</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-center text-gray-400 text-xs mt-6">
                    &copy; <?= date('Y') ?> Nano Komputer. All rights reserved.
                </p>
            </div>
        </body>

        </html>
<?php
    }
}
