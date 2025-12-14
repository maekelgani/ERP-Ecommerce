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

$productId = $_GET['id'] ?? '';
if (empty($productId)) {
    header('Location: ProductAdmin.php');
    exit;
}

$categoryRepo = new CategoryRepository();
$brandRepo = new BrandRepository();
$productRepo = new ProductRepository();

$product = $productRepo->getById($productId);
if (!$product) {
    $_SESSION['flash_error'] = 'Produk tidak ditemukan';
    header('Location: ProductAdmin.php');
    exit;
}

$stockStatus = $productRepo->getStockStatus($product['stok']);
$productStatus = $productRepo->getProductStatus($product['status_produk']);
$isUsedInOrders = $productRepo->isUsedInOrders($product['id_product']);

$imageSrc = $product['gambar']
    ? '../../uploads/products/' . htmlspecialchars($product['gambar'])
    : '../../assets/img/products/default-product.jpg';

$pageTitle = "Detail Produk - " . htmlspecialchars($product['nama_product']);
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
                <div class="flex items-center gap-3 mb-2">
                    <a href="ProductAdmin.php" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-gray-600">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Produk</h1>
                        <p class="text-gray-500 text-sm md:text-base mt-1">
                            ID: <span class="font-mono text-[#882426]"><?= htmlspecialchars($product['id_product']) ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="relative">
                            <div class="aspect-video bg-gray-100 flex items-center justify-center cursor-pointer group" onclick="openLightbox('<?= $imageSrc ?>')">
                                <img src="<?= $imageSrc ?>"
                                    alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                    class="max-h-full max-w-full object-contain transition-transform group-hover:scale-105"
                                    onerror="this.src='../../assets/img/products/default-product.jpg'">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div class="bg-white/90 rounded-full p-3">
                                        <span class="material-symbols-outlined text-3xl text-gray-700">zoom_in</span>
                                    </div>
                                </div>
                            </div>
                            <div class="absolute top-4 right-4 flex gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $productStatus['class'] ?> shadow-lg">
                                    <span class="w-2 h-2 rounded-full <?= $productStatus['dot_class'] ?> mr-2"></span>
                                    <?= $productStatus['label'] ?>
                                </span>
                            </div>
                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold <?= $stockStatus['class'] ?> shadow-lg">
                                    <span class="w-2 h-2 rounded-full <?= $stockStatus['dot_class'] ?> mr-2"></span>
                                    <?= $stockStatus['label'] ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($product['nama_product']) ?></h2>
                                    <div class="flex items-center gap-3 flex-wrap">
                                        <?php if ($product['nama_brand']): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 rounded-full text-sm font-medium text-gray-700">
                                                <span class="material-symbols-outlined text-base">verified</span>
                                                <?= htmlspecialchars($product['nama_brand']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($product['nama_kategori']): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 rounded-full text-sm font-medium text-blue-700">
                                                <span class="material-symbols-outlined text-base">category</span>
                                                <?= htmlspecialchars($product['nama_kategori']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">Harga</p>
                                    <p class="text-3xl font-bold" style="color: #882426;">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                </div>
                            </div>

                            <?php if (!empty($product['deskripsi_speksifikasi'])): ?>
                                <div class="border-t border-gray-100 pt-4 mt-4">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#882426]">description</span>
                                        Deskripsi & Spesifikasi
                                    </h3>
                                    <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed whitespace-pre-line">
                                        <?= nl2br(htmlspecialchars($product['deskripsi_speksifikasi'])) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">info</span>
                            Informasi Tambahan
                        </h3>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-blue-600">inventory</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-800"><?= $product['stok'] ?></p>
                                <p class="text-xs text-gray-500">Stok Tersedia</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-amber-600">scale</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-800"><?= number_format($product['berat_gram'] ?? 0) ?></p>
                                <p class="text-xs text-gray-500">Berat (gram)</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined text-emerald-600">calendar_today</span>
                                </div>
                                <p class="text-lg font-bold text-gray-800"><?= date('d M Y', strtotime($product['tanggal_ditambahkan'])) ?></p>
                                <p class="text-xs text-gray-500">Tanggal Ditambahkan</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 text-center">
                                <div class="w-12 h-12 rounded-full <?= $isUsedInOrders ? 'bg-emerald-100' : 'bg-gray-100' ?> flex items-center justify-center mx-auto mb-2">
                                    <span class="material-symbols-outlined <?= $isUsedInOrders ? 'text-emerald-600' : 'text-gray-400' ?>">shopping_cart</span>
                                </div>
                                <p class="text-lg font-bold text-gray-800"><?= $isUsedInOrders ? 'Ya' : 'Belum' ?></p>
                                <p class="text-xs text-gray-500">Pernah Dipesan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">toggle_on</span>
                            Status Produk
                        </h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full <?= $productStatus['class'] ?> flex items-center justify-center">
                                        <span class="w-3 h-3 rounded-full <?= $productStatus['dot_class'] ?>"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status Produk</p>
                                        <p class="font-semibold text-gray-800"><?= $productStatus['label'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full <?= $stockStatus['class'] ?> flex items-center justify-center">
                                        <span class="w-3 h-3 rounded-full <?= $stockStatus['dot_class'] ?>"></span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status Stok</p>
                                        <p class="font-semibold text-gray-800"><?= $stockStatus['label'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-xs text-blue-700 flex items-start gap-2">
                                <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                                <span>
                                    <strong>Keterangan Status Stok:</strong><br>
                                    Banyak = Stok >= 10<br>
                                    Menipis = Stok 1-9<br>
                                    Habis = Stok 0
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">settings</span>
                            Aksi
                        </h3>

                        <div class="space-y-3">
                            <a href="edit-product.php?id=<?= $product['id_product'] ?>"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-50 text-blue-700 font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                                <span class="material-symbols-outlined">edit</span>
                                Edit Produk
                            </a>

                            <?php if ($isUsedInOrders): ?>
                                <button type="button" disabled
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-400 font-semibold rounded-lg cursor-not-allowed">
                                    <span class="material-symbols-outlined">delete</span>
                                    Hapus Produk
                                </button>
                                <p class="text-xs text-gray-500 text-center">
                                    <span class="material-symbols-outlined text-xs align-middle">info</span>
                                    Produk tidak dapat dihapus karena sudah pernah digunakan dalam pesanan
                                </p>
                            <?php else: ?>
                                <button type="button"
                                    onclick="confirmDelete('<?= $product['id_product'] ?>', '<?= htmlspecialchars(addslashes($product['nama_product'])) ?>')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                    Hapus Produk
                                </button>
                            <?php endif; ?>

                            <a href="ProductAdmin.php"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                                <span class="material-symbols-outlined">arrow_back</span>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-[#882426] to-[#6d1a1c] rounded-xl shadow-sm p-6 text-white">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="material-symbols-outlined">qr_code_2</span>
                            </div>
                            <div>
                                <p class="text-sm text-white/70">ID Produk</p>
                                <p class="font-mono font-bold text-lg"><?= $product['id_product'] ?></p>
                            </div>
                        </div>
                        <p class="text-xs text-white/60">
                            Ditambahkan pada <?= date('d M Y, H:i', strtotime($product['tanggal_ditambahkan'])) ?>
                        </p>
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
                                    window.location.href = 'ProductAdmin.php';
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
    </script>
</body>

</html>