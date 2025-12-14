<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/BrandRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\BrandRepository;

AuthMiddleware::requireAdminLoginFromView();

$brandRepo = new BrandRepository();
$nextId = $brandRepo->getNextId();

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

$pageTitle = "Tambah Brand";
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
                    <a href="BrandAdmin.php" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-gray-600">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tambah Brand Baru</h1>
                        <p class="text-gray-500 text-sm md:text-base mt-1">Lengkapi informasi brand yang akan ditambahkan</p>
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

            <form action="../../app/controllers/brandController.php" method="POST" enctype="multipart/form-data" id="brandForm" class="max-w-2xl">
                <input type="hidden" name="action" value="add">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">storefront</span>
                        Informasi Brand
                    </h2>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                ID Brand
                            </label>
                            <input type="text" value="<?= htmlspecialchars($nextId) ?>" disabled
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-gray-100 text-gray-500 font-mono">
                            <p class="text-xs text-gray-400 mt-1">ID dibuat otomatis oleh sistem</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Brand <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_brand" id="namaBrand" required
                                value="<?= htmlspecialchars($formData['nama_brand'] ?? '') ?>"
                                placeholder="Masukkan nama brand..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all"
                                maxlength="100">
                            <div id="namaError" class="hidden mt-2 text-sm text-red-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span id="namaErrorText"></span>
                            </div>
                            <div id="namaSuccess" class="hidden mt-2 text-sm text-emerald-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Nama brand tersedia</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Website Resmi
                            </label>
                            <input type="url" name="website" id="websiteBrand"
                                value="<?= htmlspecialchars($formData['website'] ?? '') ?>"
                                placeholder="https://www.example.com"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                            <p class="text-xs text-gray-400 mt-1">Opsional - URL website resmi brand</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Brand
                            </label>
                            <textarea name="desc_brand" rows="4" id="descBrand"
                                placeholder="Jelaskan mengenai brand ini..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none"><?= htmlspecialchars($formData['desc_brand'] ?? '') ?></textarea>
                            <p class="text-xs text-gray-400 mt-1">Opsional - Deskripsi singkat tentang brand ini</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">image</span>
                        Logo Brand
                    </h2>

                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#882426]/50 transition-colors" id="dropZone">
                        <input type="file" name="logo_brand" id="logoInput" accept="image/*" class="hidden">
                        <div id="uploadPlaceholder">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl text-gray-400">cloud_upload</span>
                            </div>
                            <p class="text-gray-600 font-medium mb-1">Drag & drop logo atau klik untuk upload</p>
                            <p class="text-gray-400 text-sm">Format: JPG, PNG, GIF, WebP, SVG (Maks. 2MB)</p>
                            <button type="button" onclick="document.getElementById('logoInput').click()"
                                class="mt-4 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                Pilih Logo
                            </button>
                        </div>
                        <div id="logoPreview" class="hidden">
                            <img src="" alt="Preview" class="max-h-32 mx-auto rounded-lg shadow-md">
                            <button type="button" onclick="removeLogo()"
                                class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center gap-2 mx-auto">
                                <span class="material-symbols-outlined text-lg">delete</span>
                                Hapus Logo
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" id="submitBtn"
                            class="flex-1 py-3 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <span class="material-symbols-outlined">add_circle</span>
                            Simpan Brand
                        </button>
                        <a href="BrandAdmin.php"
                            class="flex-1 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">close</span>
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const namaInput = document.getElementById('namaBrand');
        const namaError = document.getElementById('namaError');
        const namaErrorText = document.getElementById('namaErrorText');
        const namaSuccess = document.getElementById('namaSuccess');
        const dropZone = document.getElementById('dropZone');
        const logoInput = document.getElementById('logoInput');
        const logoPreview = document.getElementById('logoPreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        
        let debounceTimer;
        let isValidName = false;

        namaInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const value = this.value.trim();
            
            namaError.classList.add('hidden');
            namaSuccess.classList.add('hidden');
            
            if (value.length === 0) {
                isValidName = false;
                return;
            }
            
            if (value.length < 2) {
                showError('Nama brand minimal 2 karakter');
                isValidName = false;
                return;
            }
            
            if (/<script|<\/script|javascript:|on\w+\s*=/i.test(value)) {
                showError('Nama tidak boleh mengandung script atau HTML');
                isValidName = false;
                return;
            }
            
            debounceTimer = setTimeout(() => {
                checkDuplicate(value);
            }, 500);
        });

        function checkDuplicate(name) {
            fetch(`../../app/controllers/brandController.php?action=checkDuplicate&name=${encodeURIComponent(name)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.duplicate) {
                        showError('Nama brand sudah digunakan');
                        isValidName = false;
                    } else {
                        namaSuccess.classList.remove('hidden');
                        isValidName = true;
                    }
                })
                .catch(error => {
                    console.error('Error checking duplicate:', error);
                });
        }

        function showError(message) {
            namaErrorText.textContent = message;
            namaError.classList.remove('hidden');
            namaSuccess.classList.add('hidden');
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
                logoInput.files = files;
                handleLogoPreview(files[0]);
            }
        });

        logoInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleLogoPreview(e.target.files[0]);
            }
        });

        function handleLogoPreview(file) {
            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'File harus berupa gambar', 'error');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'Ukuran file maksimal 2MB', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                logoPreview.querySelector('img').src = e.target.result;
                uploadPlaceholder.classList.add('hidden');
                logoPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeLogo() {
            logoInput.value = '';
            logoPreview.classList.add('hidden');
            uploadPlaceholder.classList.remove('hidden');
        }

        document.getElementById('brandForm').addEventListener('submit', function(e) {
            const nama = namaInput.value.trim();
            
            if (nama.length < 2) {
                e.preventDefault();
                showError('Nama brand minimal 2 karakter');
                namaInput.focus();
                return;
            }
            
            if (!isValidName && nama.length > 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Pastikan nama brand valid dan tidak duplikat',
                    icon: 'warning',
                    confirmButtonColor: '#882426'
                });
                return;
            }
        });
    </script>
</body>
</html>
