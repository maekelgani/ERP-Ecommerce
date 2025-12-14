<?php
$pageTitle = "Lengkapi Profil";
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerRepository;
use App\Auth\SessionManager;

\App\Auth\CustomerAuthMiddleware::requireLogin('completeProfile.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

$customerRepo = new CustomerRepository();
$customerData = $customerRepo->getById($customerId);

if ($customerData && !empty($customerData['no_telp'])) {
    unset($_SESSION['needs_phone_completion']);
    header('Location: landingPage.php');
    exit;
}

$isGoogleUser = ($customerData['login_type'] ?? '') === 'google';
$userName = htmlspecialchars($customerData['nama_lengkap'] ?? $customer['name'] ?? 'User');
$userEmail = htmlspecialchars($customerData['email'] ?? $customer['email'] ?? '');
$userInitial = strtoupper(substr($userName, 0, 1));
$profileImage = $customerData['profile_image'] ?? null;

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['skip'])) {
        unset($_SESSION['needs_phone_completion']);
        header('Location: landingPage.php');
        exit;
    }

    $phone = trim($_POST['no_telp'] ?? '');

    if (empty($phone)) {
        $error = 'Nomor telepon harus diisi!';
    } elseif (strlen($phone) < 10 || strlen($phone) > 15) {
        $error = 'Nomor telepon harus 10-15 digit!';
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $error = 'Nomor telepon hanya boleh berisi angka!';
    } elseif ($customerRepo->phoneExists($phone)) {
        $error = 'Nomor telepon sudah digunakan akun lain!';
    } else {
        try {
            $customerRepo->updatePhone($customerId, $phone);
            unset($_SESSION['needs_phone_completion']);
            $_SESSION['profile_completed'] = true;
            $_SESSION['toast_success'] = 'Profil berhasil dilengkapi! Selamat berbelanja!';
            header('Location: landingPage.php');
            exit;
        } catch (\Exception $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
            if (APP_DEBUG) {
                $error .= ' (' . $e->getMessage() . ')';
            }
        }
    }
}

$isWelcome = isset($_SESSION['google_welcome']);
$welcomeName = $_SESSION['google_welcome_name'] ?? $userName;
if ($isWelcome) {
    unset($_SESSION['google_welcome']);
    unset($_SESSION['google_welcome_name']);
}

include '../../components/users/head.php';
?>

<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <a href="landingPage.php" class="inline-block">
                <img src="../../assets/img/logo-nano.png" alt="Nano Komputer" class="h-12 mx-auto">
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-[#882426] p-8 text-center">
                <div class="relative inline-block mb-4">
                    <?php if ($profileImage): ?>
                        <img src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>"
                            alt="Profile"
                            class="w-24 h-24 rounded-full object-cover ring-4 ring-white/30">
                    <?php else: ?>
                        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-white text-4xl font-bold ring-4 ring-white/30">
                            <?= $userInitial ?>
                        </div>
                    <?php endif; ?>
                    <span class="absolute bottom-1 right-1 w-5 h-5 bg-green-400 border-2 border-white rounded-full flex items-center justify-center">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </div>

                <?php if ($isWelcome): ?>
                    <h1 class="text-2xl font-bold text-white mb-2">Selamat Datang, <?= htmlspecialchars($welcomeName) ?>!</h1>
                    <p class="text-white/80">Akun Google Anda berhasil terhubung</p>
                <?php else: ?>
                    <h1 class="text-2xl font-bold text-white mb-2">Lengkapi Profil Anda</h1>
                    <p class="text-white/80"><?= $userEmail ?></p>
                <?php endif; ?>
            </div>

            <div class="p-8">
                <?php if ($isGoogleUser): ?>
                    <div class="flex items-center gap-3 mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-blue-900">Login dengan Google</p>
                            <p class="text-xs text-blue-700">Anda bisa login kapan saja dengan akun Google ini</p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Tambahkan Nomor Telepon</h2>
                    <p class="text-sm text-gray-600">Nomor telepon diperlukan untuk konfirmasi pesanan dan notifikasi pengiriman.</p>
                </div>

                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none z-10">
                                <span class="text-gray-600 font-medium text-base">+62</span>
                                <span class="text-gray-300 mx-1">|</span>
                            </div>
                            <input type="tel"
                                name="no_telp"
                                id="inputPhone"
                                value="<?= htmlspecialchars($_POST['no_telp'] ?? '') ?>"
                                class="w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all text-base"
                                placeholder="812 3456 7890"
                                pattern="[0-9]{10,15}"
                                maxlength="15"
                                required
                                autofocus>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Contoh: 812 3456 7890 (10-15 digit)</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit"
                            class="flex-1 py-4 px-6 bg-[#882426] hover:bg-[#6a1c1e] text-white font-semibold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-[#882426]/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan & Lanjutkan</span>
                        </button>

                        <button type="submit"
                            name="skip"
                            value="1"
                            formnovalidate
                            class="py-4 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                            Nanti Saja
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs text-gray-500 text-center">
                        Anda dapat mengubah informasi ini kapan saja di
                        <a href="usersSetting.php" class="text-[#882426] hover:underline font-medium">Pengaturan Akun</a>
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">
                &copy; <?= date('Y') ?> Nano Komputer. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        document.getElementById('inputPhone').addEventListener('input', function(e) {
            // Hapus semua karakter non-digit
            let value = e.target.value.replace(/[^0-9]/g, '');

            // Jika dimulai dengan 62, ganti dengan 0
            if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }

            // Jika dimulai dengan 0, hapus 0 di depan
            if (value.startsWith('0')) {
                value = value.substring(1);
            }

            // Format dengan spasi untuk keterbacaan (opsional)
            if (value.length > 3 && value.length <= 7) {
                value = value.substring(0, 3) + ' ' + value.substring(3);
            } else if (value.length > 7) {
                value = value.substring(0, 3) + ' ' + value.substring(3, 7) + ' ' + value.substring(7);
            }

            e.target.value = value;
        });

        // Hilangkan spasi saat form disubmit
        document.querySelector('form').addEventListener('submit', function() {
            const phoneInput = document.getElementById('inputPhone');
            phoneInput.value = phoneInput.value.replace(/\s/g, '');
        });
    </script>
</body>

</html>