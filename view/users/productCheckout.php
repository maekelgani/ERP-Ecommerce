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
        <div class="mt-10 p-5 py-1 w-full md:px-8 lg:px-20">
            <h2 class="text-3xl font-bold mb-8 ">Checkout</h2>
            <form>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">

                        <!-- warpper card order Summary -->
                        <div class="rounded-lg bg-white shadow-sm p-6 sticky top-24">
                            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                            
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

                                <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary">Rp 694.000</span>
                                </div>
                            </div>

                            <button
                            type=""
                            id=""
                            class="bg-red-800 text-white w-full py-2 mt-4 font-semibold rounded-lg hover:bg-red-800/75"
                            >Proses Pemesanan</button>
                        </div>
                    </div>

                    <!-- FORM DATA DIRI ALAMAT DAN LAIN-LAIN -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Form Alamat -->
                        <div class="rounded-lg bg-white shadow-sm p-6">
                            <h2 class="text-xl font-bold mb-4">Alamat Tujuan</h2>
                            <!-- From Alamat -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                
                                <!-- nama lengkap -->
                                <div class="md:col-span-3">
                                    <label for="namaLengkap"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nama Lengkap</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                                <!-- alamat -->
                                <div class="md:col-span-3">
                                    <label for="Alamat"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Alamat</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                                <!-- Detail tambahan -->
                                <div class="md:col-span-3">
                                    <label for="detailAlamat"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Detail Tempat Tinggal <span class="text-gray-400 text-xs">(optional)</span></label>
                                    <input type="text" placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                                <!-- Kota -->
                                <div>
                                    <label for="Kota"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Kota</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                                <!-- Kecamatan -->
                                <div>
                                    <label for="kecamatan"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Kecamatan</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50" >
                                </div>
                                <!-- Kode Pos -->
                                <div>
                                    <label for="kdPos"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Kode Pos</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                                <!-- Nomor Telepon -->
                                <div class="md:col-span-3">
                                    <label for="noTlp"
                                    class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Nomor Telepon</label>
                                    <input type="text" required placeholder=""
                                    class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                </div>
                            </div>
                        </div>

                        <!-- Form Payment Method-->
                        <div class="rounded-lg bg-white shadow-sm p-6">
                            <h2 class="text-xl font-bold mb-4">Metode Pembayaran</h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium">Pilih metode Pembayaran</label>
                                    <select id="payment-type" class="flex h-10 items-center justify-between bg-gray-50 rounded-md border border-gray-300 px-3 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="" selected disabled>Choose payment method</option>
                                        <option value="credit-card">Credit Card</option>
                                        <option value="e-wallet">E-Wallet</option>
                                        <option value="bank-transfer">Bank Transfer</option>
                                    </select>

                                    <!-- Kalo pilihannya Credit Card -->
                                    <div id="credit-card" class="hidden space-y-4 pt-4 border-t border-gray-400 mt-5">
                                        <div>
                                            <label class="text-sm font-medium">Card Number</label>
                                            <input type="text" required
                                            class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-blue-500 text-sm bg-gray-50">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm font-medium">Expire Date</label>
                                                <input type="text" required
                                                class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-blue-500 text-sm bg-gray-50">
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium">CVV</label>
                                                <input type="text" required
                                                class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-blue-500 text-sm bg-gray-50">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium">Nama Pemilik Kartu</label>
                                            <input type="text" required
                                            class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-blue-500 text-sm bg-gray-50">
                                        </div>
                                    </div>

                                    <!-- Kalo Pilihannya E-Wallet -->
                                    <div id="e-wallet" class="hidden space-y-4 pt-4 border-t border-gray-400 mt-5">
                                    <label class="text-sm font-medium">Metode E-Wallet</label>
                                    <div class="space-y-2">
                                        <div>
                                            <input type="radio" name="ewallet" id="gopay" value="gopay">
                                            <label for="gopay" class="text-sm font-medium">Gopay</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="ewallet" id="dana" value="dana">
                                            <label for="dana" class="text-sm font-medium">Dana</label>
                                        </div>
                                        <div>
                                            <input type="radio" name="ewallet" id="ovo" value="ovo">
                                            <label for="ovo" class="text-sm font-medium">OVO</label>
                                        </div>
                                    </div>
                                    </div>

                                    <!-- Kalo Pilihannya Bank Transfer -->
                                    <div id="bank-transfer" class="hidden space-y-4 pt-4 border-t border-gray-400 mt-5">
                                        <label class="text-sm font-medium">Pilih Bank</label>
                                        <div class="space-y-2">
                                            <div>
                                                <input type="radio" name="bankTransfer" id="bca" value="bca">
                                                <label for="bca" class="text-sm font-medium">Bank BCA</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="bankTransfer" id="mandiri" value="mandiri">
                                                <label for="mandiri" class="text-sm font-medium">Bank Mandiri</label>
                                            </div>
                                            <div>
                                                <input type="radio" name="bankTransfer" id="bni" value="bni">
                                                <label for="bni" class="text-sm font-medium">Bank BNI</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="rounded-lg  bg-white shadow-sm p-6">
                            <h2 class="text-xl font-bold mb-4 text-card-foreground">Voucer Code</h2>
                            <div class="flex gap-2">
                                <input type="text"
                                class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                <button type="submit"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background h-10 px-4 py-2 bg-gray-50 outline-1 outline-gray-400 hover:bg-gray-200 transition-all duration-300 ">Apply</button>
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