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
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Manajemen Pengguna</h1>
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

    <!-- Enhanced User Modal - Consistent with CustomerList.php -->
    <div id="userModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeUserModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl" id="modalIcon">person_add</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modalTitle">Tambah User</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="modalSubtitle">Isi data pengguna administrator</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeUserModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="userForm" onsubmit="submitUserForm(event)">
                    <input type="hidden" id="userId" name="id_admin">

                    <div class="p-6 bg-gray-50 space-y-5 max-h-[60vh] overflow-y-auto" id="userFormContent">
                        <!-- Info Alert -->
                        <div class="p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium" id="formInfoText">Lengkapi data pengguna administrator dengan benar.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">badge</span>
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="userName" name="nama_lengkap" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="Masukkan nama lengkap...">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">mail</span>
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="userEmail" name="email" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="user@example.com">
                        </div>

                        <!-- No. Telepon -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">phone</span>
                                No. Telepon
                            </label>
                            <input type="text" id="userPhone" name="phone"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="+62812345678">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">lock</span>
                                Password <span id="passwordNote" class="text-xs font-normal text-gray-500">(wajib untuk pengguna baru)</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="userPassword" name="password"
                                    class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Masukkan password...">
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg" id="togglePasswordIcon">visibility_off</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1" id="passwordHint">
                                <span class="material-symbols-outlined text-sm">lightbulb</span>
                                Minimal 6 karakter
                            </p>
                        </div>

                        <!-- Role & Status Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Role -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">admin_panel_settings</span>
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select id="userRole" name="id_role" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 bg-white">
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id_role'] ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $role['role_name']))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Status -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">toggle_on</span>
                                    Status
                                </label>
                                <select id="userStatus" name="is_active"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 bg-white">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeUserModal()"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                            <span class="material-symbols-outlined text-lg">close</span>
                            Batal
                        </button>
                        <button type="submit" id="userSubmitBtn"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            <span id="submitBtnText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Delete Modal - Consistent with CustomerList.php -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Red Color for Delete -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg animate-pulse-slow">
                            <span class="material-symbols-outlined text-white text-2xl">warning</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hapus User</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan pengguna</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- User Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center ring-4 ring-red-50">
                            <span class="material-symbols-outlined text-red-500 text-4xl">person_remove</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteUserName"></p>
                            <p class="text-sm text-gray-500">Administrator</p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">error</span>
                            <div>
                                <p class="text-sm text-red-700 font-medium">Apakah Anda yakin ingin menghapus user ini?</p>
                                <p class="text-xs text-red-600 mt-1">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="deleteUserId">
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="button" onclick="confirmDelete()" id="deleteConfirmBtn"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg shadow-red-600/30">
                        <span class="material-symbols-outlined text-lg">delete_forever</span>
                        Ya, Hapus!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal Styles -->
    <style>
        @keyframes modal-in {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.8;
                transform: scale(1.05);
            }
        }

        .animate-modal-in {
            animation: modal-in 0.3s ease-out forwards;
        }

        .animate-pulse-slow {
            animation: pulse-slow 2s ease-in-out infinite;
        }

        /* Custom Scrollbar for Modal */
        #userFormContent::-webkit-scrollbar {
            width: 6px;
        }

        #userFormContent::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #userFormContent::-webkit-scrollbar-thumb {
            background: #882426;
            border-radius: 10px;
        }

        #userFormContent::-webkit-scrollbar-thumb:hover {
            background: #6d1a1c;
        }

        /* Input Focus Animation */
        input:focus,
        select:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(136, 36, 38, 0.15);
        }

        /* Button Hover Effects */
        button[type="submit"]:hover,
        #deleteConfirmBtn:hover {
            transform: translateY(-1px);
        }

        button[type="submit"]:active,
        #deleteConfirmBtn:active {
            transform: translateY(0) scale(0.98);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const API_URL = '../../app/controllers/accessController.php';
        let isEditMode = false;

        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1');
            window.location.href = '?' + params.toString();
        }

        function togglePassword() {
            const passwordInput = document.getElementById('userPassword');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility_off';
            }
        }

        function openAddUserModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Tambah User';
            document.getElementById('modalSubtitle').textContent = 'Isi data pengguna administrator';
            document.getElementById('modalIcon').textContent = 'person_add';
            document.getElementById('formInfoText').textContent = 'Lengkapi data pengguna administrator dengan benar.';
            document.getElementById('submitBtnText').textContent = 'Simpan';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('userPassword').required = true;
            document.getElementById('passwordNote').textContent = '(wajib untuk pengguna baru)';
            document.getElementById('passwordHint').innerHTML = '<span class="material-symbols-outlined text-sm">lightbulb</span> Minimal 6 karakter';
            document.getElementById('userPassword').type = 'password';
            document.getElementById('togglePasswordIcon').textContent = 'visibility_off';
            document.getElementById('userModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function editUser(id) {
            isEditMode = true;
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('modalSubtitle').textContent = 'Perbarui data pengguna';
            document.getElementById('modalIcon').textContent = 'edit';
            document.getElementById('formInfoText').textContent = 'Ubah data pengguna sesuai kebutuhan. Password kosongkan jika tidak ingin mengubah.';
            document.getElementById('submitBtnText').textContent = 'Update';
            document.getElementById('userPassword').required = false;
            document.getElementById('passwordNote').textContent = '(kosongkan jika tidak ingin mengubah)';
            document.getElementById('passwordHint').innerHTML = '<span class="material-symbols-outlined text-sm">lightbulb</span> Kosongkan jika tidak ingin mengubah password';
            document.getElementById('userPassword').type = 'password';
            document.getElementById('togglePasswordIcon').textContent = 'visibility_off';

            // Show loading state
            const submitBtn = document.getElementById('userSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Memuat...';

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
                        document.body.style.overflow = 'hidden';
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#882426'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data',
                        icon: 'error',
                        confirmButtonColor: '#882426'
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> <span id="submitBtnText">Update</span>';
                });
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function submitUserForm(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('userSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menyimpan...';

            const formData = new FormData(document.getElementById('userForm'));
            formData.append('action', isEditMode ? 'update_user' : 'create_user');

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    closeUserModal();
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#882426'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#882426'
                        });
                    }
                })
                .catch(error => {
                    closeUserModal();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        icon: 'error',
                        confirmButtonColor: '#882426'
                    });
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> <span id="submitBtnText">' + (isEditMode ? 'Update' : 'Simpan') + '</span>';
                });
        }

        function deleteUser(id, name) {
            document.getElementById('deleteUserId').value = id;
            document.getElementById('deleteUserName').textContent = name;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function confirmDelete() {
            const id = document.getElementById('deleteUserId').value;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menghapus...';

            const formData = new FormData();
            formData.append('action', 'delete_user');
            formData.append('id_admin', id);

            fetch(API_URL, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    closeDeleteModal();
                    if (data.success) {
                        Swal.fire({
                            title: 'Terhapus!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#882426'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#882426'
                        });
                    }
                })
                .catch(error => {
                    closeDeleteModal();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus data',
                        icon: 'error',
                        confirmButtonColor: '#882426'
                    });
                })
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">delete_forever</span> Ya, Hapus!';
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

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const userModal = document.getElementById('userModal');
                const deleteModal = document.getElementById('deleteModal');

                if (!userModal.classList.contains('hidden')) {
                    closeUserModal();
                }
                if (!deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            }
        });

        // Lightbox functions
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

        document.getElementById('lightbox')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
    </script>
</body>

</html>