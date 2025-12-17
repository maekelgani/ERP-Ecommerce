<?php

use App\Auth\SessionManager;

$isLoggedIn = SessionManager::isCustomerLoggedIn();
$currentCustomer = SessionManager::getCurrentCustomer();

$userName = $isLoggedIn ? htmlspecialchars($currentCustomer['name'] ?? 'User') : 'Guest';
$userEmail = $isLoggedIn ? htmlspecialchars($currentCustomer['email'] ?? '') : '';
$userInitial = $isLoggedIn ? strtoupper(substr($userName, 0, 1)) : 'G';

$cartCount = 0;
$wishlistCount = 0;
$notificationCount = 0;
$profileImage = null;

if ($isLoggedIn && isset($currentCustomer['id_customer'])) {
    try {
        $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

        $stmtCart = $db->prepare("SELECT COUNT(*) as count FROM cart WHERE id_customer = :id");
        $stmtCart->execute([':id' => $currentCustomer['id_customer']]);
        $cartCount = (int)$stmtCart->fetch()['count'];

        $stmtWishlist = $db->prepare("SELECT COUNT(*) as count FROM wishlist WHERE id_customer = :id");
        $stmtWishlist->execute([':id' => $currentCustomer['id_customer']]);
        $wishlistCount = (int)$stmtWishlist->fetch()['count'];

        $stmtNotif = $db->prepare("SELECT COUNT(*) as count FROM notification WHERE id_customer = :id AND status_baca = 'belum_dibaca'");
        $stmtNotif->execute([':id' => $currentCustomer['id_customer']]);
        $notificationCount = (int)$stmtNotif->fetch()['count'];

        $stmtProfile = $db->prepare("SELECT profile_image FROM customers WHERE id_customer = :id");
        $stmtProfile->execute([':id' => $currentCustomer['id_customer']]);
        $profileData = $stmtProfile->fetch();
        $profileImage = $profileData['profile_image'] ?? null;
    } catch (Exception $e) {
        error_log('Navbar count error: ' . $e->getMessage());
    }
}
?>

<style>
    @keyframes pulse-scale {

        0%,
        100% {
            transform: scale(1);
        }

        25% {
            transform: scale(1.3);
        }

        50% {
            transform: scale(1);
        }

        75% {
            transform: scale(1.3);
        }
    }

    .animate-pulse-scale {
        animation: pulse-scale 0.6s ease-in-out;
    }
</style>

<nav class="fixed top-0 left-0 right-0 z-50 w-full" id="mainNavbar">
    <!-- Banner Promo - akan tersembunyi saat scroll -->
    <div id="promoBanner" class="hidden md:block bg-[#882426] w-full px-5 md:px-8 lg:px-20 overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 44px;">
        <div class="flex items-center justify-center gap-2 w-full py-2.5">
            <svg class="w-4 h-4 text-white/90 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <p class="text-sm text-white font-medium tracking-wide">
                Gratis ongkir untuk pembelian di atas <span class="font-bold">Rp 100.000</span>
                <a href="#" class="ml-2 inline-flex items-center gap-1 text-white font-semibold hover:text-white/80 transition-colors underline underline-offset-2 decoration-white/50 hover:decoration-white">
                    Lihat Syarat
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </p>
        </div>
    </div>

    <div class="bg-white border-b border-gray-100 shadow-sm transition-all duration-300" id="navbarMain">
        <div class="w-full px-5 md:px-8 lg:px-20 flex justify-center">
            <div class="flex h-16 lg:h-[72px] items-center justify-between gap-4 w-full">

                <div class="flex-shrink-0">
                    <a href="../../view/users/landingPage.php" class="flex items-center gap-3 group">
                        <div class="relative">
                            <div class="absolute inset-0 bg-[#882426]/20 rounded-xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <img src="../../assets/img/logo-nano.png" alt="Nano Komputer" class="relative h-10 w-10 lg:h-11 lg:w-11 object-contain transform group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <div class="hidden sm:flex flex-col">
                            <span class="font-bold text-gray-900 text-base lg:text-lg group-hover:text-[#882426] transition-colors duration-200">Nano Komputer</span>
                            <span class="text-[10px] text-gray-500 font-medium tracking-wider uppercase">Computer Store</span>
                        </div>
                    </a>
                </div>

                <form class="hidden lg:flex flex-1 mx-6" action="../../view/users/productCollection.php" method="GET">
                    <div class="relative w-full group">
                        <div class="absolute inset-0 bg-[#882426]/5 rounded-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-300"></div>
                        <input type="text" name="search" placeholder="Cari produk..."
                            class="relative w-full h-10 pl-10 pr-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-[#882426]/50 focus:ring-2 focus:ring-[#882426]/10 transition-all" />
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#882426] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 hidden xl:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-medium text-gray-400 bg-gray-100 rounded border border-gray-200">
                            <span>Ctrl</span>
                            <span>K</span>
                        </kbd>
                    </div>
                </form>

                <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                    <button type="button" id="mobileSearchBtn" class="lg:hidden p-2 text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    <?php if ($isLoggedIn): ?>
                        <a href="../../view/users/wishlist.php" class="flex relative p-2 text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-all group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="wishlist-count-badge absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 text-[10px] font-bold text-white bg-[#882426] rounded-full ring-2 ring-white <?= $wishlistCount > 0 ? '' : 'hidden' ?>"><?= $wishlistCount ?></span>
                        </a>

                        <a href="../../view/users/cart.php" class="relative p-2 text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-all group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="cart-count-badge absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 text-[10px] font-bold text-white bg-[#882426] rounded-full ring-2 ring-white <?= $cartCount > 0 ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                        </a>

                        <button type="button" id="notificationBtn" class="hidden md:flex relative p-2 text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-all group">
                            <svg class="w-5 h-5 group-hover:scale-110 group-hover:rotate-12 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <?php if ($notificationCount > 0): ?>
                                <span class="absolute -top-1 -right-1 flex items-center justify-center h-5 w-5 text-[10px] font-bold text-white bg-[#882426] rounded-full ring-2 ring-white"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </button>

                        <div class="hidden md:flex items-center px-2 sm:px-3">
                            <div class="w-px h-8 bg-gray-200"></div>
                        </div>

                        <div class="hidden md:block relative" id="accountDropdown">
                            <button type="button" id="accountBtn" class="flex items-center gap-2 px-2 sm:px-3 py-2 text-gray-700 hover:bg-[#882426]/5 rounded-xl transition-all duration-200 group">
                                <div class="relative" id="navbarProfileContainer" data-initial="<?= $userInitial ?>">
                                    <?php if ($profileImage): ?>
                                        <img id="navbarProfilePhoto" src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover shadow-sm" data-profile-photo="navbar">
                                    <?php else: ?>
                                        <div id="navbarProfileInitial" class="w-8 h-8 rounded-full bg-[#882426] flex items-center justify-center text-white text-sm font-semibold shadow-sm" data-profile-initial="navbar">
                                            <?= $userInitial ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                                </div>
                                <div class="hidden lg:flex flex-col items-start">
                                    <span class="text-sm font-medium text-gray-900 group-hover:text-[#882426] transition-colors truncate max-w-[100px]"><?= $userName ?></span>
                                    <span class="text-[10px] text-gray-500">Cutomer</span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" id="accountChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div id="accountMenu" class="absolute right-0 mt-2 w-64 bg-[#882426] rounded-2xl shadow-2xl overflow-hidden transform scale-95 opacity-0 invisible transition-all duration-200 origin-top-right">
                                <div class="px-5 py-4 bg-[#882426]">
                                    <div class="flex items-center gap-3" id="dropdownProfileContainer" data-initial="<?= $userInitial ?>">
                                        <?php if ($profileImage): ?>
                                            <img id="dropdownProfilePhoto" src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/30" data-profile-photo="dropdown">
                                        <?php else: ?>
                                            <div id="dropdownProfileInitial" class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center text-white text-lg font-bold ring-2 ring-white/30" data-profile-initial="dropdown">
                                                <?= $userInitial ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="overflow-hidden">
                                            <p class="text-white font-semibold truncate"><?= $userName ?></p>
                                            <p class="text-white/70 text-xs truncate"><?= $userEmail ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white py-2">
                                    <a href="../../view/users/usersSetting.php" class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700 hover:bg-[#882426]/5 hover:text-[#882426] transition-all duration-200 group">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-medium">Profil Saya</span>
                                            <p class="text-[10px] text-gray-400">Kelola data pribadi</p>
                                        </div>
                                    </a>
                                    <a href="../../view/users/myOrder.php" class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700 hover:bg-[#882426]/5 hover:text-[#882426] transition-all duration-200 group">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-medium">Pesanan Saya</span>
                                            <p class="text-[10px] text-gray-400">Lacak & kelola pesanan</p>
                                        </div>
                                    </a>
                                    <a href="../../view/users/wishlist.php" class="flex items-center gap-3 px-5 py-3 text-sm text-gray-700 hover:bg-[#882426]/5 hover:text-[#882426] transition-all duration-200 group">
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-medium">Wishlist</span>
                                            <p class="text-[10px] text-gray-400">Produk favorit kamu</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="bg-white border-t border-gray-100 py-2 rounded-b-2xl">
                                    <button type="button" onclick="openNavbarLogoutModal()" class="w-full flex items-center gap-3 px-5 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-200 group">
                                        <div class="w-8 h-8 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                        <span class="font-medium">Keluar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="hidden md:flex items-center gap-2 sm:gap-3">
                            <a href="../../view/login.php" class="px-4 py-2 text-sm font-medium text-[#882426] hover:text-[#6a1c1e] hover:bg-[#882426]/5 rounded-lg transition-all duration-200">
                                Masuk
                            </a>
                            <a href="../../view/login.php#registerPanel" class="px-4 py-2.5 text-sm font-medium text-white bg-[#882426] hover:bg-[#6a1c1e] rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                Daftar
                            </a>
                        </div>
                    <?php endif; ?>

                    <button type="button" id="mobileMenuBtn" class="lg:hidden p-2 text-gray-600 hover:text-[#882426] hover:bg-[#882426]/5 rounded-lg transition-all" aria-label="Toggle Menu" aria-expanded="false">
                        <svg class="w-6 h-6 hamburger-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="w-6 h-6 close-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="hidden lg:block border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center justify-center gap-1 py-2">
                    <a href="../../view/users/categoryCollection.php" class="group relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#882426] rounded-lg transition-all duration-200">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Kategori
                        </span>
                        <span class="absolute inset-0 bg-[#882426]/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></span>
                    </a>
                    <a href="../../view/users/PromoPage.php" class="group relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#882426] rounded-lg transition-all duration-200">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Flash Sale</span>
                            <span class="px-2 py-0.5 text-[9px] font-bold text-white bg-[#882426] rounded-full animate-pulse whitespace-nowrap">HOT</span>
                        </span>
                        <span class="absolute inset-0 bg-[#882426]/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></span>
                    </a>
                    <a href="../../view/users/productCollection.php?sort=best" class="group relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#882426] rounded-lg transition-all duration-200">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Best Seller
                        </span>
                        <span class="absolute inset-0 bg-[#882426]/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></span>
                    </a>
                    <a href="../../view/users/productCollection.php?sort=newest" class="group relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#882426] rounded-lg transition-all duration-200">
                        <span class="relative z-10 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Produk Baru</span>
                            <span class="px-2 py-0.5 text-[9px] font-bold text-[#882426] bg-[#882426]/10 rounded-full animate-pulse whitespace-nowrap">NEW</span>
                        </span>
                        <span class="absolute inset-0 bg-[#882426]/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></span>
                    </a>
                    <a href="../../view/users/brandCollection.php" class="group relative px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#882426] rounded-lg transition-all duration-200">
                        <span class="relative z-10 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Brand Pilihan
                        </span>
                        <span class="absolute inset-0 bg-[#882426]/5 rounded-lg scale-0 group-hover:scale-100 transition-transform duration-200"></span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div id="mobileSearchOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300">
        <div class="absolute top-0 left-0 right-0 bg-white pt-4 px-4 pb-4 transform -translate-y-full transition-transform duration-300" id="mobileSearchPanel">
            <form class="flex items-center gap-2 navbar-search-form" action="../../view/users/productCollection.php" method="GET">
                <button type="submit" class="p-2 text-gray-400 hover:text-[#882426] transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <input type="text" name="search" placeholder="Cari produk..." autofocus
                    class="flex-1 px-4 py-3 bg-gray-50 border border-[#882426]/30 rounded-xl text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:bg-white focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/20 transition-all" />
                <button type="button" id="closeSearchBtn" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div id="mobileMenu" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm opacity-0 transition-opacity duration-300" id="mobileMenuBackdrop"></div>

        <div class="absolute top-0 right-0 w-full max-w-sm h-full bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-out overflow-y-auto" id="mobileMenuPanel">
            <div class="sticky top-0 z-10 bg-[#882426] px-5 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3" id="mobileMenuProfileContainer" data-initial="<?= $userInitial ?>">
                        <?php if ($isLoggedIn): ?>
                            <?php if ($profileImage): ?>
                                <img id="mobileMenuProfilePhoto" src="../../uploads/customers/<?= htmlspecialchars($profileImage) ?>" alt="Profile" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/30" data-profile-photo="mobile">
                            <?php else: ?>
                                <div id="mobileMenuProfileInitial" class="w-12 h-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-lg font-bold ring-2 ring-white/30" data-profile-initial="mobile">
                                    <?= $userInitial ?>
                                </div>
                            <?php endif; ?>
                            <div class="overflow-hidden">
                                <p class="text-white font-semibold truncate">Halo, <?= $userName ?>!</p>
                                <p class="text-white/70 text-sm">Selamat berbelanja</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur flex items-center justify-center ring-2 ring-white/30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Selamat Datang!</p>
                                <p class="text-white/70 text-sm">Masuk untuk pengalaman terbaik</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="closeMobileMenu" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <?php if (!$isLoggedIn): ?>
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex gap-3">
                        <a href="../../view/login.php" class="flex-1 py-3 text-center text-sm font-medium text-[#882426] border-2 border-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all">
                            Masuk
                        </a>
                        <a href="../../view/login.php?register=1" class="flex-1 py-3 text-center text-sm font-medium text-white bg-[#882426] hover:bg-[#6a1c1e] rounded-xl transition-all">
                            Daftar
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="px-5 py-4 border-b border-gray-100">
                <form class="flex items-center gap-2 navbar-search-form" action="../../view/users/productCollection.php" method="GET">
                    <button type="submit" class="p-2 text-gray-400 hover:text-[#882426] transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                    <input type="text" name="search" placeholder="Cari produk..."
                        class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#882426] focus:ring-2 focus:ring-[#882426]/10 transition-all" />
                </form>
            </div>

            <div class="px-5 py-4 space-y-1">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Menu Utama</p>
                <a href="../../view/users/categoryCollection.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <span class="font-medium">Kategori</span>
                    <svg class="w-5 h-5 ml-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="../../view/users/PromoPage.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-lg bg-red-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="font-medium">Flash Sale</span>
                    <span class="ml-auto px-2 py-0.5 text-[10px] font-bold text-white bg-[#882426] rounded-full animate-pulse">HOT</span>
                </a>
                <a href="../../view/users/productCollection.php?sort=best" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <span class="font-medium">Best Seller</span>
                </a>
                <a href="../../view/users/productCollection.php?sort=newest" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200 group">
                    <div class="w-9 h-9 rounded-lg bg-gray-100 group-hover:bg-[#882426]/10 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <span class="font-medium">Produk Baru</span>
                    <span class="ml-auto px-2 py-0.5 text-[10px] font-bold text-[#882426] bg-[#882426]/10 animate-pulse rounded-full">NEW</span>
                </a>
            </div>

            <?php if ($isLoggedIn): ?>
                <div class="mx-5 border-t border-gray-100"></div>

                <div class="px-5 py-4 space-y-1">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Akun Saya</p>
                    <a href="../../view/users/usersSetting.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium">Profil Saya</span>
                    </a>
                    <a href="../../view/users/myOrder.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="font-medium">Pesanan Saya</span>
                    </a>
                    <a href="../../view/users/cart.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="font-medium">Keranjang</span>
                        <span class="cart-count-badge ml-auto px-2 py-0.5 text-xs font-medium text-white bg-[#882426] rounded-full <?= $cartCount > 0 ? '' : 'hidden' ?>"><?= $cartCount ?></span>
                    </a>
                    <a href="../../view/users/wishlist.php" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:text-[#882426] hover:bg-[#882426]/5 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="font-medium">Wishlist</span>
                        <span class="wishlist-count-badge ml-auto px-2 py-0.5 text-xs font-medium text-[#882426] bg-[#882426]/10 rounded-full <?= $wishlistCount > 0 ? '' : 'hidden' ?>"><?= $wishlistCount ?></span>
                    </a>
                </div>

                <div class="px-5 py-4 mt-auto border-t border-gray-100">
                    <button type="button" onclick="openNavbarLogoutModal()" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-red-600 font-medium bg-red-50 hover:bg-red-100 rounded-xl transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php if ($isLoggedIn): ?>
    <div id="navbarLogoutModal" class="navbar-logout-modal-overlay">
        <div class="navbar-logout-modal-content">
            <div class="navbar-logout-modal-icon">
                <span class="material-symbols-outlined">logout</span>
            </div>
            <h2 class="navbar-logout-modal-title">Keluar dari Akun?</h2>
            <p class="navbar-logout-modal-description">Anda akan keluar dari akun Anda. Pastikan semua aktivitas Anda sudah selesai sebelum melanjutkan.</p>
            <div class="navbar-logout-modal-buttons">
                <button onclick="closeNavbarLogoutModal()" class="navbar-logout-modal-btn navbar-logout-modal-btn-cancel">
                    <span class="material-symbols-outlined">close</span>
                    Batal
                </button>
                <button onclick="confirmNavbarLogout()" id="confirmNavbarLogoutBtn" class="navbar-logout-modal-btn navbar-logout-modal-btn-logout">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar
                </button>
            </div>
        </div>
    </div>

    <style>
        .navbar-logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 1rem;
        }

        .navbar-logout-modal-overlay.show {
            display: flex;
            animation: navLogoutFadeIn 0.3s ease-out;
        }

        @keyframes navLogoutFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes navLogoutSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes navLogoutIconPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .navbar-logout-modal-content {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: navLogoutSlideUp 0.4s ease-out;
        }

        .navbar-logout-modal-icon {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: navLogoutIconPulse 2s ease-in-out infinite;
        }

        .navbar-logout-modal-icon .material-symbols-outlined {
            font-size: 2.5rem;
            color: #dc2626;
        }

        .navbar-logout-modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .navbar-logout-modal-description {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .navbar-logout-modal-buttons {
            display: flex;
            gap: 1rem;
        }

        .navbar-logout-modal-btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
        }

        .navbar-logout-modal-btn .material-symbols-outlined {
            font-size: 1.25rem;
        }

        .navbar-logout-modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .navbar-logout-modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .navbar-logout-modal-btn-logout {
            background: #dc2626;
            color: white;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        .navbar-logout-modal-btn-logout:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .navbar-logout-modal-btn:active {
            transform: scale(0.98);
        }
    </style>
<?php endif; ?>

<script>
    (function() {
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        const mobileMenuBackdrop = document.getElementById('mobileMenuBackdrop');
        const closeMobileMenu = document.getElementById('closeMobileMenu');
        const hamburgerIcon = mobileMenuBtn?.querySelector('.hamburger-icon');
        const closeIcon = mobileMenuBtn?.querySelector('.close-icon');

        function openMobileMenu() {
            mobileMenu.classList.remove('hidden');
            mobileMenuBtn.setAttribute('aria-expanded', 'true');
            if (hamburgerIcon) hamburgerIcon.classList.add('hidden');
            if (closeIcon) closeIcon.classList.remove('hidden');
            requestAnimationFrame(() => {
                mobileMenuBackdrop.classList.add('opacity-100');
                mobileMenuPanel.classList.remove('translate-x-full');
            });
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenuFn() {
            mobileMenuBackdrop.classList.remove('opacity-100');
            mobileMenuPanel.classList.add('translate-x-full');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            if (hamburgerIcon) hamburgerIcon.classList.remove('hidden');
            if (closeIcon) closeIcon.classList.add('hidden');
            document.body.style.overflow = '';
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);
        }

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                if (mobileMenu.classList.contains('hidden')) {
                    openMobileMenu();
                } else {
                    closeMobileMenuFn();
                }
            });
        }

        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', closeMobileMenuFn);
        }

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', closeMobileMenuFn);
        }

        const mobileSearchBtn = document.getElementById('mobileSearchBtn');
        const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
        const mobileSearchPanel = document.getElementById('mobileSearchPanel');
        const closeSearchBtn = document.getElementById('closeSearchBtn');

        function openSearch() {
            mobileSearchOverlay.classList.remove('hidden');
            requestAnimationFrame(() => {
                mobileSearchOverlay.classList.add('opacity-100');
                mobileSearchPanel.classList.remove('-translate-y-full');
            });
            document.body.style.overflow = 'hidden';
            mobileSearchPanel.querySelector('input').focus();
        }

        function closeSearch() {
            mobileSearchOverlay.classList.remove('opacity-100');
            mobileSearchPanel.classList.add('-translate-y-full');
            document.body.style.overflow = '';
            setTimeout(() => {
                mobileSearchOverlay.classList.add('hidden');
            }, 300);
        }

        if (mobileSearchBtn) {
            mobileSearchBtn.addEventListener('click', openSearch);
        }

        if (closeSearchBtn) {
            closeSearchBtn.addEventListener('click', closeSearch);
        }

        if (mobileSearchOverlay) {
            mobileSearchOverlay.addEventListener('click', (e) => {
                if (e.target === mobileSearchOverlay) {
                    closeSearch();
                }
            });
        }

        const accountBtn = document.getElementById('accountBtn');
        const accountMenu = document.getElementById('accountMenu');
        const accountChevron = document.getElementById('accountChevron');
        let isAccountOpen = false;

        function toggleAccountMenu() {
            isAccountOpen = !isAccountOpen;
            if (isAccountOpen) {
                accountMenu.classList.remove('scale-95', 'opacity-0', 'invisible');
                accountMenu.classList.add('scale-100', 'opacity-100', 'visible');
                if (accountChevron) accountChevron.classList.add('rotate-180');
            } else {
                accountMenu.classList.add('scale-95', 'opacity-0', 'invisible');
                accountMenu.classList.remove('scale-100', 'opacity-100', 'visible');
                if (accountChevron) accountChevron.classList.remove('rotate-180');
            }
        }

        if (accountBtn) {
            accountBtn.addEventListener('click', toggleAccountMenu);
        }

        document.addEventListener('click', (e) => {
            const accountDropdown = document.getElementById('accountDropdown');
            if (accountDropdown && !accountDropdown.contains(e.target) && isAccountOpen) {
                toggleAccountMenu();
            }
        });

        const navbarMain = document.getElementById('navbarMain');
        const promoBanner = document.getElementById('promoBanner');
        const navbarSpacer = document.getElementById('navbarSpacer');

        if (navbarMain) {
            let ticking = false;
            let lastScrollY = 0;

            const updateNavbarState = () => {
                const currentScroll = window.pageYOffset;

                if (currentScroll > 50) {
                    navbarMain.classList.add('shadow-lg');
                    navbarMain.classList.remove('shadow-sm');

                    if (promoBanner) {
                        promoBanner.style.maxHeight = '0px';
                        promoBanner.style.opacity = '0';
                    }
                    if (navbarSpacer) {
                        navbarSpacer.style.height = '112px';
                    }
                } else {
                    navbarMain.classList.remove('shadow-lg');
                    navbarMain.classList.add('shadow-sm');

                    if (promoBanner) {
                        promoBanner.style.maxHeight = '44px';
                        promoBanner.style.opacity = '1';
                    }
                    if (navbarSpacer) {
                        if (window.innerWidth >= 768) {
                            navbarSpacer.style.height = '156px';
                        } else {
                            navbarSpacer.style.height = '112px';
                        }
                    }
                }

                lastScrollY = currentScroll;
                ticking = false;
            };

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateNavbarState);
                    ticking = true;
                }
            });

            window.addEventListener('resize', () => {
                if (navbarSpacer) {
                    if (window.pageYOffset <= 50) {
                        if (window.innerWidth >= 768) {
                            navbarSpacer.style.height = '156px';
                        } else {
                            navbarSpacer.style.height = '112px';
                        }
                    }
                }
            });

            updateNavbarState();
        }

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const desktopSearch = document.querySelector('#navbarMain input[type="text"]');
                if (desktopSearch && window.innerWidth >= 1024) {
                    desktopSearch.focus();
                } else if (mobileSearchBtn && mobileSearchOverlay) {
                    openSearch();
                }
            }

            if (e.key === 'Escape') {
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    closeMobileMenuFn();
                }
                if (mobileSearchOverlay && !mobileSearchOverlay.classList.contains('hidden')) {
                    closeSearch();
                }
                if (isAccountOpen) {
                    toggleAccountMenu();
                }
            }
        });

        const searchForms = document.querySelectorAll('.navbar-search-form');
        searchForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = form.querySelector('input[type="text"]');
                if (input && input.value.trim()) {
                    window.location.href = '../../view/users/productCollection.php?search=' + encodeURIComponent(input.value.trim());
                }
            });
        });

        const navbarLogoutModal = document.getElementById('navbarLogoutModal');
        if (navbarLogoutModal) {
            navbarLogoutModal.addEventListener('click', function(e) {
                if (e.target === navbarLogoutModal) {
                    closeNavbarLogoutModal();
                }
            });
        }
    })();

    function openNavbarLogoutModal() {
        const modal = document.getElementById('navbarLogoutModal');
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeNavbarLogoutModal() {
        const modal = document.getElementById('navbarLogoutModal');
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    async function confirmNavbarLogout() {
        const confirmBtn = document.getElementById('confirmNavbarLogoutBtn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
        }

        try {
            const response = await fetch('../../api/auth/logout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect || '../../view/users/landingPage.php';
            } else {
                alert(result.message || 'Gagal logout');
                closeNavbarLogoutModal();
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<span class="material-symbols-outlined">logout</span> Keluar';
                }
            }
        } catch (error) {
            console.error('Logout error:', error);
            window.location.href = '../../view/logout.php';
            v
        }
    }
</script>