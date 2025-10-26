// CHART nya GATAU GW WAK
document.addEventListener("DOMContentLoaded", () => {    
    // Untuk Product, menampilkan Modal di Product Admin
    (function () {
        const triggers = document.querySelectorAll('.lightbox-trigger');
        const lightbox = document.getElementById('lightbox');
        const lbImage = document.getElementById('lb-image');
        const lbClose = document.getElementById('lb-close');
        const lbBackdrop = document.getElementById('lb-backdrop');

        function openLightbox(imgEl) {
        lbImage.src = imgEl.src;
        lbImage.alt = imgEl.alt || '';
        lightbox.classList.remove('hidden');

        requestAnimationFrame(() => {
            lbImage.classList.remove('opacity-0');
        });

        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', onKeyDown);
        }

        function closeLightbox() {
        lbImage.classList.add('opacity-0');
        setTimeout(() => {
            lightbox.classList.add('hidden');
            lbImage.src = '';
        }, 200);

        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKeyDown);
        }

        function onKeyDown(e) {
        if (e.key === 'Escape') closeLightbox();
        }

        triggers.forEach(img => {
        img.addEventListener('click', () => openLightbox(img));
        });

        lbBackdrop.addEventListener('click', closeLightbox);
        lbClose.addEventListener('click', closeLightbox);
    })();

    // buat Sidebar
            const sections = document.querySelectorAll('.dropdown-section');
            const sidebar = document.getElementById('sidebar');

            // === 1. Pulihkan status setiap dropdown ===
            sections.forEach(section => {
                const sectionName = section.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();
                const savedState = localStorage.getItem(`dropdown-${sectionName}`) === 'open';

                const menu = section.querySelector('.dropdown-menu');
                const arrow = section.querySelector('.arrow-icon');

                if (savedState) {
                    menu.classList.remove('hidden');
                    arrow.classList.add('rotate-180');
                } else {
                    menu.classList.add('hidden');
                    arrow.classList.remove('rotate-180');
                }
            });

            // === 2. Event buka/tutup ===
            sections.forEach(section => {
                const btn = section.querySelector('.toggle-btn');
                const menu = section.querySelector('.dropdown-menu');
                const arrow = section.querySelector('.arrow-icon');
                const sectionName = section.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();

                btn.addEventListener('click', () => {
                    const isOpen = !menu.classList.contains('hidden');

                    // Toggle dropdown yang diklik
                    menu.classList.toggle('hidden');
                    arrow.classList.toggle('rotate-180');

                    // Simpan statusnya
                    localStorage.setItem(`dropdown-${sectionName}`, menu.classList.contains('hidden') ? 'closed' : 'open');
                });
            });

            // === 3. Tandai menu aktif ===
            const menuItems = document.querySelectorAll('.menu-item');
            const activePage = localStorage.getItem('activeMenu');
            if (activePage) {
                menuItems.forEach(item => {
                    if (item.getAttribute('href') === activePage) {
                        item.classList.add('bg-gray-100', 'text-gray-800');
                    } else {
                        item.classList.remove('bg-gray-100', 'text-gray-800');
                    }
                });
            }

            // Simpan menu aktif saat diklik
            menuItems.forEach(item => {
                item.addEventListener('click', () => {
                    localStorage.setItem('activeMenu', item.getAttribute('href'));
                });
            });
            requestAnimationFrame(() => sidebar.classList.remove('invisible'));
});
