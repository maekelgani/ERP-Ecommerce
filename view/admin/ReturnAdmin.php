<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AuthMiddleware;
use App\Database\DatabaseConnection;

AuthMiddleware::requireAdminLoginFromView();

$pageTitle = "Return Management";
include '../../components/admin/head.php';

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $perPage = max(1, intval($_GET['per_page'] ?? 10));
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = $perPage;
    $offset = ($page - 1) * $limit;

    $whereConditions = [];
    $params = [];

    if (!empty($search)) {
        $whereConditions[] = "(rr.id_return LIKE :search OR rr.id_order LIKE :search OR c.nama_lengkap LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if (!empty($status)) {
        $validStatuses = ['pending', 'diproses', 'disetujui', 'ditolak', 'selesai'];
        if (in_array($status, $validStatuses)) {
            $whereConditions[] = "rr.status_return = :status";
            $params[':status'] = $status;
        }
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    $countQuery = $db->prepare("
        SELECT COUNT(DISTINCT rr.id_return) as total 
        FROM return_request rr
        LEFT JOIN customers c ON rr.id_customer = c.id_customer
        $whereClause
    ");
    $countQuery->execute($params);
    $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $countResult ? $countResult['total'] : 0;
    $totalPages = $totalRecords > 0 ? ceil($totalRecords / $limit) : 1;

    // Calculate display range
    $startEntry = $totalRecords > 0 ? $offset + 1 : 0;
    $endEntry = min($offset + $limit, $totalRecords);

    // Get status counts for stat cards
    $statusCountsQuery = $db->prepare("
        SELECT 
            'pending' as status, COUNT(*) as count FROM return_request WHERE status_return = 'pending'
        UNION ALL
        SELECT 'diproses' as status, COUNT(*) as count FROM return_request WHERE status_return = 'diproses'
        UNION ALL
        SELECT 'disetujui' as status, COUNT(*) as count FROM return_request WHERE status_return = 'disetujui'
        UNION ALL
        SELECT 'ditolak' as status, COUNT(*) as count FROM return_request WHERE status_return = 'ditolak'
        UNION ALL
        SELECT 'selesai' as status, COUNT(*) as count FROM return_request WHERE status_return = 'selesai'
    ");
    $statusCountsQuery->execute();
    $statusCounts = [];
    foreach ($statusCountsQuery->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $statusCounts[$row['status']] = intval($row['count']);
    }

    $query = $db->prepare("
        SELECT 
            rr.id_return,
            rr.id_order,
            rr.id_customer,
            rr.alasan_return,
            rr.deskripsi_return,
            rr.file_invoice,
            rr.foto_bukti,
            rr.status_return,
            rr.tanggal_pengajuan,
            rr.tanggal_diproses,
            rr.catatan_admin,
            rr.id_admin,
            c.nama_lengkap as nama_customer,
            c.email as email_customer,
            o.tanggal_order,
            MAX(p.total_bayar) as total_bayar,
            a.nama_lengkap as nama_admin
        FROM return_request rr
        LEFT JOIN customers c ON rr.id_customer = c.id_customer
        LEFT JOIN orders o ON rr.id_order = o.id_order
        LEFT JOIN payment p ON rr.id_order = p.id_order
        LEFT JOIN administrators a ON rr.id_admin = a.id_admin
        $whereClause
        GROUP BY rr.id_return
        ORDER BY 
            CASE rr.status_return 
                WHEN 'pending' THEN 1 
                WHEN 'diproses' THEN 2 
                WHEN 'disetujui' THEN 3 
                WHEN 'ditolak' THEN 4 
                WHEN 'selesai' THEN 5 
            END,
            rr.tanggal_pengajuan DESC
        LIMIT :limit OFFSET :offset
    ");

    foreach ($params as $key => $value) {
        $query->bindValue($key, $value);
    }
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $returns = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!is_array($returns)) {
        $returns = [];
    }
} catch (PDOException $e) {
    error_log('Return Management Query Error: ' . $e->getMessage());
    $returns = [];
    $totalRecords = 0;
    $totalPages = 1;
    $errorMessage = 'Terjadi kesalahan saat memuat data. Silakan coba kembali lagi.';
}

$statusColors = [
    'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-300'],
    'diproses' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-300'],
    'disetujui' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-300'],
    'ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-300'],
    'selesai' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-300'],
];

$statusLabels = [
    'pending' => 'Menunggu',
    'diproses' => 'Diproses',
    'disetujui' => 'Disetujui',
    'ditolak' => 'Ditolak',
    'selesai' => 'Selesai',
];

function formatRupiahAdmin($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatTanggalAdmin($date)
{
    if (!$date) return '-';
    return date('d M Y H:i', strtotime($date));
}
?>

<body class="bg-gray-50 h-screen flex">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-[100] flex flex-col gap-3"></div>

    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <?php if (isset($errorMessage)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-semibold text-red-800">Terjadi Kesalahan</h3>
                        <p class="text-sm text-red-700"><?= htmlspecialchars($errorMessage) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Manajemen Return</h1>
                        <p class="text-gray-500 text-sm mt-2">Kelola pengajuan pengembalian produk dari pelanggan Anda dengan mudah</p>
                    </div>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Pending Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-amber-200 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">schedule</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Pending</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $statusCounts['pending'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <!-- Processing Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-blue-200 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">manage_history</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Diproses</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $statusCounts['diproses'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <!-- Approved Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-green-200 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">verified</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Disetujui</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $statusCounts['disetujui'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <!-- Rejected Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-red-200 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">cancel</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Ditolak</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $statusCounts['ditolak'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>

                <!-- Completed Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-gray-300 transition-all duration-300">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                            <span class="material-symbols-outlined text-white text-2xl">check_circle</span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Selesai</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1"><?= $statusCounts['selesai'] ?? 0 ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 md:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Daftar Pengajuan Return</h2>
                            <p class="text-gray-500 text-sm mt-1">Total <?= count($returns) ?> return pada halaman ini</p>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex items-center gap-2 bg-white px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 text-sm">view_list</span>
                                <select id="per-page-select" onchange="changePerPage(this.value)" class="bg-transparent text-sm font-medium text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="10" <?= $perPage === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?= $perPage === 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?= $perPage === 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?= $perPage === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <span class="text-xs text-gray-500 font-medium">entries per page</span>
                            </div>
                            <form method="GET" class="flex flex-col sm:flex-row gap-2 flex-wrap">
                                <div class="relative flex-1 sm:flex-none">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">search</span>
                                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                                        placeholder="Cari ID, Order, Customer..."
                                        class="pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none w-full sm:w-64 transition-all">
                                </div>
                                <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] focus:outline-none transition-all">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="diproses" <?= $status === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="disetujui" <?= $status === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                    <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#882426] text-white rounded-lg transition-all duration-300 hover:bg-[#6d1a1c] hover:shadow-lg active:scale-95 font-mediums">
                                    <span class="material-symbols-outlined text-base">search</span>
                                    Cari
                                </button>
                                <?php if (!empty($search) || !empty($status)): ?>
                                    <a href="ReturnAdmin.php" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors whitespace-nowrap">
                                        <span class="material-symbols-outlined text-base">close</span>
                                        Reset
                                    </a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="relative w-full overflow-auto">
                    <?php if (empty($returns)): ?>
                        <div class="text-center py-16 px-4">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                                <span class="material-symbols-outlined text-gray-400 text-5xl">request_quote</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Pengajuan Return</h3>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto">Belum ada pengajuan pengembalian produk dari pelanggan. Pengajuan baru akan ditampilkan di sini.</p>
                        </div>
                    <?php else: ?>
                        <table class="w-full caption-bottom text-sm">
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ID Return</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ID Order</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Alasan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php $nomor = $offset + 1; ?>
                                <?php foreach ($returns as $return): ?>
                                    <?php $colors = $statusColors[$return['status_return']] ?? $statusColors['pending']; ?>
                                    <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-colors duration-200">
                                        <td class="px-6 py-5 text-center font-semibold text-gray-700 w-12 bg-gray-50/50"><?= $nomor++ ?></td>
                                        <td class="px-6 py-5">
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-mono font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                <?= htmlspecialchars($return['id_return']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <a href="ReturnOrderDetailPage.php?id=<?= urlencode($return['id_order']) ?>"
                                                class="inline-flex items-center gap-1.5 text-[#882426] hover:text-[#6d1a1c] font-semibold transition-colors hover:underline">
                                                <span><?= htmlspecialchars($return['id_order']) ?></span>
                                                <span class="material-symbols-outlined text-base">open_in_new</span>
                                            </a>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div>
                                                <p class="font-semibold text-gray-900"><?= htmlspecialchars($return['nama_customer'] ?? '-') ?></p>
                                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($return['email_customer'] ?? '-') ?></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-gray-700 text-sm max-w-xs line-clamp-2" title="<?= htmlspecialchars($return['alasan_return']) ?>">
                                                <?= htmlspecialchars($return['alasan_return']) ?>
                                            </p>
                                        </td>
                                        <td class="px-6 py-5">
                                            <p class="text-gray-600 text-sm font-medium"><?= formatTanggalAdmin($return['tanggal_pengajuan']) ?></p>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center px-3 py-2 rounded-full text-xs font-bold <?= $colors['bg'] ?> <?= $colors['text'] ?> border <?= $colors['border'] ?>">
                                                <?= $statusLabels[$return['status_return']] ?? ucfirst($return['status_return']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center justify-center gap-1">
                                                <button onclick="openDetailModal(this)"
                                                    data-return='<?= htmlspecialchars(json_encode($return, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES) ?>'
                                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-xs font-bold hover:bg-blue-100 transition-all duration-200 border border-blue-200 hover:border-blue-300" title="Lihat Detail">
                                                    <span class="material-symbols-outlined text-base">visibility</span>
                                                    <span>Lihat</span>
                                                </button>
                                                <?php if ($return['status_return'] === 'pending'): ?>
                                                    <button onclick="openActionModal('<?= $return['id_return'] ?>', 'diproses')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-blue-600 bg-blue-50 rounded-lg text-xs font-bold hover:bg-blue-100 transition-all duration-200 border border-blue-200 hover:border-blue-300" title="Proses">
                                                        <span class="material-symbols-outlined text-base">schedule</span>
                                                        <span>Proses</span>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if (in_array($return['status_return'], ['pending', 'diproses'])): ?>
                                                    <button onclick="openActionModal('<?= $return['id_return'] ?>', 'disetujui')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-green-600 bg-green-50 rounded-lg text-xs font-bold hover:bg-green-100 transition-all duration-200 border border-green-200 hover:border-green-300" title="Setujui">
                                                        <span class="material-symbols-outlined text-base">check_circle</span>
                                                        <span>Setujui</span>
                                                    </button>
                                                    <button onclick="openActionModal('<?= $return['id_return'] ?>', 'ditolak')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-red-600 bg-red-50 rounded-lg text-xs font-bold hover:bg-red-100 transition-all duration-200 border border-red-200 hover:border-red-300" title="Tolak">
                                                        <span class="material-symbols-outlined text-base">cancel</span>
                                                        <span>Tolak</span>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($return['status_return'] === 'disetujui'): ?>
                                                    <button onclick="openActionModal('<?= $return['id_return'] ?>', 'selesai')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-2 text-gray-600 bg-gray-100 rounded-lg text-xs font-bold hover:bg-gray-200 transition-all duration-200 border border-gray-200 hover:border-gray-300" title="Selesaikan">
                                                        <span class="material-symbols-outlined text-base">done_all</span>
                                                        <span>Selesai</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Pagination Info & Controls -->
                        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <div class="text-sm text-gray-600">
                                Showing <span class="font-semibold text-gray-800"><?= $startEntry ?></span> to <span class="font-semibold text-gray-800"><?= $endEntry ?></span> of <span class="font-semibold text-gray-800"><?= $totalRecords ?></span> entries
                            </div>

                            <!-- Pagination Navigation - Always Show -->
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <?php if ($page > 1): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                        <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                                    </a>
                                <?php else: ?>
                                    <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                        <span class="material-symbols-outlined text-lg align-middle">chevron_left</span>
                                    </button>
                                <?php endif; ?>

                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                if ($startPage > 1):
                                ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">1</a>
                                    <?php if ($startPage > 2): ?>
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <button class="px-3 py-2 rounded-lg text-white font-medium text-sm" style="background: #882426;"><?= $i ?></button>
                                    <?php else: ?>
                                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <span class="px-2 py-2 text-gray-400">...</span>
                                    <?php endif; ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium"><?= $totalPages ?></a>
                                <?php endif; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1, 'per_page' => $perPage])) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition-colors text-sm font-medium">
                                        <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                                    </a>
                                <?php else: ?>
                                    <button disabled class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed text-sm font-medium">
                                        <span class="material-symbols-outlined text-lg align-middle">chevron_right</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col transform transition-all animate-modal-in">
                <!-- Modern Header with Solid Primary Color -->
                <div class="sticky top-0 bg-[#882426] px-6 py-5 flex items-center justify-between z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">assignment_return</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Detail Pengajuan Return</h3>
                            <p class="text-white/70 text-sm mt-0.5">Informasi lengkap pengajuan pengembalian</p>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <!-- Content with Custom Scrollbar -->
                <div class="flex-1 overflow-y-auto p-6 bg-gray-50 space-y-5" id="detailContent"></div>
                <!-- Modal Footer -->
                <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 flex items-center justify-end gap-3">
                    <button onclick="closeDetailModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">
                        <span class="material-symbols-outlined text-lg">close</span>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Action Modal (Proses/Setujui/Tolak/Selesai) -->
    <div id="actionModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeActionModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all animate-modal-in">
                <!-- Modal Header with Dynamic Color -->
                <div id="actionModalHeader" class="bg-[#882426] px-6 py-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div id="actionModalIcon" class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-white text-2xl">task_alt</span>
                        </div>
                        <div>
                            <h3 id="actionModalTitle" class="text-xl font-bold text-white">Konfirmasi Aksi</h3>
                            <p id="actionModalSubtitle" class="text-white/70 text-sm mt-0.5">Pastikan keputusan Anda sudah benar</p>
                        </div>
                    </div>
                    <button onclick="closeActionModal()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all duration-200">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <!-- Status Info Alert -->
                    <div id="actionInfoAlert" class="mb-5 p-4 rounded-xl border-l-4 border-[#882426] bg-[#882426]/5">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[#882426] text-xl flex-shrink-0">info</span>
                            <div>
                                <p id="actionInfoText" class="text-sm text-gray-700 font-medium">Anda akan mengubah status pengajuan return ini.</p>
                            </div>
                        </div>
                    </div>

                    <form id="actionForm" onsubmit="submitAction(event)">
                        <input type="hidden" id="actionReturnId" name="return_id">
                        <input type="hidden" id="actionStatus" name="status">

                        <!-- Admin Note Field -->
                        <div class="mb-5">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700 mb-3">
                                <span class="material-symbols-outlined text-[#882426] text-lg">edit_note</span>
                                Catatan Admin
                            </label>
                            <textarea name="catatan_admin" id="actionNote" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] resize-none transition-all duration-200 placeholder:text-gray-400"
                                placeholder="Tambahkan catatan untuk customer (opsional)..."></textarea>
                            <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">lightbulb</span>
                                Catatan akan dikirimkan ke customer melalui notifikasi
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" onclick="closeActionModal()"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all duration-200 border-2 border-transparent">
                                <span class="material-symbols-outlined text-lg">close</span>
                                Batal
                            </button>
                            <button type="submit" id="actionSubmitBtn"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#882426] text-white font-bold rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 shadow-lg shadow-[#882426]/30">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                <span id="actionSubmitText">Konfirmasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal Styles -->
    <style>
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

        /* Circular Progress - runs from full to empty */
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

        /* Custom Scrollbar for Modal */
        #detailContent::-webkit-scrollbar {
            width: 6px;
        }

        #detailContent::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #detailContent::-webkit-scrollbar-thumb {
            background: #882426;
            border-radius: 10px;
        }

        #detailContent::-webkit-scrollbar-thumb:hover {
            background: #6d1a1c;
        }

        /* Info Card Styles */
        .info-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            padding: 1.25rem;
            transition: all 0.2s ease;
        }

        .info-card:hover {
            box-shadow: 0 4px 20px rgba(136, 36, 38, 0.1);
            border-color: #882426;
        }

        .info-card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f3f4f6;
        }

        .info-card-header .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-card-header h4 {
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* File Preview Styles */
        .file-preview {
            position: relative;
            display: inline-block;
            border-radius: 1rem;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .file-preview:hover {
            border-color: #882426;
            box-shadow: 0 4px 20px rgba(136, 36, 38, 0.15);
        }

        .file-preview img {
            display: block;
            width: 140px;
            height: 140px;
            object-fit: cover;
        }

        .file-preview-overlay {
            position: absolute;
            inset: 0;
            background: rgba(136, 36, 38, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .file-preview:hover .file-preview-overlay {
            opacity: 1;
        }

        /* Timeline Styles */
        .timeline-item {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 0;
        }

        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #882426;
            flex-shrink: 0;
            margin-top: 0.25rem;
            position: relative;
        }

        .timeline-dot::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 100%;
            width: 2px;
            height: calc(100% + 0.5rem);
            background: #e5e7eb;
            transform: translateX(-50%);
        }

        .timeline-item:last-child .timeline-dot::after {
            display: none;
        }
    </style>

    <script>
        const statusLabels = {
            'diproses': 'Proses Pengajuan',
            'disetujui': 'Setujui Pengajuan',
            'ditolak': 'Tolak Pengajuan',
            'selesai': 'Selesaikan Pengajuan'
        };

        const actionModalConfig = {
            'diproses': {
                title: 'Proses Pengajuan Return',
                subtitle: 'Pengajuan akan masuk ke status diproses',
                icon: 'pending_actions',
                btnText: 'Proses Sekarang',
                infoText: 'Pengajuan return akan ditandai sebagai "Diproses". Customer akan menerima notifikasi bahwa pengajuan sedang ditinjau.'
            },
            'disetujui': {
                title: 'Setujui Pengajuan Return',
                subtitle: 'Pengajuan akan disetujui untuk pengembalian',
                icon: 'verified',
                btnText: 'Setujui Return',
                infoText: 'Pengajuan return akan disetujui. Customer dapat melanjutkan proses pengembalian produk sesuai ketentuan.'
            },
            'ditolak': {
                title: 'Tolak Pengajuan Return',
                subtitle: 'Pengajuan akan ditolak dengan alasan',
                icon: 'cancel',
                btnText: 'Tolak Pengajuan',
                infoText: 'Pengajuan return akan ditolak. Pastikan Anda memberikan alasan yang jelas pada catatan admin.'
            },
            'selesai': {
                title: 'Selesaikan Return',
                subtitle: 'Proses return telah selesai',
                icon: 'task_alt',
                btnText: 'Selesaikan',
                infoText: 'Return akan ditandai selesai. Ini berarti proses pengembalian telah berhasil diselesaikan.'
            }
        };

        function openDetailModal(btn) {
            const data = JSON.parse(btn.dataset.return);

            // Status styling with solid colors
            const statusStyles = {
                'pending': {
                    bg: 'bg-amber-50',
                    text: 'text-amber-700',
                    border: 'border-amber-300',
                    icon: 'schedule',
                    solidBg: '#fbbf24'
                },
                'diproses': {
                    bg: 'bg-blue-50',
                    text: 'text-blue-700',
                    border: 'border-blue-300',
                    icon: 'pending_actions',
                    solidBg: '#3b82f6'
                },
                'disetujui': {
                    bg: 'bg-green-50',
                    text: 'text-green-700',
                    border: 'border-green-300',
                    icon: 'verified',
                    solidBg: '#10b981'
                },
                'ditolak': {
                    bg: 'bg-red-50',
                    text: 'text-red-700',
                    border: 'border-red-300',
                    icon: 'cancel',
                    solidBg: '#ef4444'
                },
                'selesai': {
                    bg: 'bg-gray-50',
                    text: 'text-gray-700',
                    border: 'border-gray-300',
                    icon: 'check_circle',
                    solidBg: '#6b7280'
                }
            };
            const statusStyle = statusStyles[data.status_return] || statusStyles['pending'];
            const statusMap = {
                'pending': 'Menunggu',
                'diproses': 'Diproses',
                'disetujui': 'Disetujui',
                'ditolak': 'Ditolak',
                'selesai': 'Selesai'
            };

            let invoiceHtml = '<div class="text-gray-400 text-sm italic flex items-center gap-2"><span class="material-symbols-outlined text-base">hide_image</span>Tidak ada file</div>';
            if (data.file_invoice) {
                const ext = data.file_invoice.split('.').pop().toLowerCase();
                if (ext === 'pdf') {
                    invoiceHtml = `<a href="../../uploads/returns/${data.file_invoice}" target="_blank" class="inline-flex items-center gap-3 px-5 py-3.5 bg-[#882426] text-white rounded-xl hover:bg-[#6d1a1c] transition-all duration-200 font-semibold text-sm shadow-lg shadow-[#882426]/20">
                        <span class="material-symbols-outlined text-xl">picture_as_pdf</span>
                        <span>Lihat Invoice PDF</span>
                        <span class="material-symbols-outlined text-lg">open_in_new</span>
                    </a>`;
                } else {
                    invoiceHtml = `<div class="file-preview cursor-pointer" onclick="window.open('../../uploads/returns/${data.file_invoice}', '_blank')">
                        <img src="../../uploads/returns/${data.file_invoice}" alt="Invoice">
                        <div class="file-preview-overlay">
                            <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                        </div>
                    </div>`;
                }
            }

            let photoHtml = '<div class="text-gray-400 text-sm italic flex items-center gap-2"><span class="material-symbols-outlined text-base">hide_image</span>Tidak ada foto</div>';
            if (data.foto_bukti) {
                photoHtml = `<div class="file-preview cursor-pointer" onclick="window.open('../../uploads/returns/${data.foto_bukti}', '_blank')">
                    <img src="../../uploads/returns/${data.foto_bukti}" alt="Foto Bukti">
                    <div class="file-preview-overlay">
                        <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                    </div>
                </div>`;
            }

            const content = `
                <!-- Section: ID Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="icon bg-[#882426]">
                                <span class="material-symbols-outlined text-white text-base">tag</span>
                            </div>
                            <h4>ID Return</h4>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 font-mono">${data.id_return}</p>
                    </div>
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="icon bg-blue-600">
                                <span class="material-symbols-outlined text-white text-base">shopping_bag</span>
                            </div>
                            <h4>ID Order</h4>
                        </div>
                        <a href="orderDetail.php?id=${data.id_order}" class="text-xl font-bold text-[#882426] hover:text-[#6d1a1c] transition-colors inline-flex items-center gap-2 font-mono">
                            ${data.id_order}
                            <span class="material-symbols-outlined text-base">open_in_new</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Customer & Status -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="icon bg-indigo-600">
                                <span class="material-symbols-outlined text-white text-base">person</span>
                            </div>
                            <h4>Informasi Customer</h4>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</p>
                                <p class="text-base font-bold text-gray-900">${data.nama_customer || '-'}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</p>
                                <p class="text-sm text-gray-600 break-all">${data.email_customer || '-'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="info-card" style="border-color: ${statusStyle.solidBg};">
                        <div class="info-card-header">
                            <div class="icon" style="background: ${statusStyle.solidBg};">
                                <span class="material-symbols-outlined text-white text-base">${statusStyle.icon}</span>
                            </div>
                            <h4>Status Return</h4>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm border-2 ${statusStyle.bg} ${statusStyle.text} ${statusStyle.border}">
                            <span class="material-symbols-outlined text-base">${statusStyle.icon}</span>
                            ${statusMap[data.status_return] || data.status_return}
                        </div>
                    </div>
                </div>

                <!-- Section: Alasan Pengembalian -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon bg-amber-500">
                            <span class="material-symbols-outlined text-white text-base">help</span>
                        </div>
                        <h4>Alasan Pengembalian</h4>
                    </div>
                    <p class="text-gray-700 text-base leading-relaxed">${data.alasan_return}</p>
                </div>

                ${data.deskripsi_return ? `
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon bg-purple-600">
                            <span class="material-symbols-outlined text-white text-base">description</span>
                        </div>
                        <h4>Deskripsi Detail</h4>
                    </div>
                    <p class="text-gray-700 text-base leading-relaxed">${data.deskripsi_return}</p>
                </div>
                ` : ''}

                <!-- Section: Files -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="icon bg-orange-500">
                                <span class="material-symbols-outlined text-white text-base">receipt_long</span>
                            </div>
                            <h4>File Invoice</h4>
                        </div>
                        <div class="mt-2">
                            ${invoiceHtml}
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="info-card-header">
                            <div class="icon bg-teal-500">
                                <span class="material-symbols-outlined text-white text-base">image</span>
                            </div>
                            <h4>Foto Bukti</h4>
                        </div>
                        <div class="mt-2">
                            ${photoHtml}
                        </div>
                    </div>
                </div>

                <!-- Section: Timeline -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon bg-[#882426]">
                            <span class="material-symbols-outlined text-white text-base">schedule</span>
                        </div>
                        <h4>Timeline Pengajuan</h4>
                    </div>
                    <div class="pl-1">
                        <div class="timeline-item">
                            <div class="timeline-dot" style="background: #882426;"></div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pengajuan Dibuat</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">${formatDate(data.tanggal_pengajuan)}</p>
                            </div>
                        </div>
                        ${data.tanggal_diproses ? `
                        <div class="timeline-item">
                            <div class="timeline-dot" style="background: #10b981;"></div>
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Diproses Pada</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">${formatDate(data.tanggal_diproses)}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>

                ${data.catatan_admin ? `
                <div class="info-card" style="border-left: 4px solid #882426;">
                    <div class="info-card-header">
                        <div class="icon bg-[#882426]">
                            <span class="material-symbols-outlined text-white text-base">note</span>
                        </div>
                        <h4>Catatan Admin</h4>
                    </div>
                    <div class="bg-[#882426]/5 border border-[#882426]/20 rounded-xl p-4">
                        <p class="text-gray-700 text-base leading-relaxed">${data.catatan_admin}</p>
                    </div>
                </div>
                ` : ''}

                ${data.nama_admin ? `
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon bg-rose-600">
                            <span class="material-symbols-outlined text-white text-base">admin_panel_settings</span>
                        </div>
                        <h4>Diproses Oleh</h4>
                    </div>
                    <div class="inline-flex items-center gap-3 px-4 py-2.5 bg-[#882426]/10 text-[#882426] rounded-xl font-semibold text-sm border border-[#882426]/20">
                        <div class="w-8 h-8 rounded-full bg-[#882426] flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm">person</span>
                        </div>
                        ${data.nama_admin}
                    </div>
                </div>
                ` : ''}
            `;

            document.getElementById('detailContent').innerHTML = content;
            document.getElementById('detailModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openActionModal(returnId, status) {
            const config = actionModalConfig[status] || {
                title: 'Konfirmasi Aksi',
                subtitle: 'Pastikan keputusan Anda sudah benar',
                icon: 'task_alt',
                btnText: 'Konfirmasi',
                infoText: 'Anda akan mengubah status pengajuan return ini.'
            };

            document.getElementById('actionReturnId').value = returnId;
            document.getElementById('actionStatus').value = status;
            document.getElementById('actionModalTitle').textContent = config.title;
            document.getElementById('actionModalSubtitle').textContent = config.subtitle;
            document.getElementById('actionModalIcon').innerHTML = `<span class="material-symbols-outlined text-white text-2xl">${config.icon}</span>`;
            document.getElementById('actionInfoText').textContent = config.infoText;
            document.getElementById('actionSubmitText').textContent = config.btnText;
            document.getElementById('actionNote').value = '';
            document.getElementById('actionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function submitAction(e) {
            e.preventDefault();

            const returnId = document.getElementById('actionReturnId').value;
            const status = document.getElementById('actionStatus').value;

            if (!returnId || !status) {
                showToast('error', 'Error!', 'Data tidak lengkap. Silakan coba lagi.');
                return;
            }

            const btn = document.getElementById('actionSubmitBtn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            `;

            try {
                const formData = new FormData(document.getElementById('actionForm'));
                const response = await fetch('../../api/admin/update-return-status.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Respons bukan JSON. Server error.');
                }

                const result = await response.json();

                if (result && result.success) {
                    showToast('success', 'Berhasil!', result.message || 'Status return berhasil diubah.');
                    closeActionModal();
                    setTimeout(() => location.reload(), 1500);
                } else if (result && result.message) {
                    showToast('error', 'Gagal!', result.message);
                } else {
                    showToast('error', 'Error!', 'Respons server tidak valid');
                }
            } catch (error) {
                console.error('Submit Action Error:', error);
                showToast('error', 'Error!', 'Terjadi kesalahan koneksi. Silakan coba lagi.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

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
                    progress: 'bg-emerald-500',
                    title: 'text-emerald-800',
                    progressCircle: '#10b981'
                },
                error: {
                    bg: 'bg-white',
                    border: 'border-red-200',
                    icon: 'error',
                    iconBg: 'bg-red-500',
                    iconColor: 'text-white',
                    progress: 'bg-red-500',
                    title: 'text-red-800',
                    progressCircle: '#ef4444'
                },
                warning: {
                    bg: 'bg-white',
                    border: 'border-amber-200',
                    icon: 'warning',
                    iconBg: 'bg-amber-500',
                    iconColor: 'text-white',
                    progress: 'bg-amber-500',
                    title: 'text-amber-800',
                    progressCircle: '#f59e0b'
                },
                info: {
                    bg: 'bg-white',
                    border: 'border-blue-200',
                    icon: 'info',
                    iconBg: 'bg-blue-500',
                    iconColor: 'text-white',
                    progress: 'bg-blue-500',
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

        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        }
    </script>
</body>

</html>