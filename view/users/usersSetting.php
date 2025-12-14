<?php
$pageTitle = "Pengaturan Pengguna";
require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin('usersSetting.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();

$customerData = null;
$memberSince = '';
try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM customers WHERE id_customer = :id");
    $stmt->execute([':id' => $customerId]);
    $customerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customerData && isset($customerData['created_at'])) {
        $createdDate = new DateTime($customerData['created_at']);
        $memberSince = $createdDate->format('d M Y');
    }
} catch (Exception $e) {
    error_log('Error fetching customer data: ' . $e->getMessage());
}

$nama = htmlspecialchars($customerData['nama_lengkap'] ?? $customer['name'] ?? 'User');
$email = htmlspecialchars($customerData['email'] ?? $customer['email'] ?? '');
$noTelepon = htmlspecialchars($customerData['no_telp'] ?? '');
$userInitial = strtoupper(substr($nama, 0, 1));
$profileImage = $customerData['profile_image'] ?? null;

$isGoogleUser = ($customerData['login_type'] ?? '') === 'google';
$hasLocalPassword = !empty($customerData['password_hash']);
$googleEmail = $customerData['google_email'] ?? null;

include '../../components/users/head.php';
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<body class="w-full bg-gray-100 min-h-screen" data-customer-id="<?= $customerId ?>" data-customer-logged-in="true">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <div id="navbarSpacer" class="transition-all duration-300" style="height: 160px;"></div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-16">
        <div class="mb-8">
            <?php
            $breadcrumbs = [
                ['label' => 'Beranda', 'url' => 'landingPage.php'],
                ['label' => 'Pengaturan Akun', 'url' => null]
            ];
            include '../../components/users/breadcrumb.php';
            ?>
            <h1 class="text-3xl font-bold text-gray-900">Pengaturan Akun</h1>
            <p class="text-gray-500 mt-2">Kelola profil, keamanan, dan preferensi akun Anda</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <aside class="lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-44">
                    <div class="bg-[#882426] p-6">
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                <?php if ($profileImage): ?>
                                    <img src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>"
                                        alt="Profile"
                                        class="w-16 h-16 rounded-full object-cover ring-4 ring-white/20">
                                <?php else: ?>
                                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-white text-2xl font-bold ring-4 ring-white/20">
                                        <?= $userInitial ?>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 border-2 border-[#882426] rounded-full"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h2 class="text-lg font-bold text-white truncate"><?= $nama ?></h2>
                                <p class="text-white/70 text-sm truncate"><?= $email ?></p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <?php if ($isGoogleUser): ?>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/15 text-white text-xs font-medium rounded-full">
                                    <svg class="w-6 h-6 bg-white rounded-full py-1" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                    </svg>
                                    Google
                                </div>
                            <?php endif; ?>
                            <?php if ($memberSince): ?>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/15 text-white text-xs font-medium rounded-full">
                                    <span class="material-symbols-outlined text-sm">verified</span>
                                    Sejak <?= $memberSince ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <nav class="p-3" id="settingsNav">
                        <div class="space-y-1">
                            <button data-tab="profile" class="nav-item active w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">person</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Informasi Pengguna</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Nama, email, telepon</span>
                                </div>
                            </button>

                            <button data-tab="address" class="nav-item w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">location_on</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Alamat</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Kelola alamat pengiriman</span>
                                </div>
                            </button>

                            <a href="wishlist.php" class="nav-item w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">favorite</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Wishlist</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Produk favorit Anda</span>
                                </div>
                                <span class="material-symbols-outlined text-gray-400 text-lg">arrow_forward</span>
                            </a>

                            <button data-tab="security" class="nav-item w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">shield</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Keamanan</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Password & verifikasi</span>
                                </div>
                            </button>

                            <button data-tab="notification" class="nav-item w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">notifications</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Notifikasi</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Email & push notification</span>
                                </div>
                            </button>

                            <button data-tab="preference" class="nav-item w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-left transition-all duration-200 hover:bg-gray-50">
                                <div class="nav-icon w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center transition-all duration-200">
                                    <span class="material-symbols-outlined text-gray-500 transition-colors duration-200">tune</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="nav-label font-medium text-gray-600 block transition-all duration-200">Preferensi</span>
                                    <span class="nav-desc text-xs text-gray-400 transition-colors duration-200">Bahasa & tampilan</span>
                                </div>
                            </button>
                        </div>
                    </nav>

                    <style>
                        .nav-item.active {
                            background-color: rgba(136, 36, 38, 0.08);
                        }

                        .nav-item.active .nav-icon {
                            background-color: #882426;
                        }

                        .nav-item.active .nav-icon .material-symbols-outlined {
                            color: white;
                        }

                        .nav-item.active .nav-label {
                            color: #882426;
                            font-weight: 700;
                        }

                        .nav-item.active .nav-desc {
                            color: rgba(136, 36, 38, 0.7);
                        }

                        .nav-item:not(.active):hover .nav-icon {
                            background-color: #f3f4f6;
                        }
                    </style>

                    <div class="p-3 border-t border-gray-100">
                        <button id="logoutBtn" class="w-full flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-xl transition-all duration-200 group">
                            <div class="w-10 h-10 rounded-xl bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined">logout</span>
                            </div>
                            <span class="font-medium">Keluar</span>
                        </button>
                    </div>
                </div>
            </aside>

            <div class="flex-1 min-w-0">
                <div id="tab-profile" class="tab-content">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Informasi Pribadi</h3>
                                <p class="text-gray-500 text-sm mt-1.5">Perbarui data profil Anda</p>
                            </div>
                            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-medium rounded-full">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                Terverifikasi
                            </div>
                        </div>

                        <form id="profileForm" class="p-6">
                            <div class="mb-8 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="flex flex-col sm:flex-row gap-6">
                                    <div class="flex-shrink-0">
                                        <div class="relative group">
                                            <div id="profileImageContainer" class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg bg-white">
                                                <?php if ($profileImage): ?>
                                                    <img src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>"
                                                        alt="Profile"
                                                        id="profilePreview"
                                                        class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div id="profileInitial" class="w-full h-full bg-[#882426] flex items-center justify-center text-white text-3xl font-bold">
                                                        <?= $userInitial ?>
                                                    </div>
                                                    <img src="" alt="Profile" id="profilePreview" class="w-full h-full object-cover hidden">
                                                <?php endif; ?>
                                            </div>

                                            <div class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center cursor-pointer" onclick="document.getElementById('profilePhotoInput').click()">
                                                <div class="text-center text-white">
                                                    <span class="material-symbols-outlined text-2xl">photo_camera</span>
                                                    <p class="text-xs mt-1 font-medium">Ubah Foto</p>
                                                </div>
                                            </div>

                                            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/jpeg,image/png,image/gif" class="hidden">
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-center">
                                        <h4 class="font-bold text-gray-900 text-lg">Foto Profil</h4>
                                        <p class="text-sm text-gray-500 mt-1 mb-4">Foto profil membantu orang lain mengenali Anda</p>

                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" onclick="document.getElementById('profilePhotoInput').click()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white text-sm font-medium rounded-xl hover:bg-[#6a1c1e] transition-all">
                                                <span class="material-symbols-outlined text-lg">upload</span>
                                                Unggah Foto
                                            </button>
                                            <button type="button" id="removePhotoBtn" class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-100 transition-all <?= $profileImage ? '' : 'hidden' ?>">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                                Hapus
                                            </button>
                                        </div>

                                        <div class="mt-3 flex items-start gap-2 text-xs text-gray-400">
                                            <span class="material-symbols-outlined text-sm mb-1.5">info</span>
                                            <span class="mt-1">Format: JPG, PNG, GIF. Ukuran maksimal 2MB. Resolusi minimal 200x200 piksel.</span>
                                        </div>

                                        <div id="uploadProgress" class="hidden mt-3">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div id="progressBar" class="h-full bg-[#882426] rounded-full transition-all duration-300" style="width: 0%"></div>
                                                </div>
                                                <span id="progressText" class="text-xs font-medium text-gray-600">0%</span>
                                            </div>
                                        </div>

                                        <div id="uploadSuccess" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                                            <div class="flex items-center gap-2 text-green-700">
                                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                                <span class="text-sm font-medium">Foto berhasil diunggah!</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">badge</span>
                                        <input type="text" name="nama" id="inputNama" value="<?= $nama ?>"
                                            class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                            placeholder="Masukkan nama lengkap" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">mail</span>
                                        <input type="email" name="email" id="inputEmail" value="<?= $email ?>"
                                            class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                            placeholder="email@contoh.com" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">phone</span>
                                        <input type="tel" name="no_telepon" id="inputTelepon" value="<?= $noTelepon ?>"
                                            class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                            placeholder="+62 812 3456 7890">
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                <div class="flex gap-3">
                                    <span class="material-symbols-outlined text-blue-600 flex-shrink-0">info</span>
                                    <div>
                                        <p class="font-medium text-blue-800">Kelola Alamat Pengiriman</p>
                                        <p class="text-sm text-blue-600 mt-1">Untuk mengatur alamat pengiriman, silakan kunjungi tab <a href="#" onclick="document.querySelector('[data-tab=address]').click(); return false;" class="font-semibold underline hover:text-blue-800">Alamat</a>.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                                <button type="button" id="resetProfileBtn" class="w-full sm:w-auto px-6 py-3 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all order-2 sm:order-1">
                                    Batal
                                </button>
                                <button type="submit" id="saveProfileBtn" class="w-full sm:w-auto px-8 py-3 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6a1c1e] transition-all flex items-center justify-center gap-2 order-1 sm:order-2">
                                    <span class="material-symbols-outlined text-xl">save</span>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="tab-address" class="tab-content hidden">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Alamat Tersimpan</h3>
                                <p class="text-gray-500 text-sm mt-1.5">Kelola alamat pengiriman Anda</p>
                            </div>
                            <button id="addAddressBtn" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6a1c1e] transition-all">
                                <span class="material-symbols-outlined text-xl">add</span>
                                Tambah Alamat
                            </button>
                        </div>

                        <div class="p-6">
                            <div id="addressList" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="col-span-full flex items-center justify-center py-12">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#882426]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-security" class="tab-content hidden">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Keamanan Akun</h3>
                            <p class="text-gray-500 text-sm mt-1.5">Jaga keamanan akun Anda dengan pengaturan keamanan yang tepat</p>
                        </div>

                        <div class="p-6">
                            <?php if ($isGoogleUser): ?>
                                <div class="mb-6 p-5 bg-blue-50 border border-blue-100 rounded-xl">
                                    <div class="flex gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-sm flex-shrink-0">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-blue-900">Terhubung dengan Google</p>
                                            <p class="text-sm text-blue-700 mt-1">
                                                Anda login menggunakan akun Google <strong><?= htmlspecialchars($googleEmail ?? $email) ?></strong>.
                                                <?php if ($hasLocalPassword): ?>
                                                    Anda juga memiliki password lokal yang bisa digunakan untuk login dengan email.
                                                <?php else: ?>
                                                    Anda bisa menambahkan password lokal untuk login dengan email selain menggunakan Google.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($isGoogleUser && !$hasLocalPassword): ?>
                                <div class="space-y-6">

                                    <div class="mb-6">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-amber-600">key</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">Tambah Password Lokal</h4>
                                                <p class="text-sm text-gray-500">Login alternatif dengan email dan password</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="setPasswordForm" data-is-google-user="true" data-customer-id="<?= $customerId ?>">
                                        <div class="space-y-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Password Baru <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">lock</span>
                                                    <input type="password" name="new_password" id="newPassword"
                                                        class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                                        placeholder="Masukkan password baru" required minlength="8">
                                                    <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                        <span class="material-symbols-outlined">visibility_off</span>
                                                    </button>
                                                </div>
                                                <div id="passwordStrength" class="mt-3 hidden">
                                                    <div class="flex gap-1.5 mb-2">
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500" id="strengthText">Kekuatan password</p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Konfirmasi Password <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">lock</span>
                                                    <input type="password" name="confirm_password" id="confirmPassword"
                                                        class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                                        placeholder="Ulangi password baru" required>
                                                    <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                        <span class="material-symbols-outlined">visibility_off</span>
                                                    </button>
                                                </div>
                                                <p id="passwordMatch" class="text-sm mt-2 hidden"></p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                                            <button type="submit" id="setPasswordBtn" class="px-8 py-3 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6a1c1e] transition-all flex items-center gap-2">
                                                <span class="material-symbols-outlined text-xl">add</span>
                                                Tambah Password
                                            </button>
                                        </div>
                                    </form>

                                    <div class="mt-8 p-5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-xl">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-amber-600">tips_and_updates</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-amber-800">Tips Keamanan</h4>
                                                <p class="text-sm text-amber-700">Buat password yang kuat dan aman</p>
                                            </div>
                                        </div>
                                        <!-- Layout: 3 card atas, 2 card bawah (centered) -->
                                        <div class="space-y-4">
                                            <!-- Baris pertama: 3 card -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-green-600 text-lg">check</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Min 8 karakter</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Lebih panjang lebih aman</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-blue-600 text-lg">text_format</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Huruf besar & kecil</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Kombinasi A-Z dan a-z</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-purple-600 text-lg">123</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Angka & simbol</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Tambahkan 0-9, !@#$</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Baris kedua: 2 card (centered) -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-red-600 text-lg">block</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Hindari info pribadi</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Nama, tanggal lahir</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-amber-600 text-lg">schedule</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Ganti berkala</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Setiap 3 bulan</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="space-y-6">
                                    <div class="mb-6">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-[#882426]/10 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-[#882426]">password</span>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900">Ubah Password</h4>
                                                <p class="text-sm text-gray-500">Perbarui password akun Anda secara berkala</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form id="passwordForm" data-customer-id="<?= $customerId ?>">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Password Saat Ini <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">lock</span>
                                                    <input type="password" name="current_password" id="currentPassword"
                                                        class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                                        placeholder="Masukkan password saat ini" required>
                                                    <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                        <span class="material-symbols-outlined">visibility_off</span>
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Password Baru <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">lock</span>
                                                    <input type="password" name="new_password" id="newPassword"
                                                        class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                                        placeholder="Masukkan password baru" required minlength="8">
                                                    <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                        <span class="material-symbols-outlined">visibility_off</span>
                                                    </button>
                                                </div>
                                                <div id="passwordStrength" class="mt-3 hidden">
                                                    <div class="flex gap-1.5 mb-2">
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                        <div class="h-1.5 flex-1 rounded-full bg-gray-200 strength-bar"></div>
                                                    </div>
                                                    <p class="text-xs text-gray-500" id="strengthText">Kekuatan password</p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                                                </label>
                                                <div class="relative">
                                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">lock</span>
                                                    <input type="password" name="confirm_password" id="confirmPassword"
                                                        class="w-full pl-12 pr-12 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                                        placeholder="Ulangi password baru" required>
                                                    <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                                        <span class="material-symbols-outlined">visibility_off</span>
                                                    </button>
                                                </div>
                                                <p id="passwordMatch" class="text-sm mt-2 hidden"></p>
                                            </div>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                                            <button type="button" id="resetPasswordBtn" class="w-full sm:w-auto px-6 py-3 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all order-2 sm:order-1">
                                                Batal
                                            </button>
                                            <button type="submit" id="savePasswordBtn" class="w-full sm:w-auto px-8 py-3 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6a1c1e] transition-all flex items-center justify-center gap-2 order-1 sm:order-2">
                                                <span class="material-symbols-outlined text-xl">lock</span>
                                                Ubah Password
                                            </button>
                                        </div>
                                    </form>

                                    <div class="mt-8 p-5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-xl">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-amber-600">tips_and_updates</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-amber-800">Tips Keamanan</h4>
                                                <p class="text-sm text-amber-700">Buat password yang kuat dan aman</p>
                                            </div>
                                        </div>
                                        <!-- Layout: 3 card atas, 2 card bawah (centered) -->
                                        <div class="space-y-4">
                                            <!-- Baris pertama: 3 card -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-green-600 text-lg">check</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Min 8 karakter</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Lebih panjang lebih aman</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-blue-600 text-lg">text_format</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Huruf besar & kecil</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Kombinasi A-Z dan a-z</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-purple-600 text-lg">123</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Angka & simbol</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Tambahkan 0-9, !@#$</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Baris kedua: 2 card (centered) -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl mx-auto">
                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-red-600 text-lg">block</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Hindari info pribadi</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Nama, tanggal lahir</p>
                                                    </div>
                                                </div>

                                                <div class="flex items-start gap-3 p-4 bg-white/60 rounded-xl">
                                                    <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-amber-600 text-lg">schedule</span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">Ganti berkala</p>
                                                        <p class="text-xs text-gray-500 mt-0.5">Setiap 3 bulan</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="tab-notification" class="tab-content hidden">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Pengaturan Notifikasi</h3>
                            <p class="text-gray-500 text-sm mt-1.5">Kelola preferensi notifikasi Anda</p>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-blue-600">mail</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Notifikasi Email</p>
                                            <p class="text-sm text-gray-500 mt-0.5">Terima update via email</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" checked>
                                        <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#882426]"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-green-600">local_shipping</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Update Pesanan</p>
                                            <p class="text-sm text-gray-500 mt-0.5">Notifikasi status pengiriman</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" checked>
                                        <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#882426]"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-purple-600">sell</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Promo & Diskon</p>
                                            <p class="text-sm text-gray-500 mt-0.5">Info promo dan penawaran khusus</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#882426]"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-red-500">favorite</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Wishlist Updates</p>
                                            <p class="text-sm text-gray-500 mt-0.5">Notifikasi harga produk wishlist</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" checked>
                                        <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#882426]"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                <div class="flex gap-3">
                                    <span class="material-symbols-outlined text-blue-600 flex-shrink-0">info</span>
                                    <div>
                                        <p class="font-medium text-blue-800">Fitur dalam pengembangan</p>
                                        <p class="text-sm text-blue-600 mt-1">Pengaturan notifikasi akan segera dapat disimpan ke database.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="tab-preference" class="tab-content hidden">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-6 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Preferensi</h3>
                            <p class="text-gray-500 text-sm mt-1.5">Sesuaikan pengalaman berbelanja Anda</p>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bahasa</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">translate</span>
                                        <select class="w-full pl-12 pr-10 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all appearance-none cursor-pointer">
                                            <option value="id" selected>Bahasa Indonesia</option>
                                            <option value="en">English</option>
                                        </select>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 pointer-events-none">expand_more</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mata Uang</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400">payments</span>
                                        <select class="w-full pl-12 pr-10 py-3.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all appearance-none cursor-pointer">
                                            <option value="IDR" selected>IDR - Rupiah Indonesia</option>
                                            <option value="USD">USD - US Dollar</option>
                                        </select>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-gray-400 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500">palette</span>
                                    Tampilan
                                </h4>
                                <div class="flex items-center justify-between p-5 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-yellow-400">dark_mode</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Mode Gelap</p>
                                            <p class="text-sm text-gray-500 mt-0.5">Tampilan gelap untuk kenyamanan mata</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer" id="darkModeToggle">
                                        <div class="w-12 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#882426]"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-100">
                                <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gray-500">settings</span>
                                    Aksi Akun
                                </h4>
                                <div class="space-y-3">
                                    <button id="deactivateAccountBtn" class="w-full flex items-center justify-between p-5 bg-red-50 rounded-xl hover:bg-red-100 transition-colors group">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-red-100 group-hover:bg-red-200 rounded-xl flex items-center justify-center transition-colors">
                                                <span class="material-symbols-outlined text-red-600">block</span>
                                            </div>
                                            <div class="text-left">
                                                <p class="font-semibold text-red-700">Nonaktifkan Akun</p>
                                                <p class="text-sm text-red-600 mt-0.5">Menonaktifkan akun Anda sementara</p>
                                            </div>
                                        </div>
                                        <span class="material-symbols-outlined text-red-400 group-hover:text-red-600 transition-colors">chevron_right</span>
                                    </button>
                                </div>

                                <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                                    <div class="flex gap-3">
                                        <span class="material-symbols-outlined text-amber-600 flex-shrink-0">warning</span>
                                        <div>
                                            <p class="font-medium text-amber-800">Perhatian</p>
                                            <p class="text-sm text-amber-700 mt-1">Jika akun dinonaktifkan, Anda tidak akan bisa login sampai akun diaktifkan kembali oleh tim support.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../components/users/loginRequiredModal.php'; ?>
    <?php include '../../components/users/footer.php'; ?>

    <div id="toast" class="fixed bottom-4 right-4 z-50 transform translate-y-full opacity-0 transition-all duration-300">
        <div class="flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border bg-white" id="toastContent">
            <span class="material-symbols-outlined text-xl" id="toastIcon">check_circle</span>
            <span id="toastMessage" class="font-medium"></span>
        </div>
    </div>

    <div id="logoutModal" class="logout-modal-overlay">
        <div class="logout-modal-content">
            <div class="logout-modal-icon">
                <span class="material-symbols-outlined">logout</span>
            </div>
            <h2 class="logout-modal-title">Keluar dari Akun?</h2>
            <p class="logout-modal-description">Anda akan keluar dari akun Anda. Pastikan semua aktivitas Anda sudah selesai sebelum melanjutkan.</p>
            <div class="logout-modal-buttons">
                <button onclick="closeLogoutModal()" class="logout-modal-btn logout-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button onclick="confirmLogout()" id="confirmLogoutBtn" class="logout-modal-btn logout-modal-btn-logout">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </div>
        </div>
    </div>

    <style>
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
        }

        .logout-modal-overlay.show {
            display: flex;
            animation: logoutFadeIn 0.3s ease-out;
        }

        @keyframes logoutFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes logoutSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes logoutIconPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .logout-modal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: logoutSlideUp 0.4s ease-out;
        }

        .logout-modal-icon {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: logoutIconPulse 2s ease-in-out infinite;
        }

        .logout-modal-icon .material-symbols-outlined {
            font-size: 2.5rem;
            color: #dc2626;
        }

        .logout-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .logout-modal-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .logout-modal-buttons {
            display: flex;
            gap: 1rem;
        }

        .logout-modal-btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .logout-modal-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .logout-modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .logout-modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .logout-modal-btn-logout {
            background: #dc2626;
            color: white;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        .logout-modal-btn-logout:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .logout-modal-btn:active {
            transform: scale(0.98);
        }
    </style>

    <div id="addressModal" class="address-modal-overlay">
        <div class="address-modal-content">
            <div class="address-modal-header">
                <div class="address-modal-header-icon">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <div>
                    <h3 id="addressModalTitle" class="address-modal-title text-gray-600">Tambah Alamat Baru</h3>
                    <p class="address-modal-subtitle text-gray-600">Lengkapi informasi alamat pengiriman Anda</p>
                </div>
                <button onclick="closeAddressModal()" class="address-modal-close">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="addressForm" class="address-modal-form">
                <div class="address-modal-body">
                    <input type="hidden" id="addressId" name="id_alamat">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Label Alamat</label>
                            <select name="label_alamat" id="labelAlamat" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all">
                                <option value="Rumah">Rumah</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Apartemen">Apartemen</option>
                                <option value="Kos">Kos</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Penerima <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_penerima" id="namaPenerima" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                    placeholder="Nama lengkap penerima">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor HP <span class="text-red-500">*</span></label>
                                <input type="tel" name="nomor_hp" id="nomorHp" required
                                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat_lengkap" id="alamatLengkap" rows="3" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all resize-none"
                                placeholder="Nama jalan, nomor rumah, RT/RW"></textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi <span class="text-red-500">*</span></label>
                                <select name="provinsi" id="provinsi" required class="select2-wilayah w-full">
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="provinsi_name" id="provinsiName">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                <select name="kota" id="kota" required disabled class="select2-wilayah w-full">
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                                <input type="hidden" name="kota_name" id="kotaName">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kecamatan <span class="text-red-500">*</span></label>
                                <select name="kecamatan" id="kecamatan" required disabled class="select2-wilayah w-full">
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="kecamatan_name" id="kecamatanName">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kelurahan/Desa <span class="text-red-500">*</span></label>
                                <select name="kelurahan" id="kelurahan" required disabled class="select2-wilayah w-full">
                                    <option value="">Pilih Kelurahan/Desa</option>
                                </select>
                                <input type="hidden" name="kelurahan_name" id="kelurahanName">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos <span class="text-red-500">*</span></label>
                            <input type="text" name="kode_pos" id="kodePos" required maxlength="5"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] outline-none transition-all"
                                placeholder="12345">
                        </div>
                        <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-[#882426]/5 to-[#882426]/10 rounded-xl border border-[#882426]">
                            <input type="checkbox" name="default_alamat" id="defaultAlamat" class="w-5 h-5 text-[#882426] border-gray-300 rounded focus:ring-[#882426]">
                            <label for="defaultAlamat" class="text-sm font-medium text-gray-700">Jadikan alamat utama</label>
                        </div>
                    </div>
                </div>
                <div class="address-modal-footer">
                    <button type="button" onclick="closeAddressModal()" class="address-modal-btn address-modal-btn-cancel">
                        <span class="material-symbols-outlined">close</span>
                        Batal
                    </button>
                    <button type="submit" id="saveAddressBtn" class="address-modal-btn address-modal-btn-save">
                        <span class="material-symbols-outlined">save</span>
                        <span id="saveAddressBtnText">Simpan Alamat</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .address-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
        }

        .address-modal-overlay.show {
            display: flex;
            animation: addressModalFadeIn 0.3s ease-out;
        }

        @keyframes addressModalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes addressModalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .address-modal-content {
            background: white;
            border-radius: 1.5rem;
            max-width: 640px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: addressModalSlideUp 0.4s ease-out;
            overflow: hidden;
        }

        .address-modal-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .address-modal-header-icon {
            width: 48px;
            height: 48px;
            background: #882426;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .address-modal-header-icon .material-symbols-outlined {
            font-size: 1.75rem;
            /* color: white; */
        }

        .address-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }

        .address-modal-subtitle {
            font-size: 0.875rem;
            opacity: 0.8;
            margin: 0;
        }

        .address-modal-close {
            margin-left: auto;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .address-modal-close:hover {
            background: rgba(0, 0, 0, 0.05);
            ;
        }

        .address-modal-close .material-symbols-outlined {
            font-size: 1.5rem;
            color: #6b7280;
        }

        .address-modal-form {
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .address-modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
        }

        .address-modal-footer {
            display: flex;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .address-modal-btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .address-modal-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .address-modal-btn-cancel {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .address-modal-btn-cancel:hover {
            background: #f3f4f6;
        }

        .address-modal-btn-save {
            background: #882426;
            color: white;
        }

        .address-modal-btn-save:hover {
            background: #6a1c1e;
            transform: translateY(-1px);
        }

        .address-modal-btn:active {
            transform: scale(0.98);
        }

        .address-modal-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 640px) {
            .address-modal-content {
                max-height: 95vh;
                margin: 0.5rem;
            }

            .address-modal-header {
                padding: 1rem;
            }

            .address-modal-body {
                padding: 1rem;
            }

            .address-modal-footer {
                flex-direction: column;
                padding: 1rem;
            }
        }
    </style>

    <div id="deactivateModal" class="modern-modal-overlay">
        <div class="modern-modal-content" style="max-width: 480px;">
            <div class="modern-modal-icon modern-modal-icon-danger">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <h2 class="modern-modal-title">Nonaktifkan Akun?</h2>
            <p class="modern-modal-description">Tindakan ini akan menonaktifkan akun Anda. Anda tidak akan bisa login sampai akun diaktifkan kembali oleh tim support.</p>

            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-left">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-red-600 flex-shrink-0 text-xl">info</span>
                    <div class="text-sm">
                        <p class="font-semibold text-red-700 mb-2">Yang akan terjadi:</p>
                        <ul class="space-y-1.5 text-red-600">
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></span>
                                <span>Anda akan keluar dari akun</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></span>
                                <span>Tidak bisa login sampai diaktifkan</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></span>
                                <span>Hubungi support untuk mengaktifkan kembali</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6 text-left">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Ketik <span class="text-red-600 font-bold">NONAKTIFKAN</span> untuk konfirmasi
                </label>
                <input type="text" id="deactivateConfirmInput"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white outline-none transition-all"
                    placeholder="Ketik NONAKTIFKAN">
            </div>

            <div class="modern-modal-buttons">
                <button onclick="closeDeactivateModal()" class="modern-modal-btn modern-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button onclick="confirmDeactivate()" id="confirmDeactivateBtn" class="modern-modal-btn modern-modal-btn-danger" disabled>
                    <span class="material-symbols-outlined">block</span>
                    Nonaktifkan
                </button>
            </div>
        </div>
    </div>

    <!-- Address Detail Modal -->
    <div id="addressDetailModal" class="address-modal-overlay">
        <div class="address-modal-content" style="max-width: 500px;">
            <div class="address-modal-header">
                <div class="address-modal-header-icon" id="detailLabelIcon">
                    <span class="material-symbols-outlined">location_on</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 id="detailLabelText" class="address-modal-title text-gray-600">Detail Alamat</h3>
                        <span id="detailDefaultBadge" class="px-2 py-0.5 bg-[#882426] text-white text-xs font-bold rounded hidden">UTAMA</span>
                    </div>
                    <p class="address-modal-subtitle text-gray-600">Informasi lengkap alamat pengiriman</p>
                </div>
                <button onclick="closeAddressDetailModal()" class="address-modal-close">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="address-modal-body p-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nama Penerima</p>
                            <p id="detailNamaPenerima" class="text-sm font-semibold text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nomor HP</p>
                            <p id="detailNomorHp" class="text-sm font-semibold text-gray-900">-</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Alamat Lengkap</p>
                        <p id="detailAlamatLengkap" class="text-sm text-gray-800 leading-relaxed">-</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-gray-100">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kelurahan/Desa</p>
                            <p id="detailKelurahan" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kecamatan</p>
                            <p id="detailKecamatan" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kota/Kabupaten</p>
                            <p id="detailKota" class="text-sm text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Provinsi</p>
                            <p id="detailProvinsi" class="text-sm text-gray-800">-</p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kode Pos</p>
                        <p id="detailKodePos" class="text-sm font-semibold text-gray-900">-</p>
                    </div>
                </div>
            </div>
            <div class="address-modal-footer flex gap-3 p-4 border-t border-gray-100">
                <button onclick="closeAddressDetailModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-base">close</span>
                    Tutup
                </button>
                <button id="detailEditBtn" class="flex-1 px-4 py-2.5 bg-[#882426] text-white rounded-xl font-medium hover:bg-[#6a1c1e] transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-base">edit</span>
                    Edit Alamat
                </button>
            </div>
        </div>
    </div>

    <div id="deleteAddressModal" class="modern-modal-overlay">
        <div class="modern-modal-content">
            <div class="modern-modal-icon modern-modal-icon-danger">
                <span class="material-symbols-outlined">location_off</span>
            </div>
            <h2 class="modern-modal-title">Hapus Alamat?</h2>
            <p class="modern-modal-description">Alamat pengiriman ini akan dihapus secara permanen dari akun Anda dan tidak dapat dikembalikan.</p>
            <input type="hidden" id="deleteAddressId">
            <div class="modern-modal-buttons">
                <button onclick="closeDeleteAddressModal()" class="modern-modal-btn modern-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button onclick="confirmDeleteAddress()" id="confirmDeleteAddressBtn" class="modern-modal-btn modern-modal-btn-danger">
                    <span class="material-symbols-outlined">delete</span>
                    Hapus Alamat
                </button>
            </div>
        </div>
    </div>

    <div id="deletePhotoModal" class="modern-modal-overlay">
        <div class="modern-modal-content">
            <div class="modern-modal-icon modern-modal-icon-warning">
                <span class="material-symbols-outlined">photo_camera</span>
            </div>
            <h2 class="modern-modal-title">Hapus Foto Profil?</h2>
            <p class="modern-modal-description">Foto profil Anda akan dihapus dan diganti dengan avatar inisial. Anda dapat mengunggah foto baru kapan saja.</p>
            <div class="modern-modal-buttons">
                <button onclick="closeDeletePhotoModal()" class="modern-modal-btn modern-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button onclick="confirmDeletePhoto()" id="confirmDeletePhotoBtn" class="modern-modal-btn modern-modal-btn-warning">
                    <span class="material-symbols-outlined">delete</span>
                    Hapus Foto
                </button>
            </div>
        </div>
    </div>

    <style>
        .modern-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
        }

        .modern-modal-overlay.show {
            display: flex;
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes modalIconPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .modern-modal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            animation: modalSlideUp 0.4s ease-out;
        }

        .modern-modal-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: modalIconPulse 2s ease-in-out infinite;
        }

        .modern-modal-icon .material-symbols-outlined {
            font-size: 2.5rem;
        }

        .modern-modal-icon-danger {
            background: #fee2e2;
        }

        .modern-modal-icon-danger .material-symbols-outlined {
            color: #dc2626;
        }

        .modern-modal-icon-warning {
            background: #fef3c7;
        }

        .modern-modal-icon-warning .material-symbols-outlined {
            color: #d97706;
        }

        .modern-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .modern-modal-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .modern-modal-buttons {
            display: flex;
            gap: 1rem;
        }

        .modern-modal-btn {
            flex: 1;
            padding: 0.775rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .modern-modal-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .modern-modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .modern-modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .modern-modal-btn-danger {
            background: #dc2626;
            color: white;
        }

        .modern-modal-btn-danger:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        .modern-modal-btn-warning {
            background: #d97706;
            color: white;
        }

        .modern-modal-btn-warning:hover {
            background: #b45309;
            transform: translateY(-1px);
        }

        .modern-modal-btn:active {
            transform: scale(0.98);
        }

        .modern-modal-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 480px) {
            .modern-modal-content {
                padding: 1.5rem;
                margin: 1rem;
            }

            .modern-modal-icon {
                width: 64px;
                height: 64px;
            }

            .modern-modal-icon .material-symbols-outlined {
                font-size: 2rem;
            }

            .modern-modal-title {
                font-size: 1.25rem;
            }

            .modern-modal-buttons {
                flex-direction: column;
            }
        }
    </style>

    <style>
        .select2-container--default .select2-selection--single {
            height: 48px !important;
            padding: 8px 12px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            background-color: #fff !important;
            font-size: 0.95rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #374151 !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #882426 !important;
            box-shadow: 0 0 0 3px rgba(136, 36, 38, 0.1) !important;
            outline: none !important;
        }

        .select2-container--default.select2-container--disabled .select2-selection--single {
            background-color: #f3f4f6 !important;
            cursor: not-allowed !important;
        }

        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            margin-top: 4px !important;
            z-index: 999999 !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 8px 12px !important;
            font-size: 0.9rem !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #882426 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(136, 36, 38, 0.1) !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #882426 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: rgba(136, 36, 38, 0.1) !important;
            color: #882426 !important;
        }

        .select2-results__option {
            padding: 10px 12px !important;
            font-size: 0.9rem !important;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-results__option--disabled {
            color: #9ca3af !important;
        }

        .wilayah-loading {
            position: relative;
        }

        .wilayah-loading::after {
            content: '';
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #882426;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        #customToastContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .custom-toast {
            position: relative;
            min-width: 320px;
            max-width: 400px;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: auto;
        }

        .custom-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .custom-toast.hiding {
            transform: translateX(120%);
            opacity: 0;
            margin-top: -70px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), margin-top 0.3s ease 0.2s;
        }

        .custom-toast.success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
        }

        .custom-toast.error {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
        }

        .custom-toast.warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: white;
        }

        .custom-toast.info {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
        }

        .custom-toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .custom-toast-content {
            flex: 1;
        }

        .custom-toast-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .custom-toast-message {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .custom-toast-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .custom-toast-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .custom-toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 0 0 12px 12px;
            animation: toast-progress 4s linear forwards;
        }

        @keyframes toast-progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        @media (max-width: 480px) {
            #customToastContainer {
                left: 10px;
                right: 10px;
                top: 10px;
            }

            .custom-toast {
                min-width: unset;
                max-width: unset;
                width: 100%;
            }
        }
    </style>

    <div id="customToastContainer"></div>

    <script>
        window.showCustomToast = function(message, type = 'success', title = null, duration = 4000) {
            const container = document.getElementById('customToastContainer');
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}`;

            const icons = {
                success: 'check_circle',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };
            const titles = {
                success: 'Berhasil!',
                error: 'Gagal!',
                warning: 'Perhatian!',
                info: 'Informasi'
            };

            toast.innerHTML = `
            <div class="custom-toast-icon">
                <span class="material-symbols-outlined">${icons[type]}</span>
            </div>
            <div class="custom-toast-content">
                <div class="custom-toast-title">${title || titles[type]}</div>
                <div class="custom-toast-message">${message}</div>
            </div>
            <button class="custom-toast-close">
                <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
            </button>
            <div class="custom-toast-progress" style="animation-duration: ${duration}ms;"></div>
        `;

            const closeBtn = toast.querySelector('.custom-toast-close');
            closeBtn.addEventListener('click', () => removeToast(toast));

            container.appendChild(toast);

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.add('show');
                });
            });

            const timeoutId = setTimeout(() => removeToast(toast), duration);
            toast.dataset.timeoutId = timeoutId;

            function removeToast(toastElement) {
                if (toastElement.classList.contains('hiding')) return;

                clearTimeout(parseInt(toastElement.dataset.timeoutId));
                toastElement.classList.add('hiding');
                toastElement.classList.remove('show');

                setTimeout(() => {
                    if (toastElement.parentNode) {
                        toastElement.remove();
                    }
                }, 500);
            }

            return toast;
        };

        $(document).ready(function() {
            const API_BASE = 'https://www.emsifa.com/api-wilayah-indonesia/api';
            let provincesLoaded = false;
            let select2Initialized = false;

            function initSelect2Wilayah() {
                if (select2Initialized) return;

                try {
                    if ($.fn.select2) {
                        $('#provinsi').select2({
                            placeholder: 'Pilih Provinsi',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addressModal .address-modal-content')
                        });

                        $('#kota').select2({
                            placeholder: 'Pilih Kota/Kabupaten',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addressModal .address-modal-content')
                        });

                        $('#kecamatan').select2({
                            placeholder: 'Pilih Kecamatan',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addressModal .address-modal-content')
                        });

                        $('#kelurahan').select2({
                            placeholder: 'Pilih Kelurahan/Desa',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addressModal .address-modal-content')
                        });

                        select2Initialized = true;
                        console.log('Select2 initialized successfully');
                    }
                } catch (e) {
                    console.error('Error initializing Select2:', e);
                }
            }

            function loadProvinces() {
                if (provincesLoaded) return Promise.resolve();

                const $provinsi = $('#provinsi');
                $provinsi.parent().addClass('wilayah-loading');
                $provinsi.html('<option value="">Memuat provinsi...</option>');

                return fetch(`${API_BASE}/provinces.json`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        let options = '<option value="">Pilih Provinsi</option>';
                        data.forEach(prov => {
                            options += `<option value="${prov.id}" data-name="${prov.name}">${prov.name}</option>`;
                        });
                        $provinsi.html(options).prop('disabled', false);
                        if (select2Initialized) {
                            $provinsi.trigger('change.select2');
                        }
                        provincesLoaded = true;
                        $provinsi.parent().removeClass('wilayah-loading');
                        console.log('Provinces loaded:', data.length);
                    })
                    .catch(error => {
                        console.error('Error loading provinces:', error);
                        $provinsi.html('<option value="">Gagal memuat provinsi - Coba lagi</option>');
                        $provinsi.parent().removeClass('wilayah-loading');
                    });
            }

            function loadKota(provinsiId) {
                const $kota = $('#kota');
                resetKecamatan();
                resetKelurahan();

                if (!provinsiId || provinsiId.startsWith('manual_')) {
                    resetKota();
                    return Promise.resolve();
                }

                $kota.parent().addClass('wilayah-loading');
                $kota.html('<option value="">Memuat...</option>').prop('disabled', true);

                return fetch(`${API_BASE}/regencies/${provinsiId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Pilih Kota/Kabupaten</option>';
                        data.forEach(kota => {
                            options += `<option value="${kota.id}" data-name="${kota.name}">${kota.name}</option>`;
                        });
                        $kota.html(options).prop('disabled', false);
                        if (select2Initialized) {
                            $kota.trigger('change.select2');
                        }
                        $kota.parent().removeClass('wilayah-loading');
                        console.log('Kota loaded:', data.length);
                    })
                    .catch(error => {
                        console.error('Error loading regencies:', error);
                        $kota.html('<option value="">Gagal memuat</option>');
                        $kota.parent().removeClass('wilayah-loading');
                    });
            }

            function loadKecamatan(kotaId) {
                const $kecamatan = $('#kecamatan');
                resetKelurahan();

                if (!kotaId || kotaId.startsWith('manual_')) {
                    resetKecamatan();
                    return Promise.resolve();
                }

                $kecamatan.parent().addClass('wilayah-loading');
                $kecamatan.html('<option value="">Memuat...</option>').prop('disabled', true);

                return fetch(`${API_BASE}/districts/${kotaId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Pilih Kecamatan</option>';
                        data.forEach(kec => {
                            options += `<option value="${kec.id}" data-name="${kec.name}">${kec.name}</option>`;
                        });
                        $kecamatan.html(options).prop('disabled', false);
                        if (select2Initialized) {
                            $kecamatan.trigger('change.select2');
                        }
                        $kecamatan.parent().removeClass('wilayah-loading');
                        console.log('Kecamatan loaded:', data.length);
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                        $kecamatan.html('<option value="">Gagal memuat</option>');
                        $kecamatan.parent().removeClass('wilayah-loading');
                    });
            }

            function loadKelurahan(kecamatanId) {
                const $kelurahan = $('#kelurahan');

                if (!kecamatanId || kecamatanId.startsWith('manual_')) {
                    resetKelurahan();
                    return Promise.resolve();
                }

                $kelurahan.parent().addClass('wilayah-loading');
                $kelurahan.html('<option value="">Memuat...</option>').prop('disabled', true);

                return fetch(`${API_BASE}/villages/${kecamatanId}.json`)
                    .then(response => response.json())
                    .then(data => {
                        let options = '<option value="">Pilih Kelurahan/Desa</option>';
                        data.forEach(kel => {
                            options += `<option value="${kel.id}" data-name="${kel.name}">${kel.name}</option>`;
                        });
                        $kelurahan.html(options).prop('disabled', false);
                        if (select2Initialized) {
                            $kelurahan.trigger('change.select2');
                        }
                        $kelurahan.parent().removeClass('wilayah-loading');
                        console.log('Kelurahan loaded:', data.length);
                    })
                    .catch(error => {
                        console.error('Error loading villages:', error);
                        $kelurahan.html('<option value="">Gagal memuat</option>');
                        $kelurahan.parent().removeClass('wilayah-loading');
                    });
            }

            function resetKota() {
                $('#kota').html('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled', true);
                if (select2Initialized) $('#kota').trigger('change.select2');
                $('#kotaName').val('');
            }

            function resetKecamatan() {
                $('#kecamatan').html('<option value="">Pilih Kecamatan</option>').prop('disabled', true);
                if (select2Initialized) $('#kecamatan').trigger('change.select2');
                $('#kecamatanName').val('');
            }

            function resetKelurahan() {
                $('#kelurahan').html('<option value="">Pilih Kelurahan/Desa</option>').prop('disabled', true);
                if (select2Initialized) $('#kelurahan').trigger('change.select2');
                $('#kelurahanName').val('');
            }

            function resetAllWilayah() {
                $('#provinsi').val('');
                if (select2Initialized) $('#provinsi').trigger('change.select2');
                $('#provinsiName').val('');
                resetKota();
                resetKecamatan();
                resetKelurahan();
            }

            $('#provinsi').on('change', function() {
                const $selected = $(this).find('option:selected');
                const id = $(this).val();
                const name = $selected.data('name') || $selected.text();
                if (id && name && name !== 'Pilih Provinsi') {
                    $('#provinsiName').val(name);
                }
                loadKota(id);
            });

            $('#kota').on('change', function() {
                const $selected = $(this).find('option:selected');
                const id = $(this).val();
                const name = $selected.data('name') || $selected.text();
                if (id && name && name !== 'Pilih Kota/Kabupaten') {
                    $('#kotaName').val(name);
                }
                loadKecamatan(id);
            });

            $('#kecamatan').on('change', function() {
                const $selected = $(this).find('option:selected');
                const id = $(this).val();
                const name = $selected.data('name') || $selected.text();
                if (id && name && name !== 'Pilih Kecamatan') {
                    $('#kecamatanName').val(name);
                }
                loadKelurahan(id);
            });

            $('#kelurahan').on('change', function() {
                const $selected = $(this).find('option:selected');
                const name = $selected.data('name') || $selected.text();
                if (name && name !== 'Pilih Kelurahan/Desa') {
                    $('#kelurahanName').val(name);
                }
            });

            window.initAddressWilayah = function() {
                initSelect2Wilayah();
                return loadProvinces();
            };

            window.resetAddressFormWilayah = resetAllWilayah;

            window.setAddressFormWilayahForEdit = function(addressData) {
                console.log('setAddressFormWilayahForEdit called with:', addressData);
                initSelect2Wilayah();

                provincesLoaded = false;

                loadProvinces().then(() => {
                    console.log('Provinces loaded, now finding:', addressData.provinsi);
                    if (addressData.provinsi) {
                        const $provinsi = $('#provinsi');
                        let provOption = $provinsi.find('option').filter(function() {
                            return $(this).data('name') === addressData.provinsi || $(this).text() === addressData.provinsi;
                        });

                        console.log('Found province options:', $provinsi.find('option').length);
                        if (provOption.length) {
                            console.log('Province matched:', provOption.val(), provOption.text());
                            $provinsi.val(provOption.val());
                            if (select2Initialized) $provinsi.trigger('change.select2');
                            $('#provinsiName').val(addressData.provinsi);

                            if (addressData.kota) {
                                loadKota(provOption.val()).then(() => {
                                    const $kota = $('#kota');
                                    console.log('Kota options loaded:', $kota.find('option').length);
                                    let kotaOption = $kota.find('option').filter(function() {
                                        return $(this).data('name') === addressData.kota || $(this).text() === addressData.kota;
                                    });

                                    if (kotaOption.length) {
                                        console.log('Kota matched:', kotaOption.val(), kotaOption.text());
                                        $kota.val(kotaOption.val());
                                        if (select2Initialized) $kota.trigger('change.select2');
                                        $('#kotaName').val(addressData.kota);

                                        if (addressData.kecamatan) {
                                            loadKecamatan(kotaOption.val()).then(() => {
                                                const $kecamatan = $('#kecamatan');
                                                console.log('Kecamatan options loaded:', $kecamatan.find('option').length);
                                                let kecOption = $kecamatan.find('option').filter(function() {
                                                    return $(this).data('name') === addressData.kecamatan || $(this).text() === addressData.kecamatan;
                                                });

                                                if (kecOption.length) {
                                                    console.log('Kecamatan matched:', kecOption.val(), kecOption.text());
                                                    $kecamatan.val(kecOption.val());
                                                    if (select2Initialized) $kecamatan.trigger('change.select2');
                                                    $('#kecamatanName').val(addressData.kecamatan);

                                                    if (addressData.kelurahan) {
                                                        loadKelurahan(kecOption.val()).then(() => {
                                                            const $kelurahan = $('#kelurahan');
                                                            console.log('Kelurahan options loaded:', $kelurahan.find('option').length);
                                                            let kelOption = $kelurahan.find('option').filter(function() {
                                                                return $(this).data('name') === addressData.kelurahan || $(this).text() === addressData.kelurahan;
                                                            });

                                                            if (kelOption.length) {
                                                                console.log('Kelurahan matched:', kelOption.val(), kelOption.text());
                                                                $kelurahan.val(kelOption.val());
                                                                if (select2Initialized) $kelurahan.trigger('change.select2');
                                                                $('#kelurahanName').val(addressData.kelurahan);
                                                            } else {
                                                                console.log('Kelurahan not found in options:', addressData.kelurahan);
                                                                $('#kelurahanName').val(addressData.kelurahan);
                                                            }
                                                        });
                                                    }
                                                } else {
                                                    console.log('Kecamatan not found in options:', addressData.kecamatan);
                                                    $('#kecamatanName').val(addressData.kecamatan);
                                                }
                                            });
                                        }
                                    } else {
                                        console.log('Kota not found in options:', addressData.kota);
                                        $('#kotaName').val(addressData.kota);
                                    }
                                });
                            }
                        } else {
                            console.log('Province not found in options:', addressData.provinsi);
                            $('#provinsiName').val(addressData.provinsi);
                            $('#kotaName').val(addressData.kota || '');
                            $('#kecamatanName').val(addressData.kecamatan || '');
                            $('#kelurahanName').val(addressData.kelurahan || '');
                        }
                    }
                });
            };

            window.getWilayahFormData = function() {
                const getValidName = function(hiddenId, selectId, placeholder) {
                    const hiddenVal = $(hiddenId).val();
                    if (hiddenVal && hiddenVal.trim() !== '') {
                        return hiddenVal;
                    }
                    const $selected = $(selectId + ' option:selected');
                    const dataName = $selected.data('name');
                    if (dataName && dataName.trim() !== '') {
                        return dataName;
                    }
                    const text = $selected.text();
                    if (text && text !== placeholder && !text.includes('Pilih') && !text.includes('Memuat')) {
                        return text;
                    }
                    return '';
                };

                return {
                    provinsi: getValidName('#provinsiName', '#provinsi', 'Pilih Provinsi'),
                    kota: getValidName('#kotaName', '#kota', 'Pilih Kota/Kabupaten'),
                    kecamatan: getValidName('#kecamatanName', '#kecamatan', 'Pilih Kecamatan'),
                    kelurahan: getValidName('#kelurahanName', '#kelurahan', 'Pilih Kelurahan/Desa')
                };
            };

            setTimeout(function() {
                initSelect2Wilayah();
                loadProvinces();
            }, 500);
        });
    </script>

    <script src="../../assets/js/users/usersSetting.js"></script>
</body>

</html>