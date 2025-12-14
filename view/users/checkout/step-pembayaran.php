<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#882426] rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Metode Pembayaran</h2>
                <p class="text-sm text-gray-500">Pilih cara pembayaran yang nyaman untuk Anda</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="mb-6 p-4 bg-gray-50 rounded-xl" id="shippingSummaryPreview">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Dikirim ke:</p>
                        <p class="font-medium text-gray-900 text-sm" id="paymentPreviewNama">-</p>
                        <p class="text-xs text-gray-500" id="paymentPreviewAlamat">-</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Pengiriman:</p>
                        <p class="font-medium text-gray-900 text-sm" id="paymentPreviewCourier">-</p>
                        <p class="text-xs text-gray-500" id="paymentPreviewEstimasi">-</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-3">
                <button type="button" id="btnChangeShipping" class="text-[#882426] text-sm font-medium hover:underline">
                    Ubah
                </button>
            </div>
        </div>

        <div class="mb-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-amber-800 mb-1">Punya Kode Voucher?</label>
                    <div class="flex gap-2">
                        <input type="text" id="voucherCode" placeholder="Masukkan kode voucher"
                            class="flex-1 px-4 py-2 border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm">
                        <button type="button" id="btnApplyVoucher" class="px-4 py-2 bg-amber-500 text-white font-medium rounded-lg hover:bg-amber-600 transition-colors text-sm">
                            Terapkan
                        </button>
                    </div>
                    <p class="text-xs text-amber-700 mt-2" id="voucherMessage"></p>
                </div>
            </div>
        </div>

        <h3 class="text-base font-bold text-gray-900 mb-4">Pilih Metode Pembayaran</h3>

        <div class="space-y-4">
            <div class="payment-category">
                <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors payment-category-toggle" data-category="bank">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-900">Transfer Bank</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="payment-options hidden mt-3 space-y-2 pl-4" data-category="bank">
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="bca" class="sr-only" data-type="bank" data-name="Bank BCA">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-8 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">Bank BCA</p>
                                    <p class="text-xs text-gray-500">Virtual Account / Transfer Manual</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="mandiri" class="sr-only" data-type="bank" data-name="Bank Mandiri">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="h-8 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">Bank Mandiri</p>
                                    <p class="text-xs text-gray-500">Virtual Account / Transfer Manual</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="bni" class="sr-only" data-type="bank" data-name="Bank BNI">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/f/f0/Bank_Negara_Indonesia_logo_%282004%29.svg" alt="BNI" class="h-8 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">Bank BNI</p>
                                    <p class="text-xs text-gray-500">Virtual Account / Transfer Manual</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="bri" class="sr-only" data-type="bank" data-name="Bank BRI">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/68/BANK_BRI_logo.svg" alt="BRI" class="h-8 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">Bank BRI</p>
                                    <p class="text-xs text-gray-500">Virtual Account / Transfer Manual</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="payment-category">
                <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors payment-category-toggle" data-category="ewallet">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-900">E-Wallet</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="payment-options hidden mt-3 space-y-2 pl-4" data-category="ewallet">
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="gopay" class="sr-only" data-type="ewallet" data-name="GoPay">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="Gopay" class="h-4 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">GoPay</p>
                                    <p class="text-xs text-gray-500">Bayar langsung dari aplikasi Gojek</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="ovo" class="sr-only" data-type="ewallet" data-name="OVO">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Logo_ovo_purple.svg" alt="OVO" class="h-4 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">OVO</p>
                                    <p class="text-xs text-gray-500">Bayar dengan saldo OVO</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="dana" class="sr-only" data-type="ewallet" data-name="DANA">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" alt="DANA" class="h-4 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">DANA</p>
                                    <p class="text-xs text-gray-500">Bayar dengan saldo DANA</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="shopeepay" class="sr-only" data-type="ewallet" data-name="ShopeePay">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/0/0e/Shopee_logo.svg" alt="Shopeepay" class="h-12 w-12">
                                <div>
                                    <p class="font-bold text-gray-900">ShopeePay</p>
                                    <p class="text-xs text-gray-500">Bayar dengan saldo ShopeePay</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="payment-category">
                <button type="button" class="w-full flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors payment-category-toggle" data-category="qris">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-900">QRIS</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="payment-options hidden mt-3 space-y-2 pl-4" data-category="qris">
                    <label class="payment-card block relative cursor-pointer">
                        <input type="radio" name="payment_method" value="qris" class="sr-only" data-type="qris" data-name="QRIS">
                        <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                            <div class="flex items-center gap-4">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/QRIS_logo.svg" alt="QRIS" class="h-4 w-auto">
                                <div>
                                    <p class="font-bold text-gray-900">QRIS</p>
                                    <p class="text-xs text-gray-500">Scan QR dengan aplikasi e-wallet atau m-banking</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="payment-category hidden" id="codCategory">
                <label class="payment-card block relative cursor-pointer">
                    <input type="radio" name="payment_method" value="cod" class="sr-only" data-type="cod" data-name="Bayar di Tempat (COD)">
                    <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Bayar di Tempat (COD)</p>
                                <p class="text-xs text-gray-500">Bayar saat barang diterima (khusus area tertentu)</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" id="agreeTerms" class="w-5 h-5 mt-0.5 rounded border-gray-300 text-[#882426] focus:ring-[#882426]">
                <span class="text-sm text-gray-600">
                    Saya menyetujui <a href="termsConditions.php" target="_blank" class="text-[#882426] font-medium hover:underline">Syarat & Ketentuan</a>
                    dan <a href="privacyPolicy.php" target="_blank" class="text-[#882426] font-medium hover:underline">Kebijakan Privasi</a> yang berlaku.
                </span>
            </label>
        </div>
    </div>
</div>

<div class="flex justify-between mt-6">
    <button type="button" id="btnBackToStep2" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
        </svg>
        Kembali
    </button>
    <button type="button" id="btnPlaceOrder" class="inline-flex items-center gap-2 px-8 py-4 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none" disabled>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Bayar Sekarang
        <span id="btnPlaceOrderTotal" class="ml-2"></span>
    </button>
</div>

<style>
    .payment-card.selected>div {
        border-color: #882426;
        background: rgba(136, 36, 38, 0.05);
    }

    .payment-category-toggle.expanded svg:last-child {
        transform: rotate(180deg);
    }
</style>