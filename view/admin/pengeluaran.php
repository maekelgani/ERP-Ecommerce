<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;

AuthMiddleware::requireAdminLoginFromView();
AuthMiddleware::requirePermissionFromView('manage_admin_users');

$pageTitle = "Manajemen Pengeluaran";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Pengeluaran</h1>
                    <p class="text-gray-500 mt-1">Catat dan kelola pengeluaran operasional bisnis</p>
                </div>
                <a href="ReportAdmin.php" class="text-[#882426] hover:text-[#6d1d1f] font-medium text-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                    Kembali ke Laporan
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[#882426]">add_card</span>
                            Input Pengeluaran Baru
                        </h2>
                        
                        <form id="expenseForm" onsubmit="submitExpense(event)">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal</label>
                                    <input type="date" name="date" required 
                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] outline-none">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Kategori</label>
                                    <select name="category" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] outline-none">
                                        <option value="">Pilih Kategori</option>
                                        <option value="pembelian_domain">Pembelian Domain & Server</option>
                                        <option value="gaji_karyawan">Gaji Karyawan Admin</option>
                                        <option value="barang_masuk">Restock Barang (Barang Masuk)</option>
                                        <option value="operasional">Operasional Lainnya</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Deskripsi</label>
                                    <textarea name="description" rows="3" placeholder="Contoh: Perpanjangan domain example.com"
                                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] outline-none"></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Nominal (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-2.5 text-gray-500 text-sm font-medium">Rp</span>
                                        <input type="number" name="amount" required placeholder="0"
                                            class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-[#882426] focus:border-[#882426] outline-none">
                                    </div>
                                </div>

                                <button type="submit" 
                                    class="w-full bg-[#882426] hover:bg-[#6d1d1f] text-white font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 mt-2">
                                    <span class="material-symbols-outlined text-lg">save</span>
                                    Simpan Pengeluaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                            <h2 class="font-semibold text-gray-800">Riwayat Pengeluaran</h2>
                            <select id="filterMonth" class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none">
                                <option value="this_month">Bulan Ini</option>
                                <option value="last_month">Bulan Lalu</option>
                                <option value="all">Semua</option>
                            </select>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Deskripsi</th>
                                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Nominal</th>
                                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="expenseTableBody" class="divide-y divide-gray-100">
                                    <tr>
                                        <td colspan="5" class="px-5 py-8 text-center text-gray-400">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 flex justify-center">
                            <button class="text-xs text-gray-500 hover:text-[#882426]">Lihat Lebih Banyak</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

<script>
    const tbody = document.getElementById('expenseTableBody');
    const filterSelect = document.getElementById('filterMonth');

    // Load data saat halaman dibuka
    document.addEventListener('DOMContentLoaded', () => loadExpenses());
    
    // Reload saat filter berubah
    filterSelect.addEventListener('change', () => loadExpenses());

    // 1. FUNGSI LOAD DATA (GET)
    async function loadExpenses() {
        const filter = filterSelect.value;
        tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Memuat data...</td></tr>';

        try {
            // Panggil API Backend
            const response = await fetch(`../../api/pengeluaran/list.php?filter=${filter}`);
            const result = await response.json();

            if (result.status === 'success' && result.data.length > 0) {
                renderTable(result.data);
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada data pengeluaran</td></tr>';
            }
        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-red-500">Gagal memuat data</td></tr>';
        }
    }

    function renderTable(data) {
            tbody.innerHTML = data.map(item => `
                <tr class="hover:bg-gray-50 group">
                    <td class="px-5 py-3 text-sm text-gray-600">${item.tgl_pengeluaran}</td>
                    
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getBadgeColor(item.kategori)}">
                            ${formatCategoryLabel(item.kategori)}
                        </span>
                    </td>
                    
                    <td class="px-5 py-3 text-sm text-gray-600 max-w-[200px] truncate">${item.description}</td>
                    
                    <td class="px-5 py-3 text-sm text-gray-800 font-medium text-right">${formatRupiah(item.jumlah)}</td>
                    
                    <td class="px-5 py-3 text-center">
                        <button onclick="deleteExpense(${item.id})" class="text-gray-400 hover:text-red-600 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

    // 2. FUNGSI SUBMIT (POST)
    async function submitExpense(event) {
        event.preventDefault();
        const form = document.getElementById('expenseForm');
        const formData = new FormData(form);
        
        // Konversi FormData ke JSON
        const data = Object.fromEntries(formData.entries());

        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Menyimpan...';
        btn.disabled = true;

        try {
            const response = await fetch('../../api/pengeluaran/create.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 'success') {
                form.reset();
                loadExpenses(); // Reload tabel
                alert('Data berhasil disimpan!');
            } else {
                alert('Gagal: ' + result.message);
            }
        } catch (error) {
            alert('Terjadi kesalahan sistem.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    // 3. FUNGSI DELETE
    async function deleteExpense(id) {
        if(!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

        try {
            const response = await fetch('../../api/pengeluaran/delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            
            const result = await response.json();
            if(result.status === 'success') {
                loadExpenses();
            } else {
                alert('Gagal menghapus data');
            }
        } catch (error) {
            alert('Error deleting data');
        }
    }

    // Helpers UI
    function getBadgeColor(category) {
        switch(category) {
            case 'pembelian_domain': return 'bg-blue-100 text-blue-800';
            case 'gaji_karyawan': return 'bg-purple-100 text-purple-800';
            case 'barang_masuk': return 'bg-amber-100 text-amber-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function formatCategoryLabel(cat) {
        // Ubah "gaji_karyawan" jadi "Gaji Karyawan"
        return cat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }
</script>
</body>
</html>