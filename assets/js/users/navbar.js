document.addEventListener('DOMContentLoaded', () => {

    // ===========================
    // Fungsi: HAMBURGER MENU (Users & Guest)
    // ===========================
    (function initHamburgerMenu() {
        document.querySelectorAll('header').forEach(header => {
            const menuBtn = header.querySelector('.toggle-menu');
            const mobileMenu = header.querySelector('#mobile-menu');

            if (!menuBtn || !mobileMenu) return;

            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('translate-y-0');
                mobileMenu.classList.toggle('-translate-y-5');
                menuBtn.classList.toggle('bg-gray-300');
            });

            // Klik di luar menutup menu
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                    menuBtn.classList.remove('bg-gray-300');
                }
            });
        });
    })();



    // ===========================
    // Fungsi: DROPDOWN ACCOUNT (khusus navbar users)
    // ===========================
    (function initAccountDropdown() {
        try {
            // cari tombol dengan ikon "account_circle"
            const allButtons = Array.from(document.querySelectorAll('header button'));
            const accountBtn = allButtons.find(btn => {
                return Array.from(btn.querySelectorAll('span.material-symbols-outlined'))
                    .some(span => span.textContent.trim() === 'account_circle');
            });

            const accountMenu = document.getElementById('userDropdown1');
            if (!accountBtn || !accountMenu) return;

            // toggle dropdown
            accountBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                accountMenu.classList.toggle('hidden');
            });

            // klik di luar menutup dropdown
            document.addEventListener('click', (e) => {
                if (!accountMenu.contains(e.target) && !accountBtn.contains(e.target)) {
                    accountMenu.classList.add('hidden');
                }
            });

            // tekan ESC untuk menutup
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    accountMenu.classList.add('hidden');
                }
            });

        } catch (err) {
            console.error('Account dropdown error:', err);
        }
    })();

});
