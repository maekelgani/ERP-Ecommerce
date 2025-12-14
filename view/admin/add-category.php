<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CategoryRepository.php';

use App\Auth\AuthMiddleware;
use App\Repository\CategoryRepository;

AuthMiddleware::requireAdminLoginFromView();

$categoryRepo = new CategoryRepository();
$nextId = $categoryRepo->getNextId();

$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

$pageTitle = "Tambah Kategori";
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
                    <a href="CategoryAdmin.php" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-gray-600">arrow_back</span>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Tambah Kategori Baru</h1>
                        <p class="text-gray-500 text-sm md:text-base mt-1">Lengkapi informasi kategori yang akan ditambahkan</p>
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

            <form action="../../app/controllers/categoryController.php" method="POST" id="categoryForm" enctype="multipart/form-data" class="max-w-2xl">
                <input type="hidden" name="action" value="add">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#882426]">category</span>
                        Informasi Kategori
                    </h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                ID Kategori
                            </label>
                            <input type="text" value="<?= htmlspecialchars($nextId) ?>" disabled
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm bg-gray-100 text-gray-500 font-mono">
                            <p class="text-xs text-gray-400 mt-1">ID dibuat otomatis oleh sistem</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama_kategori" id="namaKategori" required
                                value="<?= htmlspecialchars($formData['nama_kategori'] ?? '') ?>"
                                placeholder="Masukkan nama kategori..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all"
                                maxlength="100">
                            <div id="namaError" class="hidden mt-2 text-sm text-red-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span id="namaErrorText"></span>
                            </div>
                            <div id="namaSuccess" class="hidden mt-2 text-sm text-emerald-500 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                <span>Nama kategori tersedia</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Kategori
                            </label>
                            <textarea name="deskripsi_kategori" rows="4" id="deskripsiKategori"
                                placeholder="Jelaskan mengenai kategori ini..."
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all resize-none"><?= htmlspecialchars($formData['deskripsi_kategori'] ?? '') ?></textarea>
                            <p class="text-xs text-gray-400 mt-1">Opsional - Deskripsi singkat tentang kategori ini</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Icon Kategori
                            </label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#882426]/50 transition-colors" id="dropZone">
                                <input type="file" name="icon_kategori" id="iconInput" accept="image/*" class="hidden">
                                <div id="uploadPlaceholder">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-3xl text-gray-400">cloud_upload</span>
                                    </div>
                                    <p class="text-gray-600 font-medium mb-1">Drag & drop icon atau klik untuk upload</p>
                                    <p class="text-gray-400 text-sm">Format: JPG, PNG, GIF, WebP (Maks. 5MB)</p>
                                    <button type="button" onclick="document.getElementById('iconInput').click()"
                                        class="mt-4 px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                                        Pilih Icon
                                    </button>
                                </div>
                                <div id="iconPreview" class="hidden">
                                    <img src="" alt="Preview" class="max-h-32 mx-auto rounded-lg shadow-md">
                                    <button type="button" onclick="removeIcon()"
                                        class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition-colors flex items-center gap-2 mx-auto">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                        Batal Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" id="submitBtn"
                            class="flex-1 py-3 text-white font-semibold rounded-lg transition-all duration-300 hover:shadow-lg active:scale-[0.98] flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                            <span class="material-symbols-outlined">add_circle</span>
                            Simpan Kategori
                        </button>
                        <a href="CategoryAdmin.php"
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
        const dropZone = document.getElementById('dropZone');
        const iconInput = document.getElementById('iconInput');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');
        const iconPreview = document.getElementById('iconPreview');
        const iconPreviewImg = iconPreview.querySelector('img');

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-[#882426]/50', 'bg-red-50/30');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-[#882426]/50', 'bg-red-50/30');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-[#882426]/50', 'bg-red-50/30');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                iconInput.files = files;
                displayPreview(files[0]);
            }
        });

        iconInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                displayPreview(e.target.files[0]);
            }
        });

        function displayPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                iconPreviewImg.src = e.target.result;
                uploadPlaceholder.classList.add('hidden');
                iconPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeIcon() {
            iconInput.value = '';
            uploadPlaceholder.classList.remove('hidden');
            iconPreview.classList.add('hidden');
        }
    </script>
    <script>
        const namaInput = document.getElementById('namaKategori');
        const namaError = document.getElementById('namaError');
        const namaErrorText = document.getElementById('namaErrorText');
        const namaSuccess = document.getElementById('namaSuccess');
        const submitBtn = document.getElementById('submitBtn');

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
                showError('Nama kategori minimal 2 karakter');
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
            fetch(`../../app/controllers/categoryController.php?action=checkDuplicate&name=${encodeURIComponent(name)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.duplicate) {
                        showError('Nama kategori sudah digunakan');
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

        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            const nama = namaInput.value.trim();

            if (nama.length < 2) {
                e.preventDefault();
                showError('Nama kategori minimal 2 karakter');
                namaInput.focus();
                return;
            }

            if (!isValidName && nama.length > 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Gagal',
                    text: 'Pastikan nama kategori valid dan tidak duplikat',
                    icon: 'warning',
                    confirmButtonColor: '#882426'
                });
                return;
            }
        });
    </script>
</body>

</html>