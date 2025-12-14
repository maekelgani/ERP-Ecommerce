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

$categories = $categoryRepo->getAll();
$brands = $brandRepo->getAll();

$stockStatus = $productRepo->getStockStatus($product['stok']);

$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['form_data']);

$pageTitle = "Edit Produk";
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
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Produk</h1>
                        <p class="text-gray-500 text-sm md:text-base mt-1">
                            ID: <span class="font-mono text-[#882426]"><?= htmlspecialchars($product['id_product']) ?></span>
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($flashError): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3" id="errorAlert">
                <span class="material-symbols-outlined text-red-500">error</span>
                <p class="text-red-700 flex-1"><?= htmlspecialchars($flashError) ?></p>
                <button onclick="document.getElementById('errorAlert').remove()" class="text-red-500 hover:text-red-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <?php endif; ?>

            <form action="../../app/controllers/productController.php" method="POST" enctype="multipart/form-data" id="productForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_product" value="<?= htmlspecialchars($product['id_product']) ?>">
                
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426]">info</span>
                                Informasi Produk
                            </h2>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Produk <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nama_product" required minlength="3" maxlength="200"
                                        value="<?= htmlspecialchars($formData['nama_product'] ?? $product['nama_product']) ?>"
                                        placeholder="Masukkan nama produk..."
                                        class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                    <p class="text-xs text-gray-400 mt-1">Minimal 3 karakter, maksimal 200 karakter</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Kategori <span class="text-red-500">*</span>
                                        </label>
                                        <select name="id_kategori" required
                                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id_kategori'] ?>" <?= ($formData['id_kategori'] ?? $product['id_kategori']) === $cat['id_kategori'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['nama_kategori']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Brand <span class="text-red-500">*</span>
                                        </label>
                                        <select name="id_brand" required
                                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-white focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                            <option value="">Pilih Brand</option>
                                            <?php foreach ($brands as $brand): ?>
                                            <option value="<?= $brand['id_brand'] ?>" <?= ($formData['id_brand'] ?? $product['id_brand']) === $brand['id_brand'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($brand['nama_brand']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Deskripsi & Spesifikasi
                                    </label>
                                    <textarea name="deskripsi_speksifikasi" rows="6"
                                        placeholder="Tulis deskripsi dan spesifikasi produk secara detail..."
                                        class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none"><?= htmlspecialchars($formData['deskripsi_speksifikasi'] ?? $product['deskripsi_speksifikasi'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426]">image</span>
                                Gambar Produk
                            </h2>
                            
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#882426]/50 transition-colors" id="dropZone">
                                <input type="file" name="gambar" id="imageInput" accept=".jpg,.jpeg,.png,.gif,.webp" class="hidden">
                                
                                <?php if ($product['gambar']): ?>
                                <div id="currentImage">
                                    <p class="text-sm text-gray-500 mb-3">Gambar Saat Ini:</p>
                                    <img src="../../uploads/products/<?= htmlspecialchars($product['gambar']) ?>" 
                                        alt="Current" class="max-h-48 mx-auto rounded-lg shadow-md mb-4 cursor-pointer"
                                        onclick="openLightbox('../../uploads/products/<?= htmlspecialchars($product['gambar']) ?>')"
                                        onerror="this.src='../../assets/img/products/productDummy-1.png'">
                                    <p class="text-xs text-gray-400 mb-4">Klik gambar untuk preview besar | Upload gambar baru untuk mengganti</p>
                                </div>
                                <?php endif; ?>
                                
                                <div id="uploadPlaceholder">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-3xl text-gray-400">cloud_upload</span>
                                    </div>
                                    <p class="text-gray-600 font-medium mb-1">Drag & drop gambar atau klik untuk upload</p>
                                    <p class="text-gray-400 text-sm">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</p>
                                    <button type="button" onclick="document.getElementById('imageInput').click()"
                                        class="mt-4 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                        <?= $product['gambar'] ? 'Ganti Gambar' : 'Pilih Gambar' ?>
                                    </button>
                                </div>
                                <div id="imagePreview" class="hidden">
                                    <p class="text-sm text-emerald-600 mb-3 flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined">check_circle</span>
                                        Gambar Baru:
                                    </p>
                                    <img src="" alt="Preview" class="max-h-64 mx-auto rounded-lg shadow-md">
                                    <p class="text-sm text-gray-500 mt-2" id="fileName"></p>
                                    <button type="button" onclick="removeImage()"
                                        class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center gap-2 mx-auto">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                        Batalkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426]">payments</span>
                                Harga & Stok
                            </h2>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Harga <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                        <input type="text" name="harga" required id="hargaInput"
                                            value="<?= number_format($formData['harga'] ?? $product['harga'], 0, ',', '.') ?>"
                                            placeholder="0"
                                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">Harga harus lebih dari 0</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Stok <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" name="stok" id="stokInput" required min="0" 
                                            value="<?= $formData['stok'] ?? $product['stok'] ?>"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Berat (gram)
                                        </label>
                                        <input type="number" name="berat_gram" min="0" 
                                            value="<?= $formData['berat_gram'] ?? $product['berat_gram'] ?? 0 ?>"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                    </div>
                                </div>

                                <div id="stockStatusPreview" class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-1">Status Stok Saat Ini:</p>
                                    <div id="stockStatusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?= $stockStatus['class'] ?>">
                                        <span class="w-2 h-2 rounded-full <?= $stockStatus['dot_class'] ?> mr-2"></span>
                                        <span id="stockStatusLabel"><?= $stockStatus['label'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426]">toggle_on</span>
                                Status Produk
                            </h2>
                            
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="radio" name="status_produk" value="tersedia" 
                                        <?= ($formData['status_produk'] ?? $product['status_produk']) === 'tersedia' ? 'checked' : '' ?>
                                        class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-medium text-gray-700">Tersedia</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                                    <input type="radio" name="status_produk" value="habis"
                                        <?= ($formData['status_produk'] ?? $product['status_produk']) === 'habis' ? 'checked' : '' ?>
                                        class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span class="font-medium text-gray-700">Habis</span>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-gray-500 has-[:checked]:bg-gray-100">
                                    <input type="radio" name="status_produk" value="nonaktif"
                                        <?= ($formData['status_produk'] ?? $product['status_produk']) === 'nonaktif' ? 'checked' : '' ?>
                                        class="w-4 h-4 text-gray-600 focus:ring-gray-500">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        <span class="font-medium text-gray-700">Nonaktif</span>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                <p class="text-xs text-blue-700 flex items-start gap-2">
                                    <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                                    <span>Status akan otomatis menjadi "Habis" jika stok = 0, dan kembali "Tersedia" jika stok > 0 (kecuali diset "Nonaktif")</span>
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full py-3 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2"
                                    style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                                    <span class="material-symbols-outlined">save</span>
                                    Simpan Perubahan
                                </button>
                                <a href="product-details.php?id=<?= $product['id_product'] ?>"
                                    class="w-full py-3 bg-purple-50 text-purple-700 font-semibold rounded-lg hover:bg-purple-100 transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">visibility</span>
                                    Lihat Detail
                                </a>
                                <a href="ProductAdmin.php"
                                    class="w-full py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined">close</span>
                                    Batal
                                </a>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Informasi:</p>
                            <p class="text-xs text-gray-400">
                                Ditambahkan: <?= date('d M Y H:i', strtotime($product['tanggal_ditambahkan'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </form>
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
        const dropZone = document.getElementById('dropZone');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const currentImage = document.getElementById('currentImage');
        const hargaInput = document.getElementById('hargaInput');
        const stokInput = document.getElementById('stokInput');

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

        hargaInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });

        stokInput.addEventListener('input', updateStockStatus);

        function updateStockStatus() {
            const stok = parseInt(stokInput.value) || 0;
            const badge = document.getElementById('stockStatusBadge');
            const label = document.getElementById('stockStatusLabel');
            
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold';
            
            if (stok === 0) {
                badge.classList.add('bg-red-100', 'text-red-700');
                badge.querySelector('span').className = 'w-2 h-2 rounded-full bg-red-500 mr-2';
                label.textContent = 'Stok Habis';
            } else if (stok <= 9) {
                badge.classList.add('bg-amber-100', 'text-amber-700');
                badge.querySelector('span').className = 'w-2 h-2 rounded-full bg-amber-500 mr-2';
                label.textContent = 'Stok Menipis';
            } else {
                badge.classList.add('bg-emerald-100', 'text-emerald-700');
                badge.querySelector('span').className = 'w-2 h-2 rounded-full bg-emerald-500 mr-2';
                label.textContent = 'Stok Banyak';
            }
        }

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-[#882426]', 'bg-red-50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-[#882426]', 'bg-red-50');
            });
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) {
                imageInput.files = files;
                handleImagePreview(files[0]);
            }
        });

        imageInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleImagePreview(e.target.files[0]);
            }
        });

        function handleImagePreview(file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format tidak didukung',
                    text: 'Gunakan format JPG, PNG, GIF, atau WebP',
                    confirmButtonColor: '#882426'
                });
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran terlalu besar',
                    text: 'Ukuran file maksimal 5MB',
                    confirmButtonColor: '#882426'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.querySelector('img').src = e.target.result;
                document.getElementById('fileName').textContent = file.name;
                if (currentImage) currentImage.classList.add('hidden');
                uploadPlaceholder.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            if (currentImage) currentImage.classList.remove('hidden');
            uploadPlaceholder.classList.remove('hidden');
        }

        document.getElementById('productForm').addEventListener('submit', function(e) {
            const harga = hargaInput.value.replace(/\./g, '');
            hargaInput.value = harga;
        });
    </script>
</body>
</html>
