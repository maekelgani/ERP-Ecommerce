<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/RoleRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\AdminRepository;
use App\Auth\PermissionHelper;
use App\Repository\RoleRepository;

AuthMiddleware::requireAdminLoginFromView();
AuthMiddleware::requirePermissionFromView('manage_admin_users');

$adminRepo = new AdminRepository();
$roleRepo = new RoleRepository();

// Pagination logic
$perPage = (int)($_GET['per_page'] ?? 10);
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $perPage;

// Get all users
$allUsers = $adminRepo->getAllAdminsWithRoles();
$totalUsers = count($allUsers);
$totalPages = (int)ceil($totalUsers / $perPage);

// Get paginated users
$users = array_slice($allUsers, $offset, $perPage);

// Calculate display range
$startEntry = $totalUsers > 0 ? $offset + 1 : 0;
$endEntry = min($offset + $perPage, $totalUsers);

$roles = $roleRepo->getAllRoles();
$stats = $adminRepo->getAdminCountByRole();

$pageTitle = "Manajemen User";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?php include '../../components/admin/breadcrumb.php'; ?>

            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Manajemen User</h1>
                <p class="text-gray-500 mt-1">Kelola pengguna administrator sistem dengan mudah</p>
            </div>

            <div id="stats-cards" class="grid gap-4 grid-cols-1 md:grid-cols-2 mb-8">
                <?php foreach ($stats as $stat): ?>
                    <div class="rounded-xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <div class="p-6" style="background: linear-gradient(135deg, <?= $stat['role_name'] === 'super_admin' ? '#882426 0%, #6d1a1c' : '#10b981 0%, #059669' ?> 100%);">
                            <div class="flex items-center justify-between">
                                <div class="text-white">
                                    <p class="text-sm font-medium opacity-90">Role: <strong><?= htmlspecialchars(str_replace('_', ' ', $stat['role_name'])) ?></strong></p>
                                    <h3 class="text-3xl font-bold mt-2"><?= $stat['user_count'] ?></h3>
                                    <p class="text-sm opacity-90 mt-1"><?= $stat['role_name'] === 'super_admin' ? 'Full Access' : 'Limited Access' ?></p>
                                </div>
                                <span class="material-symbols-outlined text-white text-5xl opacity-20"><?= $stat['role_name'] === 'super_admin' ? 'security' : 'person' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Pengguna</h2>
                            <p class="text-gray-500 text-sm mt-1">Total <?= $totalUsers ?> pengguna terdaftar</p>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- LEFT: Entries per page -->
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">view_list</span>
                                <select id="per-page-select" onchange="changePerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>

                            <button onclick="openAddUserModal()" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-white font-medium text-sm rounded-lg shadow transition-all duration-300 hover:shadow-lg active:scale-95 whitespace-nowrap" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                <span>Tambah User</span>
                            </button>
                        </div>
                    </div>
                </div>

                <form class="p-4 md:p-6 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" id="searchInput" placeholder="Cari nama, email, atau role..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <select id="roleFilter" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id_role'] ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role['role_name']))) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="usersTable">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Telepon</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Login Terakhir</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-100">
                            <?php $nomor = $offset + 1;
                            foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50 transition-colors" data-role="<?= $user['id_role'] ?>">
                                    <td class="px-5 py-4 text-center font-semibold text-gray-800 w-12"><?= $nomor++ ?></td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <?php
                                            $profileImageSrc = isset($user['foto_profil']) && $user['foto_profil'] && file_exists('../../uploads/profil/' . $user['foto_profil'])
                                                ? '../../uploads/profil/' . htmlspecialchars($user['foto_profil'])
                                                : '../../assets/img/profil/default-profil.jpg';
                                            ?>
                                            <div class="relative group cursor-pointer" onclick="openLightbox('<?= $profileImageSrc ?>')">
                                                <img class="w-10 h-10 object-cover rounded-lg shadow-sm border border-gray-100 transition-transform group-hover:scale-105"
                                                    src="<?= $profileImageSrc ?>"
                                                    alt="<?= htmlspecialchars($user['nama_lengkap']) ?>"
                                                    onerror="this.src='../../assets/img/profil/default-profil.jpg'">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-white text-sm">zoom_in</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($user['nama_lengkap']) ?></p>
                                                <p class="text-xs text-gray-500">#<?= $user['id_admin'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
                                    <td class="px-5 py-4 text-gray-700"><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                    <td class="px-5 py-4 text-center">
                                        <?php
                                        $roleConfig = match ($user['role_name'] ?? '') {
                                            'super_admin' => [
                                                'bg' => 'bg-red-50',
                                                'text' => 'text-red-600',
                                                'border' => 'border-red-200',
                                                'gradient' => 'from-red-50 to-red-50',
                                                'icon_color' => 'text-red-600'
                                            ],
                                            'admin' => [
                                                'bg' => 'bg-blue-50',
                                                'text' => 'text-blue-600',
                                                'border' => 'border-blue-200',
                                                'gradient' => 'from-blue-50 to-blue-50',
                                                'icon_color' => 'text-blue-600'
                                            ],
                                            default => [
                                                'bg' => 'bg-gray-50',
                                                'text' => 'text-gray-600',
                                                'border' => 'border-gray-200',
                                                'gradient' => 'from-gray-50 to-gray-50',
                                                'icon_color' => 'text-gray-600'
                                            ]
                                        };
                                        ?>
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold uppercase border-2 <?= $roleConfig['bg'] ?> <?= $roleConfig['text'] ?> <?= $roleConfig['border'] ?> shadow-sm hover:shadow-md transition-all duration-200 cursor-default">
                                            <!-- <span class="material-symbols-outlined text-sm <?= $roleConfig['icon_color'] ?>">shield</span> -->
                                            <span><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A'))) ?></span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if ($user['is_active']): ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>
                                                Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        <?= $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : '<span class="text-gray-500 italic">Belum pernah</span>' ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button onclick="editUser(<?= $user['id_admin'] ?>)" class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white transition-all duration-300 hover:shadow-lg active:scale-95 bg-amber-500 hover:bg-amber-600" title="Edit User">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <?php if ($user['role_name'] !== 'super_admin' || PermissionHelper::isSuperAdmin()): ?>
                                                <button onclick="deleteUser(<?= $user['id_admin'] ?>, '<?= htmlspecialchars($user['nama_lengkap']) ?>')" class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white transition-all duration-300 hover:shadow-lg active:scale-95 bg-red-500 hover:bg-red-600" title="Hapus User">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info & Controls -->
                <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalUsers ?></span> entries
                    </div>

                    <!-- Pagination Navigation - Always Show -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <?php if ($currentPage > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                            </a>
                        <?php else: ?>
                            <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                            </button>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        if ($startPage > 1):
                        ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                            <?php if ($startPage > 2): ?>
                                <span class="px-2 py-2 text-gray-400">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <?php if ($i === $currentPage): ?>
                                <button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;"><?= $i ?></button>
                            <?php else: ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <span class="px-2 py-2 text-gray-400">...</span>
                            <?php endif; ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $totalPages ?></a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                            </a>
                        <?php else: ?>
                            <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeUserModal()"></div>
        <div class="relative z-10 max-w-lg w-full rounded-2xl border border-gray-100 bg-white shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                <div>
                    <h2 class="text-xl font-bold text-white" id="modalTitle">Tambah User</h2>
                    <p class="text-sm text-white/80">Masukkan data pengguna administrator</p>
                </div>
                <button onclick="closeUserModal()" class="p-2 hover:bg-white/20 rounded-lg transition-colors text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form id="userForm" onsubmit="submitUserForm(event)" class="p-6 space-y-5">
                <input type="hidden" id="userId" name="id_admin">

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="userName" name="nama_lengkap" required class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" placeholder="Masukkan nama lengkap">
                </div>

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="userEmail" name="email" required class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" placeholder="user@example.com">
                </div>

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">No Telepon</label>
                    <input type="text" id="userPhone" name="phone" class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" placeholder="+62812345678">
                </div>

                <div>
                    <label class="font-semibold text-sm text-gray-800 block mb-2">Password <span class="text-red-500" id="passwordRequired">*</span></label>
                    <input type="password" id="userPassword" name="password" class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" placeholder="••••••">
                    <p class="text-xs text-gray-500 mt-2" id="passwordHint">Minimal 6 karakter</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-sm text-gray-800 block mb-2">Role <span class="text-red-500">*</span></label>
                        <select id="userRole" name="id_role" required class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id_role'] ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role['role_name']))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-sm text-gray-800 block mb-2">Status</label>
                        <select id="userStatus" name="is_active" class="w-full px-4 py-2.5 text-gray-900 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeUserModal()" class="px-4 py-2.5 border border-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2.5 text-white rounded-lg font-medium transition-all duration-300 hover:shadow-lg active:scale-95" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
        <div class="absolute inset-0 bg-black/50" onclick="closeDeleteModal()"></div>
        <div class="relative z-10 max-w-sm w-full rounded-2xl border border-gray-100 bg-white shadow-xl p-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-500 text-3xl">error</span>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Hapus User?</h2>
                <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus user <strong id="deleteUserName" class="text-gray-900"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <input type="hidden" id="deleteUserId">
                <div class="flex justify-center gap-3">
                    <button onclick="closeDeleteModal()" class="px-4 py-2.5 border border-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-50 transition-colors">Batal</button>
                    <button onclick="confirmDelete()" class="px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../../app/controllers/accessController.php';
        let isEditMode = false;

        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1');
            window.location.href = '?' + params.toString();
        }

        function openAddUserModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Tambah User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').required = true;
            document.getElementById('passwordRequired').classList.remove('hidden');
            document.getElementById('passwordHint').textContent = 'Minimal 6 karakter';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function editUser(id) {
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('userPassword').required = false;
            document.getElementById('passwordRequired').classList.add('hidden');
            document.getElementById('passwordHint').textContent = 'Kosongkan jika tidak ingin mengubah password';

            fetch(`${API_URL}?action=get_user&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const user = data.data;
                        document.getElementById('userId').value = user.id_admin;
                        document.getElementById('userName').value = user.nama_lengkap;
                        document.getElementById('userEmail').value = user.email;
                        document.getElementById('userPhone').value = user.phone || '';
                        document.getElementById('userRole').value = user.id_role;
                        document.getElementById('userStatus').value = user.is_active;
                        document.getElementById('userModal').classList.remove('hidden');
                    } else {
                        alert(data.message);
                    }
                });
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function submitUserForm(e) {
            e.preventDefault();
            const formData = new FormData(document.getElementById('userForm'));
            formData.append('action', isEditMode ? 'update_user' : 'create_user');

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        function deleteUser(id, name) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmDelete() {
            const id = document.getElementById('deleteUserId').value;
            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('id_admin', id);

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const search = this.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(search) ? '' : 'none';
            });
        });

        document.getElementById('roleFilter').addEventListener('change', function() {
            const roleId = this.value;
            const rows = document.querySelectorAll('#usersTableBody tr');
            rows.forEach(row => {
                if (!roleId || row.dataset.role === roleId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Lightbox functions
        function openLightbox(src) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = src;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }
    </script>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-lg shadow-2xl">
    </div>
</body>

</html>