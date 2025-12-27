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
                        <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-gray-500" id="pageInfo">
                                Menampilkan data...
                            </span>
                            <div class="flex gap-2">
                                <button onclick="changePage('prev')" id="btnPrev" disabled
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>
                                <button onclick="changePage('next')" id="btnNext" disabled
                                    class="px-3 py-1 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- MODAL: Konfirmasi DELETE -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl transform transition-all">
            <div class="text-center">

                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-red-600 text-3xl">warning</span>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Pengeluaran</h3>
                <p class="text-gray-500 mb-6">
                    Apakah Anda yakin ingin menghapus data pengeluaran ini? <br>
                    Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200">
                        Batal
                    </button>
                    <button type="button" onclick="executeDelete()"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Animations & Styles -->
    <style>
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

        @keyframes circular-progress {
            from {
                stroke-dashoffset: 100;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        .circular-progress {
            animation: circular-progress linear forwards;
        }
    </style>

    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-5 right-5 z-[100] flex flex-col gap-3"></div>

    <script>
        // ========== TOAST NOTIFICATION SYSTEM WITH CIRCULAR PROGRESS ==========
        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toastContainer');
            const id = 'toast-' + Date.now();
            const toast = document.createElement('div');
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

        // ========== EXPENSE MANAGEMENT SCRIPTS ==========
        const tbody = document.getElementById('expenseTableBody');
        const filterSelect = document.getElementById('filterMonth');
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        const pageInfo = document.getElementById('pageInfo');
        let currentPage = 1;
        let totalPages = 1;
        let deleteTargetId = null;

        // Load data saat halaman dibuka
        document.addEventListener('DOMContentLoaded', () => loadExpenses(1));

        // Reload saat filter berubah
        filterSelect.addEventListener('change', () => {
            currentPage = 1;
            loadExpenses(1);
        });

        function changePage(direction) {
            if (direction === 'prev' && currentPage > 1) {
                currentPage--;
                loadExpenses(currentPage);
            } else if (direction === 'next' && currentPage < totalPages) {
                currentPage++;
                loadExpenses(currentPage);
            }
        }

        // 1. FUNGSI LOAD DATA (GET)
        async function loadExpenses(page) {
            const filter = filterSelect.value;
            tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Memuat data...</td></tr>';

            try {
                const response = await fetch(`../../api/pengeluaran/list.php?filter=${filter}&page=${page}`);
                const result = await response.json();

                if (result.status === 'success') {
                    if (result.data.length > 0) {
                        renderTable(result.data);
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Belum ada data pengeluaran</td></tr>';
                    }
                    updatePaginationUI(result.pagination);
                }
            } catch (error) {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="5" class="px-5 py-8 text-center text-red-500">Gagal memuat data</td></tr>';
            }
        }

        function updatePaginationUI(pagination) {
            currentPage = pagination.current_page;
            totalPages = pagination.total_pages;
            const totalRecords = pagination.total_records;
            const limit = pagination.limit;

            const start = (currentPage - 1) * limit + 1;
            const end = Math.min(currentPage * limit, totalRecords);

            if (totalRecords === 0) {
                pageInfo.innerText = "Tidak ada data";
            } else {
                pageInfo.innerText = `Menampilkan ${start}-${end} dari ${totalRecords} data`;
            }

            btnPrev.disabled = (currentPage <= 1);
            btnNext.disabled = (currentPage >= totalPages);
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
                        <button onclick="confirmDelete('${item.id}')" class="text-gray-400 hover:text-red-600 transition-colors">
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
            const data = Object.fromEntries(formData.entries());

            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span> Menyimpan...';
            btn.disabled = true;

            try {
                const response = await fetch('../../api/pengeluaran/create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    form.reset();
                    currentPage = 1;
                    loadExpenses(1);
                    showToast('success', 'Berhasil!', 'Data pengeluaran berhasil disimpan');
                } else {
                    showToast('error', 'Gagal!', result.message || 'Gagal menyimpan data');
                }
            } catch (error) {
                showToast('error', 'Error!', 'Terjadi kesalahan sistem');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // 3. FUNGSI DELETE
        function confirmDelete(id) {
            deleteTargetId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            deleteTargetId = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }

        async function executeDelete() {
            if (!deleteTargetId) return;

            const btn = document.querySelector('#deleteModal button.bg-red-600');
            const originalText = btn.innerText;
            btn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span>';
            btn.disabled = true;

            try {
                const response = await fetch('../../api/pengeluaran/delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: deleteTargetId
                    })
                });

                const result = await response.json();
                closeDeleteModal();

                if (result.status === 'success') {
                    loadExpenses(currentPage);
                    showToast('success', 'Berhasil!', 'Data berhasil dihapus dari sistem');
                } else {
                    showToast('error', 'Gagal!', result.message || 'Gagal menghapus data');
                }
            } catch (error) {
                closeDeleteModal();
                showToast('error', 'Error!', 'Terjadi kesalahan sistem saat menghapus');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        // Close delete modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const deleteModal = document.getElementById('deleteModal');
                if (deleteModal && !deleteModal.classList.contains('hidden')) {
                    closeDeleteModal();
                }
            }
        });

        // Helpers UI
        function getBadgeColor(category) {
            switch (category) {
                case 'pembelian_domain':
                    return 'bg-blue-100 text-blue-800';
                case 'gaji_karyawan':
                    return 'bg-purple-100 text-purple-800';
                case 'barang_masuk':
                    return 'bg-amber-100 text-amber-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function formatCategoryLabel(cat) {
            return cat.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function formatRupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        }
    </script>
</body>

</html>