<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-amber-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#882426] rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Metode Pengiriman</h2>
                <p class="text-sm text-gray-500">Pilih metode pengiriman yang sesuai</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="mb-6 p-4 bg-gray-50 rounded-xl" id="selectedAddressPreview">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-[#882426]/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 mb-1">Alamat Pengiriman:</p>
                    <p class="font-medium text-gray-900" id="previewNamaPenerima">-</p>
                    <p class="text-sm text-gray-600" id="previewAlamatLengkap">-</p>
                    <p class="text-sm text-gray-500" id="previewWilayah">-</p>
                </div>
                <button type="button" id="btnChangeAddress" class="text-[#882426] text-sm font-medium hover:underline">
                    Ubah
                </button>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">Pilih Metode</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="shipping-method-card block relative cursor-pointer selected">
                    <input type="radio" name="shipping_method" value="delivery" class="sr-only" checked>
                    <div class="border-2 rounded-xl p-4 transition-all duration-200 border-[#882426] bg-[#882426]/5">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-[#882426] rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Kirim ke Alamat</p>
                                <p class="text-sm text-gray-500">Dikirim langsung ke alamat Anda</p>
                            </div>
                        </div>
                    </div>
                </label>

                <label class="shipping-method-card block relative cursor-pointer">
                    <input type="radio" name="shipping_method" value="pickup" class="sr-only">
                    <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-200 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">Ambil di Toko</p>
                                <p class="text-sm text-gray-500">Gratis, ambil sendiri di lokasi</p>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <div id="deliveryOptions">
            <h3 class="text-base font-bold text-gray-900 mb-4">Pilih Jasa Pengiriman</h3>

            <div id="courierLoading" class="hidden">
                <div class="flex items-center justify-center p-8 bg-gray-50 rounded-xl">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-10 h-10 text-[#882426] animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Mengambil ongkos kirim...</p>
                        <p class="text-sm text-gray-400">Mohon tunggu sebentar</p>
                    </div>
                </div>
            </div>

            <div id="courierError" class="hidden">
                <div class="p-6 bg-red-50 rounded-xl border border-red-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-red-800">Gagal memuat ongkos kirim</p>
                            <p class="text-sm text-red-600 mt-1" id="courierErrorMessage">Terjadi kesalahan saat mengambil data ongkir.</p>
                            <button type="button" id="btnRetryShipping" class="mt-3 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="courierWeightInfo" class="hidden mb-4">
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                        <p class="text-sm text-blue-700 font-medium" id="courierWeightText">Total berat: - kg</p>
                    </div>
                </div>
            </div>

            <div id="courierFallbackNotice" class="hidden mb-4">
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm text-amber-700">Menggunakan estimasi ongkir standar. Ongkir aktual akan dikonfirmasi saat pemrosesan pesanan.</span>
                    </div>
                </div>
            </div>

            <div class="space-y-3" id="courierList">
            </div>
        </div>

        <div id="pickupOptions" class="hidden">
            <h3 class="text-base font-bold text-gray-900 mb-4">Pilih Lokasi Toko</h3>
            <div class="space-y-3" id="storeList">
                <?php if (!empty($storeLocations)): ?>
                    <?php foreach ($storeLocations as $store): ?>
                        <label class="store-card block relative cursor-pointer">
                            <input type="radio" name="store_location" value="<?= htmlspecialchars($store['id_toko']) ?>" class="sr-only"
                                data-name="<?= htmlspecialchars($store['nama_toko']) ?>"
                                data-phone="<?= htmlspecialchars($store['no_telepon'] ?? '') ?>"
                                data-address="<?= htmlspecialchars($store['alamat']) ?>">
                            <div class="border-2 rounded-xl p-4 transition-all duration-200 border-gray-200 hover:border-[#882426]/50">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-[#882426]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-[#882426]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-800"><?= htmlspecialchars($store['nama_toko']) ?></p>
                                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($store['alamat']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($store['kota_kabupaten']) ?>, <?= htmlspecialchars($store['provinsi']) ?> <?= htmlspecialchars($store['kode_pos'] ?? '') ?></p>
                                        <?php if (!empty($store['no_telepon'])): ?>
                                            <p class="text-sm text-gray-500 mt-1">Telp: <?= htmlspecialchars($store['no_telepon']) ?></p>
                                        <?php endif; ?>
                                        <div class="flex items-center gap-4 mt-2">
                                            <?php if ($store['is_open']): ?>
                                                <span class="inline-flex items-center gap-1 text-xs text-green-600">
                                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                    Buka
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 text-xs text-red-600 animate-pulse">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                                    Tutup
                                                </span>
                                            <?php endif; ?>
                                            <span class="text-xs text-gray-500"><?= htmlspecialchars($store['jam_operasional']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="p-6 text-center bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-gray-500">Tidak ada lokasi toko tersedia saat ini.</p>
                        <p class="text-sm text-gray-400 mt-1">Silakan pilih metode pengiriman lainnya.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-blue-800">Packing Aman</h4>
                    <p class="text-sm text-blue-700 mt-1">Tambahkan perlindungan ekstra untuk paket Anda</p>

                    <div class="mt-3 space-y-3">
                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-blue-200 cursor-pointer hover:border-blue-400 transition-colors">
                            <input type="checkbox" name="bubble_wrap" value="5000" id="bubbleWrapCheckbox"
                                class="w-5 h-5 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-800">Bubble Wrap</span>
                                <p class="text-xs text-gray-500">Perlindungan dasar dari benturan</p>
                            </div>
                            <span class="text-sm font-bold text-blue-600">+Rp 5.000</span>
                        </label>

                        <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-blue-200 cursor-pointer hover:border-blue-400 transition-colors">
                            <input type="checkbox" name="packing_kayu" value="20000" id="packingKayuCheckbox"
                                class="w-5 h-5 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-800">Packing Kayu</span>
                                <p class="text-xs text-gray-500">Perlindungan maksimal untuk barang besar/berat</p>
                            </div>
                            <span class="text-sm font-bold text-blue-600">+Rp 20.000</span>
                        </label>
                    </div>

                    <p class="text-xs text-blue-600 mt-2">* Bisa dipilih keduanya untuk perlindungan maksimal</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="flex justify-between mt-6">
    <button type="button" id="btnBackToStep1" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
        </svg>
        Kembali
    </button>
    <button type="button" id="btnToStep3" class="inline-flex items-center gap-2 px-8 py-4 bg-[#882426] text-white font-semibold rounded-xl hover:bg-[#6a1c1e] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none" disabled>
        Pilih Pembayaran
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
    </button>
</div>

<style>
    .shipping-method-card.selected>div {
        border-color: #882426;
        background: rgba(136, 36, 38, 0.05);
    }

    .courier-card.selected>div {
        border-color: #882426;
        background: rgba(136, 36, 38, 0.05);
    }

    .store-card.selected>div {
        border-color: #882426;
        background: rgba(136, 36, 38, 0.05);
    }

    .shipping-method-card.selected .bg-gray-200 {
        background: #882426 !important;
    }

    .shipping-method-card.selected .text-gray-600 {
        color: white !important;
    }

    .courier-card.selected .bg-gray-200 {
        background: #882426 !important;
    }

    .courier-card.selected .text-gray-600 {
        color: white !important;
    }

    .store-card.selected .bg-[#882426]\/10 {
        background: rgba(136, 36, 38, 0.1) !important;
    }
</style>