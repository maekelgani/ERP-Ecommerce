<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Settings - Customer</title>
    <link rel="stylesheet" href="../../src/output.css">
    <link rel="icon" href="../../assets/img/Nanocomp.png">
</head>
<body class="w-full bg-no-repeat h-screen [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
    <header class="sticky top-0 z-10">
        <?php include '../../components/users/navbarUsers.php' ;?>
    </header>

    <main class="max-w-full mb-10">
        <div class="mt-10 p-5 py-1 w-full px-4 md:px-18 lg:px-30">
            <!-- card Warrper -->
            <div class="lg:grid-cols-12 grid grid-cols-1 gap-8">
                <!-- Menu di kiri -->
                <aside class="lg:col-span-3">
                    <div class="bg-white rounded-lg border border-neutral-200 p-6 sticky top-24 ">
                        <div class="items-center text-center mb-6 flex flex-col">
                            <div class="mb-4 relative">
                            <img alt="Profile" src="https://placehold.co/120x120/e5e5e5/6b7280?text=User" class="border-4
                                border-neutral-100 w-24 h-24 rounded-full">
                            <button type="submit" class="absolute bottom-0 right-0 flex hover:scale-110 transition w-8 h-8 bg-neutral-900
                                rounded-full items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_Gdgukzu5x">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                            </div>
                            <p class="text-lg font-bold text-neutral-900 ">Jojon Satoru</p>
                            <p class="text-sm text-neutral-500 ">jostar.12@gmail.com</p>
                            <div class="mt-4 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full
                                ">Gold Member</div>
                        </div>
                        <!-- Navbar Menu -->
                        <nav class="space-y-1">
                            <!-- 1. Informasi Users -->
                            <a href="" class="items-center px-4 py-3 bg-primary text-white rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    account_circle
                                    </span>
                                <span>Informasi Pengguna</span>
                            </a>
                            <!-- 2. Alamat -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    edit_location_alt
                                    </span>
                                <span>Alamat</span>
                            </a>
                            <!-- 3. Favorite -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                <span class="material-symbols-outlined">
                                favorite
                                </span>
                                <span>Favorite</span>
                            </a>
                            <!-- 4-. keamanan -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    lock
                                    </span>
                                <span>Keamanan</span>
                            </a>
                            <!-- 5. Notifikasi -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    edit_notifications
                                    </span>
                                <span>Notifikasi</span>
                            </a>
                            <!-- 6. Preferensi -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    settings
                                    </span>
                                <span>Preferensi</span>
                            </a>
                        </nav>

                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <button type="submit" class="flex space-x-2  hover:bg-red-50 transition
                                w-full items-center justify-center px-4 py-3 text-red-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="Windframe_cNTviY5Yd">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="font-semibold">Logout</span>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Bagian Kanan (content) -->
                <div class="lg:col-span-9 space-y-6">

                    <!-- 1. Card informasi Pengguna -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- header -->
                        <div class="px-6 py-5 border-b border-neutral-200">
                            <p class="text-xl font-bold text-neutral-900">Personal Information</p>
                            <p class="text-sm text-neutral-500 mt-1">Update your personal details and information</p>
                        </div>
                        <!-- isi kontent -->
                        <div class="p-6">
                            <div class="md:grid-cols-2 grid grid-cols-1 gap-6">
                                <!-- nama depan -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">Nama Depan</label>
                                    <input value="John" type="text" class="border border-neutral-300 focus:ring-2
                                        focus:ring-neutral-900 focus:border-transparent outline-none transition
                                        w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                </div>
                                <!-- nama Belakang -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">Nama Belakang</label>
                                    <input value="Doe" type="text" class="border border-neutral-300 focus:ring-2
                                        focus:ring-neutral-900 focus:border-transparent outline-none transition
                                        w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                </div>
                                <!-- email addres -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">Email Address</label>
                                    <input value="john.doe@email.com" type="email" class="border border-neutral-300 
                                        focus:ring-2 focus:ring-neutral-900 focus:border-transparent outline-none
                                        transition w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                </div>
                                <!-- No telepon -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">No. Telepon</label>
                                    <input value="+1 (555) 123-4567" type="tel" class="border border-neutral-300
                                        focus:ring-2 focus:ring-neutral-900 focus:border-transparent outline-none
                                        transition w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                </div>
                                <!-- ulang tahun -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">Tanggal Ulang Tahun</label>
                                    <input value="1990-01-15" type="date" class="border border-neutral-300 focus:ring-2
                                        focus:ring-neutral-900 focus:border-transparent outline-none transition
                                        w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                </div>
                                <!-- gender -->
                                <div>
                                    <label class="text-sm font-semibold text-neutral-700 mb-2 block">Gender</label>
                                    <select type="select-one" class="border border-neutral-300 focus:ring-2
                                        focus:ring-neutral-900 focus:border-transparent outline-none transition
                                        w-full px-4 py-3 bg-neutral-50 rounded-lg">
                                    <option>Laki-Laki</option>
                                    <option>Perempuan</option>
                                    <option>Other</option>
                                    <option>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Bio -->
                            <div class="mt-6">
                                <label class="text-sm font-semibold text-neutral-700 mb-2 block">Bio</label>
                                <textarea rows="4" type="textarea" placeholder="Tell us about yourself..." class="w-full px-4 py-3 bg-neutral-50 rounded-lg border border-neutral-300 focus:ring-2 focus:ring-neutral-900 focus:border-transparent outline-none transition resize-none"></textarea>
                            </div>
                            <!-- ACTION BTN -->
                            <div class="mt-6 justify-end flex space-x-3">
                                <button type="submit" class="border border-neutral-300 hover:bg-neutral-50 transition px-6 py-3 text-neutral-700 rounded-lg font-semibold">
                                    Cancel</button>
                                <button type="submit" class="hover:bg-primary/75 transition px-6 py-3 bg-primary text-white rounded-lg font-semibold">
                                    Save Changes</button>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Card Alamat -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- Header Card -->
                        <div class="px-6 py-5 items-center justify-between border-b border-neutral-200 flex">
                            <div>
                                <p class="text-xl font-bold text-neutral-900">Alamat Tersimpan</p>
                                <p class="text-sm text-neutral-500 mt-1">Atur alamat pengiriman anda</p>
                            </div>
                            <button type="submit" class="hover:bg-neutral-800 transition flex
                                space-x-2 px-4 py-2 bg-neutral-900 text-white rounded-lg font-semibold items-center">
                                <span class="material-symbols-outlined">
                                add
                                </span>
                                <span class="sm:inline hidden">Tambah Alamat</span>
                            </button>
                        </div>
                        <!-- Isi kontent -->
                        <div class="p-6">
                            <div class="md:grid-cols-2 grid grid-cols-1 gap-4">
                                <!-- Data Pertama (1) -->
                                <div class="rounded-xl border-2 border-neutral-900 p-5 relative">
                                    <!-- tag DEFAULT -->
                                    <div class="px-2 py-1 bg-neutral-900 text-white text-xs font-bold absolute top-4 right-4
                                        rounded">
                                        DEFAULT</div>
                                    <div class="items-start mb-4 flex space-x-3">
                                        <div class="w-10 h-10 bg-neutral-100 rounded-lg items-center justify-center flex
                                            flex-shrink-0">
                                            <span class="material-symbols-outlined">
                                            home
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-neutral-900 mb-1 ">Home</p> <!-- Judul nama tag alamat-->
                                            <p class="text-sm text-neutral-600">Jl. Perjuangan 10 no24, RT 01 / RW 05 </p> <!-- alamat lengkap (address) -->
                                            <p class="text-sm text-neutral-600">Marga Jiwa, Bekasi Ujung, Kota Bekasi, Jawa Barat, 17232</p> <!-- Kelurahan, Kecamatan, Kota, Provinsi, Kode pos -->
                                            <p class="text-sm text-neutral-600 mt-1">Phone: +1 (555) 123-4567</p> <!-- no Telp-->
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="submit" class="flex-1 border border-neutral-300 hover:bg-neutral-50 transition px-3 py-2 text-neutral-700 rounded-lg text-sm
                                            font-semibold">Edit</button>
                                        <button type="submit" class="flex-1 border border-neutral-300 hover:bg-neutral-50 transition px-3 py-2 text-neutral-700 rounded-lg text-sm
                                            font-semibold">Remove</button>
                                    </div>
                                </div>
                                <!-- Tambah Data -->
                                <div class="rounded-xl items-center justify-center text-center border border-dashed border-neutral-300
                                    p-5 flex flex-col min-h-[200px] hover:border-neutral-400
                                    hover:bg-neutral-50 transition cursor-pointer">
                                <div class="w-12 h-12 bg-neutral-100 rounded-xl items-center justify-center mb-3 flex">
                                    <span class="material-symbols-outlined">
                                    add
                                    </span>
                                </div>
                                    <p class="font-bold text-neutral-900 mb-1">Tambah Alamat</p>
                                    <p class="text-sm text-neutral-500">Tambah alamat pengiriman yang lain</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Favorite -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- Card Header -->
                        <div class="px-6 py-5 border-b border-neutral-200">
                            <p class="text-xl font-bold text-neutral-900">Daftar Produk Favorite</p>
                            <p class="text-sm text-neutral-500 mt-1">Kelola produk favorite anda</p>
                        </div>
                        <!-- isi Kontent -->
                        <div class="p-6">
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
                                                <button class="text-sm font-semibold mt-2 bg-primary border border-neutral-50 flex hover:bg-primary/50 transition w-full h-8 rounded-lg items-center justify-center text-white">
                                                    Beli Sekarang
                                                </button>
                                            </div>
                                        </div>
                                        <div class="items-center pt-2 flex gap-4 border-t border-gray-100 ">
                                            <button type="submit" class=" hover:text-red-600 transition flex
                                                gap-1 text-sm text-gray-600 items-center">
                                                <span class="material-symbols-outlined">
                                                shopping_cart_checkout
                                                </span>
                                            Add to Cart
                                            </button>
                                            <button type="submit" class=" hover:text-red-600 transition flex
                                                gap-1 text-sm text-gray-600 items-center">
                                                <span class="material-symbols-outlined">
                                                delete
                                                </span>
                                            Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. keamanan -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- Header -->
                        <div class="px-6 py-5 border-b border-neutral-200">
                            <p class="text-xl font-bold text-neutral-900">Pengaturan Keamanan</p>
                            <p class="text-sm text-neutral-500 mt-1">Lindungi dan jaga keamanan akun Anda</p>
                        </div>
                        <!-- isi kontent -->
                        <div class="p-6 space-y-6">
                            <!-- Pengaturan Password -->
                            <div class="items-center justify-between pb-6 flex border-b border-neutral-200">
                                <div class="items-start flex space-x-4">
                                    <div class="w-10 h-10 bg-neutral-100 rounded-lg items-center justify-center flex flex-shrink-0">
                                        <span class="material-symbols-outlined">
                                        key
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-neutral-900 mb-1">Password</p>
                                        <p class="text-sm text-neutral-600">Last changed 3 months ago</p>
                                    </div>
                                </div>
                                <button type="submit" class="border border-neutral-300 hover:bg-neutral-50 transition px-4 py-2 text-neutral-700 rounded-lg
                                    font-semibold">Change</button>
                            </div>
                            <!-- Pengaturan Two-Factor Auth -->
                            <div class="items-center justify-between pb-6 flex border-b border-neutral-200">
                                <div class="items-start flex space-x-4">
                                    <div class="w-10 h-10 bg-neutral-100 rounded-lg items-center justify-center flex
                                        flex-shrink-0">
                                        <span class="material-symbols-outlined">
                                        lock
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-neutral-900 mb-1">Two-Factor Authentication</p>
                                        <p class="text-sm text-neutral-600">Add an extra layer of security to your account</p>
                                    </div>
                                </div>
                                <label class="items-center relative inline-flex cursor-pointer">
                                <input checked="" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary
                                    "></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Notifikasi -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- Card header -->
                        <div class="px-6 py-5 border-b border-neutral-200">
                            <p class="text-xl font-bold text-neutral-900">Pengaturan Notifikasi</p>
                            <p class="text-sm text-neutral-500 mt-1">Choose what notifications you want to receive</p>
                        </div>
                        <!-- isi kontent -->
                        <div class="p-6">
                            <div class="space-y-6">
                                <!-- section 1. Notifikasi Pesanan -->
                                <div>
                                    <p class="font-bold text-neutral-900 mb-4 ">Notifikasi Pemesanan</p>
                                    <div class="space-y-4">
                                        <div class="items-center justify-between flex">
                                            <div>
                                                <p class="font-semibold text-neutral-800">Konfirmasi Pemesanan</p>
                                                <p class="text-sm text-neutral-500">Notifikasi jika anda melakukan pemesanan</p>
                                            </div>
                                            <label class="items-center relative inline-flex cursor-pointer">
                                                <input checked="" type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primary
                                                "></div>
                                            </label>
                                        </div>
                                        <div class="items-center justify-between flex">
                                            <div>
                                                <p class="font-semibold text-neutral-800">Status Pengiriman</p>
                                                <p class="text-sm text-neutral-500">Notifikasi saat pesanan anda sedang diantar</p>
                                            </div>
                                            <label class="items-center relative inline-flex cursor-pointer">
                                                <input checked="" type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primary
                                                "></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- section 2. Notifikasi promosi -->
                                <div class="pt-6 border-t border-neutral-200">
                                    <p class="font-bold text-neutral-900 mb-4">Notifikasi promosi</p>
                                    <div class="space-y-4">
                                        <div class="items-center justify-between flex">
                                            <div>
                                                <p class="font-semibold text-neutral-800">Penawaran Spesial</p>
                                                <p class="text-sm text-neutral-500">Notifikasi penawaran esklusif dan diskon</p>
                                            </div>
                                            <label class="items-center relative inline-flex cursor-pointer">
                                                <input checked="" type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primar"></div>
                                            </label>
                                        </div>
                                        <div class="items-center justify-between flex">
                                            <div>
                                                <p class="font-semibold text-neutral-800">Product Baru</p>
                                                <p class="text-sm text-neutral-500">Notifikasi produk terbaru dari NanoCom</p>
                                            </div>
                                            <label class="items-center relative inline-flex cursor-pointer">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primar"></div>
                                            </label>
                                        </div>
                                        <div class="items-center justify-between flex">
                                            <div>
                                                <p class="font-semibold text-neutral-800">Berita Terbaru</p>
                                                <p class="text-sm text-neutral-500">Weekly roundup of trends and tips</p>
                                            </div>
                                            <label class="items-center relative inline-flex cursor-pointer">
                                                <input checked="" type="checkbox" class="sr-only peer">
                                                <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primar"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Preferensi -->
                    <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden hidden">
                        <!-- Card Header -->
                        <div class="px-6 py-5 border-b border-neutral-200">
                            <p class="text-xl font-bold text-neutral-900">Account Actions</p>
                            <p class="text-sm text-neutral-500 mt-1">Manage your account settings and data</p>
                        </div>
                        <!-- isi kontent -->
                        <div class="p-6 space-y-4">
                            <div type="submit" class="flex hover:bg-neutral-100 transition group w-full items-center
                                justify-between px-5 py-4 rounded-xl">
                                <div class="items-center flex space-x-4">
                                    <div class="w-10 h-10 bg-white border border-neutral-100 rounded-lg items-center justify-center flex">
                                        <span class="material-symbols-outlined">
                                        dark_mode
                                        </span>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-neutral-900">Mode Gelap</p>
                                    </div>
                                </div>
                                <label class="items-center relative inline-flex cursor-pointer">
                                    <input checked="" type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-neutral-200 rounded-full peer-focus:outline-none peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute
                                    after:top-[2px] after:left-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-primary
                                    "></div>
                                </label>
                            </div>
                            <button type="submit" class="flex hover:bg-neutral-100 transition group w-full items-center
                                justify-between px-5 py-4 bg-neutral-50 rounded-xl">
                                <div class="items-center flex space-x-4">
                                    <div class="w-10 h-10 bg-white rounded-lg items-center justify-center flex">
                                        <span class="material-symbols-outlined">
                                        block
                                        </span>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-neutral-900">Deactivate Account</p>
                                        <p class="text-sm text-neutral-500">Temporarily disable your account</p>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined">
                                chevron_right
                                </span>
                            </button>
                            <button type="submit" class="flex hover:bg-red-100 transition group w-full items-center
                                justify-between px-5 py-4 bg-red-50 rounded-xl">
                                <div class="items-center flex space-x-4">
                                    <div class="w-10 h-10 bg-red-100 rounded-lg items-center justify-center flex text-red-600">
                                        <span class="material-symbols-outlined">
                                        delete
                                        </span>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-bold text-red-700 ">Delete Account</p>
                                        <p class="text-sm text-red-600">Permanently remove your account and data</p>
                                    </div>
                                </div>
                                <div class="text-red-500">
                                    <span class="material-symbols-outlined">
                                    chevron_right
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- BATAS AKHIR Bagian Kanan (content) -->
            </div>
        </div>
    </main>
    <script src="/../../assets/js/users/usersSetting.js" defer></script>
</body>
</html>