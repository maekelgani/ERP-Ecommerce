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

        <main class="flex-1 overflow-y-auto">
            <div class="p-4 md:p-8 space-y-6">
                <?php include '../../components/admin/breadcrumb.php'; ?>

                <!-- Hero Section -->
                <div class="relative overflow-hidden rounded-2xl bg-[#882426] shadow-xl">
                    <div class="relative p-8 md:p-12">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="p-3 bg-white/20 backdrop-blur rounded-lg">
                                        <span class="material-symbols-outlined text-white text-2xl">admin_panel_settings</span>
                                    </div>
                                    <h1 class="text-3xl md:text-4xl font-bold text-white">Kelola Akses Sistem</h1>
                                </div>
                                <p class="text-blue-50 text-lg">Manajemen penuh peran, hak akses, dan pengguna administrator</p>
                            </div>
                            <div class="flex gap-3 flex-wrap">
                                <?php if ($canManageUsers): ?>
                                    <a href="UserManagementAdmin.php" class="px-6 py-2 bg-white text-[#882426] font-semibold rounded-lg hover:bg-blue-50 transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl">
                                        <span class="material-symbols-outlined text-xl">person_add</span>
                                        <span>Kelola User</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ($canManageRoles || $canAssignPermissions): ?>
                                    <a href="RoleManagementAdmin.php" class="px-6 py-2 bg-white/20 hover:bg-white/30 text-white font-semibold rounded-lg transition-all duration-300 flex items-center gap-2 backdrop-blur border border-white/30">
                                        <span class="material-symbols-outlined text-xl">key</span>
                                        <span>Role & Permission</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Users Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-blue-100 to-blue-50 rounded-xl group-hover:from-blue-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-blue-600 text-3xl">people</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-blue-50 text-blue-600 font-semibold rounded-full">+5%</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Total Pengguna</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= $totalUsers ?></h2>
                                <span class="text-xs text-gray-400">administrator</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Pengguna admin aktif dalam sistem</p>
                        </div>
                    </div>

                    <!-- Total Roles Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-xl group-hover:from-emerald-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-emerald-600 text-3xl">security</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-emerald-50 text-emerald-600 font-semibold rounded-full">2</div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Total Role</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= $totalRoles ?></h2>
                                <span class="text-xs text-gray-400">tipe role</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Peran yang tersedia dalam sistem</p>
                        </div>
                    </div>

                    <!-- Total Permissions Card -->
                    <div class="group relative bg-white rounded-2xl p-6 shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full -mr-12 -mt-12 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <div class="relative z-10">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-4 bg-gradient-to-br from-purple-100 to-purple-50 rounded-xl group-hover:from-purple-200 transition-all duration-300">
                                    <span class="material-symbols-outlined text-purple-600 text-3xl">vpn_key</span>
                                </div>
                                <div class="text-xs px-3 py-1 bg-purple-50 text-purple-600 font-semibold rounded-full"><?= $totalPermissions ?></div>
                            </div>
                            <h3 class="text-sm text-gray-500 font-medium mb-1">Total Permission</h3>
                            <div class="flex items-baseline gap-2">
                                <h2 class="text-4xl font-bold text-gray-900"><?= $totalPermissions ?></h2>
                                <span class="text-xs text-gray-400">hak akses</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-3">Izin akses yang dapat diberikan</p>
                        </div>
                    </div>
                </div>

                <!-- Role Statistics Section -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-lg">
                                    <span class="material-symbols-outlined text-indigo-600 text-2xl">trending_up</span>
                                </div>
                                Statistik Distribusi Role
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Persentase pengguna berdasarkan peran mereka</p>
                        </div>
                    </div>

                    <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                        <?php
                        $colors = [
                            'super_admin' => ['bg' => 'from-blue-100 to-blue-50', 'border' => 'border-blue-200', 'icon' => 'verified_user', 'text' => 'text-blue-600', 'badge' => 'bg-blue-100 text-blue-700'],
                            'admin' => ['bg' => 'from-emerald-100 to-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'admin_panel_settings', 'text' => 'text-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-700'],
                        ];

                        foreach ($stats as $index => $stat):
                            $color = $colors[$stat['role_name']] ?? ['bg' => 'from-gray-100 to-gray-50', 'border' => 'border-gray-200', 'icon' => 'person', 'text' => 'text-gray-600', 'badge' => 'bg-gray-100 text-gray-700'];
                            $percentage = $totalUsers > 0 ? round(($stat['user_count'] / $totalUsers) * 100) : 0;
                        ?>
                            <div class="group relative bg-gradient-to-br <?= $color['bg'] ?> rounded-xl p-6 border-2 <?= $color['border'] ?> hover:shadow-lg transition-all duration-300 cursor-pointer overflow-hidden">
                                <div class="absolute top-0 right-0 w-20 h-20 bg-white/30 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-300"></div>

                                <div class="relative z-10">
                                    <div class="flex items-start justify-between mb-4">
                                        <span class="material-symbols-outlined <?= $color['text'] ?> text-3xl">
                                            <?= $color['icon'] ?>
                                        </span>
                                        <span class="px-3 py-1 text-xs font-bold rounded-lg <?= $color['badge'] ?>">
                                            <?= $percentage ?>%
                                        </span>
                                    </div>

                                    <h3 class="font-semibold text-gray-900 capitalize mb-1">
                                        <?= str_replace('_', ' ', $stat['role_name']) ?>
                                    </h3>

                                    <div class="mb-4">
                                        <div class="text-2xl font-bold text-gray-900"><?= $stat['user_count'] ?></div>
                                        <p class="text-xs text-gray-600 mt-1">pengguna terdaftar</p>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-white/40 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r <?= $color['bg'] ?> h-full rounded-full transition-all duration-500" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Actions & Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions (Left Column) -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 h-full">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-xl text-amber-600">bolt</span>
                                Aksi Cepat
                            </h3>

                            <div class="space-y-3">
                                <?php if ($canManageUsers): ?>
                                    <a href="UserManagementAdmin.php" class="flex items-center gap-4 p-4 rounded-lg border-2 border-gray-100 hover:border-blue-300 hover:bg-blue-50 transition-all duration-300 group">
                                        <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-blue-600">manage_accounts</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Manajemen User</p>
                                            <p class="text-xs text-gray-500">Kelola pengguna admin</p>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-400 group-hover:text-blue-600 transition-colors">arrow_forward</span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($canManageRoles || $canAssignPermissions): ?>
                                    <a href="RoleManagementAdmin.php" class="flex items-center gap-4 p-4 rounded-lg border-2 border-gray-100 hover:border-emerald-300 hover:bg-emerald-50 transition-all duration-300 group">
                                        <div class="p-3 bg-emerald-100 group-hover:bg-emerald-200 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-emerald-600">admin_panel_settings</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">Role & Permission</p>
                                            <p class="text-xs text-gray-500">Atur peran akses</p>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-400 group-hover:text-emerald-600 transition-colors">arrow_forward</span>
                                    </a>
                                <?php endif; ?>

                                <div class="pt-4 border-t border-gray-100">
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
                                        <p class="text-xs font-semibold text-gray-600 mb-3 flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-blue-600">info</span>
                                            Informasi Sistem
                                        </p>
                                        <div class="space-y-2 text-xs">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">User Aktif:</span>
                                                <span class="font-bold text-gray-900"><?= $totalUsers ?></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Total Role:</span>
                                                <span class="font-bold text-gray-900"><?= $totalRoles ?></span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Total Permission:</span>
                                                <span class="font-bold text-gray-900"><?= $totalPermissions ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity (Right Column - 2 cols) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-xl text-amber-600">schedule</span>
                                    Aktivitas Pengguna Terakhir
                                </h3>
                                <?php if ($canManageUsers && count($users) > 5): ?>
                                    <a href="UserManagementAdmin.php" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                        Lihat Semua
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="overflow-x-auto">
                                <div class="space-y-3">
                                    <?php
                                    $recentUsers = array_slice($users, 0, 6);
                                    foreach ($recentUsers as $user):
                                        $initials = strtoupper(substr($user['nama_lengkap'], 0, 1));
                                        $roleColor = match ($user['role_name'] ?? '') {
                                            'super_admin' => 'text-blue-700 bg-blue-100',
                                            'admin' => 'text-emerald-700 bg-emerald-100',
                                            default => 'text-gray-700 bg-gray-100'
                                        };
                                    ?>
                                        <div class="grid grid-cols-2 items-center p-4 rounded-lg border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-all duration-300 group">
                                            <div class="flex items-center flex-1 gap-4 min-w-0">
                                                <div class="w-10 h-10 bg-[#882426] rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-md">
                                                    <?= $initials ?>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold text-gray-900 truncate"><?= htmlspecialchars($user['nama_lengkap']) ?></p>
                                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($user['email']) ?></p>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-3 items-center gap-6 flex-shrink-0 text-sm">

                                                <!-- ROLE -->
                                                <div class="flex justify-center">
                                                    <span class="px-3 py-1 text-xs font-bold rounded-lg <?= $roleColor ?> whitespace-nowrap">
                                                        <?= ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A')) ?>
                                                    </span>
                                                </div>

                                                <!-- STATUS -->
                                                <div class="flex items-center justify-center gap-2">
                                                    <?php if ($user['is_active']): ?>
                                                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                        <span class="text-emerald-600 font-semibold">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                                        <span class="text-gray-500 font-semibold">Nonaktif</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- LAST LOGIN -->
                                                <div class="text-right leading-tight">
                                                    <p class="font-semibold text-gray-700">
                                                        <?= $user['last_login'] ? date('d M Y', strtotime($user['last_login'])) : '-' ?>
                                                    </p>
                                                    <p class="text-xs text-gray-400">
                                                        <?= $user['last_login'] ? date('H:i', strtotime($user['last_login'])) : 'Belum login' ?>
                                                    </p>
                                                </div>

                                            </div>


                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Security Overview -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-br from-rose-100 to-rose-50 rounded-lg">
                            <span class="material-symbols-outlined text-rose-600 text-2xl">security</span>
                        </div>
                        Ringkasan Keamanan Sistem
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-lg p-5 border border-blue-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-blue-900">Pengguna Aktif</span>
                                <span class="material-symbols-outlined text-blue-600 text-xl">check_circle</span>
                            </div>
                            <p class="text-2xl font-bold text-blue-900">
                                <?php
                                $activeCount = count(array_filter($users, fn($u) => $u['is_active']));
                                echo $activeCount;
                                ?>
                            </p>
                            <p class="text-xs text-blue-700 mt-2">dari <?= $totalUsers ?> pengguna</p>
                        </div>

                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-lg p-5 border border-emerald-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-emerald-900">Role Configured</span>
                                <span class="material-symbols-outlined text-emerald-600 text-xl">admin_panel_settings</span>
                            </div>
                            <p class="text-2xl font-bold text-emerald-900"><?= $totalRoles ?></p>
                            <p class="text-xs text-emerald-700 mt-2">tipe peran tersedia</p>
                        </div>

                        <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-lg p-5 border border-purple-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-purple-900">Permission Set</span>
                                <span class="material-symbols-outlined text-purple-600 text-xl">verified_user</span>
                            </div>
                            <p class="text-2xl font-bold text-purple-900"><?= $totalPermissions ?></p>
                            <p class="text-xs text-purple-700 mt-2">hak akses didefinisikan</p>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-lg p-5 border border-amber-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-amber-900">Super Admin</span>
                                <span class="material-symbols-outlined text-amber-600 text-xl">security</span>
                            </div>
                            <p class="text-2xl font-bold text-amber-900">
                                <?php
                                $superAdminCount = 0;
                                foreach ($stats as $stat) {
                                    if ($stat['role_name'] === 'super_admin') {
                                        $superAdminCount = $stat['user_count'];
                                        break;
                                    }
                                }
                                echo $superAdminCount;
                                ?>
                            </p>
                            <p class="text-xs text-amber-700 mt-2">pengguna super admin</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        main>div>* {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        main>div>*:nth-child(n+2) {
            animation-delay: calc(0.1s * var(--index, 1));
        }

        .group:hover {
            --tw-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</body>

</html>