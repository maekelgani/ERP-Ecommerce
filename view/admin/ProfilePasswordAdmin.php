<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Auth\AdminRepository;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Ubah Password";
include '../../components/admin/head.php';

$currentAdmin = SessionManager::getCurrentAdmin();
$adminRepo = new AdminRepository();
$adminData = $adminRepo->getAdminWithPhoto($currentAdmin['id_admin']);

$adminPhoto = $adminData['photo_url'] ?? '../../assets/img/profil/default-profil.png';
$adminName = $adminData['nama_lengkap'] ?? 'Admin';
$adminRole = $adminData['role'] ?? 'admin';
$passwordUpdatedAt = $adminData['password_updated_at'] ?? null;

function formatDate($date)
{
    if (empty($date)) return 'Belum pernah diubah';
    return date('d M Y, H:i', strtotime($date));
}

function getRoleBadgeClass($role)
{
    switch ($role) {
        case 'super_admin':
            return 'bg-gradient-to-r from-red-500 to-rose-600 text-white';
        case 'admin':
            return 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white';
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
        default:
            return ucfirst($role);
    }
}
?>

<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 min-h-screen flex">
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
                    <a href="ProfileAdmin.php" class="hover:text-[#882426] transition-colors">Profil</a>
                    <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
                    <span class="text-gray-800 font-medium">Ubah Password</span>
                </nav>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                            <span class="w-10 h-10 bg-gradient-to-br from-[#882426] to-[#a83236] rounded-xl flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-white">lock</span>
                            </span>
                            Ubah Password
                        </h1>
                        <p class="text-gray-500 mt-1 ml-13">Perbarui password akun Anda untuk keamanan</p>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-[#882426] to-[#a83236] p-6">
                                <div class="flex flex-col items-center">
                                    <img src="<?= htmlspecialchars($adminPhoto); ?>"
                                        alt="<?= htmlspecialchars($adminName); ?>"
                                        class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                                    <h3 class="mt-3 font-bold text-white text-lg"><?= htmlspecialchars($adminName); ?></h3>
                                    <span class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                                        <?= getRoleLabel($adminRole); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="p-5">
                                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-100">
                                    <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shadow flex-shrink-0">
                                        <span class="material-symbols-outlined text-white">key</span>
                                    </div>
                                    <div>
                                        <p class="text-xs text-amber-600 font-semibold uppercase tracking-wider">Password Terakhir Diubah</p>
                                        <p class="text-gray-800 font-bold mt-0.5"><?= formatDate($passwordUpdatedAt); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 shadow-lg">
                            <h4 class="font-bold text-blue-800 flex items-center gap-2 text-lg">
                                <span class="material-symbols-outlined">lightbulb</span>
                                Tips Password Aman
                            </h4>
                            <ul class="mt-4 space-y-3 text-sm text-blue-700">
                                <li class="flex items-start gap-3 p-2 bg-white/50 rounded-lg">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-white text-sm">check</span>
                                    </span>
                                    <span>Minimal 8 karakter</span>
                                </li>
                                <li class="flex items-start gap-3 p-2 bg-white/50 rounded-lg">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-white text-sm">check</span>
                                    </span>
                                    <span>Kombinasi huruf besar, kecil, dan angka</span>
                                </li>
                                <li class="flex items-start gap-3 p-2 bg-white/50 rounded-lg">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-white text-sm">check</span>
                                    </span>
                                    <span>Jangan gunakan data pribadi</span>
                                </li>
                                <li class="flex items-start gap-3 p-2 bg-white/50 rounded-lg">
                                    <span class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-white text-sm">check</span>
                                    </span>
                                    <span>Ubah password secara berkala</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                                    <span class="w-8 h-8 bg-[#882426] rounded-lg flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white text-sm">lock_reset</span>
                                    </span>
                                    Form Ubah Password
                                </h3>
                            </div>

                            <form id="changePasswordForm" class="p-6">
                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                            <span class="material-symbols-outlined text-[#882426] text-lg">lock</span>
                                            Password Lama <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <input type="password"
                                                name="current_password"
                                                id="currentPassword"
                                                required
                                                class="w-full px-4 py-3.5 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                                placeholder="Masukkan password lama">
                                            <button type="button" onclick="togglePassword('currentPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                                <span class="material-symbols-outlined" id="currentPasswordIcon">visibility</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                            <span class="material-symbols-outlined text-[#882426] text-lg">key</span>
                                            Password Baru <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <input type="password"
                                                name="new_password"
                                                id="newPassword"
                                                required
                                                minlength="8"
                                                class="w-full px-4 py-3.5 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                                placeholder="Masukkan password baru"
                                                oninput="checkPasswordStrength(this.value)">
                                            <button type="button" onclick="togglePassword('newPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                                <span class="material-symbols-outlined" id="newPasswordIcon">visibility</span>
                                            </button>
                                        </div>
                                        <div class="mt-3 p-3 bg-gray-50 rounded-xl">
                                            <div class="flex gap-1.5 mb-2">
                                                <div id="strength-1" class="h-2 flex-1 rounded-full bg-gray-200 transition-all"></div>
                                                <div id="strength-2" class="h-2 flex-1 rounded-full bg-gray-200 transition-all"></div>
                                                <div id="strength-3" class="h-2 flex-1 rounded-full bg-gray-200 transition-all"></div>
                                                <div id="strength-4" class="h-2 flex-1 rounded-full bg-gray-200 transition-all"></div>
                                            </div>
                                            <p id="strengthText" class="text-xs text-gray-500 font-medium">Masukkan password baru</p>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                            <span class="material-symbols-outlined text-[#882426] text-lg">verified</span>
                                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative group">
                                            <input type="password"
                                                name="confirm_password"
                                                id="confirmPassword"
                                                required
                                                class="w-full px-4 py-3.5 pr-12 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                                placeholder="Konfirmasi password baru"
                                                oninput="checkPasswordMatch()">
                                            <button type="button" onclick="togglePassword('confirmPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                                <span class="material-symbols-outlined" id="confirmPasswordIcon">visibility</span>
                                            </button>
                                        </div>
                                        <p id="matchText" class="text-xs mt-2 font-medium hidden"></p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-4 justify-end mt-8 pt-6 border-t border-gray-100">
                                    <a href="ProfileAdmin.php"
                                        class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3.5 px-8 rounded-xl transition-all hover:shadow-md">
                                        <span class="material-symbols-outlined">close</span>
                                        Batal
                                    </a>
                                    <button type="submit"
                                        id="submitBtn"
                                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-[#882426] to-[#a83236] hover:from-[#6d1a1c] hover:to-[#882426] text-white font-semibold py-3.5 px-8 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <span class="material-symbols-outlined">lock_reset</span>
                                        Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-5 mt-6 shadow-lg">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center shadow flex-shrink-0">
                                    <span class="material-symbols-outlined text-white">warning</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-amber-800 text-lg">Perhatian</h4>
                                    <p class="text-sm text-amber-700 mt-1 leading-relaxed">
                                        Setelah mengubah password, Anda tidak akan di-logout dari sesi saat ini.
                                        Pastikan untuk mengingat password baru Anda.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + 'Icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        function checkPasswordStrength(password) {
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            const colors = {
                0: 'bg-gray-200',
                1: 'bg-red-500',
                2: 'bg-orange-500',
                3: 'bg-yellow-500',
                4: 'bg-green-500'
            };

            const texts = {
                0: {
                    text: 'Masukkan password baru',
                    color: 'text-gray-500'
                },
                1: {
                    text: 'Sangat Lemah',
                    color: 'text-red-600'
                },
                2: {
                    text: 'Lemah',
                    color: 'text-orange-600'
                },
                3: {
                    text: 'Cukup Kuat',
                    color: 'text-yellow-600'
                },
                4: {
                    text: 'Kuat',
                    color: 'text-green-600'
                }
            };

            const level = Math.min(4, strength);

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('strength-' + i);
                bar.className = 'h-2 flex-1 rounded-full transition-all ' + (i <= level ? colors[level] : 'bg-gray-200');
            }

            const strengthText = document.getElementById('strengthText');
            strengthText.textContent = texts[level].text;
            strengthText.className = 'text-xs font-medium ' + texts[level].color;
        }

        function checkPasswordMatch() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const matchText = document.getElementById('matchText');

            if (confirmPassword.length === 0) {
                matchText.classList.add('hidden');
                return;
            }

            matchText.classList.remove('hidden');

            if (newPassword === confirmPassword) {
                matchText.innerHTML = '<span class="material-symbols-outlined text-sm align-middle mr-1">check_circle</span>Password cocok';
                matchText.className = 'text-xs mt-2 font-medium text-green-600 flex items-center';
            } else {
                matchText.innerHTML = '<span class="material-symbols-outlined text-sm align-middle mr-1">cancel</span>Password tidak cocok';
                matchText.className = 'text-xs mt-2 font-medium text-red-600 flex items-center';
            }
        }

        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Tidak Cocok',
                    text: 'Konfirmasi password tidak sesuai dengan password baru',
                    confirmButtonColor: '#882426'
                });
                return;
            }

            if (newPassword.length < 8) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Terlalu Pendek',
                    text: 'Password minimal 8 karakter',
                    confirmButtonColor: '#882426'
                });
                return;
            }

            if (!/[a-zA-Z]/.test(newPassword) || !/[0-9]/.test(newPassword)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Tidak Valid',
                    text: 'Password harus mengandung huruf dan angka',
                    confirmButtonColor: '#882426'
                });
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Memproses...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('current_password', currentPassword);
                formData.append('new_password', newPassword);
                formData.append('confirm_password', confirmPassword);

                const baseUrl = window.location.origin;
                const url = '../../app/controllers/adminProfileController.php?action=change_password';

                console.log('Changing password - URL:', url);

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    if (response.status === 401) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Sesi Berakhir',
                            text: 'Silakan login kembali',
                            confirmButtonColor: '#882426'
                        }).then(() => {
                            window.location.href = '<?= $_SERVER['REQUEST_SCHEME']; ?>://<?= $_SERVER['HTTP_HOST']; ?>/view/login-admin.php';
                        });
                        return;
                    }
                    console.error('Response status:', response.status);
                    const text = await response.text();
                    console.error('Response body:', text);
                    throw new Error('HTTP error! status: ' + response.status);
                }

                const text = await response.text();
                console.log('Response:', text);

                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('Failed to parse JSON. Response text:', text);
                    throw new Error('Server error: Invalid response format');
                }

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        confirmButtonColor: '#882426',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = 'ProfileAdmin.php?password=changed';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: result.message || 'Terjadi kesalahan yang tidak diketahui',
                        confirmButtonColor: '#882426'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: error.message || 'Gagal menghubungi server. Silakan coba lagi.',
                    confirmButtonColor: '#882426'
                });
            } finally {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            }
        });
    </script>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</body>

</html>