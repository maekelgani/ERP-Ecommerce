<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Voucher";
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
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Voucher</h1>
                    <p class="text-gray-500 mt-1">Kelola voucher dan kupon diskon</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportCSV()" class="flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-200 transition-colors">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Export CSV
                    </button>
                    <button onclick="openModal()" class="flex items-center gap-2 bg-[#882426] text-white px-5 py-2.5 rounded-lg hover:bg-[#6d1d1f] transition-colors">
                        <span class="material-symbols-outlined text-lg">add</span>
                        Tambah Voucher
                    </button>
                </div>
            </div>

            <!-- Panduan Penggunaan -->
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 md:p-6 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-2xl flex-shrink-0 mt-1">local_offer</span>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-blue-800 mb-2">Panduan Lengkap Manajemen Voucher</h3>
                        <div class="text-sm text-blue-800 space-y-3">
                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">ALUR UTAMA ALGORITMA VOUCHER:</h4>
                                <p class="ml-4 leading-relaxed">
                                    Sistem voucher bekerja dengan logika: <strong>Pengguna Input Kode → Validasi Kode → Cek Status/Kuota/Periode → Terapkan Benefit</strong>.
                                    Setiap voucher unik dan dapat digunakan berkali-kali (tergantung kuota dan limit per user).
                                    Voucher otomatis berubah status berdasarkan tanggal: Terjadwal → Aktif → Berakhir.
                                </p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">ARA MEMBUAT VOUCHER BARU:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>1. Generate Kode:</strong> Klik "Tambah Voucher" → Isi atau generate kode unik (misal: SUMMER50, WELCOME20)</p>
                                    <p><strong>2. Beri Judul & Deskripsi:</strong> Isi judul voucher dan syarat/ketentuan penggunaan</p>
                                    <p><strong>3. Pilih Jenis Voucher:</strong> Tentukan tipe benefit (Diskon Persen atau Diskon Nominal)</p>
                                    <p><strong>4. Tentukan Nilai:</strong> Masukkan nilai sesuai jenis (% untuk persen, Rp untuk nominal)</p>
                                    <p><strong>5. Atur Kuota & Limit:</strong> Tentukan total kuota voucher dan maksimal penggunaan per pengguna</p>
                                    <p><strong>6. Set Periode & Status:</strong> Tentukan tanggal berlaku dan pilih status (Terjadwal/Aktif/Nonaktif)</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">JENIS-JENIS VOUCHER YANG TERSEDIA:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>Diskon Persen:</strong> Memberikan potongan harga dalam bentuk persentase dari harga total (misal 20% dari Rp 100.000 = Rp 20.000)</p>
                                    <p><strong>Diskon Nominal:</strong> Memberikan potongan harga tetap dalam rupiah (misal Rp 50.000 potongan langsung)</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">MEMAHAMI STATUS VOUCHER:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>🟢 Aktif:</strong> Voucher sudah bisa digunakan pengguna sekarang</p>
                                    <p><strong>🟡 Terjadwal:</strong> Voucher menunggu tanggal mulai, belum bisa digunakan</p>
                                    <p><strong>🔴 Berakhir:</strong> Voucher telah melewati tanggal selesai</p>
                                    <p><strong>⚪ Nonaktif:</strong> Voucher dimatikan manual, tidak bisa digunakan</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">PARAMETER PENTING YANG PERLU DIPERHATIKAN:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p><strong>Kode Voucher:</strong> Harus unik, tidak boleh sama dengan voucher lain. Format umum: UPPERCASE tanpa spasi</p>
                                    <p><strong>Jenis Voucher:</strong> Persen menghitung dari subtotal/total, Nominal adalah potongan tetap</p>
                                    <p><strong>Kuota Total:</strong> Jumlah kali maksimal voucher bisa digunakan. Jika habis, voucher tidak bisa digunakan lagi</p>
                                    <p><strong>Limit Per User:</strong> Batasan jumlah kali satu pengguna bisa menggunakan voucher yang sama</p>
                                    <p><strong>Min Pembelian:</strong> Minimal nominal pembelian agar voucher berlaku (optional, untuk kontrol kualitas transaksi)</p>
                                    <p><strong>Max Diskon:</strong> Batasan maksimal potongan yang diberikan (optional, untuk menghindari diskon terlalu besar)</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-blue-900 mb-1">TIPS MONITORING & OPTIMASI:</h4>
                                <div class="ml-4 space-y-1 text-blue-800">
                                    <p>• Pantau sisa kuota voucher secara berkala untuk mencegah kehabisan saat promosi berlangsung</p>
                                    <p>• Gunakan kode yang mudah diingat untuk meningkatkan redeemption rate (misal: SPRING2024, THANKYOU50)</p>
                                    <p>• Set minimum pembelian untuk memastikan profitabilitas transaksi dengan voucher</p>
                                    <p>• Export CSV secara berkala untuk analisis performa voucher dan ROI promosi</p>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-700">
                                    <strong>TIPS PENTING:</strong>
                                    Pastikan kode voucher UNIK dan tidak ada duplikasi. Selalu verifikasi tanggal mulai &lt; tanggal selesai.
                                    Untuk diskon persen, set max diskon agar tidak merugikan. Monitor status otomatis: pastikan voucher aktif tepat waktu.
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
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600">confirmation_number</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Voucher</p>
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
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600">schedule</span>
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
                                <input type="text" id="searchInput" placeholder="Cari kode atau judul..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            </div>
                        </div>
                        <select id="filterJenis" class="px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="">Semua Jenis</option>
                            <option value="diskon_persen">Diskon Persen</option>
                            <option value="diskon_nominal">Diskon Nominal</option>
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode & Judul</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nilai</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuota</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="voucherList" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced Voucher Modal - Consistent with CustomerList.php -->
    <div id="voucherModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all animate-modal-in max-h-[90vh] flex flex-col">
                <!-- Modern Header with Solid Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl" id="modalIcon">confirmation_number</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" id="modalTitle">Tambah Voucher</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="modalSubtitle">Buat voucher diskon baru</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form id="voucherForm" class="flex-1 overflow-hidden flex flex-col">
                    <input type="hidden" id="voucherId" name="id">

                    <div class="p-6 bg-gray-50 space-y-5 overflow-y-auto flex-1">
                        <!-- Info Alert -->
                        <div class="p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium" id="formInfoText">Lengkapi data voucher dengan benar. Pastikan kode voucher unik.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kode Voucher & Judul -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">qr_code_2</span>
                                    Kode Voucher <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input type="text" name="kode" id="kode" required
                                        class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm uppercase font-mono focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                        placeholder="KODE123">
                                    <button type="button" onclick="generateCode()" class="px-4 py-3 bg-[#882426]/10 text-[#882426] rounded-xl hover:bg-[#882426]/20 transition-colors" title="Generate Kode">
                                        <span class="material-symbols-outlined text-lg">autorenew</span>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">title</span>
                                    Judul Voucher
                                </label>
                                <input type="text" name="judul" id="judul"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Nama voucher">
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">description</span>
                                Deskripsi
                            </label>
                            <textarea name="deskripsi" id="deskripsi" rows="2"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400 resize-none"
                                placeholder="Syarat dan ketentuan voucher..."></textarea>
                        </div>

                        <!-- Jenis & Nilai -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">category</span>
                                    Jenis Voucher <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis" id="jenis" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 bg-white">
                                    <option value="diskon_persen">Diskon Persen (%)</option>
                                    <option value="diskon_nominal">Diskon Nominal (Rp)</option>
                                </select>
                            </div>
                            <div id="nilaiContainer">
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">payments</span>
                                    Nilai <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="nilai" id="nilai" min="0" step="0.01"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="0">
                            </div>
                        </div>

                        <!-- Min Belanja & Max Diskon -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">shopping_cart</span>
                                    Min. Belanja
                                </label>
                                <input type="number" name="minimal_belanja" id="minimal_belanja" min="0"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">savings</span>
                                    Maks. Diskon
                                </label>
                                <input type="number" name="maksimal_diskon" id="maksimal_diskon" min="0"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Unlimited">
                            </div>
                        </div>

                        <!-- Kuota Total & Per User -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">inventory</span>
                                    Kuota Total
                                </label>
                                <input type="number" name="kuota_total" id="kuota_total" min="0"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Unlimited">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">person</span>
                                    Kuota per User
                                </label>
                                <input type="number" name="kuota_per_pengguna" id="kuota_per_pengguna" min="0"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 placeholder:text-gray-400"
                                    placeholder="Unlimited">
                            </div>
                        </div>

                        <!-- Periode -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">event</span>
                                    Mulai
                                </label>
                                <input type="datetime-local" name="mulai_pada" id="mulai_pada"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                    <span class="material-symbols-outlined text-[#882426] text-lg">event_busy</span>
                                    Selesai
                                </label>
                                <input type="datetime-local" name="selesai_pada" id="selesai_pada"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200">
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">toggle_on</span>
                                Status
                            </label>
                            <select name="status" id="status"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] transition-all duration-200 bg-white">
                                <option value="terjadwal">Terjadwal</option>
                                <option value="aktif">Aktif</option>
                                <option value="berakhir">Berakhir</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3 flex-shrink-0">
                        <button type="button" onclick="closeModal()"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                            <span class="material-symbols-outlined text-lg">close</span>
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center gap-2 px-5 py-3 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                            <span id="submitBtnText">Simpan Voucher</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Voucher Modal - Consistent with CategoryAdmin.php -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modern Header with Primary Color -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl animate-pulse-warning">delete_forever</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Hapus Voucher</h3>
                            <p class="text-white/70 text-sm mt-0.5">Konfirmasi penghapusan</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDeleteModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 bg-gray-50 space-y-5">
                    <!-- Voucher Profile Card -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-20 h-20 rounded-2xl overflow-hidden border-4 border-white shadow-lg ring-4 ring-red-500/20 bg-gradient-to-br from-[#882426] to-[#6d1a1c] flex items-center justify-center">
                            <span class="material-symbols-outlined text-4xl text-white">confirmation_number</span>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold text-gray-800 font-mono" id="deleteVoucherCode"></p>
                            <p class="text-sm text-gray-500" id="deleteVoucherTitle"></p>
                        </div>
                    </div>

                    <!-- Warning Alert -->
                    <div class="p-4 rounded-xl border-l-4 border-red-500 bg-red-50">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-red-500 text-xl flex-shrink-0">warning</span>
                            <div>
                                <p class="text-sm font-semibold text-red-700 mb-1">Peringatan!</p>
                                <p class="text-sm text-red-600">Anda yakin ingin menghapus voucher ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua riwayat penggunaan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Voucher Details Card -->
                    <div class="bg-white rounded-xl border-2 border-gray-200 p-4 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">confirmation_number</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Kode Voucher</p>
                                <p class="text-sm font-semibold text-gray-800 font-mono" id="deleteVoucherCodeDetail">-</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100"></div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500 text-lg">local_offer</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Jenis & Nilai</p>
                                <p class="text-sm font-semibold text-gray-800" id="deleteVoucherValue">-</p>
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
                        Ya, Hapus Voucher!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Usage Modal -->
    <div id="usageModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeUsageModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all animate-modal-in max-h-[90vh] flex flex-col">
                <!-- Modern Header -->
                <div class="bg-[#882426] px-6 py-5 flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">analytics</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Riwayat Penggunaan</h3>
                            <p class="text-white/70 text-sm mt-0.5" id="usageVoucherCode">Detail penggunaan voucher</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeUsageModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 bg-gray-50 overflow-y-auto flex-1">
                    <div id="usageSummary" class="grid grid-cols-2 gap-4 mb-4">
                    </div>
                    <div id="usageList" class="space-y-3">
                        <p class="text-gray-500 text-center py-4">Memuat data...</p>
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
        let vouchers = [];
        let currentDeleteVoucher = null;

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
            loadVouchers();

            document.getElementById('searchInput').addEventListener('input', debounce(loadVouchers, 300));
            document.getElementById('filterJenis').addEventListener('change', loadVouchers);
            document.getElementById('filterStatus').addEventListener('change', loadVouchers);
            document.getElementById('jenis').addEventListener('change', toggleNilaiField);

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
                closeUsageModal();
            }
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function toggleNilaiField() {
            const container = document.getElementById('nilaiContainer');
            const input = document.getElementById('nilai');
            container.style.display = 'block';
            input.setAttribute('required', 'required');
        }

        async function loadVouchers() {
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
                const response = await fetch(`../../app/controllers/voucherController.php?${params}`);
                const data = await response.json();

                if (data.success) {
                    vouchers = data.data;
                    renderVouchers();
                    updateStats();
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error!', 'Gagal memuat data voucher');
            }
        }

        function updateStats() {
            const stats = {
                total: vouchers.length,
                aktif: 0,
                terjadwal: 0,
                berakhir: 0
            };
            vouchers.forEach(v => {
                if (stats[v.status] !== undefined) stats[v.status]++;
            });
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statAktif').textContent = stats.aktif;
            document.getElementById('statTerjadwal').textContent = stats.terjadwal;
            document.getElementById('statBerakhir').textContent = stats.berakhir;
        }

        function renderVouchers() {
            const tbody = document.getElementById('voucherList');

            if (vouchers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-12 text-center text-gray-500">Belum ada voucher</td></tr>';
                return;
            }

            const statusColors = {
                'terjadwal': 'bg-blue-100 text-blue-700',
                'aktif': 'bg-emerald-100 text-emerald-700',
                'berakhir': 'bg-red-100 text-red-700',
                'nonaktif': 'bg-gray-100 text-gray-600'
            };

            const statusDotColors = {
                'terjadwal': 'bg-blue-500',
                'aktif': 'bg-emerald-500',
                'berakhir': 'bg-red-500',
                'nonaktif': 'bg-gray-500'
            };

            const jenisLabels = {
                'diskon_persen': 'Diskon %',
                'diskon_nominal': 'Diskon Rp',
                'gratis_ongkir': 'Gratis Ongkir (Legacy)',
                'cashback': 'Cashback (Legacy)'
            };

            const jenisColors = {
                'diskon_persen': 'bg-blue-50 text-blue-600',
                'diskon_nominal': 'bg-green-50 text-green-600',
                'gratis_ongkir': 'bg-gray-50 text-gray-500',
                'cashback': 'bg-gray-50 text-gray-500'
            };

            tbody.innerHTML = vouchers.map(v => {
                const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                }) : '-';
                const nilai = v.jenis === 'diskon_persen' ? v.nilai + '%' : (v.jenis === 'gratis_ongkir' ? '-' : 'Rp ' + Number(v.nilai).toLocaleString('id-ID'));

                return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-mono font-bold text-[#882426]">${v.kode}</p>
                                    <p class="text-sm text-gray-500">${v.judul || '-'}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium ${jenisColors[v.jenis]}">${jenisLabels[v.jenis]}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">${nilai}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <p>${v.kuota_terpakai || 0}/${v.kuota_total || '∞'}</p>
                                    ${v.kuota_total ? `<div class="w-20 h-1.5 bg-gray-200 rounded-full mt-1"><div class="h-1.5 bg-[#882426] rounded-full" style="width: ${Math.min(100, (v.kuota_terpakai || 0) / v.kuota_total * 100)}%"></div></div>` : ''}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                ${formatDate(v.mulai_pada)} - ${formatDate(v.selesai_pada)}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${statusColors[v.status] || statusColors.terjadwal}">
                                    <span class="w-1.5 h-1.5 rounded-full ${statusDotColors[v.status] || statusDotColors.terjadwal} mr-1.5"></span>
                                    ${v.status.charAt(0).toUpperCase() + v.status.slice(1)}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="viewUsage('${v.id_voucher}', '${v.kode}')" class="inline-flex items-center gap-1.5 px-3 py-2 text-purple-600 bg-purple-50 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors" title="Lihat Penggunaan">
                                        <span class="material-symbols-outlined text-lg">analytics</span>
                                    </button>
                                    <button onclick="editVoucher('${v.id_voucher}')" class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button onclick="confirmDeleteVoucher('${v.id_voucher}', '${v.kode}', '${v.judul || ''}', '${v.jenis}', '${v.nilai}')" class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
            }).join('');
        }

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Voucher';
            document.getElementById('modalSubtitle').textContent = 'Buat voucher diskon baru';
            document.getElementById('modalIcon').textContent = 'add_circle';
            document.getElementById('formInfoText').textContent = 'Lengkapi data voucher dengan benar. Pastikan kode voucher unik.';
            document.getElementById('submitBtnText').textContent = 'Simpan Voucher';
            document.getElementById('voucherForm').reset();
            document.getElementById('voucherId').value = '';
            document.getElementById('nilaiContainer').style.display = 'block';
            document.getElementById('voucherModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('voucherModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function generateCode() {
            try {
                const response = await fetch('../../app/controllers/voucherController.php?action=generate_code&length=8');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('kode').value = data.code;
                    showToast('success', 'Berhasil!', 'Kode voucher berhasil digenerate');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error!', 'Gagal generate kode');
            }
        }

        async function editVoucher(id) {
            try {
                const response = await fetch(`../../app/controllers/voucherController.php?action=get&id=${id}`);
                const data = await response.json();

                if (data.success) {
                    const v = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Voucher';
                    document.getElementById('modalSubtitle').textContent = 'Perbarui data voucher: ' + v.kode;
                    document.getElementById('modalIcon').textContent = 'edit';
                    document.getElementById('formInfoText').textContent = 'Perbarui data voucher sesuai kebutuhan. Perubahan akan langsung berlaku.';
                    document.getElementById('submitBtnText').textContent = 'Simpan Perubahan';
                    document.getElementById('voucherId').value = v.id_voucher;
                    document.getElementById('kode').value = v.kode;
                    document.getElementById('judul').value = v.judul || '';
                    document.getElementById('deskripsi').value = v.deskripsi || '';
                    document.getElementById('jenis').value = v.jenis;
                    document.getElementById('nilai').value = v.nilai || '';
                    document.getElementById('minimal_belanja').value = v.minimal_belanja || '';
                    document.getElementById('maksimal_diskon').value = v.maksimal_diskon || '';
                    document.getElementById('kuota_total').value = v.kuota_total || '';
                    document.getElementById('kuota_per_pengguna').value = v.kuota_per_pengguna || '';
                    document.getElementById('mulai_pada').value = v.mulai_pada?.slice(0, 16) || '';
                    document.getElementById('selesai_pada').value = v.selesai_pada?.slice(0, 16) || '';
                    document.getElementById('status').value = v.status;
                    toggleNilaiField();
                    document.getElementById('voucherModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } catch (error) {
                showToast('error', 'Error!', 'Gagal memuat data voucher');
            }
        }

        // Delete Modal Functions
        function confirmDeleteVoucher(id, kode, judul, jenis, nilai) {
            currentDeleteVoucher = {
                id,
                kode,
                judul,
                jenis,
                nilai
            };

            // Set voucher info
            document.getElementById('deleteVoucherCode').textContent = kode;
            document.getElementById('deleteVoucherTitle').textContent = judul || 'Tidak ada judul';
            document.getElementById('deleteVoucherCodeDetail').textContent = kode;

            // Format value display
            const jenisLabels = {
                'diskon_persen': 'Diskon',
                'diskon_nominal': 'Diskon'
            };
            const nilaiFormatted = jenis === 'diskon_persen' ? nilai + '%' : 'Rp ' + Number(nilai).toLocaleString('id-ID');
            document.getElementById('deleteVoucherValue').textContent = (jenisLabels[jenis] || jenis) + ' ' + nilaiFormatted;

            // Reset checkbox and button
            document.getElementById('deleteConfirmCheck').checked = false;
            document.getElementById('deleteConfirmBtn').disabled = true;

            // Show modal
            document.getElementById('deleteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentDeleteVoucher = null;
        }

        async function executeDelete() {
            if (!currentDeleteVoucher) return;

            const confirmBtn = document.getElementById('deleteConfirmBtn');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menghapus...';

            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', currentDeleteVoucher.id);

                const response = await fetch('../../app/controllers/voucherController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                closeDeleteModal();

                if (data.success) {
                    showToast('success', 'Berhasil!', 'Voucher berhasil dihapus');
                    loadVouchers();
                } else {
                    showToast('error', 'Gagal!', data.message || 'Gagal menghapus voucher');
                }
            } catch (error) {
                closeDeleteModal();
                showToast('error', 'Error!', 'Terjadi kesalahan saat menghapus');
            }

            // Reset button
            confirmBtn.innerHTML = '<span class="material-symbols-outlined text-lg">delete_forever</span> Ya, Hapus Voucher!';
        }

        document.getElementById('voucherForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const voucherId = document.getElementById('voucherId').value;
            formData.append('action', voucherId ? 'update' : 'create');

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menyimpan...';

            try {
                const response = await fetch('../../app/controllers/voucherController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    closeModal();
                    showToast('success', 'Berhasil!', voucherId ? 'Voucher berhasil diperbarui' : 'Voucher berhasil ditambahkan');
                    loadVouchers();
                } else {
                    showToast('error', 'Gagal!', data.message || 'Gagal menyimpan voucher');
                }
            } catch (error) {
                showToast('error', 'Error!', 'Terjadi kesalahan saat menyimpan');
            }

            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> <span id="submitBtnText">Simpan Voucher</span>';
        });

        async function viewUsage(voucherId, voucherCode = '') {
            try {
                if (voucherCode) {
                    document.getElementById('usageVoucherCode').textContent = 'Kode: ' + voucherCode;
                }

                const response = await fetch(`../../app/controllers/voucherController.php?action=get_usage&voucher_id=${voucherId}`);
                const data = await response.json();

                if (data.success) {
                    const usage = data.data;

                    document.getElementById('usageSummary').innerHTML = `
                        <div class="bg-white rounded-xl p-4 text-center border-2 border-gray-200">
                            <p class="text-2xl font-bold text-gray-800">${usage.total_count}</p>
                            <p class="text-sm text-gray-500">Total Penggunaan</p>
                        </div>
                        <div class="bg-white rounded-xl p-4 text-center border-2 border-gray-200">
                            <p class="text-2xl font-bold text-[#882426]">Rp ${Number(usage.total_discount).toLocaleString('id-ID')}</p>
                            <p class="text-sm text-gray-500">Total Diskon Diberikan</p>
                        </div>
                    `;

                    if (usage.usage.length > 0) {
                        document.getElementById('usageList').innerHTML = usage.usage.map(u => `
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border-2 border-gray-200 hover:border-[#882426]/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#882426]/10 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-[#882426] text-lg">person</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">${u.nama_pengguna || 'Guest'}</p>
                                        <p class="text-sm text-gray-500">${new Date(u.digunakan_pada).toLocaleString('id-ID')}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-[#882426]">-Rp ${Number(u.jumlah_diskon).toLocaleString('id-ID')}</p>
                                    ${u.id_pesanan ? `<p class="text-xs text-gray-500">Order: ${u.id_pesanan}</p>` : ''}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        document.getElementById('usageList').innerHTML = `
                            <div class="text-center py-8">
                                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">hourglass_empty</span>
                                <p class="text-gray-500 font-medium">Belum ada penggunaan</p>
                                <p class="text-gray-400 text-sm mt-1">Voucher ini belum pernah digunakan</p>
                            </div>
                        `;
                    }

                    document.getElementById('usageModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } catch (error) {
                showToast('error', 'Error!', 'Gagal memuat data penggunaan');
            }
        }

        function closeUsageModal() {
            document.getElementById('usageModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function exportCSV() {
            const params = new URLSearchParams({
                action: 'export',
                status: document.getElementById('filterStatus').value,
                jenis: document.getElementById('filterJenis').value,
                search: document.getElementById('searchInput').value
            });
            window.location.href = `../../app/controllers/voucherController.php?${params}`;
            showToast('info', 'Export!', 'File CSV sedang diunduh...');
        }
    </script>
</body>

</html>