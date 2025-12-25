<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Voucher";
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

    <div id="voucherModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h2 id="modalTitle" class="text-lg font-semibold">Tambah Voucher</h2>
                <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="voucherForm" class="p-6 space-y-4">
                <input type="hidden" id="voucherId" name="id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="text" name="kode" id="kode" required class="flex-1 px-4 py-2 border border-gray-200 rounded-lg uppercase" placeholder="KODE123">
                            <button type="button" onclick="generateCode()" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                                <span class="material-symbols-outlined text-lg">autorenew</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                        <input type="text" name="judul" id="judul" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Nama voucher">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Syarat dan ketentuan"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Voucher <span class="text-red-500">*</span></label>
                        <select name="jenis" id="jenis" required class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                            <option value="diskon_persen">Diskon Persen (%)</option>
                            <option value="diskon_nominal">Diskon Nominal (Rp)</option>
                        </select>
                    </div>
                    <div id="nilaiContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai <span class="text-red-500">*</span></label>
                        <input type="number" name="nilai" id="nilai" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="0">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min. Belanja</label>
                        <input type="number" name="minimal_belanja" id="minimal_belanja" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Diskon</label>
                        <input type="number" name="maksimal_diskon" id="maksimal_diskon" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Unlimited">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Total</label>
                        <input type="number" name="kuota_total" id="kuota_total" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Unlimited">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota per User</label>
                        <input type="number" name="kuota_per_pengguna" id="kuota_per_pengguna" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg" placeholder="Unlimited">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg">
                        <option value="terjadwal">Terjadwal</option>
                        <option value="aktif">Aktif</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f]">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="usageModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Riwayat Penggunaan Voucher</h2>
                <button onclick="closeUsageModal()" class="p-1 hover:bg-gray-100 rounded-lg">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <div id="usageSummary" class="grid grid-cols-2 gap-4 mb-4">
                </div>
                <div id="usageList" class="space-y-3">
                    <p class="text-gray-500 text-center py-4">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let vouchers = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadVouchers();

            document.getElementById('searchInput').addEventListener('input', debounce(loadVouchers, 300));
            document.getElementById('filterJenis').addEventListener('change', loadVouchers);
            document.getElementById('filterStatus').addEventListener('change', loadVouchers);
            document.getElementById('jenis').addEventListener('change', toggleNilaiField);
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

            // Tambahkan mapping untuk warna dot indicator
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
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold animate-pulse ${statusColors[v.status] || statusColors.terjadwal}">
                                    <span class="w-1.5 h-1.5 rounded-full ${statusDotColors[v.status] || statusDotColors.terjadwal} mr-1.5"></span>
                                    ${v.status.charAt(0).toUpperCase() + v.status.slice(1)}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="viewUsage('${v.id_voucher}')" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded" title="Lihat Penggunaan">
                                        <span class="material-symbols-outlined text-lg">analytics</span>
                                    </button>
                                    <button onclick="editVoucher('${v.id_voucher}')" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </button>
                                    <button onclick="deleteVoucher('${v.id_voucher}')" class="p-1.5 text-red-500 hover:bg-red-50 rounded">
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
            document.getElementById('voucherForm').reset();
            document.getElementById('voucherId').value = '';
            document.getElementById('nilaiContainer').style.display = 'block';
            document.getElementById('voucherModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('voucherModal').classList.add('hidden');
        }

        async function generateCode() {
            try {
                const response = await fetch('../../app/controllers/voucherController.php?action=generate_code&length=8');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('kode').value = data.code;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function editVoucher(id) {
            try {
                const response = await fetch(`../../app/controllers/voucherController.php?action=get&id=${id}`);
                const data = await response.json();

                if (data.success) {
                    const v = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Voucher';
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
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        }

        async function deleteVoucher(id) {
            const result = await Swal.fire({
                title: 'Hapus Voucher?',
                text: 'Voucher yang dihapus tidak dapat dikembalikan',
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

                    const response = await fetch('../../app/controllers/voucherController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Berhasil', data.message, 'success');
                        loadVouchers();
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            }
        }

        document.getElementById('voucherForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const voucherId = document.getElementById('voucherId').value;
            formData.append('action', voucherId ? 'update' : 'create');

            try {
                const response = await fetch('../../app/controllers/voucherController.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire('Berhasil', data.message, 'success');
                    closeModal();
                    loadVouchers();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });

        async function viewUsage(voucherId) {
            try {
                const response = await fetch(`../../app/controllers/voucherController.php?action=get_usage&voucher_id=${voucherId}`);
                const data = await response.json();

                if (data.success) {
                    const usage = data.data;

                    document.getElementById('usageSummary').innerHTML = `
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-2xl font-bold text-gray-800">${usage.total_count}</p>
                                <p class="text-sm text-gray-500">Total Penggunaan</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-2xl font-bold text-[#882426]">Rp ${Number(usage.total_discount).toLocaleString('id-ID')}</p>
                                <p class="text-sm text-gray-500">Total Diskon Diberikan</p>
                            </div>
                        `;

                    if (usage.usage.length > 0) {
                        document.getElementById('usageList').innerHTML = usage.usage.map(u => `
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium">${u.nama_pengguna || 'Guest'}</p>
                                        <p class="text-sm text-gray-500">${new Date(u.digunakan_pada).toLocaleString('id-ID')}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-[#882426]">-Rp ${Number(u.jumlah_diskon).toLocaleString('id-ID')}</p>
                                        ${u.id_pesanan ? `<p class="text-xs text-gray-500">Order: ${u.id_pesanan}</p>` : ''}
                                    </div>
                                </div>
                            `).join('');
                    } else {
                        document.getElementById('usageList').innerHTML = '<p class="text-gray-500 text-center py-4">Belum ada penggunaan</p>';
                    }

                    document.getElementById('usageModal').classList.remove('hidden');
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        }

        function closeUsageModal() {
            document.getElementById('usageModal').classList.add('hidden');
        }

        function exportCSV() {
            const params = new URLSearchParams({
                action: 'export',
                status: document.getElementById('filterStatus').value,
                jenis: document.getElementById('filterJenis').value,
                search: document.getElementById('searchInput').value
            });
            window.location.href = `../../app/controllers/voucherController.php?${params}`;
        }
    </script>
</body>

</html>