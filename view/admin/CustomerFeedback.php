<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Repository/CustomerFeedbackRepository.php';

use App\Auth\AuthMiddleware;
use App\Auth\PermissionHelper;
use App\Repository\CustomerFeedbackRepository;

AuthMiddleware::requireAdminLoginFromView();

if (!PermissionHelper::canViewCustomerFeedback()) {
    header('Location: ../../view/403.php');
    exit;
}

$feedbackRepo = new CustomerFeedbackRepository();

$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'rating' => $_GET['rating'] ?? '',
    'limit' => 50,
    'offset' => isset($_GET['page']) ? (max(1, (int)$_GET['page']) - 1) * 50 : 0
];

$reviews = $feedbackRepo->getAllReviews($filters);
$totalReviews = $feedbackRepo->countReviews($filters);
$reviewStats = $feedbackRepo->getReviewStats();
$totalPages = ceil($totalReviews / 50);
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$pageTitle = "Feedback Pelanggan";
include '../../components/admin/head.php';
?>

<body class="bg-gray-50 h-screen flex">
    <?php include '../../components/admin/sidebarAdmin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="h-[60px] sticky top-0 z-10">
            <?php include '../../components/admin/NavbarAdmin.php'; ?>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Feedback Pelanggan</h1>
                <p class="text-gray-500 mt-1">Review dan feedback dari pelanggan</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-blue-500">rate_review</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $reviewStats['total'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Total Review</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-yellow-500">pending</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $reviewStats['pending'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Pending</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-green-500">check_circle</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $reviewStats['approved'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Approved</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-red-500">cancel</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= $reviewStats['rejected'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Rejected</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="material-symbols-outlined text-yellow-400">star</span>
                    </div>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($reviewStats['avg_rating'] ?? 0, 1) ?></p>
                    <p class="text-sm text-gray-500">Rating Rata-rata</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
                <form method="GET" class="p-4 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                                <input type="text" name="search" value="<?= htmlspecialchars($filters['search']) ?>" 
                                    placeholder="Cari pelanggan, produk, atau komentar..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Status</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= $filters['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                        <select name="rating" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Rating</option>
                            <option value="5" <?= $filters['rating'] === '5' ? 'selected' : '' ?>>5 Bintang</option>
                            <option value="4" <?= $filters['rating'] === '4' ? 'selected' : '' ?>>4 Bintang</option>
                            <option value="3" <?= $filters['rating'] === '3' ? 'selected' : '' ?>>3 Bintang</option>
                            <option value="2" <?= $filters['rating'] === '2' ? 'selected' : '' ?>>2 Bintang</option>
                            <option value="1" <?= $filters['rating'] === '1' ? 'selected' : '' ?>>1 Bintang</option>
                        </select>
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Filter
                        </button>
                        <?php if (!empty($filters['search']) || !empty($filters['status']) || !empty($filters['rating'])): ?>
                            <a href="CustomerFeedback.php" class="px-6 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors text-center">
                                Reset
                            </a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="p-4 space-y-4">
                    <?php if (empty($reviews)): ?>
                        <div class="text-center py-12">
                            <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">rate_review</span>
                            <p class="text-gray-500">Tidak ada review ditemukan</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start gap-4">
                                    <img src="../../uploads/products/<?= htmlspecialchars($review['product_image'] ?? 'default.jpg') ?>" alt="" class="w-16 h-16 rounded object-cover flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4 mb-2">
                                            <div>
                                                <p class="font-medium text-gray-800"><?= htmlspecialchars($review['nama_product']) ?></p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <div class="flex items-center gap-0.5">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <span class="material-symbols-outlined text-sm <?= $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' ?>">star</span>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="text-sm text-gray-500"><?= $review['rating'] ?>/5</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                                    'approved' => 'bg-green-100 text-green-700',
                                                    'rejected' => 'bg-red-100 text-red-700'
                                                ];
                                                $color = $statusColors[$review['status_review']] ?? 'bg-gray-100 text-gray-700';
                                                ?>
                                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= $color ?>">
                                                    <?= ucfirst($review['status_review']) ?>
                                                </span>
                                                <div class="relative" x-data="{ open: false }">
                                                    <button onclick="toggleDropdown(this)" class="p-1 text-gray-400 hover:text-gray-600 rounded hover:bg-gray-100">
                                                        <span class="material-symbols-outlined text-lg">more_vert</span>
                                                    </button>
                                                    <div class="dropdown-menu hidden absolute right-0 mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                        <?php if ($review['status_review'] !== 'approved'): ?>
                                                            <button onclick="updateStatus('<?= $review['id_review'] ?>', 'approved')" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                                <span class="material-symbols-outlined text-green-500 text-sm">check_circle</span>
                                                                Approve
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if ($review['status_review'] !== 'rejected'): ?>
                                                            <button onclick="updateStatus('<?= $review['id_review'] ?>', 'rejected')" class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                                <span class="material-symbols-outlined text-red-500 text-sm">cancel</span>
                                                                Reject
                                                            </button>
                                                        <?php endif; ?>
                                                        <button onclick="deleteReview('<?= $review['id_review'] ?>')" class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                                            <span class="material-symbols-outlined text-sm">delete</span>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($review['komentar'])): ?>
                                            <p class="text-gray-600 mb-3"><?= nl2br(htmlspecialchars($review['komentar'])) ?></p>
                                        <?php endif; ?>

                                        <?php if (!empty($review['foto_review'])): ?>
                                            <div class="mb-3">
                                                <img src="../../uploads/reviews/<?= htmlspecialchars($review['foto_review']) ?>" alt="" class="h-20 rounded object-cover">
                                            </div>
                                        <?php endif; ?>

                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center gap-2">
                                                    <?php if (!empty($review['customer_image'])): ?>
                                                        <img src="../../uploads/profiles/<?= htmlspecialchars($review['customer_image']) ?>" alt="" class="w-6 h-6 rounded-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <span class="material-symbols-outlined text-gray-400 text-xs">person</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="text-gray-700"><?= htmlspecialchars($review['customer_name']) ?></span>
                                                </div>
                                                <a href="CustomerDetail.php?id=<?= $review['id_customer'] ?>" class="text-blue-600 hover:underline">Lihat Profil</a>
                                            </div>
                                            <span class="text-gray-400"><?= date('d M Y H:i', strtotime($review['tanggal_review'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Menampilkan <?= ($filters['offset'] + 1) ?>-<?= min($filters['offset'] + 50, $totalReviews) ?> dari <?= $totalReviews ?> review
                        </p>
                        <div class="flex items-center gap-2">
                            <?php if ($currentPage > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" 
                                    class="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 text-sm">Sebelumnya</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                    class="px-3 py-1 border rounded text-sm <?= $i === $currentPage ? 'bg-gray-800 text-white border-gray-800' : 'border-gray-200 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" 
                                    class="px-3 py-1 border border-gray-200 rounded hover:bg-gray-50 text-sm">Selanjutnya</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleDropdown(btn) {
            const dropdown = btn.nextElementSibling;
            document.querySelectorAll('.dropdown-menu').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.relative')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.add('hidden'));
            }
        });

        function updateStatus(id, status) {
            fetch('../../app/controllers/CustomerFeedbackController.php?action=updateReviewStatus&id=' + id + '&status=' + status)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                });
        }

        function deleteReview(id) {
            if (confirm('Apakah Anda yakin ingin menghapus review ini?')) {
                fetch('../../app/controllers/CustomerFeedbackController.php?action=deleteReview&id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        }
    </script>
</body>
</html>
