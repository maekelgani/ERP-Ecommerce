<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/BrandRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\BrandRepository;

AuthMiddleware::requireAdminLoginFromView();

$brandRepo = new BrandRepository();

$filters = [
    'search' => $_GET['search'] ?? ''
];

// Pagination logic
$perPage = (int)($_GET['per_page'] ?? 10);
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $perPage;

// Get all brands for filtering/search
$allBrands = $brandRepo->getAll($filters);
$totalBrands = count($allBrands);
$totalPages = (int)ceil($totalBrands / $perPage);

// Get paginated brands
$brands = array_slice($allBrands, $offset, $perPage);

// Calculate display range
$startEntry = $totalBrands > 0 ? $offset + 1 : 0;
$endEntry = min($offset + $perPage, $totalBrands);

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$pageTitle = "Manajemen Brand";
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
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manajemen Brand Produk</h1>
                        <p class="text-gray-500 text-sm md:text-base mt-1">Kelola semua brand produk yang tersedia</p>
                    </div>
                </div>
            </div>

            <!-- Flash alerts are now handled by toast notifications -->

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">storefront</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Total Brand</p>
                            <p class="text-2xl font-bold text-gray-800"><?= $totalBrands ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Brand Aktif</p>
                            <p class="text-2xl font-bold text-gray-800"><?= count(array_filter($brands, fn($b) => $b['total_products'] > 0)) ?></p>
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
                            <p class="text-2xl font-bold text-gray-800"><?= array_sum(array_column($brands, 'total_products')) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Brand</h2>
                            <p class="text-gray-500 text-sm mt-1">Menampilkan <?= count($brands) ?> brand</p>
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
                            <div class="flex flex-wrap items-center gap-3 justify-end">

                                <form method="GET" class="flex gap-2">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                        <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
                                            placeholder="Cari brand..."
                                            class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none w-64">
                                    </div>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                        <span class="material-symbols-outlined text-base">search</span>
                                        Cari
                                    </button>
                                    <?php if (!empty($filters['search'])): ?>
                                        <a href="BrandAdmin.php" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors whitespace-nowrap">
                                            <span class="material-symbols-outlined text-base">close</span>
                                            Reset
                                        </a>
                                    <?php endif; ?>
                                </form>

                                <a href="add-brand.php" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    <span>Tambah Brand</span>
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
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Logo</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Brand</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Brand</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Website</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Produk</th>
                                <th class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($brands)): ?>
                                <tr>
                                    <td colspan="8" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">storefront</span>
                                            <p class="text-gray-500 font-medium">Belum ada brand</p>
                                            <p class="text-gray-400 text-sm mt-1">Tambahkan brand pertama Anda</p>
                                            <a href="add-brand.php" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-[#882426] bg-red-50 rounded-lg font-medium hover:bg-red-100 transition-colors">
                                                <span class="material-symbols-outlined text-lg">add</span>
                                                Tambah Brand
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $nomor = $offset + 1; ?>
                                <?php foreach ($brands as $brand): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-5 py-4 text-center font-semibold text-gray-800 w-12"><?= $nomor++ ?></td>
                                        <td class="px-5 py-4">
                                            <?php
                                            $logoPath = !empty($brand['logo_brand']) ? '../../uploads/brands/' . htmlspecialchars($brand['logo_brand']) : '';
                                            ?>
                                            <?php if (!empty($brand['logo_brand'])): ?>
                                                <div class="inline-block">
                                                    <div class="relative group w-12 h-12 cursor-pointer rounded-lg overflow-hidden" onclick="openLightbox('<?= $logoPath ?>')" title="Klik untuk preview besar">
                                                        <img src="<?= $logoPath ?>"
                                                            alt="<?= htmlspecialchars($brand['nama_brand']) ?>"
                                                            class="w-full h-full object-contain border border-gray-200 bg-white"
                                                            onerror="this.src='data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%239ca3af%22 font-size=%228%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-white text-sm">zoom_in</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-gray-500 text-xl">image</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-semibold bg-gray-100 text-gray-700">
                                                <?= htmlspecialchars($brand['id_brand']) ?>
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($brand['nama_brand']) ?></span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-gray-500 text-sm line-clamp-2 max-w-xs">
                                                <?= !empty($brand['desc_brand']) ? htmlspecialchars($brand['desc_brand']) : '<span class="text-gray-400 italic">Tidak ada deskripsi</span>' ?>
                                            </p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <?php if (!empty($brand['website'])): ?>
                                                <a href="<?= htmlspecialchars($brand['website']) ?>" target="_blank"
                                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm">
                                                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                                                    Website
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-sm italic">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <?php if ($brand['total_products'] > 0): ?>
                                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                                                    <?= $brand['total_products'] ?> Produk
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
                                                <a href="edit-brand.php?id=<?= $brand['id_brand'] ?>"
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors"
                                                    title="Edit Brand">
                                                    <span class="material-symbols-outlined text-lg">edit</span>
                                                    Edit
                                                </a>
                                                <?php if ($brand['total_products'] > 0): ?>
                                                    <button type="button"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-400 bg-gray-100 rounded-lg text-sm font-medium cursor-not-allowed"
                                                        title="Tidak dapat dihapus - masih memiliki produk"
                                                        disabled>
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                        Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <?php
                                                    // Encode brand data as JSON for safe JavaScript handling
                                                    $brandData = json_encode([
                                                        'id' => $brand['id_brand'],
                                                        'name' => $brand['nama_brand'],
                                                        'logo' => $brand['logo_brand'] ?? '',
                                                        'description' => $brand['desc_brand'] ?? '',
                                                        'website' => $brand['website'] ?? ''
                                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                                    ?>
                                                    <button type="button"
                                                        data-brand='<?= $brandData ?>'
                                                        onclick="handleDeleteClick(this)"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors"
                                                        title="Hapus Brand">
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
                        Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalBrands ?></span> entries
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

    <!-- Delete Brand Modal - Consistent with ProductAdmin.php -->
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
                            <h3 class="text-xl font-bold text-white">Hapus Brand</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan data</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Brand Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div id="deleteBrandImageContainer" class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-white p-2">
                            <img id="deleteBrandImage" src="" alt="Brand"
                                class="w-full h-full object-contain"
                                onerror="this.style.display='none'; document.getElementById('deleteBrandImageFallback').style.display='flex';">
                        </div>
                        <div id="deleteBrandImageFallback" class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-gradient-to-br from-gray-100 to-gray-200 items-center justify-center" style="display: none;">
                            <span class="material-symbols-outlined text-5xl text-gray-400">storefront</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteBrandName"></p>
                            <p class="text-sm text-gray-500" id="deleteBrandId"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600">Anda yakin ingin menghapus brand ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait brand.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Details Card -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">label</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Brand</p>
                                <p class="text-sm font-semibold text-gray-800" id="deleteBrandNameDetail">-</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">description</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Deskripsi</p>
                                <p class="text-sm font-semibold text-gray-800 line-clamp-2" id="deleteBrandDesc">-</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">language</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Website</p>
                                <p class="text-sm font-semibold text-gray-800 truncate" id="deleteBrandWebsite">-</p>
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
                        Ya, Hapus Brand!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal for Image Preview -->
    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
        <div class="relative max-w-2xl w-full">
            <div class="bg-white rounded-lg overflow-hidden shadow-2xl h-[80vh] flex items-center justify-center">
                <img id="lightbox-image" src="" alt="Brand Logo Preview" class="max-w-full max-h-full object-contain p-8">
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
        let currentDeleteBrand = null;

        // Handle delete button click - parse JSON data from data attribute
        function handleDeleteClick(button) {
            try {
                const brandData = JSON.parse(button.getAttribute('data-brand'));
                if (brandData && brandData.id) {
                    confirmDelete(brandData.id, brandData.name, brandData.logo, brandData.description, brandData.website);
                } else {
                    console.error('Invalid brand data:', brandData);
                    showToast('error', 'Error!', 'Data brand tidak valid.');
                }
            } catch (e) {
                console.error('Error parsing brand data:', e);
                showToast('error', 'Error!', 'Gagal memproses data brand.');
            }
        }

        function confirmDelete(id, name, logo = '', description = '', website = '') {
            currentDeleteBrand = {
                id: id,
                name: name
            };

            // Get DOM elements
            const imageEl = document.getElementById('deleteBrandImage');
            const imageContainer = document.getElementById('deleteBrandImageContainer');
            const fallbackContainer = document.getElementById('deleteBrandImageFallback');
            const deleteModal = document.getElementById('deleteModal');

            // Safety check for modal existence
            if (!deleteModal) {
                console.error('Delete modal not found');
                showToast('error', 'Error!', 'Modal tidak ditemukan.');
                return;
            }

            // Reset image containers visibility
            if (logo && logo.trim() !== '') {
                imageEl.src = '../../uploads/brands/' + logo;
                imageEl.style.display = 'block';
                imageContainer.style.display = 'block';
                fallbackContainer.style.display = 'none';
            } else {
                imageEl.style.display = 'none';
                imageContainer.style.display = 'none';
                fallbackContainer.style.display = 'flex';
            }

            // Set brand info - using textContent for XSS safety
            document.getElementById('deleteBrandName').textContent = name || '-';
            document.getElementById('deleteBrandId').textContent = 'ID: ' + (id || '-');
            document.getElementById('deleteBrandNameDetail').textContent = name || '-';
            document.getElementById('deleteBrandDesc').textContent = description || 'Tidak ada deskripsi';
            document.getElementById('deleteBrandWebsite').textContent = website || 'Tidak ada website';

            // Reset checkbox and button
            const confirmCheck = document.getElementById('deleteConfirmCheck');
            const confirmBtn = document.getElementById('deleteConfirmBtn');
            if (confirmCheck) confirmCheck.checked = false;
            if (confirmBtn) confirmBtn.disabled = true;

            // Show modal with animation
            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentDeleteBrand = null;
        }

        // Enable/disable delete button based on checkbox
        document.getElementById('deleteConfirmCheck').addEventListener('change', function() {
            document.getElementById('deleteConfirmBtn').disabled = !this.checked;
        });

        function executeDelete() {
            if (!currentDeleteBrand) return;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menghapus...';

            fetch(`../../app/controllers/brandController.php?action=delete&id=${currentDeleteBrand.id}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    closeDeleteModal();
                    if (data.success) {
                        showToast('success', 'Berhasil Dihapus!', data.message || 'Brand berhasil dihapus dari sistem.');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast('error', 'Gagal Menghapus!', data.message || 'Terjadi kesalahan saat menghapus brand.');
                    }
                })
                .catch(error => {
                    closeDeleteModal();
                    showToast('error', 'Error!', 'Terjadi kesalahan saat menghapus brand.');
                })
                .finally(() => {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">delete_forever</span> Ya, Hapus Brand!';
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