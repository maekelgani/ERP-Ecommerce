<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Laporan";
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
                    <h1 class="text-2xl font-bold text-gray-800">Laporan Promo & Diskon</h1>
                    <p class="text-gray-500 mt-1">Analisis performa kampanye dan voucher</p>
                </div>
                <button onclick="exportReport()" class="flex items-center gap-2 bg-[#882426] text-white px-5 py-2.5 rounded-lg hover:bg-[#6d1d1f] transition-colors">
                    <span class="material-symbols-outlined text-lg">download</span>
                    Export Laporan
                </button>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm mb-6">
                <div class="flex flex-wrap gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" id="dateFrom" class="px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" id="dateTo" class="px-4 py-2 border border-gray-200 rounded-lg">
                    </div>
                    <div class="flex items-end">
                        <button onclick="loadReport()" class="px-6 py-2 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f] transition-colors">
                            Tampilkan
                        </button>
                    </div>
                </div>
            </div>

            <div id="summaryCards" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Performa Kampanye</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kampanye</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Produk</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Penggunaan</th>
                                </tr>
                            </thead>
                            <tbody id="campaignPerformance" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                    <div class="p-4 border-b border-gray-100">
                        <h2 class="font-semibold text-gray-800">Performa Voucher</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kuota</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Diskon</th>
                                </tr>
                            </thead>
                            <tbody id="voucherPerformance" class="divide-y divide-gray-100">
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="p-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Distribusi Diskon per Jenis</h2>
                </div>
                <div id="typeDistribution" class="p-6">
                    <p class="text-gray-500 text-center">Memuat data...</p>
                </div>
            </div>
    </div>
    </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);

            document.getElementById('dateFrom').value = firstDay.toISOString().split('T')[0];
            document.getElementById('dateTo').value = today.toISOString().split('T')[0];

            loadReport();
            loadCampaignPerformance();
            loadVoucherPerformance();
        });

        async function loadReport() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            try {
                const response = await fetch(`../../app/controllers/promoReportController.php?action=summary_report&date_from=${dateFrom}&date_to=${dateTo}`);
                const data = await response.json();

                if (data.success) {
                    renderSummary(data.data);
                    renderTypeDistribution(data.data.by_type);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderSummary(data) {
            const summary = data.summary;

            document.getElementById('summaryCards').innerHTML = `
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600">redeem</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Redemption</p>
                            <p class="text-2xl font-bold text-gray-800">${summary.total_redemptions || 0}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-red-600">savings</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Diskon Diberikan</p>
                            <p class="text-2xl font-bold text-gray-800">Rp ${Number(summary.total_discount_given || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-purple-600">confirmation_number</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Voucher Digunakan</p>
                            <p class="text-2xl font-bold text-gray-800">${summary.unique_vouchers_used || 0}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-emerald-600">group</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">User Unik</p>
                            <p class="text-2xl font-bold text-gray-800">${summary.unique_users || 0}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderTypeDistribution(byType) {
            const container = document.getElementById('typeDistribution');

            if (!byType || byType.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center">Tidak ada data untuk periode ini</p>';
                return;
            }

            const total = byType.reduce((sum, t) => sum + parseInt(t.count || 0), 0);

            const typeLabels = {
                'diskon_persen': {
                    label: 'Diskon Persen',
                    color: 'bg-blue-500'
                },
                'diskon_nominal': {
                    label: 'Diskon Nominal',
                    color: 'bg-green-500'
                },
                'gratis_ongkir': {
                    label: 'Gratis Ongkir',
                    color: 'bg-teal-500'
                },
                'cashback': {
                    label: 'Cashback',
                    color: 'bg-purple-500'
                }
            };

            container.innerHTML = `
                <div class="flex gap-2 h-8 rounded-lg overflow-hidden mb-4">
                    ${byType.map(t => {
                        const info = typeLabels[t.jenis] || { label: t.jenis, color: 'bg-gray-500' };
                        const percentage = total > 0 ? (parseInt(t.count) / total * 100) : 0;
                        return ` < div class = "${info.color}"
            style = "width: ${percentage}%"
            title = "${info.label}: ${t.count}" > < /div>`;
        }).join('')
        } <
        /div> <
        div class = "grid grid-cols-2 md:grid-cols-4 gap-4" >
        $ {
            byType.map(t => {
                const info = typeLabels[t.jenis] || {
                    label: t.jenis,
                    color: 'bg-gray-500'
                };
                return `
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded ${info.color}"></div>
                                <div>
                                    <p class="text-sm font-medium">${info.label}</p>
                                    <p class="text-xs text-gray-500">${t.count} penggunaan (Rp ${Number(t.total_discount || 0).toLocaleString('id-ID')})</p>
                                </div>
                            </div>
                        `;
            }).join('')
        } <
        /div>
        `;
        }
        
        async function loadCampaignPerformance() {
            try {
                const response = await fetch('../../app/controllers/promoReportController.php?action=campaign_performance');
                const data = await response.json();
                
                const tbody = document.getElementById('campaignPerformance');
                
                if (data.success && data.data.length > 0) {
                    const tipeLabels = {
                        'diskon_produk': 'Diskon Produk',
                        'voucher': 'Voucher',
                        'flash_sale': 'Flash Sale',
                        'bundle': 'Bundle',
                        'gratis_ongkir': 'Gratis Ongkir'
                    };
                    
                    tbody.innerHTML = data.data.map(c => ` <
        tr class = "hover:bg-gray-50" >
        <
        td class = "px-4 py-3" >
        <
        p class = "font-medium" > $ {
            c.judul
        } < /p> <
        span class = "text-xs text-gray-500" > $ {
            c.status
        } < /span> <
        /td> <
        td class = "px-4 py-3 text-sm text-gray-600" > $ {
            tipeLabels[c.tipe] || c.tipe
        } < /td> <
        td class = "px-4 py-3 text-center" > $ {
            c.total_produk || 0
        } < /td> <
        td class = "px-4 py-3 text-center" >
        $ {
            c.kuota_total ? `${c.kuota_terpakai || 0}/${c.kuota_total}` : (c.kuota_terpakai || 0)
        } <
        /td> <
        /tr>
        `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        async function loadVoucherPerformance() {
            try {
                const response = await fetch('../../app/controllers/promoReportController.php?action=voucher_performance');
                const data = await response.json();
                
                const tbody = document.getElementById('voucherPerformance');
                
                if (data.success && data.data.length > 0) {
                    const jenisLabels = {
                        'diskon_persen': 'Persen',
                        'diskon_nominal': 'Nominal',
                        'gratis_ongkir': 'Ongkir',
                        'cashback': 'Cashback'
                    };
                    
                    tbody.innerHTML = data.data.map(v => ` <
        tr class = "hover:bg-gray-50" >
        <
        td class = "px-4 py-3" >
        <
        p class = "font-mono font-medium text-[#882426]" > $ {
            v.kode
        } < /p> <
        span class = "text-xs text-gray-500" > $ {
            v.judul || '-'
        } < /span> <
        /td> <
        td class = "px-4 py-3 text-sm text-gray-600" > $ {
            jenisLabels[v.jenis] || v.jenis
        } < /td> <
        td class = "px-4 py-3 text-center" >
        $ {
            v.kuota_total ? `${v.kuota_terpakai || 0}/${v.kuota_total}` : (v.total_penggunaan || 0)
        } <
        /td> <
        td class = "px-4 py-3 text-right font-medium text-[#882426]" >
        Rp $ {
            Number(v.total_diskon || 0).toLocaleString('id-ID')
        } <
        /td> <
        /tr>
        `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        function exportReport() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            window.location.href = `.. / .. / app / controllers / promoReportController.php ? action = export_report & date_from = $ {
            dateFrom
        } & date_to = $ {
            dateTo
        }
        `;
        }
    </script>
</body>

</html>