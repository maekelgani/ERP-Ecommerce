<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Services/PromoService.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

use App\Services\PromoService;

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: PromoPage.php');
    exit;
}

$promoService = new PromoService();
$campaignData = $promoService->getCampaignDetailBySlug($slug);

if (!$campaignData) {
    header('Location: PromoPage.php');
    exit;
}

$campaign = $campaignData['campaign'];
$products = $campaignData['products'];
$countdown = $campaignData['countdown_seconds'];
include '../../components/users/head.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($campaign['judul']) ?> - Nano Komputer</title>
    <link rel="stylesheet" href="../../assets/css/output.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .countdown-box {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 0.75rem;
            min-width: 4rem;
            backdrop-filter: blur(4px);
        }

        .countdown-value {
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .countdown-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            opacity: 0.8;
            margin-top: 0.25rem;
        }

        .product-card {
            transition: all 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .badge-discount {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background: #882426;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-gray-50" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <?php
    $tipeGradients = [
        'diskon_produk' => 'from-red-600 via-red-700 to-red-800',
        'flash_sale' => 'from-orange-500 via-orange-600 to-red-600',
        'voucher' => 'from-purple-600 via-purple-700 to-purple-800',
        'bundle' => 'from-green-600 via-green-700 to-green-800',
        'gratis_ongkir' => 'from-teal-500 via-teal-600 to-teal-700'
    ];
    $gradientClass = $tipeGradients[$campaign['tipe']] ?? 'from-[#882426] via-[#6d1d1f] to-[#5a1718]';
    ?>

    <header class="bg-gradient-to-r <?= $gradientClass ?> text-white">
        <div class="max-w-7xl mx-auto px-4 py-12 md:py-16">
            <nav class="text-sm mb-6">
                <a href="PromoPage.php" class="text-white/70 hover:text-white">Promo</a>
                <span class="mx-2 text-white/50">/</span>
                <span><?= htmlspecialchars($campaign['judul']) ?></span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <?php if ($campaign['tipe'] === 'flash_sale'): ?>
                        <span class="inline-flex items-center gap-1 px-4 py-1.5 bg-white/20 rounded-full text-sm mb-4">
                            <span class="material-symbols-outlined">bolt</span>
                            Flash Sale
                        </span>
                    <?php endif; ?>

                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4"><?= htmlspecialchars($campaign['judul']) ?></h1>

                    <?php if ($campaign['deskripsi']): ?>
                        <p class="text-lg text-white/80 max-w-2xl"><?= htmlspecialchars($campaign['deskripsi']) ?></p>
                    <?php endif; ?>

                    <div class="flex items-center gap-6 mt-6 text-sm text-white/70">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">inventory_2</span>
                            <?= count($products) ?> Produk
                        </span>
                        <?php if ($campaign['kuota_total']): ?>
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">group</span>
                                <?= $campaign['kuota_terpakai'] ?>/<?= $campaign['kuota_total'] ?> kuota
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($countdown > 0): ?>
                    <div class="lg:text-right" data-countdown="<?= $countdown ?>">
                        <p class="text-sm text-white/70 mb-3">Berakhir dalam:</p>
                        <div class="flex items-center gap-2 lg:justify-end">
                            <div class="countdown-box">
                                <span class="countdown-value countdown-days">00</span>
                                <span class="countdown-label">Hari</span>
                            </div>
                            <span class="text-3xl font-bold text-white/50">:</span>
                            <div class="countdown-box">
                                <span class="countdown-value countdown-hours">00</span>
                                <span class="countdown-label">Jam</span>
                            </div>
                            <span class="text-3xl font-bold text-white/50">:</span>
                            <div class="countdown-box">
                                <span class="countdown-value countdown-minutes">00</span>
                                <span class="countdown-label">Menit</span>
                            </div>
                            <span class="text-3xl font-bold text-white/50">:</span>
                            <div class="countdown-box">
                                <span class="countdown-value countdown-seconds">00</span>
                                <span class="countdown-label">Detik</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <p class="text-gray-600">Menampilkan <?= count($products) ?> produk</p>

            <div class="flex items-center gap-3">
                <select id="sortProducts" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426]">
                    <option value="default">Urutkan: Default</option>
                    <option value="price_low">Harga: Rendah ke Tinggi</option>
                    <option value="price_high">Harga: Tinggi ke Rendah</option>
                    <option value="discount">Diskon Terbesar</option>
                </select>
            </div>
        </div>

        <?php if (!empty($products)): ?>
            <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                <?php foreach ($products as $product):
                    $originalPrice = $product['harga'] ?? 0;
                    $discountedPrice = $product['harga_diskon'] ?? $originalPrice;
                    $discountPercent = $originalPrice > 0 && $discountedPrice < $originalPrice
                        ? round(($originalPrice - $discountedPrice) / $originalPrice * 100)
                        : 0;
                ?>
                    <div class="product-card bg-white border border-gray-100 rounded-xl overflow-hidden"
                        data-price="<?= $discountedPrice ?>"
                        data-discount="<?= $discountPercent ?>">
                        <a href="../../view/users/productDetail.php?id=<?= urlencode($product['id_produk']) ?>">
                            <div class="relative aspect-square bg-gray-100">
                                <img src="../../uploads/products/<?= htmlspecialchars($product['gambar'] ?? 'default.png') ?>"
                                    alt="<?= htmlspecialchars($product['nama_product']) ?>"
                                    class="w-full h-full object-cover">

                                <?php if ($discountPercent > 0): ?>
                                    <span class="badge-discount"><?= $discountPercent ?>%</span>
                                <?php endif; ?>

                                <?php if ($product['stok_promo'] && $product['stok_terpakai'] >= $product['stok_promo']): ?>
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">HABIS</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2 h-10"><?= htmlspecialchars($product['nama_product']) ?></h3>

                                <?php if ($discountedPrice < $originalPrice): ?>
                                    <p class="text-xs text-gray-400 line-through">Rp <?= number_format($originalPrice, 0, ',', '.') ?></p>
                                    <p class="text-lg font-bold text-[#882426]">Rp <?= number_format($discountedPrice, 0, ',', '.') ?></p>
                                <?php else: ?>
                                    <p class="text-lg font-bold text-gray-800">Rp <?= number_format($originalPrice, 0, ',', '.') ?></p>
                                <?php endif; ?>

                                <?php if ($product['stok_promo']): ?>
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                            <span>Stok promo</span>
                                            <span><?= $product['stok_terpakai'] ?? 0 ?>/<?= $product['stok_promo'] ?></span>
                                        </div>
                                        <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#882426] rounded-full" style="width: <?= min(100, (($product['stok_terpakai'] ?? 0) / $product['stok_promo']) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inventory_2</span>
                <p class="text-gray-500">Belum ada produk dalam kampanye ini</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include_once '../../components/customer/footerCustomer.php'; ?>

    <script>
        document.querySelectorAll('[data-countdown]').forEach(el => {
            let seconds = parseInt(el.dataset.countdown);

            const update = () => {
                if (seconds <= 0) {
                    el.innerHTML = '<p class="text-sm text-white/70">Promo telah berakhir</p>';
                    return;
                }

                const days = Math.floor(seconds / 86400);
                const hours = Math.floor((seconds % 86400) / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;

                el.querySelector('.countdown-days').textContent = String(days).padStart(2, '0');
                el.querySelector('.countdown-hours').textContent = String(hours).padStart(2, '0');
                el.querySelector('.countdown-minutes').textContent = String(minutes).padStart(2, '0');
                el.querySelector('.countdown-seconds').textContent = String(secs).padStart(2, '0');

                seconds--;
            };

            update();
            setInterval(update, 1000);
        });

        document.getElementById('sortProducts').addEventListener('change', function() {
            const grid = document.getElementById('productGrid');
            const cards = Array.from(grid.querySelectorAll('.product-card'));

            cards.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price);
                const priceB = parseFloat(b.dataset.price);
                const discountA = parseFloat(a.dataset.discount);
                const discountB = parseFloat(b.dataset.discount);

                switch (this.value) {
                    case 'price_low':
                        return priceA - priceB;
                    case 'price_high':
                        return priceB - priceA;
                    case 'discount':
                        return discountB - discountA;
                    default:
                        return 0;
                }
            });

            cards.forEach(card => grid.appendChild(card));
        });
    </script>

    <?php include '../../components/users/loginRequiredModal.php'; ?>
    <?php include '../../components/users/footer.php'; ?>
</body>

</html>