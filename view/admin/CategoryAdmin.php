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
                <div class="p-5 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Daftar Kategori</h2>
                            <p class="text-gray-500 text-sm">Menampilkan <?= count($categories) ?> kategori</p>
                        </div>
                        <div class="flex gap-2 items-center flex-wrap">
                            <form method="GET" class="flex gap-2">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                    <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>"
                                        placeholder="Cari kategori..."
                                        class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none w-64">
                                </div>
                                <button type="submit" class="px-4 py-2.5 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 transition-colors">
                                    Cari
                                </button>
                                <?php if (!empty($filters['search'])): ?>
                                    <a href="CategoryAdmin.php" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                        Reset
                                    </a>
                                <?php endif; ?>
                            </form>

                            <div class="flex items-center gap-1 bg-gray-100 px-3 py-2 rounded-lg border border-gray-200">
                                <select id="per-page-select" onchange="changePerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="text-sm text-gray-600">entries per page</span>
                            </div>

                            <a href="add-category.php" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-white font-medium text-sm rounded-lg shadow transition-all duration-300 hover:shadow-lg active:scale-95 whitespace-nowrap"
                                style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                <span>Tambah Kategori</span>
                            </a>
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
                                                        onclick="confirmDelete('<?= $category['id_kategori'] ?>', '<?= htmlspecialchars($category['nama_kategori'], ENT_QUOTES) ?>')"
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });

        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            params.set('page', '1');
            window.location.href = '?' + params.toString();
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus Kategori?',
                html: `Anda yakin ingin menghapus kategori <strong>"${name}"</strong>?<br><br><small class="text-gray-500">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#882426',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`../../app/controllers/categoryController.php?action=delete&id=${id}`, {
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
                                text: 'Terjadi kesalahan saat menghapus kategori',
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
    </script>
</body>

</html>