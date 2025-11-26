<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Customer</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../../assets/img/Nanocomp.png">
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarUsers.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <h2 class="text-3xl font-bold mb-8 ">Pembayaran</h2>
            <form>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">

                        <!-- warpper card order Summary -->
                        <div class="rounded-lg bg-white shadow-sm p-6 sticky top-24">
                            <h2 class="text-xl font-bold">Rincian Order</h2>
                            <h2 class="text-sm mb-4 text-gray-400">ID: #ORD-12491248</h2>
                            <!-- waarpper data-pelanggan/alamat tujuan -->
                            <div class="space-y-4 mb-6 text-sm">
                                <div>
                                    <h2 class="text-gray-400">Nama Pelanggan</h2>
                                    <p>Jojon Satoru</p>
                                </div>
                                <div>
                                    <h2 class="text-gray-400">No.telp</h2>
                                    <p>082432426350</p>
                                </div>
                                <div>
                                    <h2 class="text-gray-400">Alamat Tujuan</h2>
                                    <p>Jl. lurus terus blok b no.08 rt 005 rw 006, kel.Baru</p>
                                </div>
                                <div class="grid grid-cols-3 gap-8">
                                    <div>
                                        <h2 class="text-gray-400">Kota</h2>
                                        <p>Bekasi</p>
                                    </div>
                                    <div>
                                        <h2 class="text-gray-400">Kecamatan</h2>
                                        <p>Bekasi Bagian Pinggir</p>
                                    </div>
                                    <div>
                                        <h2 class="text-gray-400">Kode pos</h2>
                                        <p>123456</p>
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                            <!-- warpper barang-barang -->
                            <div class="space-y-4 mb-6">
                                <!-- barang nya disini -->
                                <div id="itemCheckout"
                                class="flex gap-3">
                                    <img alt="Gaming Laptop ROG Strix G15" 
                                    src="https://resource.logitechg.com/w_1600,c_limit,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/g305/2025-update/g305-lightspeed-mouse-profile-left-angle-black-gallery-4.png"
                                    class="w-16 h-16 object-cover rounded">
                                    
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">Logitech G304 Lightspeed Wireless Gaming Mouse</p>
                                        <p class="text-sm text-gray-400">Jumlah: 1</p>
                                        <p class="text-sm font-bold text-primary">Rp 499.000</p>
                                    </div>
                                </div>
                            </div>


                            <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                            <!-- warpper detail harga -->
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal</span>
                                    <span>Rp 499.000</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Pajak (11%)</span>
                                    <span>Rp 50.000</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Pemgemasan</span>
                                    <span>Rp 5.000</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Admin</span>
                                    <span>Rp 0</span>
                                </div>

                                <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">Rp 694.000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PEMBAYARAN ALAMAT DAN LAIN-LAIN -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Form Alamat -->
                        <div class="rounded-lg bg-white shadow-sm p-6">
                            <h2 class="text-xl font-bold">Proses Pembayaran</h2>
                            <p class="text-gray-400 text-sm mb-2">ID pembayaran: BYR0129124</p>
                            <div class="w-full h-px bg-gray-200 mb-8"></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="col-span-3">
                                    <h2 class="font-bold">Order ID</h2>
                                    <p class="text-gray-400 text-base mb-4">ORD-28324092348</p>
                                </div>

                                <div class="">
                                    <h2 class="font-bold">Metode Pembayaran</h2>
                                    <p class="text-gray-400 text-base mb-4">Bank Mandiri</p>
                                </div>
    
                                <div class="">
                                    <h2 class="font-bold">Status Pembayaran</h2>
                                    <p class="text-gray-400 text-base mb-4">Pending</p>
                                </div>
                            </div>
                            <div class="bg-gray-50/50 rounded-lg w-full h-auto justify-center items-center text-center p-5 py-10">
                                <div class="mb-10">
                                    <h2 class="text-base font-semibold mb-2">Kode Pembayaran/No. Virtual Account</h2>
                                    <div>
                                        <input type="text" value="08249124" id="vaPembayaran"
                                        class="border border-dashed border-gray-300 p-2 px-4 rounded-lg text-center">
                                        <button class="p-2 px-4 bg-primary text-white rounded-lg hover:bg-primary/75 transition-all duration-200 active:bg-primary/50 active:outline-1 active:outline-primary ">Salin</button>
                                    </div>
                                </div>
                                <div class="mb-10">
                                    <h2 class="text-sm mb-2 text-gray-800">Jumlah yang harus anda bayar</h2>
                                    <p class="text-3xl font-bold text-primary">Rp 694.000</p>
                                </div>

                                <div class="px-10 lg:px-25 mb-20">
                                    <div class="mb-4 border border-dashed p-4 border-gray-400 rounded-lg">
                                        <h2 class="mb-2 text-gray-800 text-sm">Dibatalkan dalam</h2>
                                        <p class="text-red-400">
                                            <span class="text-xl font-semibold text-primary">26 </span>Jam : 
                                            <span class="text-xl font-semibold text-primary">12 </span>Menit : 
                                            <span class="text-xl font-semibold text-primary">56 </span>Detik</p>
                                    </div>
                                    <span class="text-sm font-semibold text-info"
                                    >Pembayaran akan dibatalkan secara otomatis apabila dalam batas waktu di atas tidak dilakukan pembayaran</span>
                                </div>

                                <div class="flex flex-col gap-3 text-sm md:flex-row md:justify-between">
                                    
                                    <button
                                        class="bg-secondary p-2 px-5 rounded-lg text-primary outline outline-primary font-semibold w-full md:w-auto">
                                        Batalkan pembayaran
                                    </button>

                                    <button 
                                        class="bg-primary p-2 px-5 rounded-lg text-white font-semibold outline outline-primary w-full md:w-auto">
                                        Cek Pembayaran
                                    </button>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </form>
            
        
        </div>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>

    <script src="/assets/js/users/checkout.js" defer></script>
</body>
</html>