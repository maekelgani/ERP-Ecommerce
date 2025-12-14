<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CategoryRepository.php';
require_once __DIR__ . '/../../app/Repository/BrandRepository.php';
require_once __DIR__ . '/../../app/Repository/ProductRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\CategoryRepository;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;

AuthMiddleware::requireAdminLoginFromView();

$categoryRepo = new CategoryRepository();
$brandRepo = new BrandRepository();
$productRepo = new ProductRepository();

$filters = [
    'kategori' => $_GET['kategori'] ?? '',
    'brand' => $_GET['brand'] ?? '',
    'status' => $_GET['status'] ?? '',
    'search' => $_GET['search'] ?? '',
    'sort' => $_GET['sort'] ?? 'newest'
];

// Pagination logic
$perPage = isset($_GET['per_page']) ? min(intval($_GET['per_page']), 100) : 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Get all products with filters
$allProducts = $productRepo->getAll($filters);
$totalProducts = count($allProducts);
$totalPages = ceil($totalProducts / $perPage);
$currentPage = min($currentPage, max(1, $totalPages));

// Calculate pagination bounds
$offset = ($currentPage - 1) * $perPage;
$products = array_slice($allProducts, $offset, $perPage);

$startEntry = $totalProducts === 0 ? 0 : $offset + 1;
$endEntry = min($offset + $perPage, $totalProducts);

// Get stats
$allFiltered = $productRepo->getAll($filters);
$categories = $categoryRepo->getAll();
$brands = $brandRepo->getAll();

$totalCount = $productRepo->count();
$activeProducts = $productRepo->countByStatus('tersedia');
$lowStockProducts = $productRepo->countLowStock(9);
$outOfStockProducts = $productRepo->countOutOfStock();

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// View state (grid or table)
$currentView = $_GET['view'] ?? 'table';

$pageTitle = "Inventori Produk";
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
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Inventori Produk</h1>
                <p class="text-gray-500 text-sm md:text-base mt-1">Kelola semua produk yang tersedia di penyimpanan</p>
            </div>

            <?php if ($flashSuccess): ?>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center gap-3" id="successAlert">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <p class="text-emerald-700 flex-1"><?= htmlspecialchars($flashSuccess) ?></p>
                    <button onclick="document.getElementById('successAlert').remove()" class="text-emerald-500 hover:text-emerald-700">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3" id="errorAlert">
                    <span class="material-symbols-outlined text-red-500">error</span>
                    <p class="text-red-700 flex-1"><?= htmlspecialchars($flashError) ?></p>
                    <button onclick="document.getElementById('errorAlert').remove()" class="text-red-500 hover:text-red-700">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #882426 0%, #a83236 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $totalCount ?></p>
                            <p class="text-xs text-gray-500">Total Produk</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $activeProducts ?></p>
                            <p class="text-xs text-gray-500">Produk Aktif</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">warning</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $lowStockProducts ?></p>
                            <p class="text-xs text-gray-500">Stok Menipis</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-red-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-2xl">remove_shopping_cart</span>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-800"><?= $outOfStockProducts ?></p>
                            <p class="text-xs text-gray-500">Stok Habis</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold text-gray-800">Daftar Produk</h2>
                            <p class="text-xs text-gray-500">Menampilkan <?= count($products) ?> produk</p>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="flex items-center gap-1 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200">
                                <select id="per-page-select" onchange="changePerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>

                            <a href="add-product.php" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-white font-medium text-sm rounded-lg shadow transition-all duration-300 hover:shadow-lg active:scale-95 whitespace-nowrap"
                                style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                <span>Tambah Porduk</span>
                            </a>

                            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                                <button id="view-table" class="view-toggle px-2 py-1.5 rounded-md text-sm font-medium transition-all active"
                                    style="background: #882426; color: white;">
                                    <span class="material-symbols-outlined text-base align-middle">table_rows</span>
                                </button>
                                <button id="view-grid" class="view-toggle px-2 py-1.5 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-200 transition-all">
                                    <span class="material-symbols-outlined text-base align-middle">grid_view</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="GET" class="p-4 md:p-6 bg-gray-50/50 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row gap-4">
                        <div class="flex-1 relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                            <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" placeholder="Cari nama produk atau brand..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all" />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <select name="kategori" class="pl-4 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all min-w-[140px] appearance-none bg-no-repeat bg-[length:16px_16px] bg-[right_12px_center]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E');">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_kategori'] ?>" <?= $filters['kategori'] === $cat['id_kategori'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="brand" class="pl-4 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all min-w-[130px] appearance-none bg-no-repeat bg-[length:16px_16px] bg-[right_12px_center]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E');">
                                <option value="">Semua Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?= $brand['id_brand'] ?>" <?= $filters['brand'] === $brand['id_brand'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($brand['nama_brand']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="status" class="pl-4 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all min-w-[130px] appearance-none bg-no-repeat bg-[length:16px_16px] bg-[right_12px_center]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E');">
                                <option value="">Semua Status</option>
                                <option value="tersedia" <?= $filters['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                <option value="habis" <?= $filters['status'] === 'habis' ? 'selected' : '' ?>>Habis</option>
                                <option value="nonaktif" <?= $filters['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>

                            <select name="sort" class="pl-4 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all min-w-[130px] appearance-none bg-no-repeat bg-[length:16px_16px] bg-[right_12px_center]" style="background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b7280%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E');">
                                <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                <option value="oldest" <?= $filters['sort'] === 'oldest' ? 'selected' : '' ?>>Terlama</option>
                                <option value="price-asc" <?= $filters['sort'] === 'price-asc' ? 'selected' : '' ?>>Harga Terendah</option>
                                <option value="price-desc" <?= $filters['sort'] === 'price-desc' ? 'selected' : '' ?>>Harga Tertinggi</option>
                                <option value="stock-asc" <?= $filters['sort'] === 'stock-asc' ? 'selected' : '' ?>>Stok Terendah</option>
                                <option value="stock-desc" <?= $filters['sort'] === 'stock-desc' ? 'selected' : '' ?>>Stok Tertinggi</option>
                            </select>

                            <button type="submit" class="px-5 py-2.5 text-white font-medium rounded-lg transition-all duration-300 hover:shadow-lg active:scale-95 flex items-center gap-2"
                                style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                                <span class="material-symbols-outlined text-lg">filter_alt</span>
                                <span class="hidden sm:inline">Filter</span>
                            </button>

                            <a href="ProductAdmin.php" class="px-4 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-all active:scale-95 flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">refresh</span>
                            </a>
                        </div>
                    </div>
                </form>

                <div id="table-view">
                    <div class="hidden md:block overflow-x-auto scrollbar-hide">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Kategori</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                                    <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Status</th>
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                                                <p class="text-gray-500 font-medium">Belum ada produk</p>
                                                <p class="text-gray-400 text-sm mb-4">Mulai tambahkan produk pertama Anda</p>
                                                <a href="add-product.php" class="px-4 py-2 text-white rounded-lg text-sm font-medium" style="background: #882426;">
                                                    Tambah Produk
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $nomor = $offset + 1; ?>
                                    <?php foreach ($products as $product): ?>
                                        <?php
                                        $stockStatus = $productRepo->getStockStatus($product['stok']);
                                        $productStatus = $productRepo->getProductStatus($product['status_produk']);
                                        $imageSrc = $product['gambar']
                                            ? '../../uploads/products/' . htmlspecialchars($product['gambar'])
                                            : '../../assets/img/products/default-product.jpg';
                                        $isUsedInOrders = $productRepo->isUsedInOrders($product['id_product']);
                                        ?>
                                        <tr class="hover:bg-gray-50/50 transition-colors" data-id="<?= $product['id_product'] ?>">
                                            <td class="px-4 py-4 text-center font-semibold text-gray-800 w-12"><?= $nomor++ ?></td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="relative group cursor-pointer" onclick="openLightbox('<?= $imageSrc ?>')">
                                                        <img class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-100 transition-transform group-hover:scale-105"
                                                            src="<?= $imageSrc ?>"
                                                            alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                                            onerror="this.src='../../assets/img/products/default-product.jpg'">
                                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-white">zoom_in</span>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="font-semibold text-gray-800 text-sm truncate max-w-[200px]"><?= htmlspecialchars($product['nama_product']) ?></p>
                                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($product['nama_brand'] ?? '-') ?></p>
                                                        <p class="text-xs text-gray-400 font-mono"><?= $product['id_product'] ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 hidden lg:table-cell">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                    <?= htmlspecialchars($product['nama_kategori'] ?? '-') ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <p class="font-semibold text-gray-800">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-col gap-1">
                                                    <span class="font-semibold text-gray-800"><?= $product['stok'] ?></span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium <?= $stockStatus['class'] ?>">
                                                        <span class="w-1.5 h-1.5 rounded-full <?= $stockStatus['dot_class'] ?> mr-1"></span>
                                                        <?= $stockStatus['label'] ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 hidden sm:table-cell">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $productStatus['class'] ?>">
                                                    <span class="w-1.5 h-1.5 rounded-full <?= $productStatus['dot_class'] ?> mr-1.5"></span>
                                                    <?= $productStatus['label'] ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="product-details.php?id=<?= $product['id_product'] ?>"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-purple-600 bg-purple-50 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors"
                                                        title="Lihat Detail">
                                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                                        <span class="hidden xl:inline">Details</span>
                                                    </a>
                                                    <a href="edit-product.php?id=<?= $product['id_product'] ?>"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors"
                                                        title="Edit">
                                                        <span class="material-symbols-outlined text-lg">edit</span>
                                                        <span class="hidden xl:inline">Edit</span>
                                                    </a>
                                                    <?php if ($isUsedInOrders): ?>
                                                        <button type="button"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-400 bg-gray-100 rounded-lg text-sm font-medium cursor-not-allowed"
                                                            title="Tidak dapat dihapus - produk sudah digunakan dalam pesanan"
                                                            disabled>
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                            <span class="hidden xl:inline">Hapus</span>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button"
                                                            onclick="confirmDelete('<?= $product['id_product'] ?>', '<?= htmlspecialchars(addslashes($product['nama_product'])) ?>')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors"
                                                            title="Hapus">
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                            <span class="hidden xl:inline">Hapus</span>
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
                            Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalProducts ?></span> entries
                        </div>

                        <!-- Pagination Navigation - Always Show -->
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1, 'per_page' => $perPage, 'view' => 'table'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
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
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'per_page' => $perPage, 'view' => 'table'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'per_page' => $perPage, 'view' => 'table'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages, 'per_page' => $perPage, 'view' => 'table'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $totalPages ?></a>
                            <?php endif; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1, 'per_page' => $perPage, 'view' => 'table'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                    <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                                </a>
                            <?php else: ?>
                                <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                    <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="md:hidden p-4 space-y-4" id="mobile-list">
                        <?php if (empty($products)): ?>
                            <div class="text-center py-12">
                                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                                <p class="text-gray-500 font-medium">Belum ada produk</p>
                                <a href="add-product.php" class="mt-4 inline-block px-4 py-2 text-white rounded-lg text-sm font-medium" style="background: #882426;">
                                    Tambah Produk
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <?php
                                $stockStatus = $productRepo->getStockStatus($product['stok']);
                                $productStatus = $productRepo->getProductStatus($product['status_produk']);
                                $imageSrc = $product['gambar']
                                    ? '../../uploads/products/' . htmlspecialchars($product['gambar'])
                                    : '../../assets/img/products/default-product.jpg';
                                $isUsedInOrders = $productRepo->isUsedInOrders($product['id_product']);
                                ?>
                                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                                    <div class="flex gap-4">
                                        <div class="relative cursor-pointer" onclick="openLightbox('<?= $imageSrc ?>')">
                                            <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                                class="w-20 h-20 object-cover rounded-lg shadow-sm"
                                                onerror="this.src='../../assets/img/products/default-product.jpg'">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white">zoom_in</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-800 text-sm truncate"><?= htmlspecialchars($product['nama_product']) ?></p>
                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($product['nama_brand'] ?? '-') ?></p>
                                            <p class="text-xs text-gray-400 font-mono"><?= $product['id_product'] ?></p>
                                            <p class="font-bold text-[#882426] mt-1">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $stockStatus['class'] ?>">
                                                Stok: <?= $product['stok'] ?>
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $productStatus['class'] ?>">
                                                <?= $productStatus['label'] ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <a href="product-details.php?id=<?= $product['id_product'] ?>" class="p-2 rounded-lg hover:bg-purple-50 text-purple-600 transition-colors">
                                                <span class="material-symbols-outlined text-xl">visibility</span>
                                            </a>
                                            <a href="edit-product.php?id=<?= $product['id_product'] ?>" class="p-2 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors">
                                                <span class="material-symbols-outlined text-xl">edit</span>
                                            </a>
                                            <?php if (!$isUsedInOrders): ?>
                                                <button onclick="confirmDelete('<?= $product['id_product'] ?>', '<?= htmlspecialchars(addslashes($product['nama_product'])) ?>')"
                                                    class="p-2 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                                                    <span class="material-symbols-outlined text-xl">delete</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="grid-view" class="hidden">
                    <div class="p-4 md:p-6">
                        <?php if (empty($products)): ?>
                            <div class="text-center py-12">
                                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                                <p class="text-gray-500 font-medium">Belum ada produk</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $stockStatus = $productRepo->getStockStatus($product['stok']);
                                    $productStatus = $productRepo->getProductStatus($product['status_produk']);
                                    $imageSrc = $product['gambar']
                                        ? '../../uploads/products/' . htmlspecialchars($product['gambar'])
                                        : '../../assets/img/products/default-product.jpg';
                                    $isUsedInOrders = $productRepo->isUsedInOrders($product['id_product']);
                                    ?>
                                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                                        <div class="relative aspect-square cursor-pointer" onclick="openLightbox('<?= $imageSrc ?>')">
                                            <img src="<?= $imageSrc ?>" alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                                class="w-full h-full object-cover"
                                                onerror="this.src='../../assets/img/products/default-product.jpg'">
                                            <div class="absolute top-2 right-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium <?= $stockStatus['class'] ?> shadow-sm">
                                                    <?= $stockStatus['label'] ?>
                                                </span>
                                            </div>
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                                            </div>
                                        </div>
                                        <div class="p-3">
                                            <p class="font-semibold text-gray-800 text-sm truncate"><?= htmlspecialchars($product['nama_product']) ?></p>
                                            <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($product['nama_brand'] ?? '-') ?></p>
                                            <p class="font-bold text-[#882426] mt-1">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                                                <span class="text-xs text-gray-500">Stok: <?= $product['stok'] ?></span>
                                                <div class="flex items-center gap-0.5">
                                                    <a href="product-details.php?id=<?= $product['id_product'] ?>" class="p-1.5 rounded-lg hover:bg-purple-50 text-purple-600 transition-colors">
                                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                                    </a>
                                                    <a href="edit-product.php?id=<?= $product['id_product'] ?>" class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors">
                                                        <span class="material-symbols-outlined text-lg">edit</span>
                                                    </a>
                                                    <?php if (!$isUsedInOrders): ?>
                                                        <button onclick="confirmDelete('<?= $product['id_product'] ?>', '<?= htmlspecialchars(addslashes($product['nama_product'])) ?>')"
                                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                                                            <span class="material-symbols-outlined text-lg">delete</span>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination Info & Controls for Grid View -->
                    <div class="px-4 md:px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div class="text-sm text-gray-600">
                            Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalProducts ?></span> entries
                        </div>

                        <!-- Pagination Navigation - Always Show -->
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1, 'per_page' => $perPage, 'view' => 'grid'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
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
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'per_page' => $perPage, 'view' => 'grid'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                                <?php if ($startPage > 2): ?>
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <?php if ($i === $currentPage): ?>
                                    <button class="px-3 py-2 rounded-lg text-white font-medium" style="background: #882426;"><?= $i ?></button>
                                <?php else: ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'per_page' => $perPage, 'view' => 'grid'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <span class="px-2 py-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages, 'per_page' => $perPage, 'view' => 'grid'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $totalPages ?></a>
                            <?php endif; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1, 'per_page' => $perPage, 'view' => 'grid'])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
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
            </div>
        </main>
    </div>

    <div id="lightbox" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-10">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>
        <img id="lightbox-image" src="" alt="Preview" class="max-w-[90%] max-h-[85vh] object-contain rounded-lg shadow-2xl">
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

        // Get current view from URL parameter
        const currentView = '<?= $currentView ?>';
        const currentPageUrl = new URLSearchParams(window.location.search).get('page') || '1';

        // Save current page to localStorage for this view
        function saveCurrentPageToStorage() {
            if (currentView === 'grid') {
                localStorage.setItem('product_page_grid', currentPageUrl);
            } else {
                localStorage.setItem('product_page_table', currentPageUrl);
            }
        }

        // Initialize view on page load
        function initializeView() {
            // Save page for current view
            saveCurrentPageToStorage();

            if (currentView === 'grid') {
                document.getElementById('grid-view').classList.remove('hidden');
                document.getElementById('table-view').classList.add('hidden');
                document.getElementById('view-grid').style.background = '#882426';
                document.getElementById('view-grid').style.color = 'white';
                document.getElementById('view-table').style.background = '';
                document.getElementById('view-table').style.color = '';
            } else {
                document.getElementById('table-view').classList.remove('hidden');
                document.getElementById('grid-view').classList.add('hidden');
                document.getElementById('view-table').style.background = '#882426';
                document.getElementById('view-table').style.color = 'white';
                document.getElementById('view-grid').style.background = '';
                document.getElementById('view-grid').style.color = '';
            }
        }

        // Initialize view on page load
        document.addEventListener('DOMContentLoaded', initializeView);

        // Add view parameter to URLs when toggling views - use saved page for each view
        document.getElementById('view-table').addEventListener('click', function() {
            const params = new URLSearchParams(window.location.search);

            // Get the saved page for table view, or default to 1
            const savedTablePage = localStorage.getItem('product_page_table') || '1';

            params.set('view', 'table');
            params.set('page', savedTablePage);

            window.location.href = '?' + params.toString();
        });

        document.getElementById('view-grid').addEventListener('click', function() {
            const params = new URLSearchParams(window.location.search);

            // Get the saved page for grid view, or default to 1
            const savedGridPage = localStorage.getItem('product_page_grid') || '1';

            params.set('view', 'grid');
            params.set('page', savedGridPage);

            window.location.href = '?' + params.toString();
        });

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Produk?',
                html: `Anda yakin ingin menghapus produk <strong>"${name}"</strong>?<br><br><small class="text-gray-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#882426',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../../app/controllers/productController.php?action=delete&id=${id}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#882426'
                                }).then(() => {
                                    window.location.reload();
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
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus produk',
                                icon: 'error',
                                confirmButtonColor: '#882426'
                            });
                        });
                }
            });
        }

        setTimeout(() => {
            const alerts = document.querySelectorAll('#successAlert, #errorAlert');
            alerts.forEach(alert => {
                if (alert) {
                    alert.style.transition = 'opacity 0.3s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }
            });
        }, 5000);

        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1');
            window.location.href = '?' + params.toString();
        }
    </script>

    <style>
        /* Hidden scrollbar but still scrollable */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</body>

</html>