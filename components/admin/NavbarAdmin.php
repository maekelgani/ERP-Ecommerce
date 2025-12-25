<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\SessionManager;
use App\Auth\AdminRepository;
use App\Repository\NotificationRepository;

$currentAdmin = SessionManager::getCurrentAdmin();

// Jika admin tidak login, redirect ke login page
if (!$currentAdmin) {
    header('Location: ../../view/login-admin.php');
    exit;
}

// Ambil data admin lengkap dengan foto dari database
$adminRepo = new AdminRepository();
$adminData = $adminRepo->getAdminWithPhoto($currentAdmin['id_admin']);

// Logika ini memastikan foto_url sudah valid sebelum dikirim ke frontend
$adminPhoto = $adminData['photo_url'] ?? '../../assets/img/profil/default-profil.png';
$adminName = $adminData['nama_lengkap'] ?? 'Admin';
// Gunakan role_name dari session yang sudah di-fetch dari database saat login
$adminRole = $currentAdmin['role_name'] ?? 'admin';

// Validasi akhir: Jika foto tidak ada di server, gunakan default
if (!file_exists(__DIR__ . '../../' . ltrim($adminPhoto, '/'))) {
    $adminPhoto = '../../assets/img/profil/default-profil.png';
}

// Ambil data notifikasi untuk badge
$notificationRepo = new NotificationRepository();
$unreadCount = $notificationRepo->getUnreadCount();
?>

<header class="sticky top-0 z-30 bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 shadow-sm">
    <nav class="flex items-center justify-between px-4 md:px-6 py-2 h-full">
        <!-- Left Section - Burger Menu -->
        <div class="flex items-center gap-4">
            <!-- Burger Button for Desktop Collapse -->
            <button
                id="toggleSidebarBtn"
                class="hidden lg:block p-2 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Toggle sidebar">
                <span class="material-symbols-outlined text-gray-700">menu</span>
            </button>

            <!-- Burger Button for Mobile Drawer -->
            <button
                id="openDrawerBtn"
                class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Open menu">
                <span class="material-symbols-outlined text-gray-700">menu</span>
            </button>

            <div class="hidden md:block">
                <h1 class="text-xl font-semibold text-gray-800">
                    Selamat Datang <span class="text-[#882426]"><?= htmlspecialchars($adminName); ?></span>
                </h1>
                <p class="text-sm text-gray-500">Kelola data dan kontrol sistem dari sini</p>
            </div>
        </div>

        <!-- Center Section - Time Display -->
        <!-- <div class="hidden md:flex flex-col items-end">
            <div class="text-lg font-semibold text-gray-800" id="currentTime">--:--:--</div>
            <div class="text-xs text-gray-400" id="currentDate">-- -- ----</div>
        </div> -->

        <!-- Right Section: Icons and Profile -->
        <nav class="flex items-center gap-4 navbar-right">
            <div class="hidden md:flex flex-col items-end">
                <div class="text-lg font-semibold text-gray-800" id="currentTime">--:--:--</div>
                <div class="text-xs text-gray-400" id="currentDate">-- -- ----</div>
            </div>

            <!-- Notification Button -->
            <div class="relative">
                <button class="icon-button" id="notification-btn" title="Notifikasi">
                    <span class="material-symbols-outlined">notifications_active</span>
                    <span class="notification-badge" id="notification-badge" <?= $unreadCount === 0 ? 'class="notification-badge hidden"' : '' ?>>
                        <?= $unreadCount > 99 ? '99+' : $unreadCount; ?>
                    </span>
                </button>
                <!-- Notification Dropdown -->
                <div id="notification-dropdown" class="dropdown-menu hidden">
                    <div class="px-4 py-3 font-semibold text-sm border-b border-gray-200 flex justify-between items-center">
                        <span>Notifikasi <span id="notif-count">(<?= $unreadCount; ?>)</span></span>
                        <?php if ($unreadCount > 0): ?>
                            <button id="mark-all-as-read-btn" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Tandai Dibaca
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="max-h-64 overflow-y-auto" id="notifications-container">
                        <!-- Notification items akan dimuat via JavaScript -->
                        <div class="px-4 py-8 text-center text-gray-500">
                            <span class="material-symbols-outlined block text-3xl mb-2">notifications_none</span>
                            <p class="text-sm">Memuat notifikasi...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Button -->
            <div class="relative">
                <button class="icon-button" id="settings-btn" title="Pengaturan">
                    <span class="material-symbols-outlined">settings</span>
                </button>
                <!-- Settings Dropdown -->
                <div id="settings-dropdown" class="dropdown-menu hidden">
                    <div class="px-4 py-3 font-semibold text-sm border-b border-gray-200">
                        Pengaturan
                    </div>
                    <a href="../../view/admin/WebManagement.php" class="dropdown-item">
                        <span class="material-symbols-outlined">tune</span>
                        <span>Pengaturan Website</span>
                    </a>
                    <a href="../../view/admin/WebManagement.php" class="dropdown-item">
                        <span class="material-symbols-outlined">palette</span>
                        <span>Preferensi Tampilan</span>
                    </a>
                </div>
            </div>

            <!-- Profile Section with Photo -->
            <div class="relative">
                <button class="icon-button" id="profile-btn" title="<?= htmlspecialchars($adminName); ?>">
                    <img
                        src="<?= htmlspecialchars($adminPhoto); ?>"
                        alt="<?= htmlspecialchars($adminName); ?>"
                        class="profile-photo" />
                </button>

                <!-- Profile Dropdown -->
                <div id="profile-dropdown" class="dropdown-menu hidden">
                    <!-- Header dengan nama, email, dan role -->
                    <div class="dropdown-header">
                        <div class="dropdown-header-name"><?= htmlspecialchars($adminName); ?></div>
                        <div class="dropdown-header-email"><?= htmlspecialchars($currentAdmin['email']); ?></div>
                        <span class="role-badge <?= htmlspecialchars($adminRole); ?> mt-2 inline-block">
                            <?= ucfirst(str_replace('_', ' ', $adminRole)); ?>
                        </span>
                    </div>

                    <!-- Menu Items -->
                    <a href="../../view/admin/ProfileAdmin.php" class="dropdown-item">
                        <span class="material-symbols-outlined">visibility</span>
                        <span>Lihat Profil</span>
                    </a>
                    <a href="../../view/admin/ProfileEditAdmin.php" class="dropdown-item">
                        <span class="material-symbols-outlined">edit</span>
                        <span>Edit Profil</span>
                    </a>
                    <a href="../../view/admin/ProfilePasswordAdmin.php" class="dropdown-item">
                        <span class="material-symbols-outlined">lock</span>
                        <span>Ubah Password</span>
                    </a>
                    <button id="logout-btn" class="dropdown-item logout">
                        <span class="material-symbols-outlined">logout</span>
                        <span>Keluar</span>
                    </button>
                </div>
            </div>
        </nav>
    </nav>
</header>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-icon">
            <span class="material-symbols-outlined" style="font-size: 3.5rem;">logout</span>
        </div>
        <h2 class="modal-title">Keluar dari Sistem?</h2>
        <p class="modal-description">Anda akan keluar dari akun admin. Pastikan semua pekerjaan Anda sudah tersimpan dengan baik sebelum melanjutkan.</p>
        <div class="modal-buttons">
            <button id="cancelLogoutBtn" class="modal-btn modal-btn-cancel">
                <span class="material-symbols-outlined" style="font-size: 1rem;">close</span>
                Batal
            </button>
            <button id="confirmLogoutBtn" class="modal-btn modal-btn-logout">
                <span class="material-symbols-outlined" style="font-size: 1rem;">logout</span>
                Keluar
            </button>
        </div>
    </div>
</div>

<script src="../../assets/js/notifications.js"></script>
<script>
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        const timeElement = document.getElementById('currentTime');
        const dateElement = document.getElementById('currentDate');
        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    }
    setInterval(updateTime, 1000);
    updateTime();

    document.addEventListener('DOMContentLoaded', function() {
        const settingsBtn = document.getElementById('settings-btn');
        const settingsDropdown = document.getElementById('settings-dropdown');
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        function closeAllDropdowns() {
            if (settingsDropdown) settingsDropdown.classList.add('hidden');
            if (profileDropdown) profileDropdown.classList.add('hidden');
            // Note: Notification dropdown is handled by NotificationManager class
        }

        function toggleDropdown(dropdown) {
            const isHidden = dropdown.classList.contains('hidden');
            closeAllDropdowns();
            if (isHidden) {
                dropdown.classList.remove('hidden');
            }
        }

        if (settingsBtn && settingsDropdown) {
            settingsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown(settingsDropdown);
            });
        }

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown(profileDropdown);
            });
        }

        document.addEventListener('click', function(e) {
            const isClickInsideDropdown = e.target.closest('.dropdown-menu');
            const isClickInsideNotification = e.target.closest('[id*="notification"]');
            if (!isClickInsideDropdown && !isClickInsideNotification) {
                closeAllDropdowns();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });

        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

        if (logoutBtn && logoutModal) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                logoutModal.classList.add('show');
                if (profileDropdown) profileDropdown.classList.add('hidden');
            });
        }

        if (cancelLogoutBtn && logoutModal) {
            cancelLogoutBtn.addEventListener('click', function() {
                logoutModal.classList.remove('show');
            });
        }

        if (confirmLogoutBtn) {
            confirmLogoutBtn.addEventListener('click', function() {
                window.location.href = '../../app/handlers/LogoutHandler.php';
            });
        }

        if (logoutModal) {
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) {
                    logoutModal.classList.remove('show');
                }
            });
        }
    });
</script>