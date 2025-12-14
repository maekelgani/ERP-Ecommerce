<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Web Management";
include '../../components/admin/head.php';
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div id="main-header" class="flex justify-between items-center mb-4">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold">Web Management</h1>
                    <p class="text-gray-400">Kelola pengaturan dan informasi mengenai website anda</p>
                </div>
            </div>

            <div class="flex flex-wrap mb-4 overflow-x-auto">
                <nav class="bg-gray-100 rounded-lg p-1 font-semibold text-sm gap-2 flex flex-nowrap">
                    <button class="tab-btn-managementweb p-1.5 px-3 rounded-lg text-gray-400 cursor-pointer whitespace-nowrap hover:bg-white/50 transition-all">Lokasi Toko</button>
                    <button class="tab-btn-managementweb p-1.5 px-3 rounded-lg text-gray-400 cursor-pointer whitespace-nowrap hover:bg-white/50 transition-all">Support Tickets</button>
                </nav>
            </div>

            <!-- Tab 1: Lokasi Toko -->
            <div class="tab-content-managementweb">
                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Lokasi Toko</h2>
                            <p class="text-sm text-gray-500">Kelola semua lokasi toko fisik Anda</p>
                        </div>
                        <button id="btnAddStore" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                            <span class="material-symbols-outlined text-xl">add</span>
                            Tambah Toko
                        </button>
                    </div>

                    <div id="storeList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="flex items-center justify-center py-12 col-span-full">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-800"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Support Tickets -->
            <div class="tab-content-managementweb hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600">confirmation_number</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900" id="statTotal">0</p>
                                <p class="text-xs text-gray-500">Total Tiket</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-yellow-600">pending</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900" id="statOpen">0</p>
                                <p class="text-xs text-gray-500">Open</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-600">sync</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900" id="statInProgress">0</p>
                                <p class="text-xs text-gray-500">In Progress</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="material-symbols-outlined text-green-600">check_circle</span>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900" id="statResolved">0</p>
                                <p class="text-xs text-gray-500">Resolved</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white shadow-md p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Support Tickets</h2>
                            <p class="text-sm text-gray-500">Kelola tiket bantuan dari pelanggan</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 mb-6">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" id="ticketSearch" placeholder="Cari tiket..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                        <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Semua Status</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>
                        <select id="filterKategori" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Semua Kategori</option>
                            <option value="General">General</option>
                            <option value="Garansi & Servis">Garansi & Servis</option>
                            <option value="Aktivasi Akun">Aktivasi Akun</option>
                            <option value="Komplain">Komplain</option>
                            <option value="Pertanyaan Produk">Pertanyaan Produk</option>
                            <option value="Status Pesanan">Status Pesanan</option>
                        </select>
                        <input type="date" id="filterDateFrom" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800" title="Dari Tanggal">
                        <input type="date" id="filterDateTo" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800" title="Sampai Tanggal">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3 text-left">ID Tiket</th>
                                    <th class="px-4 py-3 text-left">Pengaju</th>
                                    <th class="px-4 py-3 text-left">Subjek</th>
                                    <th class="px-4 py-3 text-left">Kategori</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ticketTableBody" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-800 mx-auto"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal: Add/Edit Store -->
    <div id="storeModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900" id="storeModalTitle">Tambah Lokasi Toko</h3>
                <button id="closeStoreModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="storeForm" class="p-6 space-y-4">
                <input type="hidden" id="storeId" name="id_toko">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                        <input type="text" id="namaToko" name="nama_toko" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <input type="tel" id="noTelepon" name="no_telepon" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                        <input type="text" id="kodePos" name="kode_pos" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea id="alamatToko" name="alamat" rows="2" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                        <select id="provinsiToko" name="provinsi" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 select2-provinsi">
                            <option value="">Pilih Provinsi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten <span class="text-red-500">*</span></label>
                        <select id="kotaToko" name="kota_kabupaten" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 select2-kota">
                            <option value="">Pilih Kota/Kabupaten</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                        <select id="kecamatanToko" name="kecamatan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 select2-kecamatan">
                            <option value="">Pilih Kecamatan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan</label>
                        <select id="kelurahanToko" name="kelurahan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800 select2-kelurahan">
                            <option value="">Pilih Kelurahan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                        <input type="time" id="jamBuka" name="jam_buka" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                        <input type="time" id="jamTutup" name="jam_tutup" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="isActive" name="is_active" value="1" checked class="w-4 h-4 text-gray-800 border-gray-300 rounded focus:ring-gray-800">
                    <label for="isActive" class="text-sm font-medium text-gray-700">Toko Aktif</label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" id="cancelStoreBtn" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-600 text-3xl">warning</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Hapus</h3>
                <p class="text-gray-500 mb-6" id="deleteMessage">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button id="cancelDeleteBtn" class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">Batal</button>
                    <button id="confirmDeleteBtn" class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Ticket Detail -->
    <div id="ticketModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Detail Tiket</h3>
                    <p class="text-sm text-gray-500" id="ticketIdDisplay"></p>
                </div>
                <button id="closeTicketModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="ticketDetailContent">
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js" defer></script>
    <script src="../../assets/js/admin/webManagement.js" defer></script>
</body>

</html>