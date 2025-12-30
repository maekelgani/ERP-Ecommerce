<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Auth\SessionManager;
use App\Auth\AdminRepository;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Edit Profil Admin";
include '../../components/admin/head.php';

$currentAdmin = SessionManager::getCurrentAdmin();
$adminRepo = new AdminRepository();
$adminData = $adminRepo->getAdminWithPhoto($currentAdmin['id_admin']);

$adminPhoto = $adminData['photo_url'] ?? '../../assets/img/profil/default-profil.png';
$adminName = $adminData['nama_lengkap'] ?? '';
$adminEmail = $adminData['email'] ?? '';
$adminUsername = $adminData['username'] ?? '';
$adminPhone = $adminData['phone'] ?? '';
$adminRole = $adminData['role'] ?? 'admin';

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
                    <a href="ProfileAdmin.php" class="hover:text-[#882426] transition-colors">Profil</a>
                    <span class="material-symbols-outlined text-gray-400 mx-2 text-sm">chevron_right</span>
                    <span class="text-gray-800 font-medium">Edit Profil</span>
                </nav>
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 flex items-center gap-3">
                            <span class="w-10 h-10 bg-gradient-to-br from-[#882426] to-[#a83236] rounded-xl flex items-center justify-center shadow-lg">
                                <span class="material-symbols-outlined text-white">edit</span>
                            </span>
                            Edit Profil
                        </h1>
                        <p class="text-gray-500 mt-1 ml-13">Perbarui informasi akun Anda</p>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <form id="editProfileForm" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="update_profile">

                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-3">
                                <span class="w-8 h-8 bg-[#882426] rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-white text-sm">photo_camera</span>
                                </span>
                                Foto Profil
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="flex flex-col md:flex-row items-center gap-8">
                                <div class="relative group">
                                    <div class="absolute -inset-2 bg-gradient-to-r from-[#882426] to-[#a83236] rounded-full opacity-20 blur-lg group-hover:opacity-30 transition-opacity"></div>
                                    <div class="relative">
                                        <img src="<?= htmlspecialchars($adminPhoto); ?>"
                                            alt="Preview"
                                            class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-2xl"
                                            id="photoPreview">
                                        <label for="photoInput"
                                            class="absolute inset-0 rounded-full bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                            <span class="material-symbols-outlined text-white text-4xl mb-1">add_a_photo</span>
                                            <span class="text-white text-xs font-medium">Ganti Foto</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex-1 text-center md:text-left">
                                    <input type="file"
                                        name="photo"
                                        id="photoInput"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        onchange="previewPhoto(this)">
                                    <label for="photoInput"
                                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#882426] to-[#a83236] hover:from-[#6d1a1c] hover:to-[#882426] text-white font-semibold py-3 px-6 rounded-xl cursor-pointer transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                        <span class="material-symbols-outlined">upload</span>
                                        Pilih Foto Baru
                                    </label>
                                    <p class="text-sm text-gray-500 mt-4 flex items-center justify-center md:justify-start gap-2">
                                        <span class="material-symbols-outlined text-gray-400 text-lg">info</span>
                                        Format: JPG, PNG, WebP. Maksimal 5MB
                                    </p>
                                    <p id="photoFileName" class="text-sm text-green-600 mt-2 hidden flex items-center justify-center md:justify-start gap-2">
                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                        <span></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">badge</span>
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="text"
                                            name="nama_lengkap"
                                            value="<?= htmlspecialchars($adminName); ?>"
                                            required
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                            placeholder="Masukkan nama lengkap">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">email</span>
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <input type="email"
                                            name="email"
                                            value="<?= htmlspecialchars($adminEmail); ?>"
                                            required
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                            placeholder="Masukkan email">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">alternate_email</span>
                                        Username
                                    </label>
                                    <div class="relative group">
                                        <input type="text"
                                            name="username"
                                            value="<?= htmlspecialchars($adminUsername); ?>"
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                            placeholder="Masukkan username (opsional)">
                                    </div>
                                    <p class="text-xs text-gray-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">info</span>
                                        Huruf, angka, underscore. 3-30 karakter
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">phone</span>
                                        Nomor Telepon
                                    </label>
                                    <div class="relative group">
                                        <input type="tel"
                                            name="phone"
                                            value="<?= htmlspecialchars($adminPhone); ?>"
                                            class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all outline-none bg-gray-50 focus:bg-white"
                                            placeholder="Contoh: 08123456789">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-gray-100 to-gray-50 rounded-2xl p-6 border border-gray-200" id="roleEditSection">
                        <div class="flex items-start gap-4 mb-4">
                            <div class="w-10 h-10 bg-gray-300 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-gray-600" id="roleEditIcon">lock</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-700" id="roleEditTitle">Informasi Tidak Dapat Diubah</h3>
                                <p class="text-sm text-gray-500" id="roleEditDesc">Data berikut hanya dapat diubah oleh Super Admin</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm" id="roleContainer">
                                <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">Role</label>
                                <div class="mt-1" id="roleDisplay">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold shadow <?= getRoleBadgeClass($adminRole); ?>">
                                        <?= getRoleLabel($adminRole); ?>
                                    </span>
                                </div>
                                <select name="role" id="roleSelect" class="hidden w-full px-3 py-2 mt-1 border border-gray-200 rounded-lg focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/20 outline-none">
                                    <option value="admin" <?= $adminRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="super_admin" <?= $adminRole === 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                <label class="text-xs text-gray-500 font-medium uppercase tracking-wider">ID Admin</label>
                                <p class="font-bold text-gray-800 text-lg mt-1 font-mono">#<?= htmlspecialchars($adminData['id_admin']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-end pt-4">
                        <a href="ProfileAdmin.php"
                            class="flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3.5 px-8 rounded-xl transition-all hover:shadow-md">
                            <span class="material-symbols-outlined">close</span>
                            Batal
                        </a>
                        <button type="submit"
                            id="submitBtn"
                            class="flex items-center justify-center gap-2 bg-gradient-to-r from-[#882426] to-[#a83236] hover:from-[#6d1a1c] hover:to-[#882426] text-white font-semibold py-3.5 px-8 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <span class="material-symbols-outlined">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let isSuperAdmin = <?= json_encode($adminRepo->isSuperAdmin($currentAdmin['id_admin'])); ?>;

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format Tidak Valid',
                        text: 'Gunakan format JPG, PNG, atau WebP',
                        confirmButtonColor: '#882426'
                    });
                    input.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran maksimal 5MB',
                        confirmButtonColor: '#882426'
                    });
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);

                const fileNameEl = document.getElementById('photoFileName');
                fileNameEl.querySelector('span:last-child').textContent = 'File dipilih: ' + file.name;
                fileNameEl.classList.remove('hidden');
                fileNameEl.classList.add('flex');
            }
        }

        // Initialize role section visibility
        if (isSuperAdmin) {
            document.getElementById('roleEditIcon').textContent = 'edit';
            document.getElementById('roleEditTitle').textContent = 'Informasi yang Dapat Diubah';
            document.getElementById('roleEditDesc').textContent = 'Sebagai Super Admin, Anda dapat mengedit role';
            document.getElementById('roleEditSection').classList.remove('from-gray-100', 'to-gray-50');
            document.getElementById('roleEditSection').classList.add('from-blue-50', 'to-indigo-50');
            document.getElementById('roleContainer').classList.remove('bg-white', 'border-gray-200');
            document.getElementById('roleContainer').classList.add('bg-white');
            document.getElementById('roleDisplay').classList.add('hidden');
            document.getElementById('roleSelect').classList.remove('hidden');
        }

        document.getElementById('editProfileForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Menyimpan...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData(this);

                // Remove role field if not super admin
                if (!isSuperAdmin) {
                    formData.delete('role');
                }

                const url = '../../app/controllers/adminProfileController.php?action=update_profile';

                console.log('Submitting form to:', url);
                console.log('Form data keys:', Array.from(formData.keys()));

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
                    showToast('success', 'Berhasil!', result.message || 'Profil berhasil diperbarui');
                    setTimeout(() => {
                        window.location.href = 'ProfileAdmin.php?updated=success';
                    }, 2000);
                } else {
                    showToast('error', 'Gagal!', result.message || 'Terjadi kesalahan yang tidak diketahui');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Terjadi Kesalahan', error.message || 'Gagal menghubungi server. Silakan coba lagi.');
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

    <script>
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
    </script>
</body>

</html>