<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order - Customer</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../../assets/img/Nanocomp.png">
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarUsers.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <div class="mb-8">
                <p class="text-4xl font-bold text-gray-900 mb-2">Pesanan Saya</p>
                <p class="text-gray-600">Kelola dan lacak semua pesanan Anda</p>
            </div>
            <!-- btn Section -->
            <div class="lg:flex-row mb-8 flex flex-col gap-6">
                <button type="submit" onclick="filterOrders('all')"  
                class="tab-btn hover:bg-primary transition-all duration-300 hover:shadow-lg px-6
                    py-3 bg-primary text-white font-semibold rounded-lg shadow-md">Semua Pesanan</button>

                <button type="submit" onclick="filterOrders('Dikemas')"  
                class="tab-btn hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Diproses</button>

                <button type="submit" onclick="filterOrders('Dikirim')"  
                class="tab-btn hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Dikirim</button>

                <button type="submit" onclick="filterOrders('Selesai')"  
                class="tab-btn hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Selesai</button>

                <button type="submit" onclick="filterOrders('Dibatalkan')"  
                class="tab-btn hover:bg-primary hover:text-white border border-gray-200
                    transition-all duration-300 px-6 py-3 bg-white/50 text-gray-700
                    font-semibold rounded-lg">Dibatalkan</button>
            </div>

            <!-- card 1. Order Selesai -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200
                overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-red-50 px-6 py-4 sm:flex-row sm:items-center justify-between items-start border-b
                border-gray-200 flex flex-col gap-3">
                <div class="items-center flex gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order ID</p>
                    <p class="font-bold text-gray-900">#ORD-2024-00156</p>
                </div>
                <div class="h-10 w-px bg-gray-300"></div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pemesanan</p>
                    <p class="font-semibold text-gray-900">15 Januari 2024</p>
                </div>
                </div>
                <div class="items-center flex gap-2">
                <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-semibold rounded-full
                    ">Selesai</span>
                </div>
            </div>
            <div class="p-6">
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover w-full md:w-32 h-32 rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Sepatu Sneakers Premium Sport Edition</p>
                    <p class="text-gray-600 mb-3">Warna: Hitam | Ukuran: 42</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">2</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 1.499.000</p>
                    </div>
                </div>
                </div>
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover w-full md:w-32 h-32 rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Tas Ransel Canvas Anti Air</p>
                    <p class="text-gray-600 mb-3">Warna: Navy Blue | Kapasitas: 25L</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">1</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 350.000</p>
                    </div>
                </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Total Harga</p>
                    <p class="font-semibold text-gray-900">Rp 1.849.000</p>
                </div>
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Ongkos Kirim</p>
                    <p class="font-semibold text-gray-900">Rp 25.000</p>
                </div>
                <div class="justify-between items-center mb-6 text-lg flex">
                    <p class="font-bold text-gray-900">Total Pembayaran</p>
                    <p class="font-bold text-red-600">Rp 1.874.000</p>
                </div>
                <div class="sm:flex-row flex flex-col gap-3">
                    <button type="submit" class="flex-1 hover:bg-primary/75 transition-all duration-300 px-6 py-3
                        bg-primary text-white font-semibold rounded-lg">Beli Lagi</button>
                    <button type="submit" class="flex-1 hover:bg-gray-100 border
                        border-gray-300 transition-all duration-300 px-6 py-3 bg-white/50
                        text-gray-700 font-semibold rounded-lg">Lihat Detail</button>
                </div>
                </div>
            </div>
            </div>

            <!-- card 2. Order Dikirim -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200
                overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-red-50 px-6 py-4 sm:flex-row sm:items-center justify-between items-start border-b
                border-gray-200 flex flex-col gap-3">
                <div class="items-center flex gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order ID</p>
                    <p class="font-bold text-gray-900">#ORD-2024-00155</p>
                </div>
                <div class="h-10 w-px bg-gray-300"></div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pemesanan</p>
                    <p class="font-semibold text-gray-900">12 Januari 2024</p>
                </div>
                </div>
                <div class="items-center flex gap-2">
                <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full
                    ">Dikirim</span>
                </div>
            </div>
            <div class="p-6">
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover w-full md:w-32 h-32 rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Smartwatch Fitness Tracker Pro</p>
                    <p class="text-gray-600 mb-3">Warna: Rose Gold | Garansi: 1 Tahun</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">1</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 2.199.000</p>
                    </div>
                </div>
                </div>
                <div class="bg-blue-50 rounded-lg mb-6 border border-blue-200 p-4">
                <div class="items-start flex gap-3">
                    <div class="mt-1">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20" id="Windframe_hrxCU67pZ">
                                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path>
                                            </svg>
                    </div>
                    <div class="flex-1">
                    <p class="font-semibold text-blue-900 mb-1">Paket Dalam Pengiriman</p>
                    <p class="text-sm text-blue-700">Paket Anda sedang dalam perjalanan. Estimasi sampai: 14
                        Januari 2024</p>
                    <p class="text-sm text-blue-700 mt-2">
                        Nomor Resi:
                        <span class="font-semibold">JNE123456789012</span>
                    </p>
                    </div>
                </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Total Harga</p>
                    <p class="font-semibold text-gray-900">Rp 2.199.000</p>
                </div>
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Ongkos Kirim</p>
                    <p class="font-semibold text-gray-900">Rp 15.000</p>
                </div>
                <div class="justify-between items-center mb-6 text-lg flex">
                    <p class="font-bold text-gray-900">Total Pembayaran</p>
                    <p class="font-bold text-red-600">Rp 2.214.000</p>
                </div>
                <div class="sm:flex-row flex flex-col gap-3">
                    <button type="submit" class="flex-1 hover:bg-primary/75 transition-all duration-300 px-6 py-3
                        bg-primary text-white font-semibold rounded-lg">Lacak Pesanan</button>
                    <button type="submit" class="flex-1 hover:bg-gray-100 border
                        border-gray-300 transition-all duration-300 px-6 py-3 bg-white/50
                        text-gray-700 font-semibold rounded-lg">Hubungi Penjual</button>
                </div>
                </div>
            </div>
            </div>

            <!-- card 3. Order Diproses -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200
                overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-red-50 px-6 py-4 sm:flex-row sm:items-center justify-between items-start border-b
                border-gray-200 flex flex-col gap-3">
                <div class="items-center flex gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order ID</p>
                    <p class="font-bold text-gray-900">#ORD-2024-00154</p>
                </div>
                <div class="h-10 w-px bg-gray-300"></div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pemesanan</p>
                    <p class="font-semibold text-gray-900">10 Januari 2024</p>
                </div>
                </div>
                <div class="items-center flex gap-2">
                <span class="px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full
                    ">Diproses</span>
                </div>
            </div>
            <div class="p-6">
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover w-full md:w-32 h-32 rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Wireless Earbuds Premium Sound</p>
                    <p class="text-gray-600 mb-3">Warna: Putih | Bluetooth 5.0 | Noise Cancelling</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">1</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 899.000</p>
                    </div>
                </div>
                </div>
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover w-full md:w-32 h-32 rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Kaos Polos Premium Cotton Combed</p>
                    <p class="text-gray-600 mb-3">Warna: Hitam | Ukuran: L | Bahan: 100% Cotton</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">3</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 225.000</p>
                    </div>
                </div>
                </div>
                <div class="bg-yellow-50 rounded-lg mb-6 border border-yellow-200
                    p-4">
                <div class="items-start flex gap-3">
                    <div class="mt-1">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" id="Windframe_wlAgqagUF">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                    </div>
                    <div class="flex-1">
                    <p class="font-semibold text-yellow-900 mb-1">Pesanan Sedang Diproses</p>
                    <p class="text-sm text-yellow-700">Penjual sedang menyiapkan pesanan Anda. Estimasi
                        pengiriman: 11 Januari 2024</p>
                    </div>
                </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Total Harga</p>
                    <p class="font-semibold text-gray-900">Rp 1.124.000</p>
                </div>
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Ongkos Kirim</p>
                    <p class="font-semibold text-gray-900">Rp 20.000</p>
                </div>
                <div class="justify-between items-center mb-6 text-lg flex">
                    <p class="font-bold text-gray-900">Total Pembayaran</p>
                    <p class="font-bold text-red-600">Rp 1.144.000</p>
                </div>
                <div class="sm:flex-row flex flex-col gap-3">
                    <button type="submit" class="flex-1 hover:bg-gray-100 border
                        border-gray-300 transition-all duration-300 px-6 py-3 bg-white/50
                        text-gray-700 font-semibold rounded-lg">Lihat Detail</button>
                    <button type="submit" class="flex-1 hover:bg-red-50 border
                        border-red-300 transition-all duration-300 px-6 py-3 bg-white/50 
                        text-red-600 font-semibold rounded-lg">Batalkan Pesanan</button>
                </div>
                </div>
            </div>
            </div>

            <!-- card 4. Order Dibatalkan -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200
                overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="bg-red-50 px-6 py-4 sm:flex-row sm:items-center justify-between items-start border-b
                border-gray-200 flex flex-col gap-3">
                <div class="items-center flex gap-4">
                <div>
                    <p class="text-sm text-gray-600">Order ID</p>
                    <p class="font-bold text-gray-900">#ORD-2024-00153</p>
                </div>
                <div class="h-10 w-px bg-gray-300"></div>
                <div>
                    <p class="text-sm text-gray-600">Tanggal Pemesanan</p>
                    <p class="font-semibold text-gray-900">8 Januari 2024</p>
                </div>
                </div>
                <div class="items-center flex gap-2">
                <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-full
                ">Dibatalkan</span>
                </div>
            </div>
            <div class="p-6">
                <div class="md:flex-row mb-6 flex flex-col gap-6">
                <img alt="Product" src="https://placehold.co/120x120" class="object-cover opacity-60 w-full md:w-32 h-32
                    rounded-lg">
                <div class="flex-1">
                    <p class="text-xl font-bold text-gray-900 mb-2">Jaket Hoodie Fleece Winter Edition</p>
                    <p class="text-gray-600 mb-3">Warna: Abu-abu | Ukuran: XL</p>
                    <div class="items-center mb-3 flex gap-4">
                    <p class="text-gray-700">
                        Jumlah:
                        <span class="font-semibold">1</span>
                    </p>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <p class="text-lg font-bold text-red-600">Rp 450.000</p>
                    </div>
                </div>
                </div>
                <div class="bg-red-50 rounded-lg mb-6 border border-red-200 p-4">
                <div class="items-start flex gap-3">
                    <div class="mt-1">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20" id="Windframe_a5rSufI5m">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                    </div>
                    <div class="flex-1">
                    <p class="font-semibold text-red-900 mb-1">Pesanan Dibatalkan</p>
                    <p class="text-sm text-red-700">Pesanan dibatalkan oleh pembeli pada 8 Januari 2024. Dana
                        telah dikembalikan ke saldo Anda.</p>
                    </div>
                </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Total Harga</p>
                    <p strikethrough="" class="font-semibold text-gray-900">Rp 450.000</p>
                </div>
                <div class="justify-between items-center mb-3 flex">
                    <p class="text-gray-600">Ongkos Kirim</p>
                    <p strikethrough="" class="font-semibold text-gray-900">Rp 18.000</p>
                </div>
                <div class="justify-between items-center mb-6 text-lg flex">
                    <p class="font-bold text-gray-900">Total Pembayaran</p>
                    <p strikethrough="" class="font-bold text-gray-500">Rp 468.000</p>
                </div>
                <div class="sm:flex-row flex flex-col gap-3">
                    <button type="submit" class="flex-1 hover:bg-primary/75 transition-all duration-300 px-6 py-3
                        bg-primary  text-white font-semibold rounded-lg">Beli Lagi</button>
                    <button type="submit" class="flex-1 hover:bg-gray-100 border
                        border-gray-300 transition-all duration-300 px-6 py-3 bg-white/50
                        text-gray-700 font-semibold rounded-lg">Lihat Detail</button>
                </div>
                </div>
            </div>
            </div>

        </div>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>
</body>
</html>