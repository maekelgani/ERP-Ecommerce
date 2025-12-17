<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Diskon";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
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
                    <h1 class="text-2xl font-bold text-gray-800">Diskon Produk</h1>
                    <p class="text-gray-500 mt-1">Kelola diskon per produk</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openMassModal()" class="flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                        <span class="material-symbols-outlined text-lg">playlist_add</span>
                        Mass Assign
                    </button>
                    <button onclick="openModal()" class="flex items-center gap-2 bg-[#882426] text-white px-5 py-2.5 rounded-lg hover:bg-[#6d1d1f] transition-colors">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Diskon
                    </button>
                </div>
            </div>

            <!-- Panduan Penggunaan -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 md:p-6 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-2xl flex-shrink-0 mt-1">help</span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-blue-800 mb-2">Panduan Lengkap Manajemen Diskon</h3>
                        <div class="text-sm text-blue-800 space-y-3">
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">ALUR UTAMA ALGORITMA DISKON:</h4>
                                <p class="ml-4 leading-relaxed">
                                    Sistem diskon bekerja dengan logika: <strong>Harga Normal → Terapkan Diskon → Harga Akhir</strong>.
                                    Diskon akan otomatis diaktifkan pada tanggal mulai dan deaktifkan pada tanggal selesai.
                                    Setiap transaksi akan memeriksa: apakah diskon aktif? apakah masih ada stok promo? apakah pengguna belum mencapai limit?
                                </p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">CARA MENAMBAH DISKON INDIVIDUAL:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>1. Pilih Produk:</strong> Klik "Tambah Diskon" → Pilih produk dari dropdown</p>
                                    <p><strong>2. Beri Label:</strong> Isi label untuk identifikasi (misal: "Flash Sale 50%", "Diskon Member")</p>
                                    <p><strong>3. Tentukan Jenis & Nilai:</strong> Pilih Persen (%) atau Nominal (Rp), lalu masukkan nilai diskonnya</p>
                                    <p><strong>4. Atur Stok & Limit:</strong> Tentukan stok promo total dan maksimal qty per pengguna (kosongkan jika unlimited)</p>
                                    <p><strong>5. Set Periode:</strong> Tentukan tanggal mulai dan selesai diskon</p>
                                    <p><strong>6. Pilih Status:</strong> Terjadwal (menunggu tanggal mulai), Aktif (berlaku sekarang), atau Nonaktif (tidak berlaku)</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">FITUR MASS ASSIGN (BULK DISKON):</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>Gunakan untuk:</strong> Menerapkan diskon yang sama ke banyak produk sekaligus</p>
                                    <p><strong>Langkah:</strong> Klik "Mass Assign" → Pilih produk yang ingin didiskon → Atur pengaturan diskon → Klik Simpan</p>
                                    <p><strong>Keuntungan:</strong> Hemat waktu untuk kampanye flash sale atau promosi kategori</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">MEMAHAMI STATUS DISKON:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>🟢 Aktif:</strong> Diskon sedang berlaku dan dapat digunakan pelanggan</p>
                                    <p><strong>🟡 Terjadwal:</strong> Diskon menunggu waktu mulai, belum bisa digunakan</p>
                                    <p><strong>🔴 Berakhir:</strong> Diskon telah melewati tanggal selesai</p>
                                    <p><strong>⚪ Nonaktif:</strong> Diskon dimatikan secara manual</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">PARAMETER PENTING YANG PERLU DIPERHATIKAN:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>Jenis Diskon:</strong> Persen menghitung dari harga normal, Nominal adalah potongan harga tetap</p>
                                    <p><strong>Stok Promo:</strong> Jumlah total produk yang bisa didiskon. Jika habis, diskon tidak berlaku lagi</p>
                                    <p><strong>Maks Qty/User:</strong> Batasan jumlah item yang bisa dibeli dengan diskon per pengguna per transaksi</p>
                                    <p><strong>Tanggal Mulai & Selesai:</strong> Pastikan format benar! Sistem menggunakan datetime untuk presisi jam</p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-800">
                                    <strong>TIPS PENTING:</strong>
                                    Selalu verifikasi tanggal mulai &lt; tanggal selesai. Jangan beri diskon lebih besar dari harga produk.
                                    Untuk diskon persen, nilai max 100%. Monitor stok promo untuk mencegah kehabisan di tengah promosi.
                                </p>
                            </div>
                        </div>
                    </div>
                    <button onclick="this.closest('.bg-blue-50').style.display='none'" class="text-blue-800 hover:text-blue-900 flex-shrink-0">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600">percent</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Diskon</p>
                            <p id="statTotal" class="text-xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Aktif</p>
                            <p id="statAktif" class="text-xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-amber-600">schedule</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Terjadwal</p>
                            <p id="statTerjadwal" class="text-xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-red-600">timer_off</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Berakhir</p>
                            <p id="statBerakhir" class="text-xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100">
                    <div class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <span class="material-symbols-outlined text-lg">search</span>
                                </span>
                                <input type="text" id="searchInput" placeholder="Cari produk atau label..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            </div>
                        </div>
                        <select id="filterJenis" class="px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">Semua Jenis</option>
                            <option value="persen">Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                        <select id="filterStatus" class="px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="berakhir">Berakhir</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diskon</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="diskonList" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </main>
    </div>

    <div id="diskonModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h2 id="modalTitle" class="text-lg font-semibold">Tambah Diskon</h2>
                <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="diskonForm" class="p-6 space-y-4">
                <input type="hidden" id="diskonId" name="id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk <span class="text-red-500">*</span></label>
                    <select name="id_produk" id="id_produk" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="">Pilih Produk</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label Diskon</label>
                    <input type="text" name="label" id="label" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Contoh: Flash Sale 50%">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Diskon <span class="text-red-500">*</span></label>
                        <select name="jenis" id="jenis" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="persen">Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                        <input type="number" name="nilai" id="nilai" required min="0.01" step="0.01" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Contoh: 10">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok Promo</label>
                        <input type="number" name="stok_promo" id="stok_promo" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Unlimited">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maks Qty/User</label>
                        <input type="number" name="maks_qty_per_pengguna" id="maks_qty_per_pengguna" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Unlimited">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mulai</label>
                        <input type="datetime-local" name="mulai_pada" id="mulai_pada" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selesai</label>
                        <input type="datetime-local" name="selesai_pada" id="selesai_pada" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_nonaktif" id="is_nonaktif" class="w-4 h-4 rounded border-gray-300 text-[#882426] focus:ring-[#882426]">
                            <span class="text-sm text-gray-600">Nonaktifkan diskon</span>
                        </label>
                    </div>
                    <input type="hidden" name="status" id="status" value="aktif">
                    <p class="text-xs text-gray-500 mt-2">
                        <span class="material-symbols-outlined text-sm align-middle">info</span>
                        Status akan ditentukan otomatis: <strong>Terjadwal</strong> jika waktu mulai di masa depan,
                        <strong>Aktif</strong> jika sudah memasuki periode, <strong>Berakhir</strong> jika melewati waktu selesai.
                    </p>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="massModal"
        class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4 transition-all duration-300"
        onclick="closeMassModal()">
        <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl transform transition-all duration-300"
            onclick="event.stopPropagation()">
            <div class="sticky top-0 z-10 px-6 py-5 flex items-center justify-between border-b border-gray-100" style="background: linear-gradient(135deg, #882426 0%, #6d1a1c 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">playlist_add</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Mass Assign Diskon</h2>
                        <p class="text-white/70 text-sm">Terapkan diskon ke banyak produk sekaligus</p>
                    </div>
                </div>
                <button onclick="closeMassModal()" class="p-2 hover:bg-white/10 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-white">close</span>
                </button>
            </div>
            <form id="massForm" class="overflow-y-auto max-h-[calc(90vh-140px)]">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <div class="p-6 border-r border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-[#882426]">inventory_2</span>
                            <h3 class="font-semibold text-gray-800">Pilih Produk</h3>
                        </div>
                        <div class="relative mb-4">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <span class="material-symbols-outlined text-lg">search</span>
                            </span>
                            <input type="text" id="massProductSearch" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] bg-white">
                        </div>
                        <div id="massProductList"
                            class="space-y-2 h-[340px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
                            <p class="text-gray-500 text-center py-4">Memuat produk...</p>
                        </div>
                        <div class="mt-4 p-3 bg-[#882426]/5 rounded-xl border border-[#882426]/10">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Produk terpilih:</span>
                                <span id="selectedCount" class="text-lg font-bold text-[#882426]">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-[#882426]">tune</span>
                            <h3 class="font-semibold text-gray-800">Pengaturan Diskon</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-base text-gray-400">label</span>
                                        Label Diskon
                                    </span>
                                </label>
                                <input type="text" name="mass_label" id="mass_label" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Contoh: Promo Akhir Tahun 2025">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">category</span>
                                            Jenis <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <select name="mass_jenis" id="mass_jenis" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                                        <option value="persen">Persen (%)</option>
                                        <option value="nominal">Nominal (Rp)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">price_change</span>
                                            Nilai <span class="text-red-500">*</span>
                                        </span>
                                    </label>
                                    <input type="number" name="mass_nilai" id="mass_nilai" required min="0.01" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Contoh: 10">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">inventory</span>
                                            Stok Promo
                                        </span>
                                    </label>
                                    <input type="number" name="mass_stok_promo" id="mass_stok_promo" min="0" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Unlimited">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">person</span>
                                            Maks Qty/User
                                        </span>
                                    </label>
                                    <input type="number" name="mass_maks_qty" id="mass_maks_qty" min="0" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Unlimited">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">event</span>
                                            Mulai
                                        </span>
                                    </label>
                                    <input type="datetime-local" name="mass_mulai" id="mass_mulai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-base text-gray-400">event_busy</span>
                                            Selesai
                                        </span>
                                    </label>
                                    <input type="datetime-local" name="mass_selesai" id="mass_selesai" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-base text-gray-400">toggle_on</span>
                                        Status Diskon
                                    </span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" name="mass_is_nonaktif" id="mass_is_nonaktif" class="w-5 h-5 rounded border-gray-300 text-[#882426] focus:ring-[#882426]">
                                        <span class="text-sm text-gray-600">Nonaktifkan diskon (override manual)</span>
                                    </label>
                                </div>
                                <input type="hidden" name="mass_status" id="mass_status" value="aktif">
                                <p class="text-xs text-gray-500 mt-3 flex items-start gap-1">
                                    <span class="material-symbols-outlined text-sm mt-0.5">info</span>
                                    <span>Status ditentukan otomatis: <strong class="text-amber-600">Terjadwal</strong> (waktu mulai di masa depan),
                                        <strong class="text-emerald-600">Aktif</strong> (periode berjalan), <strong class="text-red-600">Berakhir</strong> (melewati waktu selesai).</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-gray-100 p-4 flex gap-3">
                    <button type="button" onclick="closeMassModal()" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-gradient-to-r from-[#882426] to-[#a52a2c] text-white rounded-xl hover:from-[#6d1d1f] hover:to-[#882426] font-medium transition-all shadow-lg shadow-[#882426]/25">
                        <span class="flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            Terapkan Diskon
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let diskons = [];
        let products = [];
        let selectedProducts = new Set();

        document.addEventListener('DOMContentLoaded', () => {
            loadDiskons();
            loadProducts();

            document.getElementById('searchInput').addEventListener('input', debounce(loadDiskons, 300));
            document.getElementById('filterJenis').addEventListener('change', loadDiskons);
            document.getElementById('filterStatus').addEventListener('change', loadDiskons);
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        async function loadDiskons() {
            const search = document.getElementById('searchInput').value;
            const jenis = document.getElementById('filterJenis').value;
            const status = document.getElementById('filterStatus').value;

            try {
                const params = new URLSearchParams({
                    action: 'list',
                    search,
                    jenis,
                    status
                });
                const response = await fetch(`../../app/controllers/diskonController.php?${params}`);
                const data = await response.json();

                if (data.success) {
                    diskons = data.data;
                    renderDiskons();
                    updateStats();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function loadProducts() {
            try {
                const response = await fetch('../../app/controllers/diskonController.php?action=get_products');
                const data = await response.json();
                console.log('Products loaded:', data);
                if (data.success && data.data) {
                    products = data.data;
                    renderProductSelect();
                    renderMassProductList();
                } else {
                    console.error('Failed to load products:', data.message);
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        function renderProductSelect() {
            const select = document.getElementById('id_produk');
            select.innerHTML = '<option value="">Pilih Produk</option>' +
                products.map(p => `<option value="${p.id_product}">${p.nama_product} - Rp ${Number(p.harga).toLocaleString('id-ID')}</option>`).join('');
        }

        function updateStats() {
            const stats = {
                total: diskons.length,
                aktif: 0,
                terjadwal: 0,
                berakhir: 0
            };
            diskons.forEach(d => {
                const displayStatus = d.computed_status || d.status;
                if (stats[displayStatus] !== undefined) stats[displayStatus]++;
            });
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statAktif').textContent = stats.aktif;
            document.getElementById('statTerjadwal').textContent = stats.terjadwal;
            document.getElementById('statBerakhir').textContent = stats.berakhir;
        }

        function renderDiskons() {
            const tbody = document.getElementById('diskonList');

            if (diskons.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-12 text-center text-gray-500">Belum ada diskon</td></tr>';
                return;
            }

            const statusColors = {
                'terjadwal': 'bg-blue-100 text-blue-700',
                'aktif': 'bg-emerald-100 text-emerald-700',
                'berakhir': 'bg-red-100 text-red-700',
                'nonaktif': 'bg-gray-100 text-gray-600'
            };

            // Tambahkan mapping untuk warna dot indicator
            const statusDotColors = {
                'terjadwal': 'bg-blue-500',
                'aktif': 'bg-emerald-500',
                'berakhir': 'bg-red-500',
                'nonaktif': 'bg-gray-500'
            };

            tbody.innerHTML = diskons.map(d => {
                const displayStatus = d.computed_status || d.status;
                const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                }) : '-';
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="../../uploads/products/${d.gambar_produk || 'default.png'}" alt="" class="w-10 h-10 rounded object-cover" onerror="this.src='../../assets/img/default-product.png'">
                                <div>
                                    <p class="font-medium text-gray-800">${d.nama_product || 'Produk'}</p>
                                    <p class="text-xs text-gray-500">${d.label || '-'}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-[#882426]">${d.jenis === 'persen' ? d.nilai + '%' : 'Rp ' + Number(d.nilai).toLocaleString('id-ID')}</span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-400 line-through text-sm">Rp ${Number(d.harga_awal || 0).toLocaleString('id-ID')}</p>
                            <p class="font-medium text-emerald-600">Rp ${Number(d.harga_diskon || 0).toLocaleString('id-ID')}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            ${formatDate(d.mulai_pada)} - ${formatDate(d.selesai_pada)}
                        </td>
                        <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${displayStatus === 'aktif' ? 'animate-pulse' : ''} ${statusColors[displayStatus] || statusColors.terjadwal}">
                                    <span class="w-1.5 h-1.5 rounded-full ${statusDotColors[displayStatus] || statusDotColors.terjadwal} mr-1.5"></span>
                                    ${displayStatus.charAt(0).toUpperCase() + displayStatus.slice(1)}
                                </span>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editDiskon('${d.id_diskon}')" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <button onclick="deleteDiskon('${d.id_diskon}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Diskon';
            document.getElementById('diskonForm').reset();
            document.getElementById('diskonId').value = '';
            document.getElementById('diskonModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('diskonModal').classList.add('hidden');
        }

        async function editDiskon(id) {
            try {
                const response = await fetch(`../../app/controllers/diskonController.php?action=get&id=${id}`);
                const data = await response.json();

                if (data.success) {
                    const d = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Diskon';
                    document.getElementById('diskonId').value = d.id_diskon;
                    document.getElementById('id_produk').value = d.id_produk || '';
                    document.getElementById('label').value = d.label || '';
                    document.getElementById('jenis').value = d.jenis;
                    document.getElementById('nilai').value = d.nilai;
                    document.getElementById('stok_promo').value = d.stok_promo || '';
                    document.getElementById('maks_qty_per_pengguna').value = d.maks_qty_per_pengguna || '';
                    document.getElementById('mulai_pada').value = d.mulai_pada?.slice(0, 16) || '';
                    document.getElementById('selesai_pada').value = d.selesai_pada?.slice(0, 16) || '';
                    document.getElementById('is_nonaktif').checked = d.status === 'nonaktif';
                    document.getElementById('status').value = d.status;
                    document.getElementById('diskonModal').classList.remove('hidden');
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        }

        async function deleteDiskon(id) {
            const result = await Swal.fire({
                title: 'Hapus Diskon?',
                text: 'Diskon yang dihapus tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#882426',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    const response = await fetch('../../app/controllers/diskonController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Berhasil', data.message, 'success');
                        loadDiskons();
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            }
        }

        document.getElementById('diskonForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const nilaiField = document.getElementById('nilai');
            const nilai = parseFloat(nilaiField.value);
            if (isNaN(nilai) || nilai <= 0) {
                Swal.fire('Perhatian', 'Masukkan nilai diskon yang valid (lebih dari 0)', 'warning');
                nilaiField.focus();
                return;
            }

            const formData = new FormData(e.target);
            const diskonId = document.getElementById('diskonId').value;
            formData.append('action', diskonId ? 'update' : 'create');

            const isNonaktif = document.getElementById('is_nonaktif').checked;
            formData.set('status', isNonaktif ? 'nonaktif' : 'aktif');

            console.log('Submitting diskon form:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            try {
                const response = await fetch('../../app/controllers/diskonController.php', {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text();
                console.log('Response:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    Swal.fire('Error', 'Server response invalid: ' + responseText.substring(0, 100), 'error');
                    return;
                }

                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success');
                    closeModal();
                    loadDiskons();
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Submit error:', error);
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
            }
        });

        function openMassModal() {
            selectedProducts.clear();
            document.getElementById('selectedCount').textContent = '0';
            document.getElementById('massModal').classList.remove('hidden');
            renderMassProductList();
        }

        function closeMassModal() {
            document.getElementById('massModal').classList.add('hidden');
        }

        function renderMassProductList(search = '') {
            const container = document.getElementById('massProductList');
            const filtered = products.filter(p => p.nama_product.toLowerCase().includes(search.toLowerCase()));

            if (filtered.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada produk</p>';
                return;
            }
            container.innerHTML = filtered.map(p => `
                <label class="flex items-center gap-3 p-2 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" value="${p.id_product}" ${selectedProducts.has(p.id_product) ? 'checked' : ''} onchange="toggleProduct('${p.id_product}')" class="rounded text-[#882426]">
                    <img src="../../uploads/products/${p.gambar || 'default.png'}" alt="" class="w-8 h-8 rounded object-cover" onerror="this.src='../../assets/img/default-product.png'">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">${p.nama_product}</p>
                        <p class="text-xs text-gray-500">Rp ${Number(p.harga).toLocaleString('id-ID')}</p>
                    </div>
                </label>
            `).join('');
        }

        function toggleProduct(id) {
            if (selectedProducts.has(id)) {
                selectedProducts.delete(id);
            } else {
                selectedProducts.add(id);
            }
            document.getElementById('selectedCount').textContent = selectedProducts.size;
        }

        document.getElementById('massProductSearch').addEventListener('input', (e) => {
            renderMassProductList(e.target.value);
        });

        document.getElementById('massForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (selectedProducts.size === 0) {
                Swal.fire('Perhatian', 'Pilih minimal satu produk', 'warning');
                return;
            }

            const nilaiValue = document.getElementById('mass_nilai').value;
            if (!nilaiValue || parseFloat(nilaiValue) <= 0) {
                Swal.fire('Perhatian', 'Masukkan nilai diskon yang valid', 'warning');
                return;
            }

            const isNonaktif = document.getElementById('mass_is_nonaktif').checked;

            const formData = new FormData();
            formData.append('action', 'mass_create');
            formData.append('product_ids', JSON.stringify([...selectedProducts]));
            formData.append('label', document.getElementById('mass_label').value);
            formData.append('jenis', document.getElementById('mass_jenis').value);
            formData.append('nilai', nilaiValue);
            formData.append('stok_promo', document.getElementById('mass_stok_promo').value);
            formData.append('maks_qty_per_pengguna', document.getElementById('mass_maks_qty').value);
            formData.append('mulai_pada', document.getElementById('mass_mulai').value);
            formData.append('selesai_pada', document.getElementById('mass_selesai').value);
            formData.append('status', isNonaktif ? 'nonaktif' : 'aktif');

            console.log('Mass create form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            try {
                const response = await fetch('../../app/controllers/diskonController.php', {
                    method: 'POST',
                    body: formData
                });

                const responseText = await response.text();
                console.log('Mass create response:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    Swal.fire('Error', 'Server response invalid', 'error');
                    return;
                }

                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success');
                    closeMassModal();
                    loadDiskons();
                    document.getElementById('massForm').reset();
                    selectedProducts.clear();
                    document.getElementById('selectedCount').textContent = '0';
                } else {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan', 'error');
                }
            } catch (error) {
                console.error('Mass create error:', error);
                Swal.fire('Error', 'Terjadi kesalahan koneksi', 'error');
            }
        });
    </script>
</body>

</html>