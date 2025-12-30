<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Auth\AdminRepository;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Profil Admin";
include '../../components/admin/head.php';

$currentAdmin = SessionManager::getCurrentAdmin();
$adminRepo = new AdminRepository();
$adminData = $adminRepo->getAdminWithPhoto($currentAdmin['id_admin']);

$adminPhoto = $adminData['photo_url'] ?? '../../assets/img/profil/default-profil.png';
$adminName = $adminData['nama_lengkap'] ?? 'Admin';
$adminEmail = $adminData['email'] ?? '';
$adminUsername = $adminData['username'] ?? '-';
$adminPhone = $adminData['phone'] ?? '-';
$adminRole = $adminData['role'] ?? 'admin';
$adminIsActive = $adminData['is_active'] ?? true;
$adminLastLogin = $adminData['last_login'] ?? null;
$adminCreatedAt = $adminData['created_at'] ?? null;
$adminUpdatedAt = $adminData['updated_at'] ?? null;
$passwordUpdatedAt = $adminData['password_updated_at'] ?? null;

function formatDate($date)
{
    if (empty($date)) return '-';
    return date('d M Y, H:i', strtotime($date));
}

function getRoleBadgeClass($role)
{
    switch ($role) {
        case 'super_admin':
            return 'bg-gradient-to-r from-red-500 to-rose-600 text-white';
        case 'admin':
            return 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white';
        case 'moderator':
            return 'bg-gradient-to-r from-green-500 to-emerald-600 text-white';
        default:
            return 'bg-gradient-to-r from-gray-500 to-gray-600 text-white';
    }
}

function getRoleLabel($role)
{
    switch ($role) {
        case 'super_admin':
            return 'Super Admin';
        case 'admin':
            return 'Admin';
        case 'moderator':
            return 'Moderator';
        default:
            return ucfirst($role);
    }
}
?>

<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 min-h-screen flex">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[100] flex flex-col gap-3"></div>

    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            <div class="mb-8">
                <nav class="flex items-center text-sm text-gray-500 mb-3">
                    <a href="DashboardAdmin.php" class="hover:text-[#882426] transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">home</span>
                        Dashboard
                    </a>
                    <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
                    <span class="text-gray-800 font-medium">Profil Admin</span>
                </nav>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                            <span class="w-10 h-10 bg-gradient-to-br from-[#882426] to-[#a83236] rounded-xl flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-white">account_circle</span>
                            </span>
                            Profil Admin
                        </h1>
                        <p class="text-gray-500 mt-1 ml-13">Kelola informasi akun administrator Anda</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 lg:gap-8">
                <div class="xl:col-span-4">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden sticky top-24">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#882426] via-[#a83236] to-[#6d1a1c]"></div>
                            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\" 60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"30\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>

                            <div class="relative p-8 pb-24">
                                <div class="flex justify-center">
                                    <div class="relative group cursor-pointer" onclick="openLightbox('<?= htmlspecialchars($adminPhoto); ?>')">
                                        <div class="absolute -inset-1 bg-white/30 rounded-full blur-sm group-hover:bg-white/40 transition-all"></div>
                                        <img src="<?= htmlspecialchars($adminPhoto); ?>"
                                            alt="<?= htmlspecialchars($adminName); ?>"
                                            class="relative w-32 h-32 rounded-full object-cover border-4 border-white shadow-2xl transition-transform group-hover:scale-105"
                                            id="profile-photo-preview">
                                        <div class="absolute inset-0 rounded-full bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                            <span class="material-symbols-outlined text-white text-4xl drop-shadow-lg">zoom_in</span>
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-8 h-8 bg-green-500 rounded-full border-4 border-white shadow-lg flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white text-sm">check</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative -mt-16 px-6 pb-6">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
                                <h2 class="text-xl font-bold text-gray-800 mb-1"><?= htmlspecialchars($adminName); ?></h2>
                                <p class="text-gray-500 text-sm mb-3"><?= htmlspecialchars($adminEmail); ?></p>
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold shadow-md <?= getRoleBadgeClass($adminRole); ?>">
                                    <span class="material-symbols-outlined text-sm">
                                        <?= $adminRole === 'super_admin' ? 'shield' : 'verified_user'; ?>
                                    </span>
                                    <?= getRoleLabel($adminRole); ?>
                                </span>
                            </div>

                            <div class="mt-6 space-y-3">
                                <a href="ProfileEditAdmin.php"
                                    class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-[#882426] to-[#a83236] hover:from-[#6d1a1c] hover:to-[#882426] text-white font-semibold py-3.5 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <span class="material-symbols-outlined">edit</span>
                                    Edit Profil
                                </a>
                                <a href="ProfilePasswordAdmin.php"
                                    class="flex items-center justify-center gap-2 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3.5 px-4 rounded-xl transition-all hover:shadow-md">
                                    <span class="material-symbols-outlined">lock</span>
                                    Ubah Password
                                </a>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 p-6">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426] text-lg">verified_user</span>
                                Status Akun
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 text-sm">Status</span>
                                    <?php if ($adminIsActive): ?>
                                        <span class="flex items-center gap-2 text-green-600 bg-green-100 px-3 py-1.5 rounded-full text-sm font-semibold">
                                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                            Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="flex items-center gap-2 text-red-600 bg-red-100 px-3 py-1.5 rounded-full text-sm font-semibold">
                                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                            Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                    <span class="text-gray-600 text-sm">Login Terakhir</span>
                                    <span class="text-gray-800 text-sm font-medium"><?= formatDate($adminLastLogin); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                                <span class="w-8 h-8 bg-[#882426] rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">person</span>
                                </span>
                                Informasi Pribadi
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-blue-600">badge</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nama Lengkap</label>
                                            <p class="text-gray-800 font-semibold text-lg mt-1"><?= htmlspecialchars($adminName); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-green-600">email</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Email</label>
                                            <p class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($adminEmail); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-purple-600">alternate_email</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Username</label>
                                            <p class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($adminUsername); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-orange-600">phone</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nomor Telepon</label>
                                            <p class="text-gray-800 font-semibold mt-1"><?= htmlspecialchars($adminPhone); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-red-600">shield</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Role</label>
                                            <p class="mt-2">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold shadow <?= getRoleBadgeClass($adminRole); ?>">
                                                    <?= getRoleLabel($adminRole); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="material-symbols-outlined text-gray-600">tag</span>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">ID Admin</label>
                                            <p class="text-gray-800 font-semibold mt-1 font-mono">#<?= htmlspecialchars($adminData['id_admin']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                                <span class="w-8 h-8 bg-[#882426] rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">schedule</span>
                                </span>
                                Riwayat Akun
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-xl border border-blue-100">
                                    <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center shadow-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-white">calendar_today</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Akun Dibuat</p>
                                        <p class="text-gray-800 font-bold mt-0.5"><?= formatDate($adminCreatedAt); ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-green-50 to-green-100/50 rounded-xl border border-green-100">
                                    <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center shadow-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-white">update</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-green-600 font-semibold uppercase tracking-wider">Terakhir Diperbarui</p>
                                        <p class="text-gray-800 font-bold mt-0.5"><?= formatDate($adminUpdatedAt); ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-purple-50 to-purple-100/50 rounded-xl border border-purple-100">
                                    <div class="w-12 h-12 rounded-xl bg-purple-500 flex items-center justify-center shadow-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-white">login</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-purple-600 font-semibold uppercase tracking-wider">Login Terakhir</p>
                                        <p class="text-gray-800 font-bold mt-0.5"><?= formatDate($adminLastLogin); ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-amber-50 to-amber-100/50 rounded-xl border border-amber-100">
                                    <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shadow-lg flex-shrink-0">
                                        <span class="material-symbols-outlined text-white">key</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-amber-600 font-semibold uppercase tracking-wider">Password Diubah</p>
                                        <p class="text-gray-800 font-bold mt-0.5"><?= formatDate($passwordUpdatedAt); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($adminRole !== 'super_admin'): ?>
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shadow flex-shrink-0">
                                    <span class="material-symbols-outlined text-white">info</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-amber-800 text-lg">Informasi Hak Akses</h4>
                                    <p class="text-sm text-amber-700 mt-1 leading-relaxed">
                                        Sebagai Admin biasa, Anda hanya dapat mengelola profil dan password pribadi Anda.
                                        Hubungi Super Admin jika memerlukan perubahan role atau akses tambahan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10 bg-black/50 rounded-full p-2 hover:bg-black/70">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-2xl shadow-2xl">
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-center">
            <p class="text-white font-semibold text-lg drop-shadow-lg"><?= htmlspecialchars($adminName); ?></p>
            <p class="text-white/70 text-sm"><?= getRoleLabel($adminRole); ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        // Toast Notification System with Circular Progress
        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const id = 'toast-' + Date.now();
            toast.id = id;
            const colors = {
                success: {
                    bg: 'bg-white',
                    border: 'border-emerald-200',
                    icon: 'check_circle',
                    iconBg: 'bg-emerald-500',
                    iconColor: 'text-white',
                    title: 'text-emerald-800',
                    progressCircle: '#10b981'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-red-200',
                    icon: 'error',
                    iconBg: 'bg-red-500',
                    iconColor: 'text-white',
                    title: 'text-red-800',
                    progressCircle: '#ef4444'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    icon: 'warning',
                    iconBg: 'bg-amber-500',
                    iconColor: 'text-white',
                    title: 'text-amber-800',
                    progressCircle: '#f59e0b'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-blue-200',
                    icon: 'info',
                    iconBg: 'bg-blue-500',
                    iconColor: 'text-white',
                    title: 'text-blue-800',
                    progressCircle: '#3b82f6'
                }
            };
            const c = colors[type] || colors.info;
            toast.className = `${c.bg} border ${c.border} rounded-xl shadow-2xl overflow-hidden min-w-[320px] max-w-[400px] toast-enter`;
            toast.innerHTML = `
                <div class="p-4 flex items-start gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 ${c.iconBg} rounded-full flex items-center justify-center ${c.iconColor} shadow-lg">
                            <span class="material-symbols-outlined">${c.icon}</span>
                        </div>
                        <svg class="absolute -top-1 -left-1 w-12 h-12 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" stroke="#e5e7eb" stroke-width="2.5"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" stroke="${c.progressCircle}" stroke-width="2.5" stroke-dasharray="100" stroke-dashoffset="0" stroke-linecap="round" class="circular-progress" style="animation-duration: ${duration}ms;"></circle>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold ${c.title}">${title}</p>
                        <p class="text-sm text-gray-600 mt-0.5">${message}</p>
                    </div>
                    <button onclick="removeToast('${id}')" class="flex-shrink-0 w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>`;
            container.appendChild(toast);
            setTimeout(() => removeToast(id), duration);
        }

        function removeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }
        window.showToast = showToast;

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('updated') === 'success') {
                window.history.replaceState({}, document.title, window.location.pathname);
                showToast('success', 'Berhasil!', 'Profil berhasil diperbarui', 4000);
            }

            if (urlParams.get('password') === 'changed') {
                window.history.replaceState({}, document.title, window.location.pathname);
                showToast('success', 'Berhasil!', 'Password berhasil diubah', 4000);
            }
        });
    </script>

    <style>
        #lightbox {
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #lightbox img {
            animation: scaleIn 0.3s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Toast Animation Styles */
        @keyframes circularProgress {
            from {
                stroke-dashoffset: 0;
            }

            to {
                stroke-dashoffset: 100;
            }
        }

        .circular-progress {
            animation: circularProgress linear forwards;
        }

        @keyframes toastEnter {
            from {
                opacity: 0;
                transform: translateX(100%) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastExit {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }

            to {
                opacity: 0;
                transform: translateX(100%) scale(0.9);
            }
        }

        .toast-enter {
            animation: toastEnter 0.4s cubic-bezier(0.21, 1.02, 0.73, 1) forwards;
        }

        .toast-exit {
            animation: toastExit 0.3s cubic-bezier(0.06, 0.71, 0.55, 1) forwards;
        }
    </style>
</body>

</html>