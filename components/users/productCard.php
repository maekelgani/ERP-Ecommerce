<?php
function renderProductCard(array $product, \App\Helper\ProductLandingHelper $productHelper): string {
    $svgPlaceholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%23f3f4f6'/%3E%3Cpath d='M200 120c-44.18 0-80 35.82-80 80s35.82 80 80 80 80-35.82 80-80-35.82-80-80-80zm0 140c-33.14 0-60-26.86-60-60s26.86-60 60-60 60 26.86 60 60-26.86 60-60 60z' fill='%23d1d5db'/%3E%3Cpath d='M200 160c-22.09 0-40 17.91-40 40s17.91 40 40 40 40-17.91 40-40-17.91-40-40-40z' fill='%23d1d5db'/%3E%3C/svg%3E";
    
    $imagePath = !empty($product['gambar']) ? '../../uploads/products/' . htmlspecialchars($product['gambar']) : $svgPlaceholder;
    $productName = htmlspecialchars($product['nama_product']);
    $productId = $product['id_product'] ?? '';
    $description = htmlspecialchars(substr($product['deskripsi_speksifikasi'] ?? '', 0, 100));
    $stockBadge = $productHelper->getStockBadge($product['stok'], $product['status_produk']);
    
    $hasDiscount = !empty($product['has_discount']);
    $originalPrice = $productHelper->formatPrice($product['harga_asli'] ?? $product['harga']);
    $finalPrice = $productHelper->formatPrice($product['harga_final'] ?? $product['harga']);
    $discountBadge = htmlspecialchars($product['discount_badge'] ?? '');
    
    $disabledClass = !$stockBadge['available'] ? 'opacity-50 cursor-not-allowed' : '';
    $disabledAttr = !$stockBadge['available'] ? 'disabled' : '';
    
    $html = <<<HTML
    <li class="group bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col h-full overflow-hidden">
        <a href="productDetail.php?id={$productId}" class="block">
            <div class="relative aspect-square overflow-hidden">
                <img src="{$imagePath}" alt="{$productName}" class="w-full h-full object-cover" loading="lazy"
                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 400 400%27%3E%3Crect width=%27400%27 height=%27400%27 fill=%27%23f3f4f6%27/%3E%3Cpath d=%27M200 120c-44.18 0-80 35.82-80 80s35.82 80 80 80 80-35.82 80-80-35.82-80-80-80zm0 140c-33.14 0-60-26.86-60-60s26.86-60 60-60 60 26.86 60 60-26.86 60-60 60z%27 fill=%27%23d1d5db%27/%3E%3Cpath d=%27M200 160c-22.09 0-40 17.91-40 40s17.91 40 40 40 40-17.91 40-40-17.91-40-40-40z%27 fill=%27%23d1d5db%27/%3E%3C/svg%3E'" />
HTML;

    if ($hasDiscount) {
        $html .= <<<HTML
                <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-bold z-10">{$discountBadge}</span>
HTML;
    }

    if (!$stockBadge['available']) {
        $html .= <<<HTML
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Stok Habis</span>
                </div>
HTML;
    } else {
        $html .= <<<HTML
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                    <span class="text-white text-sm font-medium">Lihat Detail</span>
                </div>
HTML;
    }

    $html .= <<<HTML
            </div>
        </a>
        <div class="p-4 sm:p-5 flex flex-col justify-between flex-grow">
            <div class="flex-grow">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-primary font-bold text-lg">{$finalPrice}</p>
HTML;

    if ($hasDiscount) {
        $html .= <<<HTML
                    <p class="text-gray-400 text-sm line-through">{$originalPrice}</p>
HTML;
    }

    $html .= <<<HTML
                </div>
                <a href="productDetail.php?id={$productId}">
                    <h3 class="mt-1.5 text-sm sm:text-base font-semibold text-gray-900 line-clamp-2 group-hover:text-primary transition-colors">{$productName}</h3>
                </a>
HTML;

    if (!empty($description)) {
        $html .= <<<HTML
                <p class="mt-2 text-gray-500 text-xs sm:text-sm line-clamp-2">{$description}...</p>
HTML;
    }

    $html .= <<<HTML
            </div>
            <div class="mt-4 flex gap-2">
                <button class="w-10 flex-shrink-0 rounded-lg bg-gray-100 px-2.5 py-2.5 text-sm font-medium text-gray-700 transition-all hover:bg-red-50 hover:text-red-500 hover:shadow-sm"
                    onclick="event.preventDefault(); event.stopPropagation(); addToWishlist('{$productId}')"
                    data-wishlist-product="{$productId}">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <button class="w-10 flex-shrink-0 rounded-lg bg-gray-100 px-2.5 py-2.5 text-sm font-medium text-gray-600 transition-all hover:bg-gray-50 hover:border-[#882426] hover:text-[#882426] {$disabledClass}" {$disabledAttr}
                    onclick="event.preventDefault(); event.stopPropagation(); addToCart('{$productId}')">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </button>
                <button type="button" class="flex-1 rounded-lg bg-primary px-3 py-2.5 text-sm font-medium text-white transition-all hover:bg-[#A14646] hover:shadow-md {$disabledClass}" {$disabledAttr}
                    onclick="event.preventDefault(); event.stopPropagation(); buyNow('{$productId}')">
                    Beli Sekarang
                </button>
            </div>
        </div>
    </li>
HTML;

    return $html;
}
