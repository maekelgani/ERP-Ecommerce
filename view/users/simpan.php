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
        <?php include '../../components/users/navbarGuest.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full md:px-8 lg:px-20">
            <h2 class="text-3xl font-bold mb-8 ">Checkout</h2>
            <form>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-1">

                        <!-- warpper card order Summary -->
                        <div class="rounded-lg border bg-white shadow-sm p-6 sticky top-24">
                            <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                            
                            <!-- warpper barang-barang -->
                            <div class="space-y-4 mb-6">
                                <!-- barang nya disini -->
                                <div id="itemCheckout"
                                class="flex gap-3">
                                    <img alt="Gaming Laptop ROG Strix G15" 
                                    src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400"
                                    class="w-16 h-16 object-cover rounded">
                                    
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">ASUS ROG Strix GeForce RTX 3070 Ti OC Edition 8GB GDDR6X</p>
                                        <p class="text-sm text-gray-400">Jumlah: 1</p>
                                        <p class="text-sm font-bold text-blue-500">Rp 15.000.000</p>
                                    </div>
                                </div>
                                <!-- barang ke 2 -->
                                <div id="itemCheckout"
                                class="flex gap-3">
                                    <img alt="Gaming Laptop ROG Strix G15" 
                                    src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400"
                                    class="w-16 h-16 object-cover rounded">
                                    
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">ASUS ROG Strix GeForce RTX 3070 Ti OC Edition 8GB GDDR6X</p>
                                        <p class="text-sm text-gray-400">Jumlah: 1</p>
                                        <p class="text-sm font-bold text-blue-500">Rp 15.000.000</p>
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                            <!-- warpper detail harga -->
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-500">
                                    <span>Subtotal</span>
                                    <span>Rp 15.000.000</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Pajak (11%)</span>
                                    <span>Rp 1.650.000</span>
                                </div>
                                <div class="flex justify-between text-gray-500">
                                    <span>Pemgemasan</span>
                                    <span>Rp 5.000</span>
                                </div>

                                <div class="shrink-0 bg-gray-200 h-px w-full my-4"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-blue-500">Rp 16.655.000</span>
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
                        <div class="rounded-lg border bg-white shadow-sm p-6">
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
                        <div class="rounded-lg border bg-white shadow-sm p-6">
                            <h2 class="text-xl font-bold mb-4">Metode Pembayaran</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="" class="text-sm font-medium">Pilih metode Pembayaran</label>
                                    <button type="button" 
                                    role="combobox" aria-controls="radix-:r0:" aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed" data-placeholder="" 
                                    class="flex h-10 items-center justify-between bg-gray-50 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;&gt;span]:line-clamp-1 w-full" id="payment-type">
                                        <span style="pointer-events: none;">Choose payment method</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50" aria-hidden="true">
                                            <path d="m6 9 6 6 6-6"></path>
                                        </svg>
                                    </button>
                                    
                                    <select aria-hidden="true" tabindex="-1" 
                                    style="position: absolute; border: 0px; width: 1px; height: 1px; padding: 0px; margin: -1px; overflow: hidden; clip: rect(0px, 0px, 0px, 0px); white-space: nowrap; overflow-wrap: normal;">
                                        <option value="credit-card">Credit Card</option>
                                        <option value="e-wallet">E-Wallet</option>
                                        <option value="bank-transfer">Bank Transfer</option>
                                    </select>

                                    <!-- Kalo pilihannya Credit Card -->
                                    <div class="space-y-4 pt-4 border-t border-gray-400 mt-5">
                                        <!-- Nomor kartu -->
                                        <div>
                                            <label for="" class="text-sm font-medium">Card Number</label>
                                            <input type="text" required placeholder=""
                                            class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                        </div>
                                        <!-- Expire Date & CVV -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Expire Date -->
                                            <div>
                                                <label for="" class="text-sm font-medium">Expire Date</label>
                                                <input type="text" required placeholder=""
                                                class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                            </div>
                                            <div>
                                                <label for="" class="text-sm font-medium">CVV</label>
                                                <input type="text" required placeholder=""
                                                class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                            </div>
                                        </div>
                                        <div>
                                            <label for="" class="text-sm font-medium">Nama Pemilik Kartu</label>
                                            <input type="text" required placeholder=""
                                            class="w-full outline-1 outline-gray-400 shadow rounded-md px-1 py-1.5 focus:outline-red-nano focus:outline-2 text-sm bg-gray-50">
                                        </div>
                                    </div>

                                    <!-- Kalo Pilihannya E-Wallet -->
                                    <div class="space-y-4 pt-4 border-t border-gray-400 mt-5">
                                        <label class="text-sm font-medium">Metode E-Wallet</label>
                                        <div id="radioGroup" class="space-y-2">
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
                                    <div class="space-y-4 pt-4 border-t border-gray-400 mt-5">
                                        <label class="text-sm font-medium">Pilih Bank</label>
                                        <div id="radioGroup" class="space-y-2">
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
                        <div class="rounded-lg border bg-white shadow-sm p-6"></div>
                    </div>

                </div>

            </form>
            
        
        </div>
    </main>

    <footer>
        <?php include '../../components/users/footer.php' ;?>
    </footer>
</body>
</html>