<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Report";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6 flex justify-between items-end">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Laporan</h1>
                    <p class="text-gray-500 mt-1">Buat dan unduh laporan bisnis Anda</p>
                </div>
                <div>
                    <a href="FormPengeluaran.php" class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-medium">
                        <span class="material-symbols-outlined text-lg">add_card</span>
                        Input Pengeluaran
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 mb-6">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Generate Report</h2>
                    <p class="text-gray-500 text-sm">Pilih periode waktu dan format file untuk laporan Anda</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Periode Waktu</label>
                        <select id="periodSelect" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] focus:outline-none">
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month" selected>Bulan Ini</option>
                            <option value="last_month">Bulan Lalu</option>
                            <option value="6months">6 Bulan Terakhir</option>
                            <option value="year">Tahun Ini</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Format File</label>
                        <select id="formatSelect" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] focus:outline-none">
                            <option value="pdf">PDF (Cetak)</option>
                            <option value="excel">Excel / CSV</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- CARD BARU -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-xl p-6 text-gray-500 shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Pendapatan (Revenue)</p>
                        <h3 id="txtRevenue" class="text-2xl font-bold text-green-600">Rp 0</h3>
                    </div>
                    <span class="material-symbols-outlined absolute right-4 bottom-4 text-white/10 text-6xl">trending_up</span>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Pengeluaran (Expenses)</p>
                        <h3 id="txtExpense" class="text-2xl font-bold text-red-600">Rp 0</h3>
                    </div>
                    <span class="material-symbols-outlined absolute right-4 bottom-4 text-red-100 text-6xl">trending_down</span>
                </div>

                <div class="bg-[#882426] rounded-xl p-6 text-white shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-white text-sm font-medium mb-1">Laba Bersih (Net Profit)</p>
                        <h3 id="txtProfit" class="text-2xl font-bold text-white">Rp 0</h3>
                        <p class="text-xs text-white mt-1">*Pendapatan - Pengeluaran</p>
                    </div>
                    <span class="material-symbols-outlined absolute right-4 bottom-4 text-white/10 text-6xl">account_balance_wallet</span>
                </div>
            </div>
            <!-- ============ -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-blue-600">trending_up</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Laporan Penjualan</h3>
                            <p class="text-gray-500 text-sm">Detail mengenai data penjualan termasuk pendapatan, pembelian dan kategori yang sedang tren</p>
                        </div>
                    </div>
                    <div id="salesSummary" class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Total Order</p>
                            <p id="salesOrders" class="text-lg font-bold text-gray-800">-</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Pendapatan</p>
                            <p id="salesRevenue" class="text-lg font-bold text-[#882426]">-</p>
                        </div>
                    </div>
                    <button onclick="downloadReport('sales')" class="w-full bg-[#882426] hover:bg-[#6d1d1f] text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Download
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-purple-600">shopping_bag</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Laporan Pembelian</h3>
                            <p class="text-gray-500 text-sm">Detail lengkap penjualan dengan status dan detail pengiriman</p>
                        </div>
                    </div>
                    <div id="ordersSummary" class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Total Pesanan</p>
                            <p id="ordersCount" class="text-lg font-bold text-gray-800">-</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Selesai</p>
                            <p id="ordersComplete" class="text-lg font-bold text-emerald-600">-</p>
                        </div>
                    </div>
                    <button onclick="downloadReport('orders')" class="w-full bg-[#882426] hover:bg-[#6d1d1f] text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Download
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-amber-600">group</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Laporan Pelanggan</h3>
                            <p class="text-gray-500 text-sm">Detail mengenai data customer termasuk pendaftaran dan riwayat pembelian</p>
                        </div>
                    </div>
                    <div id="customersSummary" class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Total Pelanggan</p>
                            <p id="customersCount" class="text-lg font-bold text-gray-800">-</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Pelanggan Aktif</p>
                            <p id="customersActive" class="text-lg font-bold text-emerald-600">-</p>
                        </div>
                    </div>
                    <button onclick="downloadReport('customers')" class="w-full bg-[#882426] hover:bg-[#6d1d1f] text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Download
                    </button>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-emerald-600">inventory_2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Laporan Persediaan</h3>
                            <p class="text-gray-500 text-sm">Detail stok produk, status ketersediaan dan pergerakan stok</p>
                        </div>
                    </div>
                    <div id="inventorySummary" class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Total Produk</p>
                            <p id="inventoryProducts" class="text-lg font-bold text-gray-800">-</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500">Stok Menipis</p>
                            <p id="inventoryLow" class="text-lg font-bold text-amber-600">-</p>
                        </div>
                    </div>
                    <button onclick="downloadReport('inventory')" class="w-full bg-[#882426] hover:bg-[#6d1d1f] text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-lg">download</span>
                        Download
                    </button>
                </div>
            </div>

            <div class="mt-6 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-800">Produk Terlaris</h2>
                    <span class="text-sm text-gray-500">Berdasarkan periode terpilih</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Terjual</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody id="topProductsTable" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadReportData();

            document.getElementById('periodSelect').addEventListener('change', loadReportData);
        });

        async function loadReportData() {
            const period = document.getElementById('periodSelect').value;

            try {
                const [salesRes, ordersRes, customersRes, inventoryRes, topProductsRes] = await Promise.all([
                    fetch(`../../app/controllers/reportController.php?action=sales&period=${period}`),
                    fetch(`../../app/controllers/reportController.php?action=orders&period=${period}`),
                    fetch(`../../app/controllers/reportController.php?action=customers&period=${period}`),
                    fetch(`../../app/controllers/reportController.php?action=inventory`),
                    fetch(`../../app/controllers/reportController.php?action=top_products&period=${period}&limit=10`)
                ]);

                const resFinance = await fetch(`../../app/controllers/reportController.php?action=financial_summary&period=${period}`);
                const jsonFinance = await resFinance.json();

                if (jsonFinance.success) {
                    // 1. Update Pendapatan (Revenue)
                    document.getElementById('txtRevenue').textContent = formatRupiah(jsonFinance.data.revenue);

                    // 2. Update Pengeluaran (Expense) -> ID ini ada di HTML Card "Total Pengeluaran"
                    document.getElementById('txtExpense').textContent = formatRupiah(jsonFinance.data.expense);

                    // 3. Update Laba Bersih (Net Profit) -> ID ini ada di HTML Card "Laba Bersih"
                    document.getElementById('txtProfit').textContent = formatRupiah(jsonFinance.data.net_profit);

                    // Ganti warna teks Laba Bersih dinamis (Merah jika rugi, Putih/Hijau jika untung)
                    const profitElem = document.getElementById('txtProfit');
                    if (jsonFinance.data.net_profit < 0) {
                        profitElem.classList.add('text-red-300'); // Jika minus warnanya agak merah
                        profitElem.innerText = "- " + formatRupiah(Math.abs(jsonFinance.data.net_profit));
                    } else {
                        profitElem.classList.remove('text-red-300');
                    }
                }

                const [sales, orders, customers, inventory, topProducts] = await Promise.all([
                    salesRes.json(),
                    ordersRes.json(),
                    customersRes.json(),
                    inventoryRes.json(),
                    topProductsRes.json()
                ]);

                if (sales.success) {
                    document.getElementById('salesOrders').textContent = sales.data.summary.total_orders;
                    document.getElementById('salesRevenue').textContent = formatRupiah(sales.data.summary.total_revenue);
                }

                if (orders.success) {
                    document.getElementById('ordersCount').textContent = orders.data.orders.length;
                    const complete = orders.data.status_breakdown.find(s => s.status_order === 'selesai');
                    document.getElementById('ordersComplete').textContent = complete ? complete.count : 0;
                }

                if (customers.success) {
                    document.getElementById('customersCount').textContent = customers.data.summary.total_customers;
                    document.getElementById('customersActive').textContent = customers.data.summary.active_users;
                }

                if (inventory.success) {
                    document.getElementById('inventoryProducts').textContent = inventory.data.summary.total_products;
                    document.getElementById('inventoryLow').textContent = inventory.data.summary.low_stock;
                }

                if (topProducts.success) {
                    renderTopProducts(topProducts.data);
                }

            } catch (error) {
                console.error('Error loading report data:', error);
            }
        }

        function renderTopProducts(products) {
            const tbody = document.getElementById('topProductsTable');

            if (!products || products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data penjualan</td></tr>';
                return;
            }

            tbody.innerHTML = products.map((p, i) => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="w-6 h-6 rounded-full bg-[#882426] text-white text-xs font-bold flex items-center justify-center">${i + 1}</span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-800 truncate max-w-xs">${p.nama_product}</p>
                    </td>
                    <td class="px-4 py-3 text-gray-600">${p.nama_kategori || '-'}</td>
                    <td class="px-4 py-3 text-right text-gray-600">${formatRupiah(p.harga)}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${p.qty_sold} unit
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-[#882426]">${formatRupiah(p.revenue)}</td>
                </tr>
            `).join('');
        }

        function downloadReport(type) {
            const period = document.getElementById('periodSelect').value;
            const format = document.getElementById('formatSelect').value;

            const action = format === 'pdf' ? 'export_pdf' : 'export_excel';
            const url = `../../app/controllers/reportController.php?action=${action}&type=${type}&period=${period}`;

            if (format === 'pdf') {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }

        function formatRupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        }
    </script>
</body>

</html>