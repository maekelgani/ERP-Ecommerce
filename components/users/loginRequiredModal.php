<?php
$loginUrl = '../../view/login.php';
$registerUrl = '../../view/login.php';
?>
<div id="loginRequiredModal" class="fixed inset-0 z-[9999] hidden">
    <!-- Background overlay gelap - klik di sini akan close modal -->
    <div class="absolute inset-0 bg-black/50 transition-opacity duration-300"></div>

    <div class="absolute inset-0 flex items-center justify-center p-4" onclick="closeLoginModal()">
        <div id="loginModalContent" class="relative w-full max-w-md transform scale-95 opacity-0 transition-all duration-300 ease-out" onclick="event.stopPropagation()">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <button onclick="closeLoginModal()"
                    class="absolute top-4 right-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-800 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="bg-primary p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-3">
                        <span id="modalIcon" class="material-symbols-outlined text-white text-3xl">lock</span>
                    </div>
                    <h3 id="modalTitle" class="text-xl font-bold text-white">Login Diperlukan</h3>
                </div>

                <div class="p-6">
                    <div class="text-center mb-6">
                        <p id="modalMessage" class="text-gray-600 leading-relaxed">
                            Silakan masuk ke akun Anda untuk melanjutkan.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <a id="modalLoginBtn" href="<?= $loginUrl ?>"
                            class="flex items-center justify-center gap-2 w-full py-3.5 px-6 bg-primary text-white font-semibold rounded-xl hover:opacity-90 transition-all shadow-lg hover:shadow-xl">
                            <span class="material-symbols-outlined">login</span>
                            <span>Masuk Sekarang</span>
                        </a>

                        <div class="relative flex items-center justify-center my-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <span class="relative px-3 text-sm text-gray-400 bg-white">atau</span>
                        </div>

                        <a id="modalRegisterBtn" href="<?= $registerUrl ?>"
                            class="flex items-center justify-center gap-2 w-full py-3.5 px-6 bg-white border-2 border-primary text-primary font-semibold rounded-xl hover:bg-primary/5 transition-all">
                            <span class="material-symbols-outlined">person_add</span>
                            <span>Daftar Akun Baru</span>
                        </a>
                    </div>

                    <div class="mt-6 flex items-center justify-center gap-4 text-xs text-gray-400">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-green-500 text-sm">verified_user</span>
                            <span>Aman</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-blue-500 text-sm">speed</span>
                            <span>Cepat</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-orange-500 text-sm">support_agent</span>
                            <span>24/7 Support</span>
                        </div>
                    </div>

                    <button onclick="closeLoginModal()"
                        class="mt-4 w-full py-2 text-gray-500 hover:text-gray-700 text-sm transition-colors">
                        Lanjutkan sebagai tamu
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const loginModalMessages = {
        'cart': {
            icon: 'shopping_cart',
            title: 'Keranjang Belanja',
            message: 'Masuk untuk menambahkan produk ke keranjang dan menikmati pengalaman belanja yang lebih baik.'
        },
        'wishlist': {
            icon: 'favorite',
            title: 'Simpan ke Wishlist',
            message: 'Masuk untuk menyimpan produk favorit Anda dan dapatkan notifikasi saat ada promo menarik.'
        },
        'checkout': {
            icon: 'credit_card',
            title: 'Checkout',
            message: 'Masuk untuk melanjutkan proses checkout dan menyelesaikan pesanan Anda.'
        },
        'order': {
            icon: 'receipt_long',
            title: 'Pesanan Saya',
            message: 'Masuk untuk melihat riwayat dan melacak status pesanan Anda.'
        },
        'profile': {
            icon: 'account_circle',
            title: 'Profil Saya',
            message: 'Masuk untuk mengakses dan mengelola profil akun Anda.'
        },
        'default': {
            icon: 'lock',
            title: 'Login Diperlukan',
            message: 'Silakan masuk ke akun Anda untuk mengakses fitur ini.'
        }
    };

    function showLoginModal(type = 'default', redirectUrl = null) {
        const modal = document.getElementById('loginRequiredModal');
        const content = document.getElementById('loginModalContent');
        const config = loginModalMessages[type] || loginModalMessages['default'];

        document.getElementById('modalIcon').textContent = config.icon;
        document.getElementById('modalTitle').textContent = config.title;
        document.getElementById('modalMessage').textContent = config.message;

        const loginBtn = document.getElementById('modalLoginBtn');
        const registerBtn = document.getElementById('modalRegisterBtn');
        const redirect = redirectUrl || encodeURIComponent(window.location.href);

        loginBtn.href = `<?= $loginUrl ?>?redirect=${redirect}`;
        registerBtn.href = `<?= $registerUrl ?>?redirect=${redirect}`;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    }

    function closeLoginModal() {
        const modal = document.getElementById('loginRequiredModal');
        const content = document.getElementById('loginModalContent');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 200);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLoginModal();
        }
    });

    function requireLoginForAction(action, targetUrl = null) {
        const isLoggedIn = document.body.dataset.customerLoggedIn === 'true';

        if (isLoggedIn) {
            if (targetUrl) {
                window.location.href = targetUrl;
            }
            return true;
        }

        showLoginModal(action, targetUrl);
        return false;
    }
</script>