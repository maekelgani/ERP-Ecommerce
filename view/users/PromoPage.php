<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Services/PromoService.php';

$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();
$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();

use App\Services\PromoService;

$promoService = new PromoService();
$promoData = $promoService->getPromoLandingData();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo & Diskon - Nano Komputer</title>
    <link rel="stylesheet" href="../../assets/css/output.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .countdown-box {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            min-width: 3rem;
        }

        .countdown-value {
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1;
        }

        .countdown-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        .promo-card {
            transition: all 0.3s ease;
        }

        .promo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .product-card {
            transition: all 0.2s ease;
        }

        .product-card:hover {
            transform: scale(1.02);
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

<body class="bg-gray-50">
    <?php include_once '../../components/customer/navbarCustomer.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Promo & Diskon Spesial</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">Nikmati berbagai penawaran menarik dan hemat lebih banyak untuk pembelian produk komputer dan aksesoris favorit Anda</p>
        </div>

        <?php if (empty($promoData)): ?>
            <div class="text-center py-16">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">local_offer</span>
                <h2 class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Promo Aktif</h2>
                <p class="text-gray-500 mb-6">Promo menarik akan segera hadir. Pantau terus halaman ini!</p>
                <a href="../../view/customer/shop.php" class="inline-flex items-center gap-2 px-6 py-3 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f] transition-colors">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    Lihat Semua Produk
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-12">
                <?php foreach ($promoData as $promo):
                    $campaign = $promo['campaign'];
                    $products = $promo['products'];
                    $countdown = $promo['countdown_seconds'];

                    $tipeColors = [
                        'diskon_produk' => 'from-red-600 to-red-800',
                        'flash_sale' => 'from-orange-500 to-red-600',
                        'voucher' => 'from-purple-600 to-purple-800',
                        'bundle' => 'from-green-600 to-green-800',
                        'gratis_ongkir' => 'from-teal-500 to-teal-700'
                    ];
                    $gradientClass = $tipeColors[$campaign['tipe']] ?? 'from-[#882426] to-[#6d1d1f]';
                ?>
                    <section class="promo-card rounded-2xl overflow-hidden shadow-lg">
                        <div class="bg-gradient-to-r <?= $gradientClass ?> text-white p-6 md:p-8">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <?php if ($campaign['tipe'] === 'flash_sale'): ?>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-white/20 rounded-full text-sm mb-3">
                                            <span class="material-symbols-outlined text-lg">bolt</span>
                                            Flash Sale
                                        </span>
                                    <?php endif; ?>
                                    <h2 class="text-2xl md:text-3xl font-bold mb-2"><?= htmlspecialchars($campaign['judul']) ?></h2>
                                    <?php if ($campaign['deskripsi']): ?>
                                        <p class="text-white/80 max-w-xl"><?= htmlspecialchars($campaign['deskripsi']) ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if ($countdown > 0): ?>
                                    <div class="flex-shrink-0" data-countdown="<?= $countdown ?>">
                                        <p class="text-sm text-white/70 mb-2 text-center md:text-right">Berakhir dalam:</p>
                                        <div class="flex items-center gap-2">
                                            <div class="countdown-box">
                                                <span class="countdown-value countdown-days">00</span>
                                                <span class="countdown-label">Hari</span>
                                            </div>
                                            <span class="text-2xl font-bold">:</span>
                                            <div class="countdown-box">
                                                <span class="countdown-value countdown-hours">00</span>
                                                <span class="countdown-label">Jam</span>
                                            </div>
                                            <span class="text-2xl font-bold">:</span>
                                            <div class="countdown-box">
                                                <span class="countdown-value countdown-minutes">00</span>
                                                <span class="countdown-label">Menit</span>
                                            </div>
                                            <span class="text-2xl font-bold">:</span>
                                            <div class="countdown-box">
                                                <span class="countdown-value countdown-seconds">00</span>
                                                <span class="countdown-label">Detik</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-white p-6">
                            <?php if (!empty($products)): ?>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    <?php foreach ($products as $product): ?>
                                        <a href="../../view/customer/detailProduct.php?id=<?= urlencode($product['id_produk']) ?>" class="product-card bg-white border border-gray-100 rounded-xl overflow-hidden">
                                            <div class="relative aspect-square bg-gray-100">
                                                <img src="../../uploads/produk/<?= htmlspecialchars($product['gambar'] ?? 'default.png') ?>" alt="<?= htmlspecialchars($product['nama_product']) ?>" class="w-full h-full object-cover">
                                                <?php if ($product['diskon_nilai']): ?>
                                                    <span class="badge-discount">
                                                        <?= $product['diskon_jenis'] === 'persen' ? $product['diskon_nilai'] . '%' : 'Hemat ' . number_format($product['diskon_nilai'], 0, ',', '.') ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="p-3">
                                                <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-2"><?= htmlspecialchars($product['nama_product']) ?></h3>
                                                <?php if ($product['harga_diskon']): ?>
                                                    <p class="text-xs text-gray-400 line-through">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                                    <p class="text-sm font-bold text-[#882426]">Rp <?= number_format($product['harga_diskon'], 0, ',', '.') ?></p>
                                                <?php else: ?>
                                                    <p class="text-sm font-bold text-gray-800">Rp <?= number_format($product['harga'], 0, ',', '.') ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($promo['total_products'] > 8): ?>
                                    <div class="text-center mt-6">
                                        <a href="../../view/customer/promoCampaign.php?slug=<?= urlencode($campaign['slug']) ?>" class="inline-flex items-center gap-2 text-[#882426] font-medium hover:underline">
                                            Lihat semua <?= $promo['total_products'] ?> produk
                                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-center text-gray-500 py-4">Produk akan segera tersedia</p>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <section class="mt-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl p-8 md:p-12">
            <div class="max-w-2xl mx-auto text-center">
                <span class="material-symbols-outlined text-5xl text-[#882426] mb-4">confirmation_number</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Punya Kode Voucher?</h2>
                <p class="text-gray-600 mb-6">Masukkan kode voucher Anda untuk mendapatkan diskon tambahan saat checkout</p>
                <form id="voucherCheckForm" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="text" id="voucherCode" placeholder="Masukkan kode voucher" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] uppercase" required>
                    <button type="submit" class="px-6 py-3 bg-[#882426] text-white rounded-lg hover:bg-[#6d1d1f] transition-colors font-medium">
                        Cek Voucher
                    </button>
                </form>
                <div id="voucherResult" class="mt-4 hidden"></div>
            </div>
        </section>
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

        document.getElementById('voucherCheckForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = document.getElementById('voucherCode').value;
            const resultDiv = document.getElementById('voucherResult');

            try {
                const response = await fetch('../../app/controllers/voucherController.php?action=validate&code=' + encodeURIComponent(code) + '&cart_total=0');
                const data = await response.json();

                resultDiv.classList.remove('hidden');

                if (data.valid) {
                    const voucher = data.voucher;
                    let valueText = '';
                    switch (voucher.jenis) {
                        case 'diskon_persen':
                            valueText = `Diskon ${voucher.nilai}%`;
                            break;
                        case 'diskon_nominal':
                            valueText = `Diskon Rp ${Number(voucher.nilai).toLocaleString('id-ID')}`;
                            break;
                        case 'gratis_ongkir':
                            valueText = 'Gratis Ongkir';
                            break;
                        case 'cashback':
                            valueText = `Cashback ${voucher.nilai}%`;
                            break;
                    }

                    resultDiv.innerHTML = `
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-left">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                                <div>
                                    <p class="font-semibold text-emerald-700">${voucher.judul || 'Voucher Valid'}</p>
                                    <p class="text-sm text-emerald-600">${valueText}</p>
                                    ${voucher.minimal_belanja > 0 ? `<p class="text-xs text-gray-500 mt-1">Min. belanja Rp ${Number(voucher.minimal_belanja).toLocaleString('id-ID')}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-red-600">error</span>
                                <p class="text-red-700">${data.message}</p>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.classList.remove('hidden');
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-700">Terjadi kesalahan. Silakan coba lagi.</p>
                    </div>
                `;
            }
        });
    </script>

    <?php include '../../components/users/loginRequiredModal.php'; ?>
</body>

</html>