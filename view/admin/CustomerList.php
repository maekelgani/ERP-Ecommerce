<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CrmRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CrmRepository;

AuthMiddleware::requireAdminLoginFromView();

if (!PermissionHelper::canViewCustomers() && !PermissionHelper::canManageCustomers()) {
    header('Location: ../../view/403.php');
    exit;
}

$canManageCustomers = PermissionHelper::canManageCustomers();

$crmRepo = new CrmRepository();

$perPage = (int)($_GET['per_page'] ?? 10);
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $perPage;

$filters = [
    'search' => $_GET['search'] ?? '',
    'is_active' => $_GET['is_active'] ?? '',
    'login_type' => $_GET['login_type'] ?? '',
    'limit' => $perPage,
    'offset' => $offset
];

$customers = $crmRepo->getAllCustomers($filters);
$totalCustomers = $crmRepo->countCustomers($filters);
$totalPages = (int)ceil($totalCustomers / $perPage);

$startEntry = $totalCustomers > 0 ? $offset + 1 : 0;
$endEntry = min($offset + $perPage, $totalCustomers);

$pageTitle = "Daftar Pelanggan";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-lg text-green-700 flex items-center justify-between shadow-sm" id="successAlert">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600">check_circle</span>
                        <span><?= htmlspecialchars($_SESSION['flash_success']) ?></span>
                    </div>
                    <button onclick="closeAlert('successAlert')" class="text-green-600 hover:text-green-800 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <script>
                    setTimeout(() => {
                        const alert = document.getElementById('successAlert');
                        if (alert) {
                            alert.style.transition = 'opacity 0.3s ease-out';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 300);
                        }
                    }, 4000);
                </script>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border border-red-300 rounded-lg text-red-700 flex items-center justify-between shadow-sm" id="errorAlert">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-600">error</span>
                        <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
                    </div>
                    <button onclick="closeAlert('errorAlert')" class="text-red-600 hover:text-red-800 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <script>
                    setTimeout(() => {
                        const alert = document.getElementById('errorAlert');
                        if (alert) {
                            alert.style.transition = 'opacity 0.3s ease-out';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 300);
                        }
                    }, 4000);
                </script>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <div class="mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Daftar Pelanggan</h1>
                    <p class="text-gray-500 text-sm md:text-base mt-1">Total <?= number_format($totalCustomers) ?> pelanggan terdaftar</p>
                </div>
            </div>

            <!-- Perhatian Box -->
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 md:p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-amber-100">
                            <span class="material-symbols-outlined text-amber-600 text-xl">warning</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm md:text-base font-semibold text-amber-900 mb-1">Perhatian</h3>
                        <p class="text-sm text-amber-800 leading-relaxed">
                            Daftar pelanggan ini digunakan khusus untuk pelanggan yang tidak bisa mendaftar akun sendiri atau tidak mengerti cara mendaftar melalui aplikasi. Fitur ini juga berfungsi sebagai sarana untuk update dan pengembangan sistem membership e-commerce Toko Nano Komputer di masa mendatang.
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Pelanggan</h2>
                            <p class="text-sm text-gray-500 mt-1">Menampilkan data pelanggan yang terdaftar dan aktif dalam sistem</p>
                        </div>
                        <!-- KONTROL -->
                        <div class="flex items-center justify-between gap-4">
                            <!-- LEFT: Entries per page -->
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">
                                    view_list
                                </span>
                                <select onchange="window.location.href='?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>&per_page=' + this.value"
                                    class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <?php foreach ([5, 10, 20, 50] as $option): ?>
                                        <option value="<?= $option ?>" <?= $perPage === $option ? 'selected' : '' ?>>
                                            <?= $option ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>

                            <!-- RIGHT: Tambah User -->
                            <?php if ($canManageCustomers): ?>
                                <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    <span>Tambah Pelanggan</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <form method="GET" class="p-4 md:p-6 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" placeholder="Cari nama, email, atau telepon..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <select name="is_active" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Status</option>
                                <option value="1" <?= $filters['is_active'] === '1' ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= $filters['is_active'] === '0' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                            <select name="login_type" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                <option value="">Semua Tipe</option>
                                <option value="regular" <?= $filters['login_type'] === 'regular' ? 'selected' : '' ?>>Regular</option>
                                <option value="google" <?= $filters['login_type'] === 'google' ? 'selected' : '' ?>>Google</option>
                            </select>

                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                <span class="material-symbols-outlined text-lg">filter_alt</span>
                                <span class="hidden sm:inline">Filter</span>
                            </button>

                            <a href="CustomerList.php" class="px-4 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-all active:scale-95 flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">refresh</span>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Orders</th>
                                <th class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Belanja</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bergabung</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($customers)): ?>
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada pelanggan ditemukan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = $filters['offset'] + 1;
                                foreach ($customers as $customer): ?>
                                    <tr class="hover:bg-orange-50 transition-colors">
                                        <td class="px-4 py-3 text-center font-medium text-gray-700">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#882426]/10 text-[#882426]"><?= $no++ ?></span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <?php
                                                $profileImage = !empty($customer['profile_image'])
                                                    ? '../../uploads/customers/' . htmlspecialchars($customer['profile_image'])
                                                    : '../../assets/img/profil/default-customer.jpg';
                                                ?>
                                                <div class="relative group cursor-pointer" onclick="openLightbox('<?= $profileImage ?>')">
                                                    <img class="w-12 h-12 object-cover rounded-lg shadow-sm border border-gray-100 transition-transform group-hover:scale-105"
                                                        src="<?= $profileImage ?>"
                                                        alt="<?= htmlspecialchars($customer['nama_lengkap']) ?>"
                                                        onerror="this.src='../../assets/img/profil/default-customer.jpg'">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-white">zoom_in</span>
                                                    </div>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($customer['nama_lengkap']) ?></p>
                                                    <p class="text-xs text-gray-400">ID: <?= $customer['id_customer'] ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-sm text-gray-800"><?= htmlspecialchars($customer['email']) ?></p>
                                            <p class="text-xs text-gray-400"><?= htmlspecialchars($customer['no_telp'] ?? '-') ?></p>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if ($customer['login_type'] === 'google'): ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs">
                                                    <div class="w-5 h-5 rounded-xl bg-white flex items-center justify-center shadow-sm flex-shrink-0">
                                                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                                                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                                                        </svg>
                                                    </div>
                                                    Google
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                                                    <span class="material-symbols-outlined text-xs">mail</span>
                                                    Regular
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <?php if ($customer['is_active']): ?>
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
                                        <td class="px-4 py-3 text-center">
                                            <span class="font-medium text-gray-800"><?= $customer['total_orders'] ?? 0 ?></span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium text-gray-800">
                                            Rp <?= number_format($customer['total_spending'] ?? 0, 0, ',', '.') ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <?= date('d M Y', strtotime($customer['created_at'])) ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="CustomerDetail.php?id=<?= $customer['id_customer'] ?>"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-purple-600 bg-purple-50 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors"
                                                    title="Lihat Detail">
                                                    <span class="material-symbols-outlined text-lg">visibility</span>
                                                </a>
                                                <?php if ($canManageCustomers): ?>
                                                    <button onclick="openEditModal(<?= htmlspecialchars(json_encode($customer)) ?>)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors"
                                                        title="Edit Pelanggan">
                                                        <span class="material-symbols-outlined text-lg">edit</span>
                                                    </button>
                                                    <button onclick="confirmToggleStatus(<?= htmlspecialchars(json_encode($customer)) ?>)"
                                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg transition-all duration-300 hover:shadow-lg active:scale-95 <?= $customer['is_active'] ? ' text-red-600 bg-red-50 hover:bg-red-100' : 'text-amber-600 bg-amber-50 hover:bg-amber-100' ?>"
                                                        title="<?= $customer['is_active'] ? 'Nonaktifkan Pelanggan' : 'Aktifkan Pelanggan' ?>">
                                                        <span class="material-symbols-outlined text-lg"><?= $customer['is_active'] ? 'person_off' : 'person_check' ?></span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info & Controls -->
                <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
                    <div class="flex items-center gap-4">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalCustomers ?></span> entries
                        </div>
                    </div>

                    <!-- Pagination Navigation -->
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

    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-lg shadow-2xl">
    </div>

    <!-- Status Change Modal - Consistent with Edit Pelanggan Modal -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeStatusModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl" id="statusModalIcon">sync_alt</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="statusModalTitle">Ubah Status Pelanggan</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="statusModalSubtitle">Konfirmasi perubahan status</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeStatusModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Customer Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-24 h-24 rounded-xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-[#882426]/20">
                            <img id="statusCustomerImage" src="" alt="Customer"
                                class="w-full h-full object-cover"
                                onerror="this.src='../../assets/img/profil/default-customer.jpg'">
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="statusCustomerName"></p>
                            <p class="text-sm text-gray-500" id="statusCustomerId"></p>
                        </div>
                    </div>

                    <!-- Action Description -->
                    <div class="p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                            <div>
                                <p class="text-sm text-gray-700" id="statusActionText"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#882426]/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#882426] text-lg">mail</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Email</p>
                                <p class="text-sm font-semibold text-gray-800 truncate" id="statusCustomerEmail"></p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-[#882426]/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[#882426] text-lg">swap_horiz</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Status Baru</p>
                                <span id="statusNewBadge" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeStatusModal()"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="button" onclick="executeStatusChange()" id="statusConfirmBtn"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                        <span class="material-symbols-outlined text-lg">check_circle</span>
                        Ya, Ubah Status!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Customer Modal - Consistent with ReturnAdmin.php -->
    <div id="customerModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl" id="modalIcon">person_add</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modalTitle">Tambah Pelanggan</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="modalSubtitle">Isi data pelanggan baru</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="customerForm" method="POST" action="../../app/controllers/CustomerController.php">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id_customer" id="customerId">

                    <div class="p-6 bg-gray-50 space-y-5 max-h-[60vh] overflow-y-auto" id="customerFormContent">
                        <!-- Info Alert -->
                        <div class="p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium" id="formInfoText">Lengkapi data pelanggan dengan benar untuk proses registrasi.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">badge</span>
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_lengkap" id="namaLengkap" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="Masukkan nama lengkap pelanggan...">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">mail</span>
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="contoh@email.com">
                        </div>

                        <!-- No. Telepon -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">phone</span>
                                No. Telepon
                            </label>
                            <input type="text" name="no_telp" id="noTelp"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                placeholder="08xxxxxxxxxx">
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">lock</span>
                                Password <span id="passwordNote" class="text-xs font-normal text-gray-500">(wajib untuk pelanggan baru)</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Masukkan password...">
                                <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <span class="material-symbols-outlined text-lg" id="togglePasswordIcon">visibility_off</span>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">lightbulb</span>
                                Minimal 6 karakter kombinasi huruf dan angka
                            </p>
                        </div>

                        <!-- Status Aktif -->
                        <div class="p-4 bg-white rounded-xl border-2 border-gray-200">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="is_active" id="isActive" value="1" checked
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-[#882426] transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-all duration-300"></div>
                                </div>
                                <div>
                                    <span class="text-sm font-semibold text-gray-700">Status Aktif</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Pelanggan dapat login dan bertransaksi</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                            <span class="material-symbols-outlined text-lg">close</span>
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            <span id="submitBtnText">Simpan</span>
                        </button>
                    </div>
                </form>
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

        .animate-modal-in {
            animation: modal-in 0.3s ease-out forwards;
        }

        /* Custom Scrollbar for Modal */
        #customerFormContent::-webkit-scrollbar {
            width: 6px;
        }

        #customerFormContent::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #customerFormContent::-webkit-scrollbar-thumb {
            background: #882426;
            border-radius: 10px;
        }

        #customerFormContent::-webkit-scrollbar-thumb:hover {
            background: #6d1a1c;
        }

        /* Custom Toggle Switch */
        #isActive:checked+div {
            background-color: #882426;
        }

        #isActive:checked~div:last-child {
            transform: translateX(20px);
        }
    </style>

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

        document.getElementById('lightbox')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility_off';
            }
        }

        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
            document.getElementById('modalSubtitle').textContent = 'Isi data pelanggan baru';
            document.getElementById('modalIcon').textContent = 'person_add';
            document.getElementById('formInfoText').textContent = 'Lengkapi data pelanggan dengan benar untuk proses registrasi.';
            document.getElementById('submitBtnText').textContent = 'Simpan';
            document.getElementById('formAction').value = 'add';
            document.getElementById('customerId').value = '';
            document.getElementById('namaLengkap').value = '';
            document.getElementById('email').value = '';
            document.getElementById('noTelp').value = '';
            document.getElementById('password').value = '';
            document.getElementById('password').required = true;
            document.getElementById('password').type = 'password';
            document.getElementById('togglePasswordIcon').textContent = 'visibility_off';
            document.getElementById('passwordNote').textContent = '(wajib untuk pelanggan baru)';
            document.getElementById('isActive').checked = true;
            document.getElementById('customerModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function openEditModal(customer) {
            document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
            document.getElementById('modalSubtitle').textContent = 'Perbarui data pelanggan';
            document.getElementById('modalIcon').textContent = 'edit';
            document.getElementById('formInfoText').textContent = 'Ubah data pelanggan sesuai kebutuhan. Password kosongkan jika tidak ingin mengubah.';
            document.getElementById('submitBtnText').textContent = 'Update';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('customerId').value = customer.id_customer;
            document.getElementById('namaLengkap').value = customer.nama_lengkap;
            document.getElementById('email').value = customer.email;
            document.getElementById('noTelp').value = customer.no_telp || '';
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('password').type = 'password';
            document.getElementById('togglePasswordIcon').textContent = 'visibility_off';
            document.getElementById('passwordNote').textContent = '(kosongkan jika tidak ingin mengubah)';
            document.getElementById('isActive').checked = customer.is_active == 1;
            document.getElementById('customerModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('customerModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('customerModal');
                if (!modal.classList.contains('hidden')) {
                    closeModal();
                }
            }
        });

        // Status Modal Variables
        let currentStatusCustomer = null;
        let newStatusValue = null;

        function confirmToggleStatus(customer) {
            currentStatusCustomer = customer;

            const profileImage = customer.profile_image ?
                '../../uploads/customers/' + customer.profile_image :
                '../../assets/img/profil/default-customer.jpg';

            const actionText = customer.is_active ? 'menonaktifkan' : 'mengaktifkan';
            const statusLabel = customer.is_active ? 'Nonaktif' : 'Aktif';
            newStatusValue = customer.is_active ? 0 : 1;

            // Update modal icon based on action
            const modalIcon = document.getElementById('statusModalIcon');
            modalIcon.textContent = customer.is_active ? 'person_off' : 'person_check';

            // Update modal subtitle
            document.getElementById('statusModalSubtitle').textContent = customer.is_active ? 'Nonaktifkan pelanggan' : 'Aktifkan pelanggan';

            // Update customer info
            document.getElementById('statusCustomerImage').src = profileImage;
            document.getElementById('statusCustomerName').textContent = customer.nama_lengkap;
            document.getElementById('statusCustomerId').textContent = 'ID: ' + customer.id_customer;
            document.getElementById('statusCustomerEmail').textContent = customer.email;
            document.getElementById('statusActionText').innerHTML = 'Anda yakin ingin <strong>' + actionText + '</strong> pelanggan ini? Perubahan status akan mempengaruhi akses login pelanggan ke sistem.';

            // Update status badge
            const statusBadge = document.getElementById('statusNewBadge');
            if (newStatusValue) {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1 bg-emerald-100 text-emerald-700';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>' + statusLabel;
            } else {
                statusBadge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mt-1 bg-red-100 text-red-700';
                statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>' + statusLabel;
            }

            // Show modal
            document.getElementById('statusModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentStatusCustomer = null;
            newStatusValue = null;
        }

        function executeStatusChange() {
            if (!currentStatusCustomer) return;

            const confirmBtn = document.getElementById('statusConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Memproses...';

            fetch('../../app/controllers/CustomerController.php?action=toggleStatus&id=' + currentStatusCustomer.id_customer + '&status=' + newStatusValue)
                .then(response => response.json())
                .then(data => {
                    closeStatusModal();
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status pelanggan telah diubah',
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
                    closeStatusModal();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengubah status pelanggan',
                        icon: 'error',
                        confirmButtonColor: '#882426'
                    });
                })
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> Ya, Ubah Status!';
                });
        }

        // Close status modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const statusModal = document.getElementById('statusModal');
                if (statusModal && !statusModal.classList.contains('hidden')) {
                    closeStatusModal();
                }
            }
        });
    </script>
</body>

</html>