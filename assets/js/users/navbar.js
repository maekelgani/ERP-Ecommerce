// ======== HAMBURGER MENU TOGGLER ========
document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.querySelector('header button'); // tombol hamburger
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // mencegah bubbling ke document
            mobileMenu.classList.toggle('hidden');
            mobileMenu.classList.toggle('translate-y-0');
            mobileMenu.classList.toggle('-translate-y-5');
            menuBtn.classList.toggle('bg-gray-300'); // efek visual saat aktif
        });

        // Klik di luar menu → tutup menu
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                menuBtn.classList.remove('bg-gray-300');
            }
        });
    }
});
