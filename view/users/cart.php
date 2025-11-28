<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Customer</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../../assets/img/Nanocomp.png">
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarUsers.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <h2 class="text-3xl font-bold ">Keranjang</h2>
            <p class="text-gray-600 mb-8">Review your items and proceed to checkout</p>
            
            <div class="lg:grid-cols-3 lg:gap-8 grid grid-cols-1 gap-6">
                <div class="lg:col-span-2 space-y-4">

                    <!-- product 1 -->
                    <div class="bg-white rounded-xl sm:p-6 shadow-sm border border-gray-200 
                        p-4 hover:shadow-md transition">
                        <div class="sm:flex-row flex flex-col gap-4">
                            <!-- gambar di sini -->
                            <div class="max-w-lg w-40 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img alt="Product" src="https://placehold.co/200x200/fb923c/ffffff?text=Product+1" class="object-cover w-full
                                    h-full">
                            </div>

                            <div class="flex-1 space-y-3">
                                <div class="items-start justify-between flex gap-4">
                                    <div>
                                        <p class="text-lg font-semibold text-gray-900">Premium Wireless Headphones</p>
                                        <p class="text-sm text-gray-600 mt-1 ">Color: Black | Size: Standard</p>
                                    </div>
                                    <button type="submit" class="hover:text-red-600 transition flex-shrink-0 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_vz5VBQx5r">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="items-center justify-between flex">
                                    <div class="items-center flex gap-3">
                                        <button type="submit" class="border border-gray-300 flex
                                            hover:bg-gray-100 transition w-8 h-8 rounded-lg items-center justify-center
                                            text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_dRX5y8aab">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="text-gray-900 font-medium w-8 text-center">1</span>
                                        <button type="submit" class="border border-gray-300 flex
                                            hover:bg-gray-100 transition w-8 h-8 rounded-lg items-center justify-center
                                            text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_Knumnrx2A">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-right">
                                    <p class="text-xl font-bold text-primary">Rp 124.353.535</p>
                                    </div>
                                </div>
                                <div class="items-center pt-2 flex gap-4 border-t border-gray-100 ">
                                    <button type="submit" class=" hover:text-red-600 transition flex
                                        gap-1 text-sm text-gray-600 items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_E16rNSRKq">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Save for later
                                    </button>
                                    <button type="submit" class=" hover:text-red-600 transition flex
                                        gap-1 text-sm text-gray-600 items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_nHTfDSXGj">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="bg-white rounded-xl sm:p-6 shadow-sm border border-gray-200  p-4
                        hover:shadow-md transition">
                        <div class="sm:flex-row flex flex-col gap-4">
                            <div class="max-w-lg w-40 h-32 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img alt="Product" src="https://placehold.co/200x200/fb923c/ffffff?text=Product+2" class="object-cover w-full
                                    h-full">
                            </div>
                            <div class="flex-1 space-y-3">
                                <div class="items-start justify-between flex gap-4">
                                    <div>
                                    <p class="text-lg font-semibold text-gray-900">Smart Watch Series 7</p>
                                    <p class="text-sm text-gray-600 mt-1 ">Color: Silver | Size: 42mm</p>
                                    </div>
                                    <button type="submit" class="hover:text-red-600 transition flex-shrink-0 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_JU8ifd61T">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    </button>
                                </div>
                                <div class="items-center justify-between flex">
                                    <div class="items-center flex gap-3">
                                    <button type="submit" class="border border-gray-300 flex
                                        hover:bg-gray-100 transition w-8 h-8 rounded-lg items-center justify-center
                                        text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_W9n0N8w1J">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="text-gray-900 font-medium w-8 text-center">2</span>
                                    <button type="submit" class="border border-gray-300 flex
                                        hover:bg-gray-100 transition w-8 h-8 rounded-lg items-center justify-center
                                        text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_PDFMLCg5J">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                    </div>
                                    <div class="text-right">
                                    <p class="text-xl font-bold text-primary">Rp 10.000.000</p>
                                    <p class="text-xs text-gray-500 ">Rp 5.000.000</p>
                                    </div>
                                </div>
                                <div class="items-center pt-2 flex gap-4 border-t border-gray-100 ">
                                    <button type="submit" class=" hover:text-red-600 transition flex
                                        gap-1 text-sm text-gray-600 items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_nt4g9y0rb">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                    Save for later
                                    </button>
                                    <button type="submit" class=" hover:text-red-600 transition flex
                                        gap-1 text-sm text-gray-600 items-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_0nw8Dg8Hg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 sticky top-24">
                    <p class="text-xl font-bold text-gray-900 mb-6">Rincian Keranjang</p>
                    <div class="mb-6 space-y-4">
                        <!-- Harga Di sini -->
                        <div class="justify-between text-gray-600 flex ">
                            <span>Subtotal (4 items)</span>
                            <span class="font-semibold text-gray-900">Rp 100.000</span>
                        </div>
                        <div class="justify-between text-gray-600 flex ">
                            <span>Pajak (11%)</span>
                            <span class="font-semibold text-gray-900">Rp 11.000</span>
                        </div>
                        <div class="pt-4 border-t border-gray-200 ">
                            <div class="justify-between items-center flex">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-red-600">Rp 111.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- action btn 
                    1. BTN CHECKOUT-->
                    <a href="/../../view/users/productCheckout.php" type="submit" 
                    class="bg-primary transition duration-200 flex space-x-2 w-full text-white font-semibold py-2 rounded-lg mb-3 items-center justify-center">
                        <span>Lanjut Checkout</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_R53B5VT6A">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <!-- 2. BTN Jelajah KATALOG -->
                    <a href="/../../view/users/productCollection.php" type="submit" class="bg-secondary text-primary outline outline-primary transition duration-200 flex space-x-2 w-full font-semibold py-2 rounded-lg mb-3 items-center justify-center">
                        Jelajah Katalog</a>

                    <!-- Keterangan yang ada dibawah Continue Shopping -->
                    <div class="space-y-3">
                        <div class="items-center text-sm text-gray-600 flex space-x-2 ">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20" id="Windframe_bcZtzeqd1">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Free shipping on orders over $50</span>
                        </div>
                        <div class="items-center text-sm text-gray-600 flex space-x-2 ">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20" id="Windframe_rbr3X0Tej">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>30-day money back guarantee</span>
                        </div>
                        <div class="items-center text-sm text-gray-600 flex space-x-2 ">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20" id="Windframe_dUYqxi0jU">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>Secure payment processing</span>
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200 ">
                        <p class="text-sm font-semibold text-gray-900 mb-3">Accepted Payment Methods</p>
                        <div class="items-center flex space-x-2">
                        <div class="w-12 h-8 bg-gray-200 items-center justify-center rounded flex">
                            <svg class="w-8 h-8" viewBox="0 0 48 48" fill="none" id="Windframe_mWvlcRdlZ">
                                <rect width="48" height="48" rx="4" fill="#1434CB"></rect>
                                <path d="M18 19L24 32L30 19H18Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="w-12 h-8 bg-gray-200 items-center justify-center rounded flex">
                            <svg class="w-8 h-8" viewBox="0 0 48 48" fill="none" id="Windframe_33Bv3YPGJ">
                                <rect width="48" height="48" rx="4" fill="#EB001B"></rect>
                                <circle cx="20" cy="24" r="10" fill="#FF5F00"></circle>
                                <circle cx="28" cy="24" r="10" fill="#F79E1B"></circle>
                            </svg>
                        </div>
                        <div class="w-12 h-8 bg-gray-200 items-center justify-center rounded flex">
                            <svg class="w-8 h-8" viewBox="0 0 48 48" fill="none" id="Windframe_WgieU2J2v">
                                <rect width="48" height="48" rx="4" fill="#00579F"></rect>
                                <text x="24" y="30" font-size="12" font-weight="bold" fill="white" text-anchor="middle">AMEX</text>
                            </svg>
                        </div>
                        <div class="w-12 h-8 bg-gray-200 items-center justify-center rounded flex">
                            <svg class="w-8 h-8" viewBox="0 0 48 48" fill="none" id="Windframe_WyhXVQzu6">
                                <rect width="48" height="48" rx="4" fill="#003087"></rect>
                                <text x="24" y="30" font-size="10" font-weight="bold" fill="white" text-anchor="middle">PayPal</text>
                            </svg>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>

    <script src="/assets/js/users/checkout.js" defer></script>
</body>
</html>