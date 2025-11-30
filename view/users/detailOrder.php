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
            <!-- btn kembali -->
            <div class="mb-6">
                <a href="#" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Pesanan Saya
                </a>
            </div>
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- LEFT COLUMN: Order Info, Status, Items -->
                <div class="lg:w-2/3 space-y-6">
                    
                    <!-- Status Timeline -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative z-0">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Status Pesanan</h3>
                        
                        <!-- Stepper Container -->
                        <div class="relative flex items-center justify-between w-full">
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-0"></div>
                            <div id="progress-bar" class="absolute left-0 top-1/2 transform -translate-y-1/2 h-1 bg-green-500 transition-all duration-500 -z-0" style="width: 50%;"></div>

                            <!-- Step 1: Dikemas -->
                            <div class="relative z-10 flex flex-col items-center group">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold border-4 border-white shadow-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-gray-900">Dikemas</p>
                                <p class="text-[10px] text-gray-500">20 Okt 14:00</p>
                            </div>

                            <!-- Step 2: Dikirim -->
                            <div class="relative z-10 flex flex-col items-center group">
                                <div id="step-sent" class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold border-4 border-white shadow-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-gray-900">Dikirim</p>
                                <p class="text-[10px] text-gray-500">21 Okt 09:30</p>
                            </div>

                            <!-- Step 3: Selesai -->
                            <div class="relative z-10 flex flex-col items-center group">
                                <div id="step-done" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 font-bold border-4 border-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-gray-500">Selesai</p>
                            </div>
                        </div>
                    </div>

                    <!-- Product List -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between">
                            <h3 class="font-bold text-gray-900">Daftar Produk</h3>
                            <span class="text-sm text-gray-500">No. Pesanan: <span class="text-gray-900 font-medium">ORD-20231020-002</span></span>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Item 1 -->
                            <div class="flex gap-4">
                                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                    <img src="https://placehold.co/150x150/ec4899/ffffff?text=Keyboard" alt="Product" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 flex flex-col md:flex-row justify-between">
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-lg">Mechanical Keyboard RGB</h4>
                                        <p class="text-sm text-gray-500 mt-1">Varian: Red Switch | White</p>
                                        <p class="text-sm text-gray-500">Qty: 1</p>
                                    </div>
                                    <div class="mt-2 md:mt-0 text-right">
                                        <p class="font-bold text-gray-900 text-lg">Rp 2.500.000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Info -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Informasi Pengiriman
                        </h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Penerima</p>
                                <p class="font-medium text-gray-900">John Doe</p>
                                <p class="text-gray-600 text-sm mt-1">(+62) 812-3456-7890</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Alamat</p>
                                <p class="text-gray-600 text-sm leading-relaxed">
                                    Jl. Teknologi No. 12, Kawasan Digital Valley,<br>
                                    Jakarta Selatan, DKI Jakarta 12345
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Kurir</p>
                                <p class="font-medium text-gray-900">JNE - Reguler</p>
                                <p class="text-xs text-blue-600 mt-1 cursor-pointer hover:underline">Resi: JP1234567890</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Payment & Summary -->
                <div class="lg:w-1/3 space-y-6">
                    
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm sticky top-24">
                        <h3 class="font-bold text-gray-900 mb-4">Rincian Pembayaran</h3>
                        
                        <div class="space-y-3 pb-4 border-b border-gray-100">
                            <div class="flex justify-between text-gray-600 text-sm">
                                <span>Metode Pembayaran</span>
                                <span class="font-medium text-gray-900">BCA Virtual Account</span>
                            </div>
                            <div class="flex justify-between text-gray-600 text-sm">
                                <span>Waktu Pembayaran</span>
                                <span class="text-gray-900">20 Okt 2023, 13:45</span>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Harga (1 Barang)</span>
                                <span>Rp 2.500.000</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Total Ongkos Kirim</span>
                                <span>Rp 25.000</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Layanan</span>
                                <span>Rp 2.000</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200 flex justify-between items-center">
                                <span class="font-bold text-gray-900 text-lg">Total Belanja</span>
                                <span class="font-bold text-primary text-xl">Rp 2.527.000</span>
                            </div>
                        </div>

                        <!-- Action Buttons based on status -->
                        <div class="mt-8 space-y-3">
                            <button class="w-full bg-primary hover:bg-primary/75 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Lacak Paket
                            </button>
                            <button class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold py-3 rounded-lg transition duration-200">
                                Bantuan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
    <footer class="sticky top-0 z-10">
        <?php include '../../components/users/footer.php' ;?>
    </footer>

</body>
</html>