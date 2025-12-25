<?php
$pageTitle = "Keranjang Belanja";
require_once __DIR__ . '/../../config/config.php';

\App\Auth\CustomerAuthMiddleware::requireLogin('cart.php');

$customer = \App\Auth\CustomerAuthMiddleware::getCurrentCustomer();
$customerId = \App\Auth\CustomerAuthMiddleware::getCustomerId();
$isLoggedIn = \App\Auth\CustomerAuthMiddleware::isLoggedIn();

use App\Helper\ProductLandingHelper;
use App\Helper\DiscountHelper;

$productHelper = new ProductLandingHelper();
$discountHelper = new DiscountHelper();

$cartItems = [];
$inStockItems = [];
$outOfStockItems = [];
$lowStockItems = [];
$subtotal = 0;
$subtotalBeforeDiscount = 0;
$totalSavings = 0;
$totalItems = 0;
$stockAdjustments = [];

if ($customerId) {
    try {
        $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

        // Fetch cart items with product info (tanpa discount JOIN)
        $stmt = $db->prepare("
            SELECT c.id_cart, c.jumlah as quantity, c.harga_satuan, 
                   c.tanggal_ditambahkan as created_at, c.tgl_diubah as updated_at,
                   p.id_product, p.nama_product, p.harga, p.stok, p.status_produk, 
                   p.gambar, p.deskripsi_speksifikasi,
                   k.nama_kategori, b.nama_brand
            FROM cart c
            JOIN products p ON c.id_product = p.id_product
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            WHERE c.id_customer = :customer_id
            ORDER BY c.tanggal_ditambahkan DESC
        ");
        $stmt->execute([':customer_id' => $customerId]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Apply discounts using DiscountHelper (dengan computeStatus logic)
        $productIds = array_column($cartItems, 'id_product');
        $activeDiscounts = $discountHelper->getActiveDiscountsForProducts($productIds);

        foreach ($cartItems as &$cartItem) {
            $productId = $cartItem['id_product'];
            $discount = $activeDiscounts[$productId] ?? null;

            // Set default values
            $cartItem['has_discount'] = false;
            $cartItem['harga_final'] = $cartItem['harga'];
            $cartItem['discount_label'] = '';
            $cartItem['discount_badge'] = '';
            $cartItem['savings_per_item'] = 0;

            if ($discount) {
                $discountedPrice = $discountHelper->calculateDiscountedPrice($cartItem['harga'], $discount);
                $cartItem['has_discount'] = true;
                $cartItem['harga_final'] = $discountedPrice;
                $cartItem['discount_label'] = $discountHelper->getDiscountLabel($discount);
                $cartItem['discount_badge'] = $discountHelper->getDiscountBadge($discount);
                $cartItem['savings_per_item'] = $cartItem['harga'] - $discountedPrice;
                $cartItem['id_diskon'] = $discount['id_diskon'];
            }
        }
        unset($cartItem);

        foreach ($cartItems as &$item) {
            $isAvailable = $item['stok'] > 0 && $item['status_produk'] !== 'habis' && $item['status_produk'] !== 'nonaktif';

            if ($item['quantity'] > $item['stok'] && $item['stok'] > 0) {
                $oldQty = $item['quantity'];
                $item['quantity'] = $item['stok'];
                $stockAdjustments[] = [
                    'product' => $item['nama_product'],
                    'old_qty' => $oldQty,
                    'new_qty' => $item['stok']
                ];

                $stmtUpdate = $db->prepare("UPDATE cart SET jumlah = :qty WHERE id_cart = :cart_id");
                $stmtUpdate->execute([':qty' => $item['stok'], ':cart_id' => $item['id_cart']]);
            }

            if ($isAvailable) {
                $inStockItems[] = $item;
                $subtotal += $item['harga_final'] * $item['quantity'];
                $subtotalBeforeDiscount += $item['harga'] * $item['quantity'];
                $totalSavings += $item['savings_per_item'] * $item['quantity'];
                $totalItems += $item['quantity'];

                if ($item['stok'] <= 5) {
                    $lowStockItems[] = $item['nama_product'];
                }
            } else {
                $outOfStockItems[] = $item;
            }
        }
        unset($item);

        $allProductIds = array_map(function ($item) {
            return $item['id_product'];
        }, $cartItems);

        $wishlistedProducts = [];
        if (!empty($allProductIds)) {
            $placeholders = implode(',', array_fill(0, count($allProductIds), '?'));
            $stmtWishlist = $db->prepare("SELECT id_product FROM wishlist WHERE id_customer = ? AND id_product IN ($placeholders)");
            $params = array_merge([$customerId], $allProductIds);
            $stmtWishlist->execute($params);
            $wishlistedProducts = $stmtWishlist->fetchAll(PDO::FETCH_COLUMN);
        }
    } catch (Exception $e) {
        error_log('Cart error: ' . $e->getMessage());
    }
}

$wishlistedProducts = $wishlistedProducts ?? [];

$taxRate = 0.11;
$taxAmount = $subtotal * $taxRate;
$grandTotal = $subtotal + $taxAmount;

$breadcrumbs = [
    ['label' => 'Home', 'url' => 'landingPage.php'],
    ['label' => 'Keranjang', 'url' => null]
];

include '../../components/users/head.php';
?>

<style>
    #customToastContainer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 999999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
    }

    .custom-toast {
        position: relative;
        min-width: 320px;
        max-width: 400px;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        transform: translateX(120%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    }

    .custom-toast.show {
        transform: translateX(0);
        opacity: 1;
    }

    .custom-toast.hiding {
        transform: translateX(120%);
        opacity: 0;
        margin-top: -70px;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55), margin-top 0.3s ease 0.2s;
    }

    .custom-toast.success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
    }

    .custom-toast.error {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }

    .custom-toast.warning {
        background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
        color: white;
    }

    .custom-toast.info {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        color: white;
    }

    .custom-toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .custom-toast-content {
        flex: 1;
    }

    .custom-toast-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .custom-toast-message {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .custom-toast-close {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }

    .custom-toast-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .custom-toast-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 0 0 12px 12px;
        animation: toast-progress 4s linear forwards;
    }

    @keyframes toast-progress {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    @media (max-width: 480px) {
        #customToastContainer {
            left: 10px;
            right: 10px;
            top: 10px;
        }

        .custom-toast {
            min-width: unset;
            max-width: unset;
            width: 100%;
        }
    }
</style>

<body class="w-full bg-gray-50 min-h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" data-customer-logged-in="<?= $isLoggedIn ? 'true' : 'false' ?>">
    <header>
        <?php include '../../components/users/navbarUsers.php'; ?>
    </header>

    <main class="max-w-full mb-10 pt-16 md:pt-40 lg:pt-[172px]">
        <div class="w-full px-4 md:px-8 lg:px-20 py-6">
            <?php include '../../components/users/breadcrumb.php'; ?>

            <?php if (!empty($stockAdjustments)): ?>
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-amber-800 mb-1">Jumlah Produk Disesuaikan</h4>
                            <p class="text-sm text-amber-700">Beberapa produk di keranjang Anda telah disesuaikan karena stok terbatas:</p>
                            <ul class="mt-2 space-y-1">
                                <?php foreach ($stockAdjustments as $adj): ?>
                                    <li class="text-sm text-amber-600">
                                        <span class="font-medium"><?= htmlspecialchars($adj['product']) ?></span>:
                                        <?= $adj['old_qty'] ?> → <?= $adj['new_qty'] ?> item
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-28 h-28 bg-gray-100 rounded-full mb-6">
                        <svg class="w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Keranjang Anda Kosong</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">Belum ada produk di keranjang. Yuk, mulai belanja dan temukan produk terbaik untuk Anda!</p>
                    <a href="productCollection.php" class="inline-flex items-center gap-2 px-8 py-4 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Jelajahi Produk
                    </a>
                </div>
            <?php else: ?>
                <div class="lg:grid lg:grid-cols-3 lg:gap-8">
                    <div class="lg:col-span-2 space-y-6">
                        <?php if (!empty($inStockItems)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="text-lg font-bold text-gray-900">Produk Tersedia</h2>
                                                <p class="text-sm text-gray-500"><?= count($inStockItems) ?> produk siap untuk checkout</p>
                                            </div>
                                        </div>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" id="selectAllInStock" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary" checked>
                                            <span class="text-sm text-gray-600">Pilih Semua</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="divide-y divide-gray-100" id="inStockItems">
                                    <?php foreach ($inStockItems as $item):
                                        $imagePath = !empty($item['gambar']) ? '../../uploads/products/' . htmlspecialchars($item['gambar']) : '../../assets/img/placeholder-product.png';
                                        $hasDiscount = $item['has_discount'] ?? false;
                                        $itemPrice = $item['harga_final'] ?? $item['harga'];
                                        $itemTotal = $itemPrice * $item['quantity'];
                                        $discountLabel = $item['discount_label'] ?? '';
                                        $discountPercent = $hasDiscount ? round((($item['harga'] - $itemPrice) / $item['harga']) * 100) : 0;

                                        $stockStatus = 'in_stock';
                                        $stockClass = 'bg-emerald-100 text-emerald-700';
                                        $stockText = 'Tersedia';
                                        if ($item['stok'] <= 0) {
                                            $stockStatus = 'out_of_stock';
                                            $stockClass = 'bg-red-100 text-red-700';
                                            $stockText = 'Habis';
                                        } elseif ($item['stok'] <= 5) {
                                            $stockStatus = 'low_stock';
                                            $stockClass = 'bg-amber-100 text-amber-700';
                                            $stockText = 'Stok Menipis (' . $item['stok'] . ')';
                                        }
                                    ?>
                                        <div class="p-4 sm:p-6 hover:bg-gray-50/50 transition-colors cart-item"
                                            data-cart-id="<?= $item['id_cart'] ?>"
                                            data-product-id="<?= $item['id_product'] ?>"
                                            data-price="<?= $itemPrice ?>"
                                            data-original-price="<?= $item['harga'] ?>"
                                            data-has-discount="<?= $hasDiscount ? 'true' : 'false' ?>"
                                            data-savings="<?= $item['savings_per_item'] ?? 0 ?>"
                                            data-stock="<?= $item['stok'] ?>">
                                            <div class="flex gap-4">
                                                <div class="flex items-start gap-4 flex-shrink-0">
                                                    <input type="checkbox" class="item-checkbox w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary mt-8" checked>
                                                    <a href="productDetail.php?id=<?= urlencode($item['id_product']) ?>" class="w-24 sm:w-32 h-24 sm:h-32 bg-gray-100 rounded-xl overflow-hidden group block">
                                                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                            onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23f3f4f6%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>No Image</text></svg>'">
                                                    </a>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="flex-1 min-w-0">
                                                            <?php if (!empty($item['nama_brand'])): ?>
                                                                <span class="text-xs text-[#882426] font-medium"><?= htmlspecialchars($item['nama_brand']) ?></span>
                                                            <?php endif; ?>
                                                            <a href="productDetail.php?id=<?= urlencode($item['id_product']) ?>" class="block">
                                                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 hover:text-[#882426] transition-colors line-clamp-2"><?= htmlspecialchars($item['nama_product']) ?></h3>
                                                            </a>
                                                            <?php if (!empty($item['nama_kategori'])): ?>
                                                                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($item['nama_kategori']) ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <button onclick="showDeleteModal('<?= $item['id_cart'] ?>', '<?= htmlspecialchars(addslashes($item['nama_product'])) ?>')"
                                                            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all flex-shrink-0"
                                                            title="Hapus dari keranjang">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <div class="mt-3 flex flex-wrap items-center gap-2">
                                                        <span class="px-2.5 py-1 text-xs font-medium rounded-full <?= $stockClass ?>"><?= $stockText ?></span>
                                                        <?php if ($hasDiscount): ?>
                                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">-<?= $discountPercent ?>%</span>
                                                            <?php if (!empty($discountLabel)): ?>
                                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-700"><?= htmlspecialchars($discountLabel) ?></span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="mt-4 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                                                <button onclick="updateQuantity('<?= $item['id_cart'] ?>', -1, <?= $item['stok'] ?>)"
                                                                    class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed qty-btn-minus"
                                                                    <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                    </svg>
                                                                </button>
                                                                <input type="number" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stok'] ?>"
                                                                    class="w-14 h-10 text-center border-x border-gray-300 text-gray-900 font-medium focus:ring-2 focus:ring-[#882426]/20 focus:border-[#882426] qty-input [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                                    data-cart-id="<?= $item['id_cart'] ?>" data-max="<?= $item['stok'] ?>"
                                                                    onchange="updateQuantityDirect('<?= $item['id_cart'] ?>', this.value, <?= $item['stok'] ?>)">
                                                                <button onclick="updateQuantity('<?= $item['id_cart'] ?>', 1, <?= $item['stok'] ?>)"
                                                                    class="w-10 h-10 flex items-center justify-center bg-gray-50 hover:bg-gray-100 text-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed qty-btn-plus"
                                                                    <?= $item['quantity'] >= $item['stok'] ? 'disabled' : '' ?>>
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <span class="text-xs text-gray-400">Maks: <?= $item['stok'] ?></span>
                                                        </div>
                                                        <div class="text-right">
                                                            <?php if ($hasDiscount): ?>
                                                                <p class="text-sm text-gray-400 line-through"><?= $productHelper->formatPrice($item['harga'] * $item['quantity']) ?></p>
                                                            <?php endif; ?>
                                                            <p class="text-xl font-bold text-[#882426] item-total"><?= $productHelper->formatPrice($itemTotal) ?></p>
                                                            <p class="text-sm text-gray-400"><?= $productHelper->formatPrice($itemPrice) ?>/pcs</p>
                                                        </div>
                                                    </div>

                                                    <?php $isInWishlist = in_array($item['id_product'], $wishlistedProducts); ?>
                                                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap gap-4">
                                                        <button onclick="moveToWishlist('<?= $item['id_product'] ?>', '<?= $item['id_cart'] ?>', <?= $isInWishlist ? 'true' : 'false' ?>)"
                                                            class="inline-flex items-center gap-1.5 text-sm <?= $isInWishlist ? 'text-[#882426]' : 'text-gray-600 hover:text-[#882426]' ?> transition-colors group"
                                                            data-product-id="<?= $item['id_product'] ?>"
                                                            data-in-wishlist="<?= $isInWishlist ? 'true' : 'false' ?>">
                                                            <svg class="w-4 h-4 <?= $isInWishlist ? 'fill-[#882426]' : 'group-hover:fill-current' ?>" fill="<?= $isInWishlist ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                            </svg>
                                                            <?= $isInWishlist ? 'Di Wishlist' : 'Simpan ke Wishlist' ?>
                                                        </button>
                                                        <button onclick="showDeleteModal('<?= $item['id_cart'] ?>', '<?= htmlspecialchars(addslashes($item['nama_product'])) ?>')"
                                                            class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-red-500 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($outOfStockItems)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="text-lg font-bold text-gray-900">Produk Tidak Tersedia</h2>
                                                <p class="text-sm text-gray-500"><?= count($outOfStockItems) ?> produk tidak dapat diproses saat ini</p>
                                            </div>
                                        </div>
                                        <button onclick="showDeleteAllModal()" class="text-sm text-red-500 hover:text-red-600 font-medium flex items-center gap-1 hover:bg-red-50 px-3 py-2 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Semua
                                        </button>
                                    </div>
                                </div>

                                <div class="divide-y divide-gray-100" id="outOfStockItems">
                                    <?php foreach ($outOfStockItems as $item):
                                        $imagePath = !empty($item['gambar']) ? '../../uploads/products/' . htmlspecialchars($item['gambar']) : '../../assets/img/placeholder-product.png';
                                    ?>
                                        <div class="p-4 sm:p-6 bg-gray-50/50 cart-item-unavailable" data-cart-id="<?= $item['id_cart'] ?>">
                                            <div class="flex gap-4">
                                                <div class="w-24 sm:w-32 h-24 sm:h-32 bg-gray-200 rounded-xl overflow-hidden flex-shrink-0 relative">
                                                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['nama_product']) ?>"
                                                        class="w-full h-full object-cover opacity-40 grayscale"
                                                        onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23f3f4f6%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%239ca3af%22 font-size=%2212%22>No Image</text></svg>'">
                                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                                        <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow">Stok Habis</span>
                                                    </div>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="flex-1 min-w-0">
                                                            <?php if (!empty($item['nama_brand'])): ?>
                                                                <span class="text-xs text-gray-400 font-medium"><?= htmlspecialchars($item['nama_brand']) ?></span>
                                                            <?php endif; ?>
                                                            <h3 class="text-base sm:text-lg font-semibold text-gray-500 line-clamp-2"><?= htmlspecialchars($item['nama_product']) ?></h3>
                                                            <?php if (!empty($item['nama_kategori'])): ?>
                                                                <p class="text-sm text-gray-400 mt-1"><?= htmlspecialchars($item['nama_kategori']) ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                        <button onclick="showDeleteModal('<?= $item['id_cart'] ?>', '<?= htmlspecialchars(addslashes($item['nama_product'])) ?>')" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all flex-shrink-0">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <div class="mt-4">
                                                        <p class="text-lg font-bold text-gray-400 line-through"><?= $productHelper->formatPrice($item['harga']) ?></p>
                                                        <p class="text-sm text-gray-400 mt-1">Jumlah: <?= $item['quantity'] ?> item</p>
                                                    </div>

                                                    <?php $isInWishlistOos = in_array($item['id_product'], $wishlistedProducts); ?>
                                                    <div class="mt-4 pt-4 border-t border-gray-200 flex flex-wrap gap-4">
                                                        <button onclick="moveToWishlist('<?= $item['id_product'] ?>', '<?= $item['id_cart'] ?>', <?= $isInWishlistOos ? 'true' : 'false' ?>)"
                                                            class="inline-flex items-center gap-1.5 text-sm <?= $isInWishlistOos ? 'text-[#882426]' : 'text-gray-500 hover:text-[#882426]' ?> transition-colors"
                                                            data-product-id="<?= $item['id_product'] ?>"
                                                            data-in-wishlist="<?= $isInWishlistOos ? 'true' : 'false' ?>">
                                                            <svg class="w-4 h-4 <?= $isInWishlistOos ? 'fill-[#882426]' : '' ?>" fill="<?= $isInWishlistOos ? 'currentColor' : 'none' ?>" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                            </svg>
                                                            <?= $isInWishlistOos ? 'Di Wishlist' : 'Simpan ke Wishlist' ?>
                                                        </button>
                                                        <button onclick="showDeleteModal('<?= $item['id_cart'] ?>', '<?= htmlspecialchars(addslashes($item['nama_product'])) ?>')"
                                                            class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-red-500 transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="lg:col-span-1 mt-6 lg:mt-0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-28" id="orderSummary">
                            <div class="bg-white px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Ringkasan Belanja
                                </h2>
                            </div>

                            <div class="p-6">
                                <div class="space-y-4 mb-6">
                                    <!-- Harga Normal - selalu ada, disembunyikan jika tidak ada diskon -->
                                    <div id="originalPriceRow" class="flex justify-between text-gray-600 <?= $totalSavings > 0 ? '' : 'hidden' ?>">
                                        <span>Harga Normal</span>
                                        <span class="font-semibold text-gray-400 line-through" id="originalPriceDisplay"><?= $productHelper->formatPrice($subtotalBeforeDiscount) ?></span>
                                    </div>
                                    <!-- Total Hemat - selalu ada, disembunyikan jika tidak ada diskon -->
                                    <div id="savingsRow" class="flex justify-between text-emerald-600 bg-emerald-50 -mx-6 px-6 py-3 <?= $totalSavings > 0 ? '' : 'hidden' ?>">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-semibold">Total Hemat</span>
                                        </div>
                                        <span class="font-bold" id="savingsDisplay">-<?= $productHelper->formatPrice($totalSavings) ?></span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Subtotal (<span id="totalItemsCount"><?= $totalItems ?></span> item)</span>
                                        <span class="font-semibold text-gray-900" id="subtotalDisplay"><?= $productHelper->formatPrice($subtotal) ?></span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <span>Pajak (PPN 11%)</span>
                                            <div class="relative group">
                                                <svg class="w-4 h-4 text-gray-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-48 p-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg z-10">
                                                    Pajak Pertambahan Nilai sesuai peraturan pemerintah
                                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="font-semibold text-gray-900" id="taxDisplay"><?= $productHelper->formatPrice($taxAmount) ?></span>
                                    </div>
                                    <div class="h-px bg-gray-200"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total Belanja</span>
                                        <span class="text-2xl font-bold text-[#882426]" id="grandTotalDisplay"><?= $productHelper->formatPrice($grandTotal) ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($inStockItems)): ?>
                                    <a href="productCheckout.php?from=cart"
                                        id="checkoutBtn"
                                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-[#882426] text-white font-semibold rounded-xl hover:from-[#6a1c1e] hover:to-[#882426] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span>Lanjut ke Checkout</span>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </a>
                                <?php else: ?>
                                    <button disabled class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-gray-300 text-gray-500 font-semibold rounded-xl cursor-not-allowed mb-3">
                                        <span>Tidak Ada Produk Tersedia</span>
                                    </button>
                                <?php endif; ?>

                                <a href="productCollection.php"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-[#882426] text-[#882426] font-semibold rounded-xl hover:bg-[#882426]/5 transition-all duration-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                    </svg>
                                    Lanjut Belanja
                                </a>
                            </div>

                            <div class="px-6 pb-6 space-y-4">
                                <div class="p-4 bg-emerald-50 rounded-xl">
                                    <h4 class="font-semibold text-emerald-800 mb-3 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        Keuntungan Berbelanja
                                    </h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2 text-sm text-emerald-700">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Gratis ongkir untuk pembelian di atas Rp 500.000</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-emerald-700">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Garansi produk 100% original</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-emerald-700">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Pembayaran aman & terpercaya</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-sm text-emerald-700">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            <span>Garansi 30 hari pengembalian</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- <div class="p-4 bg-gray-50 rounded-xl">
                                    <p class="text-sm font-semibold text-gray-900 mb-3">Metode Pembayaran</p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-blue-600">VISA</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-red-500">Mastercard</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-blue-800">BCA</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-orange-500">BNI</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-blue-600">Mandiri</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-green-600">GoPay</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-blue-500">OVO</span>
                                        </div>
                                        <div class="px-3 py-2 bg-white rounded-lg border border-gray-200 shadow-sm">
                                            <span class="text-xs font-bold text-red-600">ShopeePay</span>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../components/users/footer.php'; ?>
    <?php include '../../components/users/loginRequiredModal.php'; ?>

    <div id="notificationContainer" class="fixed top-24 right-4 z-50 space-y-2"></div>

    <script>
        window.APP_CONFIG = {
            baseUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>',
            apiUrl: '<?= rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/') ?>/api'
        };
    </script>
    <script src="../../assets/js/users/product-actions.js"></script>

    <style>
        @keyframes quantity-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .qty-animate {
            animation: quantity-pulse 0.2s ease-out;
        }

        @keyframes fade-slide-out {
            from {
                opacity: 1;
                transform: translateX(0);
                max-height: 300px;
            }

            to {
                opacity: 0;
                transform: translateX(-30px);
                max-height: 0;
                padding: 0;
                margin: 0;
            }
        }

        .fade-out-item {
            animation: fade-slide-out 0.4s ease-out forwards;
            overflow: hidden;
        }

        @keyframes price-update {
            0% {
                color: #882426;
                transform: scale(1);
            }

            50% {
                color: #22c55e;
                transform: scale(1.08);
            }

            100% {
                color: #882426;
                transform: scale(1);
            }
        }

        .price-animate {
            animation: price-update 0.4s ease-out;
        }

        @keyframes slide-in-right {
            from {
                opacity: 0;
                transform: translateX(100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slide-in-right 0.3s ease-out;
        }

        @keyframes slide-out-right {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        .slide-out {
            animation: slide-out-right 0.3s ease-out forwards;
        }
    </style>

    <script>
        const TAX_RATE = 0.11;

        function getApiUrl(endpoint) {
            if (window.APP_CONFIG && window.APP_CONFIG.apiUrl) {
                return window.APP_CONFIG.apiUrl + '/' + endpoint;
            }
            return '../../api/' + endpoint;
        }

        function formatPrice(price) {
            return 'Rp ' + Math.round(price).toLocaleString('id-ID').replace(/,/g, '.');
        }

        // Initialize toast notification system
        function initCustomToast() {
            if (document.getElementById('customToastContainer')) return;

            const container = document.createElement('div');
            container.id = 'customToastContainer';
            document.body.appendChild(container);

            window.showCustomToast = function(message, type = 'success', title = null, duration = 4000) {
                const container = document.getElementById('customToastContainer');
                const toast = document.createElement('div');
                toast.className = `custom-toast ${type}`;

                const icons = {
                    success: 'check_circle',
                    error: 'error',
                    warning: 'warning',
                    info: 'info'
                };
                const titles = {
                    success: 'Berhasil!',
                    error: 'Gagal!',
                    warning: 'Perhatian!',
                    info: 'Informasi'
                };

                toast.innerHTML = `
                <div class="custom-toast-icon">
                    <span class="material-symbols-outlined">${icons[type]}</span>
                </div>
                <div class="custom-toast-content">
                    <div class="custom-toast-title">${title || titles[type]}</div>
                    <div class="custom-toast-message">${message}</div>
                </div>
                <button class="custom-toast-close">
                    <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                </button>
                <div class="custom-toast-progress" style="animation-duration: ${duration}ms;"></div>
            `;

                const closeBtn = toast.querySelector('.custom-toast-close');
                closeBtn.addEventListener('click', () => removeToast(toast));

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        toast.classList.add('show');
                    });
                });

                const timeoutId = setTimeout(() => removeToast(toast), duration);
                toast.dataset.timeoutId = timeoutId;

                function removeToast(toastElement) {
                    if (toastElement.classList.contains('hiding')) return;

                    clearTimeout(parseInt(toastElement.dataset.timeoutId));
                    toastElement.classList.add('hiding');
                    toastElement.classList.remove('show');

                    setTimeout(() => {
                        if (toastElement.parentNode) {
                            toastElement.remove();
                        }
                    }, 500);
                }

                return toast;
            };
        }

        // Wrapper function for backward compatibility
        function showNotification(message, type = 'success') {
            if (typeof window.showCustomToast === 'function') {
                window.showCustomToast(message, type);
            }
        }

        function recalculateTotals() {
            let subtotal = 0;
            let subtotalBeforeDiscount = 0;
            let totalSavings = 0;
            let totalItems = 0;

            document.querySelectorAll('#inStockItems .cart-item').forEach(item => {
                const checkbox = item.querySelector('.item-checkbox');
                if (checkbox && !checkbox.checked) return;

                const price = parseFloat(item.dataset.price);
                const originalPrice = parseFloat(item.dataset.originalPrice);
                const savings = parseFloat(item.dataset.savings) || 0;
                const qtyInput = item.querySelector('.qty-input');
                const quantity = parseInt(qtyInput.value);

                subtotal += price * quantity;
                subtotalBeforeDiscount += originalPrice * quantity;
                totalSavings += savings * quantity;
                totalItems += quantity;

                const itemTotal = item.querySelector('.item-total');
                if (itemTotal) {
                    itemTotal.textContent = formatPrice(price * quantity);
                }
            });

            const tax = subtotal * TAX_RATE;
            const grandTotal = subtotal + tax;

            const totalItemsEl = document.getElementById('totalItemsCount');
            const subtotalEl = document.getElementById('subtotalDisplay');
            const taxEl = document.getElementById('taxDisplay');
            const grandTotalEl = document.getElementById('grandTotalDisplay');
            const savingsEl = document.getElementById('savingsDisplay');
            const originalPriceEl = document.getElementById('originalPriceDisplay');
            const originalPriceRow = document.getElementById('originalPriceRow');
            const savingsRow = document.getElementById('savingsRow');

            if (totalItemsEl) totalItemsEl.textContent = totalItems;
            if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);
            if (taxEl) taxEl.textContent = formatPrice(tax);

            // Update dan show/hide elemen Total Hemat berdasarkan ada tidaknya savings
            if (totalSavings > 0) {
                if (savingsEl) savingsEl.textContent = '-' + formatPrice(totalSavings);
                if (originalPriceEl) originalPriceEl.textContent = formatPrice(subtotalBeforeDiscount);
                if (originalPriceRow) originalPriceRow.classList.remove('hidden');
                if (savingsRow) savingsRow.classList.remove('hidden');
            } else {
                if (originalPriceRow) originalPriceRow.classList.add('hidden');
                if (savingsRow) savingsRow.classList.add('hidden');
            }

            if (grandTotalEl) {
                grandTotalEl.textContent = formatPrice(grandTotal);
                grandTotalEl.classList.add('price-animate');
                setTimeout(() => grandTotalEl.classList.remove('price-animate'), 400);
            }
        }

        function updateSummaryFromAPI(data) {
            const subtotalEl = document.getElementById('subtotalDisplay');
            const taxEl = document.getElementById('taxDisplay');
            const grandTotalEl = document.getElementById('grandTotalDisplay');
            const totalItemsEl = document.getElementById('totalItemsCount');
            const savingsEl = document.getElementById('savingsDisplay');
            const originalPriceEl = document.getElementById('originalPriceDisplay');
            const originalPriceRow = document.getElementById('originalPriceRow');
            const savingsRow = document.getElementById('savingsRow');

            if (data.subtotal !== undefined && subtotalEl) {
                subtotalEl.textContent = formatPrice(data.subtotal);
            }
            if (data.tax !== undefined && taxEl) {
                taxEl.textContent = formatPrice(data.tax);
            }
            if (data.grand_total !== undefined && grandTotalEl) {
                grandTotalEl.textContent = formatPrice(data.grand_total);
                grandTotalEl.classList.add('price-animate');
                setTimeout(() => grandTotalEl.classList.remove('price-animate'), 400);
            }
            if (data.total_items !== undefined && totalItemsEl) {
                totalItemsEl.textContent = data.total_items;
            }

            if (data.total_savings !== undefined) {
                if (data.total_savings > 0) {
                    if (savingsEl) savingsEl.textContent = '-' + formatPrice(data.total_savings);
                    if (originalPriceEl && data.subtotal_before_discount !== undefined) {
                        originalPriceEl.textContent = formatPrice(data.subtotal_before_discount);
                    }
                    if (originalPriceRow) originalPriceRow.classList.remove('hidden');
                    if (savingsRow) savingsRow.classList.remove('hidden');
                } else {
                    if (originalPriceRow) originalPriceRow.classList.add('hidden');
                    if (savingsRow) savingsRow.classList.add('hidden');
                }
            }
        }

        function updateQuantity(cartId, change, maxStock) {
            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            if (!cartItem) return;

            const qtyInput = cartItem.querySelector('.qty-input');
            const minusBtn = cartItem.querySelector('.qty-btn-minus');
            const plusBtn = cartItem.querySelector('.qty-btn-plus');

            let currentQty = parseInt(qtyInput.value);
            let newQty = currentQty + change;

            if (newQty < 1) newQty = 1;
            if (newQty > maxStock) {
                showNotification(`Maksimal pembelian ${maxStock} item`, 'warning');
                return;
            }

            if (newQty === currentQty) return;

            qtyInput.value = newQty;
            qtyInput.disabled = true;
            if (minusBtn) minusBtn.disabled = true;
            if (plusBtn) plusBtn.disabled = true;

            qtyInput.classList.add('qty-animate');
            setTimeout(() => qtyInput.classList.remove('qty-animate'), 200);

            fetch(getApiUrl('cart/update.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        cart_id: cartId,
                        quantity: newQty
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.adjusted) {
                            qtyInput.value = data.quantity;
                            showNotification(data.message || 'Jumlah disesuaikan dengan stok', 'warning');
                        }

                        const itemTotal = cartItem.querySelector('.item-total');
                        if (itemTotal && data.item_total !== undefined) {
                            itemTotal.textContent = formatPrice(data.item_total);
                            itemTotal.classList.add('price-animate');
                            setTimeout(() => itemTotal.classList.remove('price-animate'), 400);
                        }

                        updateSummaryFromAPI(data);

                        const actualQty = data.quantity || parseInt(qtyInput.value);
                        const actualMaxStock = data.max_stock || maxStock;
                        if (minusBtn) minusBtn.disabled = actualQty <= 1;
                        if (plusBtn) plusBtn.disabled = actualQty >= actualMaxStock;
                    } else {
                        qtyInput.value = currentQty;
                        showNotification(data.message || 'Gagal memperbarui jumlah', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    qtyInput.value = currentQty;
                    showNotification('Terjadi kesalahan', 'error');
                })
                .finally(() => {
                    qtyInput.disabled = false;
                });
        }

        function updateQuantityDirect(cartId, value, maxStock) {
            let newQty = parseInt(value);
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            if (newQty > maxStock) {
                newQty = maxStock;
                showNotification(`Maksimal pembelian ${maxStock} item`, 'warning');
            }

            const cartItem = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
            if (!cartItem) return;

            const qtyInput = cartItem.querySelector('.qty-input');
            qtyInput.value = newQty;

            fetch(getApiUrl('cart/update.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        cart_id: cartId,
                        quantity: newQty
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.adjusted) {
                            qtyInput.value = data.quantity;
                            showNotification(data.message || 'Jumlah disesuaikan dengan stok', 'warning');
                        }

                        const itemTotal = cartItem.querySelector('.item-total');
                        if (itemTotal && data.item_total !== undefined) {
                            itemTotal.textContent = formatPrice(data.item_total);
                        }

                        updateSummaryFromAPI(data);

                        const minusBtn = cartItem.querySelector('.qty-btn-minus');
                        const plusBtn = cartItem.querySelector('.qty-btn-plus');
                        const actualQty = data.quantity || newQty;
                        const actualMaxStock = data.max_stock || maxStock;
                        if (minusBtn) minusBtn.disabled = actualQty <= 1;
                        if (plusBtn) plusBtn.disabled = actualQty >= actualMaxStock;
                    } else {
                        showNotification(data.message || 'Gagal memperbarui jumlah', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
                });
        }

        function removeFromCart(cartId) {
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            if (cartItem) {
                cartItem.style.opacity = '0.5';
                cartItem.style.pointerEvents = 'none';
            }

            fetch(getApiUrl('cart/remove.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        cart_id: cartId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clearCartSelectionOnRemove(cartId);

                        if (cartItem) {
                            cartItem.classList.add('fade-out-item');
                            setTimeout(() => {
                                cartItem.remove();
                                updateSummaryFromAPI(data);
                                checkEmptyCart();
                                updateCheckoutButton();
                            }, 400);
                        }
                        showNotification('Produk dihapus dari keranjang', 'success');

                        if (data.cart_count !== undefined && typeof updateCartCount === 'function') {
                            updateCartCount(data.cart_count, true);
                        }
                    } else {
                        if (cartItem) {
                            cartItem.style.opacity = '1';
                            cartItem.style.pointerEvents = 'auto';
                        }
                        showNotification(data.message || 'Gagal menghapus produk', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (cartItem) {
                        cartItem.style.opacity = '1';
                        cartItem.style.pointerEvents = 'auto';
                    }
                    showNotification('Terjadi kesalahan', 'error');
                });
        }

        function removeAllOutOfStock() {
            const outOfStockItems = document.querySelectorAll('.cart-item-unavailable');
            if (outOfStockItems.length === 0) return;

            const cartIds = Array.from(outOfStockItems).map(item => item.dataset.cartId);

            outOfStockItems.forEach(item => {
                item.style.opacity = '0.5';
                item.style.pointerEvents = 'none';
            });

            Promise.all(cartIds.map(cartId =>
                    fetch(getApiUrl('cart/remove.php'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            cart_id: cartId
                        })
                    }).then(res => res.json())
                ))
                .then(results => {
                    const successCount = results.filter(r => r.success).length;

                    outOfStockItems.forEach(item => {
                        item.classList.add('fade-out-item');
                    });

                    setTimeout(() => {
                        outOfStockItems.forEach(item => item.remove());
                        checkEmptyCart();
                    }, 400);

                    showNotification(`${successCount} produk tidak tersedia dihapus`, 'success');

                    const lastResult = results.filter(r => r.success).pop();
                    if (lastResult) {
                        updateSummaryFromAPI(lastResult);
                        if (lastResult.cart_count !== undefined && typeof updateCartCount === 'function') {
                            updateCartCount(lastResult.cart_count, true);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    outOfStockItems.forEach(item => {
                        item.style.opacity = '1';
                        item.style.pointerEvents = 'auto';
                    });
                    showNotification('Terjadi kesalahan', 'error');
                });
        }

        function checkEmptyCart() {
            const allItems = document.querySelectorAll('.cart-item, .cart-item-unavailable');
            if (allItems.length === 0) {
                location.reload();
                return;
            }

            const inStockContainer = document.getElementById('inStockItems');
            if (inStockContainer && inStockContainer.querySelectorAll('.cart-item').length === 0) {
                const section = inStockContainer.closest('.bg-white');
                if (section) section.remove();

                const checkoutBtn = document.getElementById('checkoutBtn');
                if (checkoutBtn) {
                    checkoutBtn.classList.remove('from-[#882426]', 'to-[#a12c2e]', 'hover:from-[#6a1c1e]', 'hover:to-[#882426]');
                    checkoutBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                    checkoutBtn.removeAttribute('href');
                    checkoutBtn.innerHTML = '<span>Tidak Ada Produk Tersedia</span>';
                }
            }

            const outOfStockContainer = document.getElementById('outOfStockItems');
            if (outOfStockContainer && outOfStockContainer.querySelectorAll('.cart-item-unavailable').length === 0) {
                const section = outOfStockContainer.closest('.bg-white');
                if (section) section.remove();
            }
        }

        function moveToWishlist(productId, cartId, isAlreadyInWishlist = false) {
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            const wishlistBtn = cartItem ? cartItem.querySelector(`[data-product-id="${productId}"]`) : null;

            if (wishlistBtn) {
                wishlistBtn.style.pointerEvents = 'none';
                wishlistBtn.style.opacity = '0.5';
            }

            fetch(getApiUrl('wishlist/add.php'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (wishlistBtn) {
                        wishlistBtn.style.pointerEvents = 'auto';
                        wishlistBtn.style.opacity = '1';
                    }

                    if (data.success) {
                        if (data.action === 'added') {
                            if (wishlistBtn) {
                                wishlistBtn.setAttribute('data-in-wishlist', 'true');
                                wishlistBtn.classList.remove('text-gray-600', 'text-gray-500');
                                wishlistBtn.classList.add('text-[#882426]');
                                wishlistBtn.innerHTML = `
                                    <svg class="w-4 h-4 fill-[#882426]" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    Di Wishlist
                                `;
                                wishlistBtn.onclick = function() {
                                    moveToWishlist(productId, cartId, true);
                                };
                            }
                            showNotification('Produk ditambahkan ke wishlist', 'success');
                        } else if (data.action === 'removed') {
                            if (wishlistBtn) {
                                wishlistBtn.setAttribute('data-in-wishlist', 'false');
                                wishlistBtn.classList.remove('text-[#882426]');
                                wishlistBtn.classList.add('text-gray-600');
                                wishlistBtn.innerHTML = `
                                    <svg class="w-4 h-4 group-hover:fill-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    Simpan ke Wishlist
                                `;
                                wishlistBtn.onclick = function() {
                                    moveToWishlist(productId, cartId, false);
                                };
                            }
                            showNotification('Produk dihapus dari wishlist', 'success');
                        }

                        if (data.wishlist_count !== undefined && typeof updateWishlistCount === 'function') {
                            updateWishlistCount(data.wishlist_count, true);
                        }
                    } else {
                        showNotification(data.message || 'Gagal memperbarui wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (wishlistBtn) {
                        wishlistBtn.style.pointerEvents = 'auto';
                        wishlistBtn.style.opacity = '1';
                    }
                    showNotification('Terjadi kesalahan', 'error');
                });
        }

        const CART_SELECTION_KEY = 'nano_cart_selected_items';

        function saveCartSelection() {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const selectedItems = [];

            itemCheckboxes.forEach(checkbox => {
                const cartItem = checkbox.closest('.cart-item');
                if (cartItem && checkbox.checked) {
                    selectedItems.push(cartItem.dataset.cartId);
                }
            });

            localStorage.setItem(CART_SELECTION_KEY, JSON.stringify(selectedItems));
            updateCheckoutButton();
        }

        function loadCartSelection() {
            const saved = localStorage.getItem(CART_SELECTION_KEY);
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');

            if (!saved) {
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                saveCartSelection();
                return;
            }

            try {
                const selectedItems = JSON.parse(saved);
                const currentCartIds = [];

                itemCheckboxes.forEach(checkbox => {
                    const cartItem = checkbox.closest('.cart-item');
                    if (cartItem) {
                        const cartId = cartItem.dataset.cartId;
                        currentCartIds.push(cartId);
                        checkbox.checked = selectedItems.includes(cartId);
                    }
                });

                const validSelectedItems = selectedItems.filter(id => currentCartIds.includes(id));
                if (validSelectedItems.length !== selectedItems.length) {
                    localStorage.setItem(CART_SELECTION_KEY, JSON.stringify(validSelectedItems));
                }

                updateSelectAllCheckbox();
                recalculateTotals();
            } catch (e) {
                console.error('Error loading cart selection:', e);
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                saveCartSelection();
            }
        }

        function updateSelectAllCheckbox() {
            const selectAllCheckbox = document.getElementById('selectAllInStock');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');

            if (!selectAllCheckbox || itemCheckboxes.length === 0) return;

            const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }

        function getSelectedCartIds() {
            const selectedIds = [];
            document.querySelectorAll('#inStockItems .cart-item').forEach(item => {
                const checkbox = item.querySelector('.item-checkbox');
                if (checkbox && checkbox.checked) {
                    selectedIds.push(item.dataset.cartId);
                }
            });
            return selectedIds;
        }

        function updateCheckoutButton() {
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (!checkoutBtn) return;

            const selectedIds = getSelectedCartIds();

            if (selectedIds.length === 0) {
                checkoutBtn.classList.add('pointer-events-none', 'opacity-50');
                checkoutBtn.setAttribute('href', '#');
                checkoutBtn.onclick = function(e) {
                    e.preventDefault();
                    showNotification('Pilih minimal 1 produk untuk checkout', 'warning');
                };
            } else {
                checkoutBtn.classList.remove('pointer-events-none', 'opacity-50');
                checkoutBtn.removeAttribute('onclick');
                checkoutBtn.onclick = null;
                checkoutBtn.setAttribute('href', 'productCheckout.php?from=cart&items=' + selectedIds.join(','));
            }
        }

        function clearCartSelectionOnRemove(cartId) {
            const saved = localStorage.getItem(CART_SELECTION_KEY);
            if (!saved) return;

            try {
                let selectedItems = JSON.parse(saved);
                selectedItems = selectedItems.filter(id => id !== cartId);
                localStorage.setItem(CART_SELECTION_KEY, JSON.stringify(selectedItems));
            } catch (e) {
                console.error('Error clearing cart selection:', e);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAllInStock');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');

            loadCartSelection();
            updateCheckoutButton();

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    saveCartSelection();
                    recalculateTotals();
                });
            }

            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllCheckbox();
                    saveCartSelection();
                    recalculateTotals();
                });
            });
        });
    </script>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0" id="deleteModalContent">
                <div class="p-6">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 text-center mb-2" id="deleteModalTitle">Hapus Produk?</h3>
                    <p class="text-gray-500 text-center mb-6" id="deleteModalMessage">Apakah Anda yakin ingin menghapus produk ini dari keranjang?</p>

                    <div class="flex gap-3">
                        <button onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors">
                            Batal
                        </button>
                        <button onclick="confirmDelete()"
                            id="confirmDeleteBtn"
                            class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deleteModalCartId = null;
        let deleteModalType = 'single';
        let isDeleteInProgress = false;

        function showDeleteModal(cartId, productName) {
            if (isDeleteInProgress) return;
            deleteModalCartId = cartId;
            deleteModalType = 'single';

            document.getElementById('deleteModalTitle').textContent = 'Hapus Produk?';
            document.getElementById('deleteModalMessage').innerHTML = `Apakah Anda yakin ingin menghapus <span class="font-semibold text-gray-700">"${productName}"</span> dari keranjang?`;

            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function showDeleteAllModal() {
            if (isDeleteInProgress) return;
            const outOfStockItems = document.querySelectorAll('.cart-item-unavailable');
            if (outOfStockItems.length === 0) return;

            deleteModalType = 'all';

            document.getElementById('deleteModalTitle').textContent = 'Hapus Semua Produk Tidak Tersedia?';
            document.getElementById('deleteModalMessage').innerHTML = `Apakah Anda yakin ingin menghapus <span class="font-semibold text-red-500">${outOfStockItems.length} produk</span> yang tidak tersedia dari keranjang?`;

            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');

            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');

            setTimeout(() => {
                modal.classList.add('hidden');
                deleteModalCartId = null;
            }, 200);
        }

        function confirmDelete() {
            if (isDeleteInProgress) return;
            isDeleteInProgress = true;

            const cartId = deleteModalCartId;
            const type = deleteModalType;

            closeDeleteModal();

            if (type === 'all') {
                removeAllOutOfStock();
            } else if (cartId) {
                removeFromCart(cartId);
            }

            setTimeout(() => {
                isDeleteInProgress = false;
            }, 500);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
        // Initialize toast on page load
        document.addEventListener('DOMContentLoaded', initCustomToast);
    </script>
</body>

</html>