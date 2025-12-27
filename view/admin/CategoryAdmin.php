<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CategoryRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\CategoryRepository;

AuthMiddleware::requireAdminLoginFromView();

$categoryRepo = new CategoryRepository();

$filters = [
    'search' => $_GET['search'] ?? ''
];

// Pagination logic
$perPage = (int)($_GET['per_page'] ?? 10);
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $perPage;

// Get all categories for filtering/search
$allCategories = $categoryRepo->getAll($filters);
$totalCategories = count($allCategories);
$totalPages = (int)ceil($totalCategories / $perPage);

// Get paginated categories
$categories = array_slice($allCategories, $offset, $perPage);

// Calculate display range
$startEntry = $totalCategories > 0 ? $offset + 1 : 0;
$endEntry = min($offset + $perPage, $totalCategories);

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$pageTitle = "Manajemen Kategori";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manajemen Kategori</h1>
                    <p class="text-gray-500 text-sm md:text-base mt-1">Kelola semua kategori produk yang tersedia</p>
                </div>
            </div>

            <!-- Flash alerts are now handled by toast notifications -->

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">category</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Kategori</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $totalCategories ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Kategori Aktif</p>
                            <p class="text-2xl font-bold text-gray-800"><?= count(array_filter($categories, fn($c) => $c['total_products'] > 0)) ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Produk</p>
                            <p class="text-2xl font-bold text-gray-800"><?= array_sum(array_column($categories, 'total_products')) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Kategori</h2>
                            <p class="text-gray-500 text-sm mt-1">Menampilkan <?= count($categories) ?> kategori</p>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- LEFT: Entries per page -->
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">view_list</span>
                                <select id="per-page-select" onchange="changePerPage(this.value)"
                                    class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>

                            <!-- RIGHT: Search + Tambah Kategori -->
                            <div class="flex flex-wrap items-center gap-3 justify-end">
                                <!-- Search Form -->
                                <form method="GET" class="flex items-center gap-2">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            search
                                        </span>
                                        <input type="text" name="search"
                                            value="<?= htmlspecialchars($filters['search']) ?>"
                                            placeholder="Cari kategori..."
                                            class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm
                                                    focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]
                                                    focus:outline-none w-64">
                                    </div>

                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                        <span class="material-symbols-outlined text-base">search</span>
                                        Cari
                                    </button>

                                    <?php if (!empty($filters['search'])): ?>
                                        <a href="CategoryAdmin.php"
                                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors whitespace-nowrap">
                                            <span class="material-symbols-outlined text-base">close</span>
                                            Reset
                                        </a>
                                    <?php endif; ?>
                                </form>

                                <!-- Tambah Kategori -->
                                <a href="add-category.php"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-white font-medium text-sm rounded-lg shadow transition-all duration-300 hover:shadow-lg active:scale-95 whitespace-nowrap bg-[#882426] hover:bg-[#6d1d1f] transition-colors">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    <span>Tambah Kategori</span>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Kategori</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Produk</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">folder_off</span>
                                            <p class="text-gray-500 font-medium">Belum ada kategori</p>
                                            <p class="text-gray-400 text-sm mt-1">Tambahkan kategori pertama Anda</p>
                                            <a href="add-category.php" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-[#882426] bg-red-50 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                                <span class="material-symbols-outlined text-lg">add</span>
                                                Tambah Kategori
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $nomor = $offset + 1; ?>
                                <?php foreach ($categories as $category): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-4 text-center font-semibold text-gray-800 w-12"><?= $nomor++ ?></td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-700">
                                                <?= htmlspecialchars($category['id_kategori']) ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($category['icon_kategori'])): ?>
                                                    <?php $iconPath = '../../uploads/category/' . htmlspecialchars($category['icon_kategori']); ?>
                                                    <div class="relative group w-10 h-10 cursor-pointer rounded-lg overflow-hidden flex-shrink-0" onclick="openLightbox('<?= $iconPath ?>')" title="Klik untuk preview besar">
                                                        <img src="<?= $iconPath ?>" class="w-full h-full object-cover border border-gray-200 bg-white" alt="<?= htmlspecialchars($category['nama_kategori']) ?>" onerror="this.src='data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%239ca3af%22 font-size=%228%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-white text-sm">zoom_in</span>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center flex-shrink-0">
                                                        <span class="material-symbols-outlined text-gray-500 text-lg">image</span>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="font-semibold text-gray-800"><?= htmlspecialchars($category['nama_kategori']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-gray-500 text-sm line-clamp-2 max-w-xs">
                                                <?= !empty($category['deskripsi_kategori']) ? htmlspecialchars($category['deskripsi_kategori']) : '<span class="text-gray-400 italic">Tidak ada deskripsi</span>' ?>
                                            </p>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <?php if ($category['total_products'] > 0): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                                                    <?= $category['total_products'] ?> Produk
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                                                    0 Produk
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="edit-category.php?id=<?= $category['id_kategori'] ?>"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors"
                                                    title="Edit Kategori">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                    Edit
                                                </a>
                                                <?php if ($category['total_products'] > 0): ?>
                                                    <button type="button"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-400 bg-gray-100 rounded-lg text-sm font-medium cursor-not-allowed"
                                                        title="Tidak dapat dihapus - masih memiliki produk"
                                                        disabled>
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                        Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button"
                                                        onclick="confirmDelete('<?= $category['id_kategori'] ?>', '<?= htmlspecialchars($category['nama_kategori'], ENT_QUOTES) ?>', '<?= htmlspecialchars($category['icon_kategori'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($category['deskripsi_kategori'] ?? '', ENT_QUOTES) ?>')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors"
                                                        title="Hapus Kategori">
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                        Hapus
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
                <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalCategories ?></span> entries
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

    <!-- Delete Category Modal - Consistent with ProductAdmin.php -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Red/Danger Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl animate-pulse-warning">delete_forever</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hapus Kategori</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan data</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Category Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div id="deleteCategoryImageContainer" class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-white">
                            <img id="deleteCategoryImage" src="" alt="Category"
                                class="w-full h-full object-cover"
                                onerror="this.style.display='none'; document.getElementById('deleteCategoryImageFallback').style.display='flex';">
                        </div>
                        <div id="deleteCategoryImageFallback" class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-gradient-to-br from-gray-100 to-gray-200 items-center justify-center" style="display: none;">
                            <span class="material-symbols-outlined text-4xl text-gray-400">category</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteCategoryName"></p>
                            <p class="text-sm text-gray-500" id="deleteCategoryId"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600">Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait kategori.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Category Details Card -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">label</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Kategori</p>
                                <p class="text-sm font-semibold text-gray-800" id="deleteCategoryNameDetail">-</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">description</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Deskripsi</p>
                                <p class="text-sm font-semibold text-gray-800 line-clamp-2" id="deleteCategoryDesc">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox Confirmation -->
                    <label class="flex items-center gap-3 p-4 bg-white rounded-xl border-2 border-gray-200 cursor-pointer hover:border-red-300 transition-colors">
                        <input type="checkbox" id="deleteConfirmCheck" class="w-5 h-5 text-red-600 border-2 border-gray-300 rounded focus:ring-red-500 focus:ring-offset-0">
                        <span class="text-sm text-gray-700">Saya mengerti dan ingin melanjutkan penghapusan</span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Batal
                    </button>
                    <button type="button" onclick="executeDelete()" id="deleteConfirmBtn" disabled
                        class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg shadow-red-600/30 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span class="material-symbols-outlined text-lg">delete_forever</span>
                        Ya, Hapus Kategori!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox for Image Preview -->
    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
        <div class="relative max-w-2xl w-full">
            <div class="bg-white rounded-lg overflow-hidden shadow-2xl h-[80vh] flex items-center justify-center">
                <img id="lightbox-image" src="" alt="Category Icon Preview" class="max-w-full max-h-full object-contain p-8">
            </div>
        </div>
        <button onclick="closeLightbox()"
            class="fixed top-4 right-4 z-50 text-white hover:text-gray-300 transition-colors rounded-full p-2"
            title="Tutup (ESC)">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3"></div>

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

        @keyframes pulse-warning {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .animate-pulse-warning {
            animation: pulse-warning 1.5s ease-in-out infinite;
        }

        #deleteConfirmCheck:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }

        /* Toast Animations */
        @keyframes toast-in {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toast-out {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .toast-enter {
            animation: toast-in 0.4s ease-out forwards;
        }

        .toast-exit {
            animation: toast-out 0.3s ease-in forwards;
        }

        @keyframes progress-shrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .progress-animate {
            animation: progress-shrink linear forwards;
        }

        /* Circular Progress - runs from full to empty */
        @keyframes circular-progress {
            0% {
                stroke-dashoffset: 0;
            }

            100% {
                stroke-dashoffset: 100;
            }
        }

        .circular-progress {
            animation: circular-progress linear forwards;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toast Notification System with Progress Bar
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
                    progress: 'bg-emerald-500',
                    title: 'text-emerald-800',
                    progressCircle: '#10b981'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-red-200',
                    icon: 'error',
                    iconBg: 'bg-red-500',
                    iconColor: 'text-white',
                    progress: 'bg-red-500',
                    title: 'text-red-800',
                    progressCircle: '#ef4444'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    icon: 'warning',
                    iconBg: 'bg-amber-500',
                    iconColor: 'text-white',
                    progress: 'bg-amber-500',
                    title: 'text-amber-800',
                    progressCircle: '#f59e0b'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-blue-200',
                    icon: 'info',
                    iconBg: 'bg-blue-500',
                    iconColor: 'text-white',
                    progress: 'bg-blue-500',
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
                            <circle id="${id}-progress-circle" cx="18" cy="18" r="16" fill="none" stroke="${c.progressCircle}" stroke-width="2.5" 
                                stroke-dasharray="100" stroke-dashoffset="0" stroke-linecap="round"
                                class="circular-progress" style="animation-duration: ${duration}ms;"></circle>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold ${c.title}">${title}</p>
                        <p class="text-sm text-gray-600 mt-0.5">${message}</p>
                    </div>
                    <button onclick="removeToast('${id}')" class="flex-shrink-0 w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                removeToast(id);
            }, duration);
        }

        function removeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }
        }

        // Delete Modal Variables
        let currentDeleteCategory = null;

        function confirmDelete(id, name, icon = '', description = '') {
            currentDeleteCategory = {
                id,
                name
            };

            // Reset image containers visibility
            const imageEl = document.getElementById('deleteCategoryImage');
            const imageContainer = document.getElementById('deleteCategoryImageContainer');
            const fallbackContainer = document.getElementById('deleteCategoryImageFallback');

            if (icon && icon.trim() !== '') {
                imageEl.src = '../../uploads/category/' + icon;
                imageEl.style.display = 'block';
                imageContainer.style.display = 'block';
                fallbackContainer.style.display = 'none';
            } else {
                imageEl.style.display = 'none';
                imageContainer.style.display = 'none';
                fallbackContainer.style.display = 'flex';
            }

            // Set category info
            document.getElementById('deleteCategoryName').textContent = name;
            document.getElementById('deleteCategoryId').textContent = 'ID: ' + id;
            document.getElementById('deleteCategoryNameDetail').textContent = name;
            document.getElementById('deleteCategoryDesc').textContent = description || 'Tidak ada deskripsi';

            // Reset checkbox and button
            document.getElementById('deleteConfirmCheck').checked = false;
            document.getElementById('deleteConfirmBtn').disabled = true;

            // Show modal
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentDeleteCategory = null;
        }

        // Enable/disable delete button based on checkbox
        document.getElementById('deleteConfirmCheck').addEventListener('change', function() {
            document.getElementById('deleteConfirmBtn').disabled = !this.checked;
        });

        function executeDelete() {
            if (!currentDeleteCategory) return;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menghapus...';

            fetch(`../../app/controllers/categoryController.php?action=delete&id=${currentDeleteCategory.id}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    closeDeleteModal();
                    if (data.success) {
                        showToast('success', 'Berhasil Dihapus!', data.message || 'Kategori berhasil dihapus dari sistem.');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast('error', 'Gagal Menghapus!', data.message || 'Terjadi kesalahan saat menghapus kategori.');
                    }
                })
                .catch(error => {
                    closeDeleteModal();
                    showToast('error', 'Error!', 'Terjadi kesalahan saat menghapus kategori.');
                })
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">delete_forever</span> Ya, Hapus Kategori!';
                });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const deleteModal = document.getElementById('deleteModal');
                if (deleteModal && !deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
                closeLightbox();
            }
        });

        // Lightbox functions
        function openLightbox(imageSrc) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-image');
            img.src = imageSrc;
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
        }

        document.getElementById('lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1');
            window.location.href = '?' + params.toString();
        }

        // Show flash messages as toast on page load
        <?php if ($flashSuccess): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('success', 'Berhasil!', '<?= addslashes($flashSuccess) ?>');
            });
        <?php endif; ?>

        <?php if ($flashError): ?>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('error', 'Error!', '<?= addslashes($flashError) ?>');
            });
        <?php endif; ?>
    </script>
</body>

</html>