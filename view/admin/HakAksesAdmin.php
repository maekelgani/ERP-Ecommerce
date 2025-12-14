<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/RoleRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\AdminRepository;
use App\Auth\PermissionHelper;
use App\Repository\RoleRepository;

AuthMiddleware::requireAdminLoginFromView();
AuthMiddleware::requireAnyPermissionFromView(['manage_admin_users', 'manage_roles', 'assign_role_permissions']);

$adminRepo = new AdminRepository();
$roleRepo = new RoleRepository();

$stats = $adminRepo->getAdminCountByRole();
$users = $adminRepo->getAllAdminsWithRoles();
$roles = $roleRepo->getAllRolesWithUserCount();

$totalUsers = $adminRepo->getTotalAdminCount();
$totalRoles = $roleRepo->getRoleCount();
$totalPermissions = $roleRepo->getPermissionCount();

$canManageUsers = PermissionHelper::canManageUsers();
$canManageRoles = PermissionHelper::canManageRoles();
$canAssignPermissions = PermissionHelper::canAssignRolePermissions();

$pageTitle = "Kelola Akses";
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

            <div class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold">Kelola Akses</h1>
                    <p class="text-gray-400">Kelola peran dan hak akses sistem administrator</p>
                </div>
            </div>

            <div class="grid gap-4 grid-cols-1 md:grid-cols-3 mb-6">
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <span class="material-symbols-outlined text-blue-600 text-2xl">people</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold"><?= $totalUsers ?></h3>
                            <p class="text-gray-500 text-sm">Total Pengguna</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-full">
                            <span class="material-symbols-outlined text-green-600 text-2xl">security</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold"><?= $totalRoles ?></h3>
                            <p class="text-gray-500 text-sm">Total Role</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-5">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-full">
                            <span class="material-symbols-outlined text-purple-600 text-2xl">key</span>
                        </div>
                        <div>
                            <h3 class="text-3xl font-bold"><?= $totalPermissions ?></h3>
                            <p class="text-gray-500 text-sm">Total Permission</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2 mb-6">
                <?php if ($canManageUsers): ?>
                    <a href="UserManagementAdmin.php" class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition-colors">
                                <span class="material-symbols-outlined text-blue-600 text-3xl">manage_accounts</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Manajemen User</h2>
                                <p class="text-gray-500 text-sm">Kelola pengguna administrator</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400"><?= $totalUsers ?> pengguna terdaftar</span>
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">arrow_forward</span>
                        </div>
                    </a>
                <?php endif; ?>

                <?php if ($canManageRoles || $canAssignPermissions): ?>
                    <a href="RoleManagementAdmin.php" class="rounded-lg border border-gray-200 bg-white shadow-md p-6 hover:shadow-lg transition-shadow cursor-pointer group">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-green-50 rounded-lg group-hover:bg-green-100 transition-colors">
                                <span class="material-symbols-outlined text-green-600 text-3xl">admin_panel_settings</span>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Role & Permission</h2>
                                <p class="text-gray-500 text-sm">Kelola role dan hak akses</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400"><?= $totalRoles ?> role, <?= $totalPermissions ?> permission</span>
                            <span class="material-symbols-outlined text-gray-400 group-hover:text-green-600 transition-colors">arrow_forward</span>
                        </div>
                    </a>
                <?php endif; ?>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4 mb-6">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined">shield</span>
                    Statistik Role
                </h2>
                <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                    <?php foreach ($stats as $stat): ?>
                        <div class="border rounded-lg p-4 <?= $stat['role_name'] === 'super_admin' ? 'border-blue-300 bg-blue-50' : '' ?>">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="material-symbols-outlined text-gray-600">
                                    <?= $stat['role_name'] === 'super_admin' ? 'verified_user' : 'person' ?>
                                </span>
                                <div>
                                    <h3 class="font-semibold capitalize"><?= htmlspecialchars(str_replace('_', ' ', $stat['role_name'])) ?></h3>
                                    <p class="text-sm text-gray-500"><?= $stat['user_count'] ?> pengguna</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 pl-9">
                                <?= $stat['role_name'] === 'super_admin' ? 'Akses penuh ke semua fitur' : ($stat['role_description'] ?? 'Akses terbatas sesuai permission') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white shadow-md p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined">history</span>
                        Aktivitas Terakhir
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b bg-gray-50">
                            <tr>
                                <th class="h-10 px-4 text-left font-semibold text-gray-600">Pengguna</th>
                                <th class="h-10 px-4 text-left font-semibold text-gray-600">Role</th>
                                <th class="h-10 px-4 text-left font-semibold text-gray-600">Status</th>
                                <th class="h-10 px-4 text-left font-semibold text-gray-600">Login Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recentUsers = array_slice($users, 0, 5);
                            foreach ($recentUsers as $user):
                            ?>
                                <tr class="border-t hover:bg-gray-50">
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-gray-600 text-sm font-medium">
                                                    <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <p class="font-medium"><?= htmlspecialchars($user['nama_lengkap']) ?></p>
                                                <p class="text-xs text-gray-400"><?= htmlspecialchars($user['email']) ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <?php
                                        $roleClass = match ($user['role_name'] ?? '') {
                                            'super_admin' => 'text-blue-800 border-blue-500 bg-blue-100',
                                            'admin' => 'text-green-800 border-green-500 bg-green-100',
                                            default => 'text-gray-800 border-gray-500 bg-gray-100'
                                        };
                                        ?>
                                        <span class="px-2 py-1 text-xs border rounded-lg <?= $roleClass ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A'))) ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <?php if ($user['is_active']): ?>
                                            <span class="flex items-center gap-1 text-green-600">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="flex items-center gap-1 text-gray-400">
                                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-gray-500 text-sm">
                                        <?= $user['last_login'] ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($canManageUsers && count($users) > 5): ?>
                    <div class="mt-4 text-center">
                        <a href="UserManagementAdmin.php" class="text-blue-600 hover:text-blue-800 text-sm">
                            Lihat semua pengguna &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>