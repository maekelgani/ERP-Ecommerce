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
                            <!-- 3-. keamanan -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    lock
                                    </span>
                                <span>Keamanan</span>
                            </a>
                            <!-- 4. Notifikasi -->
                            <a href="" class="items-center px-4 py-3 text-neutral-600 rounded-lg font-semibold flex space-x-3">
                                    <span class="material-symbols-outlined">
                                    edit_notifications
                                    </span>
                                <span>Notifikasi</span>
                            </a>
                            <!-- 5. Preferensi -->
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

                </div>
            </div>
        </div>
    </main>
</body>
</html>