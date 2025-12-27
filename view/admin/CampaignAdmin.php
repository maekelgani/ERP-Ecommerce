<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Kampanye";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3"></div>

    <!-- Sidebar Component -->
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <!-- Navbar -->
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Kampanye Promo</h1>
                    <p class="text-gray-500 mt-1">Kelola kampanye promosi, flash sale, dan event diskon</p>
                </div>
                <button onclick="openModal()" class="flex items-center gap-2 bg-[#882426] text-white px-5 py-2.5 rounded-lg hover:bg-[#6d1d1f] transition-colors">
                    <span class="material-symbols-outlined text-lg">add</span>
                    Tambah Kampanye
                </button>
            </div>

            <!-- Panduan Penggunaan -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 md:p-6 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-2xl flex-shrink-0 mt-1">info</span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-blue-900 mb-2">Panduan Cara Menambah Kampanye</h3>
                        <div class="text-sm text-blue-800 space-y-2">
                            <div class="flex items-start gap-2">
                                <span class="font-bold flex-shrink-0">1. Isi Data Kampanye:</span>
                                <span>Klik "Tambah Kampanye" dan lengkapi semua field yang ditandai (*). Pilih tipe kampanye sesuai kebutuhan (Diskon Produk, Voucher, Flash Sale, Bundle, atau Gratis Ongkir).</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-bold flex-shrink-0">2. Atur Waktu & Status:</span>
                                <span>Tentukan tanggal mulai dan selesai kampanye. Pilih status "Draf" untuk persiapan atau "Aktif" untuk langsung berjalan.</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-bold flex-shrink-0">3. Simpan Kampanye:</span>
                                <span>Setelah mengisi data, klik "Simpan" untuk menyimpan kampanye. Kampanye akan tersimpan dengan status sesuai yang dipilih.</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-bold flex-shrink-0">4. Kelola Produk:</span>
                                <span>Setelah kampanye tersimpan, klik tombol "Produk" pada kampanye untuk menambahkan produk yang akan mengikuti kampanye ini.</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="font-bold flex-shrink-0">5. Aktifkan Kampanye:</span>
                                <span>Jika status masih "Draf", ubah status menjadi "Aktif" agar kampanye mulai berjalan pada waktu yang telah ditentukan.</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <p class="text-xs text-blue-700">
                                <stro>Tips:</stro ng> Persiapkan data produk dan harga diskon terlebih dahulu sebelum membuat kampanye. Pastikan tanggal mulai tidak lebih besar dari tanggal selesai.
                            </p>
                        </div>
                    </div>
                    <button onclick="this.closest('.bg-blue-50').style.display='none'" class="text-blue-800 hover:text-blue-900 flex-shrink-0">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <span class="material-symbols-outlined text-lg">search</span>
                                </span>
                                <input type="text" id="searchInput" placeholder="Cari kampanye..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            </div>
                        </div>
                        <select id="filterTipe" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            <option value="">Semua Tipe</option>
                            <option value="diskon_produk">Diskon Produk</option>
                            <option value="voucher">Voucher</option>
                            <option value="flash_sale">Flash Sale</option>
                            <option value="bundle">Bundle</option>
                            <option value="gratis_ongkir">Gratis Ongkir</option>
                        </select>
                        <select id="filterStatus" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            <option value="">Semua Status</option>
                            <option value="draf">Draf</option>
                            <option value="aktif">Aktif</option>
                            <option value="berakhir">Berakhir</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div id="campaignList" class="p-4">
                    <div class="flex justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#882426]"></div>
                    </div>
                </div>
            </div>

            <div id="pagination" class="flex justify-center gap-2"></div>
        </main>
    </div>

    <!-- Modal Tambah/Edit Kampanye - Modern Design -->
    <div id="campaignModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden transform transition-all animate-modal-in" onclick="event.stopPropagation()">
                <!-- Modern Header with Gradient -->
                <div class="sticky top-0 z-10 px-6 py-5 flex items-center justify-between" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span id="modalIcon" class="material-symbols-outlined text-white text-2xl">campaign</span>
                        </div>
                        <div>
                            <h2 id="modalTitle" class="text-xl font-bold text-white">Tambah Kampanye Baru</h2>
                            <p id="modalSubtitle" class="text-white/70 text-sm mt-0.5">Buat kampanye promo baru</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="campaignForm" class="overflow-y-auto max-h-[calc(90vh-180px)]" enctype="multipart/form-data">
                    <div class="p-6 bg-gray-50 space-y-5">
                        <input type="hidden" id="campaignId" name="id">

                        <!-- Judul Kampanye -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base text-[#882426]">title</span>
                                    Judul Kampanye <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="text" name="judul" id="judul" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all" placeholder="Contoh: Flash Sale Akhir Tahun">
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base text-[#882426]">description</span>
                                    Deskripsi
                                </span>
                            </label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all" placeholder="Deskripsi singkat tentang kampanye ini"></textarea>
                        </div>

                        <!-- Tipe & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">category</span>
                                        Tipe Kampanye <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <select name="tipe" id="tipe" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                                    <option value="">Pilih Tipe</option>
                                    <option value="diskon_produk">Diskon Produk</option>
                                    <option value="voucher">Voucher</option>
                                    <option value="flash_sale">Flash Sale</option>
                                    <option value="bundle">Bundle</option>
                                    <option value="gratis_ongkir">Gratis Ongkir</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">toggle_on</span>
                                        Status
                                    </span>
                                </label>
                                <select name="status" id="status" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                                    <option value="draf">Draf</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="berakhir">Berakhir</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Periode -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">event</span>
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="datetime-local" name="mulai_pada" id="mulai_pada" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">event_busy</span>
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <input type="datetime-local" name="selesai_pada" id="selesai_pada" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                            </div>
                        </div>

                        <!-- Kuota & Banner -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">inventory</span>
                                        Kuota Total
                                    </span>
                                </label>
                                <input type="number" name="kuota_total" id="kuota_total" min="0" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all" placeholder="Kosongkan jika unlimited">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-base text-[#882426]">image</span>
                                        Banner Kampanye
                                    </span>
                                </label>
                                <input type="file" name="banner" id="banner" accept="image/*" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#882426] file:text-white hover:file:bg-[#6d1a1c]">
                                <p class="text-xs text-gray-500 mt-1.5">Format: JPG, PNG, GIF, WebP. Maks: 5MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                            <span class="material-symbols-outlined text-lg">close</span>
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-[#882426] to-[#a52a2c] text-white font-semibold rounded-xl hover:from-[#6d1d1f] hover:to-[#882426] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            <span id="submitBtnText">Simpan Kampanye</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal - Modern Design -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in" onclick="event.stopPropagation()">
                <!-- Modern Header with Red/Danger Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl animate-pulse-warning">delete_forever</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hapus Kampanye</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Campaign Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div id="deleteCampaignImageContainer" class="w-42 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-gradient-to-br from-[#882426] to-[#6d1a1c]">
                            <img id="deleteCampaignImage" src="" alt="Campaign"
                                class="w-full h-full object-contain"
                                onerror="this.style.display='none'; document.getElementById('deleteCampaignImageFallback').style.display='flex';">
                        </div>
                        <div id="deleteCampaignImageFallback" class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-gradient-to-br from-[#882426] to-[#6d1a1c] items-center justify-center" style="display: none;">
                            <span class="material-symbols-outlined text-4xl text-white/70">campaign</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800" id="deleteCampaignName"></p>
                            <p class="text-sm text-gray-500" id="deleteCampaignType"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600">Anda yakin ingin menghapus kampanye ini? Semua produk yang terhubung akan terlepas dari kampanye. Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Details Card -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">campaign</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nama Kampanye</p>
                                <p class="text-sm font-semibold text-gray-800" id="deleteCampaignNameDetail">-</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">event</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Periode</p>
                                <p class="text-sm font-semibold text-gray-800" id="deleteCampaignPeriod">-</p>
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
                        Ya, Hapus Kampanye!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal - Modern Design -->
    <div id="productModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeProductModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all animate-modal-in" onclick="event.stopPropagation()">
                <!-- Modern Header -->
                <div class="sticky top-0 z-10 px-6 py-5 flex items-center justify-between" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">inventory_2</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Kelola Produk Kampanye</h2>
                            <p class="text-white/70 text-sm mt-0.5">Tambah atau hapus produk dari kampanye</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeProductModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 bg-gray-50 overflow-y-auto max-h-[calc(90vh-100px)]">
                    <input type="hidden" id="selectedCampaignId">

                    <div class="mb-4">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <span class="material-symbols-outlined text-lg">search</span>
                            </span>
                            <input type="text" id="productSearch" placeholder="Cari produk untuk ditambahkan..." class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl border-2 border-gray-200 p-4">
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#882426]">inventory_2</span>
                                Produk Tersedia
                            </h3>
                            <div id="availableProducts" class="space-y-2 max-h-[400px] overflow-y-auto">
                                <p class="text-gray-500 text-center py-4">Memuat produk...</p>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border-2 border-emerald-200 p-4">
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                                Produk Terpilih
                            </h3>
                            <div id="selectedProducts" class="space-y-2 max-h-[400px] overflow-y-auto">
                                <p class="text-gray-500 text-center py-4">Belum ada produk dipilih</p>
                            </div>
                        </div>
                    </div>
                </div>
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

        /* Circular Progress */
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
        let campaigns = [];
        let currentDeleteCampaign = null;

        // Toast Notification System with Circular Progress
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
                    title: 'text-emerald-800',
                    progressCircle: '#10b981'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-red-200',
                    icon: 'error',
                    iconBg: 'bg-red-500',
                    iconColor: 'text-white',
                    title: 'text-red-800',
                    progressCircle: '#ef4444'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    icon: 'warning',
                    iconBg: 'bg-amber-500',
                    iconColor: 'text-white',
                    title: 'text-amber-800',
                    progressCircle: '#f59e0b'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-blue-200',
                    icon: 'info',
                    iconBg: 'bg-blue-500',
                    iconColor: 'text-white',
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

        document.addEventListener('DOMContentLoaded', () => {
            loadCampaigns();

            document.getElementById('searchInput').addEventListener('input', debounce(loadCampaigns, 300));
            document.getElementById('filterTipe').addEventListener('change', loadCampaigns);
            document.getElementById('filterStatus').addEventListener('change', loadCampaigns);

            // Enable/disable delete button based on checkbox
            document.getElementById('deleteConfirmCheck').addEventListener('change', function() {
                document.getElementById('deleteConfirmBtn').disabled = !this.checked;
            });
        });

        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
                closeProductModal();
            }
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        async function loadCampaigns() {
            const search = document.getElementById('searchInput').value;
            const tipe = document.getElementById('filterTipe').value;
            const status = document.getElementById('filterStatus').value;

            try {
                const params = new URLSearchParams({
                    action: 'list',
                    search,
                    tipe,
                    status
                });
                const response = await fetch(`../../app/controllers/promoController.php?${params}`);
                const data = await response.json();

                if (data.success) {
                    campaigns = data.data;
                    renderCampaigns();
                }
            } catch (error) {
                console.error('Error loading campaigns:', error);
            }
        }

        function renderCampaigns() {
            const container = document.getElementById('campaignList');

            if (campaigns.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <span class="material-symbols-outlined text-5xl text-gray-300">campaign</span>
                        <p class="text-gray-500 mt-2">Belum ada kampanye</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${campaigns.map(campaign => renderCampaignCard(campaign)).join('')}
                </div>
            `;
        }

        function renderCampaignCard(campaign) {
            const statusColors = {
                'draf': 'bg-gray-100 text-gray-700',
                'aktif': 'bg-emerald-100 text-emerald-700',
                'berakhir': 'bg-red-100 text-red-700',
                'nonaktif': 'bg-amber-100 text-amber-700'
            };
            // Tambahkan mapping untuk warna dot indicator
            const statusDotColors = {
                'draf': 'bg-gray-500',
                'aktif': 'bg-emerald-500',
                'berakhir': 'bg-red-500',
                'nonaktif': 'bg-amber-500'
            };

            const tipeIcons = {
                'diskon_produk': 'percent',
                'voucher': 'confirmation_number',
                'flash_sale': 'bolt',
                'bundle': 'inventory_2',
                'gratis_ongkir': 'local_shipping'
            };

            const startDate = new Date(campaign.mulai_pada).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
            const endDate = new Date(campaign.selesai_pada).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            return `
                <div class="campaign-card bg-white border border-gray-100 rounded-xl overflow-hidden">
                    ${campaign.banner ? `
                        <div class="h-32 bg-gray-100 overflow-hidden">
                            <img src="../../uploads/promo/${campaign.banner}" alt="${campaign.judul}" class="w-full h-full object-contain md:object-cover">
                        </div>
                    ` : `
                        <div class="h-32 bg-gradient-to-br from-[#882426] to-[#6d1d1f] flex items-center justify-center">
                            <span class="material-symbols-outlined text-5xl text-white/50">${tipeIcons[campaign.tipe] || 'campaign'}</span>
                        </div>
                    `}
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-semibold text-gray-800 line-clamp-1">${campaign.judul}</h3>
                            <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold animate-pulse ${statusColors[campaign.status] || statusColors.draf} flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDotColors[campaign.status] || statusDotColors.draf} mr-1.5"></span>
                                ${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                            <span class="material-symbols-outlined text-base">event</span>
                            <span>${startDate} - ${endDate}</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-base">inventory_2</span>
                                ${campaign.jumlah_produk || 0} produk
                            </span>
                            ${campaign.kuota_total ? `
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-base">group</span>
                                    ${campaign.kuota_terpakai || 0}/${campaign.kuota_total}
                                </span>
                            ` : ''}
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openProductModal('${campaign.id_kampanye}')" class="flex-1 flex items-center justify-center gap-1 px-3 py-2 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-base">inventory_2</span>
                                Produk
                            </button>
                            <button onclick="editCampaign('${campaign.id_kampanye}')" class="flex items-center justify-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </button>
                            <button onclick="deleteCampaign('${campaign.id_kampanye}')" class="flex items-center justify-center p-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-base">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Kampanye Baru';
            document.getElementById('modalSubtitle').textContent = 'Buat kampanye promo baru';
            document.getElementById('modalIcon').textContent = 'campaign';
            document.getElementById('campaignForm').reset();
            document.getElementById('campaignId').value = '';
            document.getElementById('campaignModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('campaignModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function editCampaign(id) {
            try {
                const response = await fetch(`../../app/controllers/promoController.php?action=get&id=${id}`);
                const data = await response.json();

                if (data.success && data.data) {
                    const campaign = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Kampanye';
                    document.getElementById('modalSubtitle').textContent = 'Perbarui pengaturan kampanye';
                    document.getElementById('modalIcon').textContent = 'edit';
                    document.getElementById('campaignId').value = campaign.id_kampanye;
                    document.getElementById('judul').value = campaign.judul || '';
                    document.getElementById('deskripsi').value = campaign.deskripsi || '';
                    document.getElementById('tipe').value = campaign.tipe || '';
                    document.getElementById('status').value = campaign.status || 'draf';

                    // Format datetime for input
                    if (campaign.mulai_pada) {
                        const mulaiDate = campaign.mulai_pada.replace(' ', 'T').slice(0, 16);
                        document.getElementById('mulai_pada').value = mulaiDate;
                    }
                    if (campaign.selesai_pada) {
                        const selesaiDate = campaign.selesai_pada.replace(' ', 'T').slice(0, 16);
                        document.getElementById('selesai_pada').value = selesaiDate;
                    }

                    document.getElementById('kuota_total').value = campaign.kuota_total || '';
                    document.getElementById('campaignModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    showToast('error', 'Error!', data.message || 'Gagal memuat data kampanye');
                }
            } catch (error) {
                console.error('Error editing campaign:', error);
                showToast('error', 'Error!', 'Gagal memuat data kampanye');
            }
        }

        // Delete functions with new modal
        function deleteCampaign(id) {
            const campaign = campaigns.find(c => c.id_kampanye === id);
            if (!campaign) return;

            currentDeleteCampaign = {
                id: id,
                judul: campaign.judul,
                tipe: campaign.tipe,
                banner: campaign.banner,
                mulai_pada: campaign.mulai_pada,
                selesai_pada: campaign.selesai_pada
            };

            // Set image
            const imageEl = document.getElementById('deleteCampaignImage');
            const imageContainer = document.getElementById('deleteCampaignImageContainer');
            const fallbackContainer = document.getElementById('deleteCampaignImageFallback');

            if (campaign.banner) {
                imageEl.src = '../../uploads/promo/' + campaign.banner;
                imageEl.style.display = 'block';
                imageContainer.style.display = 'block';
                fallbackContainer.style.display = 'none';
            } else {
                imageEl.style.display = 'none';
                imageContainer.style.display = 'none';
                fallbackContainer.style.display = 'flex';
            }

            // Set info
            const tipeLabels = {
                'diskon_produk': 'Diskon Produk',
                'voucher': 'Voucher',
                'flash_sale': 'Flash Sale',
                'bundle': 'Bundle',
                'gratis_ongkir': 'Gratis Ongkir'
            };

            document.getElementById('deleteCampaignName').textContent = campaign.judul || 'Kampanye';
            document.getElementById('deleteCampaignType').textContent = tipeLabels[campaign.tipe] || campaign.tipe;
            document.getElementById('deleteCampaignNameDetail').textContent = campaign.judul || 'Kampanye';

            const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }) : '-';
            document.getElementById('deleteCampaignPeriod').textContent = formatDate(campaign.mulai_pada) + ' - ' + formatDate(campaign.selesai_pada);

            // Reset checkbox
            document.getElementById('deleteConfirmCheck').checked = false;
            document.getElementById('deleteConfirmBtn').disabled = true;

            // Show modal
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentDeleteCampaign = null;
        }

        async function executeDelete() {
            if (!currentDeleteCampaign) return;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menghapus...';

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', currentDeleteCampaign.id);

                const response = await fetch('../../app/controllers/promoController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                closeDeleteModal();

                if (data.success) {
                    showToast('success', 'Berhasil Dihapus!', data.message || 'Kampanye berhasil dihapus dari sistem.');
                    loadCampaigns();
                } else {
                    showToast('error', 'Gagal Menghapus!', data.message || 'Terjadi kesalahan saat menghapus kampanye.');
                }
            } catch (error) {
                closeDeleteModal();
                showToast('error', 'Error!', 'Terjadi kesalahan saat menghapus kampanye.');
            } finally {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">delete_forever</span> Ya, Hapus Kampanye!';
            }
        }

        document.getElementById('campaignForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            document.getElementById('submitBtnText').textContent = 'Menyimpan...';

            const formData = new FormData(e.target);
            const campaignId = document.getElementById('campaignId').value;
            formData.append('action', campaignId ? 'update' : 'create');

            try {
                const response = await fetch('../../app/controllers/promoController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showToast('success', campaignId ? 'Berhasil Diperbarui!' : 'Berhasil Ditambahkan!', data.message || 'Kampanye berhasil disimpan.');
                    closeModal();
                    loadCampaigns();
                } else {
                    showToast('error', 'Gagal!', data.message || 'Terjadi kesalahan');
                }
            } catch (error) {
                showToast('error', 'Error!', 'Terjadi kesalahan koneksi');
            } finally {
                submitBtn.disabled = false;
                document.getElementById('submitBtnText').textContent = 'Simpan Kampanye';
            }
        });

        function openProductModal(campaignId) {
            document.getElementById('selectedCampaignId').value = campaignId;
            document.getElementById('productModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loadCampaignProducts(campaignId);
            loadAvailableProducts(campaignId);
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function loadCampaignProducts(campaignId) {
            try {
                const response = await fetch(`../../app/controllers/promoController.php?action=get_products&campaign_id=${campaignId}`);
                const data = await response.json();

                const container = document.getElementById('selectedProducts');
                if (data.success && data.data && data.data.length > 0) {
                    container.innerHTML = data.data.map(p => `
                        <div class="flex items-center gap-3 p-3 border-2 border-emerald-100 rounded-xl hover:bg-emerald-50/50 transition-colors">
                            <img src="../../uploads/products/${p.gambar || 'default.png'}" alt="${p.nama_product}" class="w-10 h-10 object-cover rounded-lg" onerror="this.src='../../assets/img/default-product.png'">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate">${p.nama_product}</p>
                            </div>
                            <button onclick="detachProduct('${campaignId}', '${p.id_produk}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus dari kampanye">
                                <span class="material-symbols-outlined text-lg">remove_circle</span>
                            </button>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada produk dipilih</p>';
                }
            } catch (error) {
                console.error('Error loading campaign products:', error);
            }
        }

        async function loadAvailableProducts(campaignId, search = '') {
            try {
                const response = await fetch(`../../app/controllers/promoController.php?action=get_available_products&campaign_id=${campaignId}&search=${search}`);
                const data = await response.json();

                const container = document.getElementById('availableProducts');
                if (data.success && data.data && data.data.length > 0) {
                    container.innerHTML = data.data.map(p => `
                        <div class="flex items-center gap-3 p-3 border-2 border-gray-100 rounded-xl hover:bg-gray-50 transition-colors">
                            <img src="../../uploads/products/${p.gambar || 'default.png'}" alt="${p.nama_product}" class="w-10 h-10 object-cover rounded-lg" onerror="this.src='../../assets/img/default-product.png'">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate">${p.nama_product}</p>
                                <p class="text-xs text-gray-500">Rp ${Number(p.harga).toLocaleString('id-ID')}</p>
                            </div>
                            <button onclick="attachProduct('${campaignId}', '${p.id_product}')" class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Tambah ke kampanye">
                                <span class="material-symbols-outlined text-lg">add_circle</span>
                            </button>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada produk tersedia</p>';
                }
            } catch (error) {
                console.error('Error loading available products:', error);
            }
        }

        document.getElementById('productSearch').addEventListener('input', debounce((e) => {
            const campaignId = document.getElementById('selectedCampaignId').value;
            loadAvailableProducts(campaignId, e.target.value);
        }, 300));

        async function attachProduct(campaignId, productId) {
            try {
                const formData = new FormData();
                formData.append('action', 'attach_products');
                formData.append('campaign_id', campaignId);
                formData.append('product_ids', JSON.stringify([productId]));

                const response = await fetch('../../app/controllers/promoController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showToast('success', 'Berhasil!', 'Produk ditambahkan ke kampanye');
                    loadCampaignProducts(campaignId);
                    loadAvailableProducts(campaignId, document.getElementById('productSearch').value);
                    loadCampaigns();
                } else {
                    showToast('error', 'Gagal!', data.message || 'Gagal menambahkan produk');
                }
            } catch (error) {
                console.error('Error attaching product:', error);
                showToast('error', 'Error!', 'Terjadi kesalahan');
            }
        }

        async function detachProduct(campaignId, productId) {
            try {
                const formData = new FormData();
                formData.append('action', 'detach_product');
                formData.append('campaign_id', campaignId);
                formData.append('product_id', productId);

                const response = await fetch('../../app/controllers/promoController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showToast('success', 'Berhasil!', 'Produk dihapus dari kampanye');
                    loadCampaignProducts(campaignId);
                    loadAvailableProducts(campaignId, document.getElementById('productSearch').value);
                    loadCampaigns();
                } else {
                    showToast('error', 'Gagal!', data.message || 'Gagal menghapus produk');
                }
            } catch (error) {
                console.error('Error detaching product:', error);
                showToast('error', 'Error!', 'Terjadi kesalahan');
            }
        }
    </script>
</body>

</html>