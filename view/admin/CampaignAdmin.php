<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Kampanye";
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

    <div id="campaignModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h2 id="modalTitle" class="text-lg font-semibold">Tambah Kampanye Baru</h2>
                <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form id="campaignForm" class="p-6 space-y-4" enctype="multipart/form-data">
                <input type="hidden" id="campaignId" name="id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kampanye <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" id="judul" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Contoh: Flash Sale Akhir Tahun">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Deskripsi singkat tentang kampanye ini"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kampanye <span class="text-red-500">*</span></label>
                        <select name="tipe" id="tipe" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            <option value="">Pilih Tipe</option>
                            <option value="diskon_produk">Diskon Produk</option>
                            <option value="voucher">Voucher</option>
                            <option value="flash_sale">Flash Sale</option>
                            <option value="bundle">Bundle</option>
                            <option value="gratis_ongkir">Gratis Ongkir</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                            <option value="draf">Draf</option>
                            <option value="aktif">Aktif</option>
                            <option value="berakhir">Berakhir</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="mulai_pada" id="mulai_pada" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="selesai_pada" id="selesai_pada" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Total</label>
                        <input type="number" name="kuota_total" id="kuota_total" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]" placeholder="Kosongkan jika unlimited">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banner Kampanye</label>
                        <input type="file" name="banner" id="banner" accept="image/*" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF, WebP. Maks: 5MB</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f] transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 p-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Kelola Produk Kampanye</h2>
                <button onclick="closeProductModal()" class="p-1 hover:bg-gray-100 rounded-lg transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6">
                <input type="hidden" id="selectedCampaignId">

                <div class="mb-4">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <span class="material-symbols-outlined text-lg">search</span>
                        </span>
                        <input type="text" id="productSearch" placeholder="Cari produk untuk ditambahkan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-700 mb-3">Produk Tersedia</h3>
                        <div id="availableProducts" class="space-y-2 max-h-[400px] overflow-y-auto">
                            <p class="text-gray-500 text-center py-4">Memuat produk...</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-700 mb-3">Produk Terpilih</h3>
                        <div id="selectedProducts" class="space-y-2 max-h-[400px] overflow-y-auto">
                            <p class="text-gray-500 text-center py-4">Belum ada produk dipilih</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let campaigns = [];

        document.addEventListener('DOMContentLoaded', () => {
            loadCampaigns();

            document.getElementById('searchInput').addEventListener('input', debounce(loadCampaigns, 300));
            document.getElementById('filterTipe').addEventListener('change', loadCampaigns);
            document.getElementById('filterStatus').addEventListener('change', loadCampaigns);
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
            document.getElementById('campaignForm').reset();
            document.getElementById('campaignId').value = '';
            document.getElementById('campaignModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('campaignModal').classList.add('hidden');
        }

        async function editCampaign(id) {
            try {
                const response = await fetch(`../../app/controllers/promoController.php?action=get&id=${id}`);
                const data = await response.json();

                console.log('Edit campaign response:', data);

                if (data.success && data.data) {
                    const campaign = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Kampanye';
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
                } else {
                    Swal.fire('Error', data.message || 'Gagal memuat data kampanye', 'error');
                }
            } catch (error) {
                console.error('Error editing campaign:', error);
                Swal.fire('Error', 'Gagal memuat data kampanye', 'error');
            }
        }

        async function deleteCampaign(id) {
            const result = await Swal.fire({
                title: 'Hapus Kampanye?',
                text: 'Kampanye yang dihapus tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#882426',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    const response = await fetch('../../app/controllers/promoController.php', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Berhasil', data.message, 'success');
                        loadCampaigns();
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            }
        }

        document.getElementById('campaignForm').addEventListener('submit', async (e) => {
            e.preventDefault();

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
                    Swal.fire('Berhasil', data.message, 'success');
                    closeModal();
                    loadCampaigns();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            }
        });

        function openProductModal(campaignId) {
            document.getElementById('selectedCampaignId').value = campaignId;
            document.getElementById('productModal').classList.remove('hidden');
            loadCampaignProducts(campaignId);
            loadAvailableProducts(campaignId);
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        async function loadCampaignProducts(campaignId) {
            try {
                const response = await fetch(`../../app/controllers/promoController.php?action=get_products&campaign_id=${campaignId}`);
                const data = await response.json();

                const container = document.getElementById('selectedProducts');
                if (data.success && data.data && data.data.length > 0) {
                    container.innerHTML = data.data.map(p => `
                        <div class="flex items-center gap-3 p-2 border border-gray-100 rounded-lg hover:bg-gray-50">
                            <img src="../../uploads/products/${p.gambar || 'default.png'}" alt="${p.nama_product}" class="w-10 h-10 object-cover rounded" onerror="this.src='../../assets/img/default-product.png'">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate">${p.nama_product}</p>
                            </div>
                            <button onclick="detachProduct('${campaignId}', '${p.id_produk}')" class="p-1 text-red-500 hover:bg-red-50 rounded">
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
                        <div class="flex items-center gap-3 p-2 border border-gray-100 rounded-lg hover:bg-gray-50">
                            <img src="../../uploads/products/${p.gambar || 'default.png'}" alt="${p.nama_product}" class="w-10 h-10 object-cover rounded" onerror="this.src='../../assets/img/default-product.png'">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm truncate">${p.nama_product}</p>
                                <p class="text-xs text-gray-500">Rp ${Number(p.harga).toLocaleString('id-ID')}</p>
                            </div>
                            <button onclick="attachProduct('${campaignId}', '${p.id_product}')" class="p-1 text-emerald-500 hover:bg-emerald-50 rounded">
                                <span class="material-symbols-outlined text-lg">add_circle</span>
                            </button>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<p class="text-gray-500 text-center py-4">Tidak ada produk tersedia</p>';
                    console.log('Available products response:', data);
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
                    loadCampaignProducts(campaignId);
                    loadAvailableProducts(campaignId, document.getElementById('productSearch').value);
                    loadCampaigns();
                }
            } catch (error) {
                console.error('Error attaching product:', error);
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
                    loadCampaignProducts(campaignId);
                    loadAvailableProducts(campaignId, document.getElementById('productSearch').value);
                    loadCampaigns();
                }
            } catch (error) {
                console.error('Error detaching product:', error);
            }
        }
    </script>
</body>

</html>