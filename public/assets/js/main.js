
document.addEventListener("DOMContentLoaded", () => {    
    // Untuk Product, menampilkan Modal gambar prodyct di Product Admin
    (function () {
        const triggers = document.querySelectorAll('.lightbox-trigger');
        const lightbox = document.getElementById('lightbox');
        const lbImage = document.getElementById('lb-image');
        const lbClose = document.getElementById('lb-close');
        const lbBackdrop = document.getElementById('lb-backdrop');
        // pengecekan jika gada variabel diatas
        if (!triggers.length || !lightbox || !lbImage || !lbClose || !lbBackdrop) return;


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

    // UNTUK HALAMAN WEBMANAGEMENT
    (function () {
        const tabBtns = document.querySelectorAll(".tab-btn");
        const tabContents = document.querySelectorAll(".tab-content");
        //pengecekan kalo aga variabel diatas
        if (!tabBtns.length || !tabContents.length) return;

        let activeTabIndex = parseInt(localStorage.getItem("activeTabIndex")) || 0;

        function setActiveTab(index) {

            tabBtns.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add("bg-white", "text-black");
                    btn.classList.remove("text-gray-400");
                } else {
                    btn.classList.remove("bg-white", "text-black");
                    btn.classList.add("text-gray-400");
                }
            });

            tabContents.forEach((content, i) => {
                content.classList.toggle("hidden", i !== index);
            });

            localStorage.setItem("activeTabIndex", index);
        }

        tabBtns.forEach((btn, index) => {
            btn.addEventListener("click", () => setActiveTab(index));
        });

        setActiveTab(activeTabIndex);
    })();

    (function () {
            const sections = document.querySelectorAll('.dropdown-section');
            const sidebar = document.getElementById('sidebar');
            if (!sections || !sidebar) return;

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

        // === 3. Tandai menu aktif berdasarkan URL saat ini ===
        const menuItems = document.querySelectorAll('.menu-item');
        const currentUrl = window.location.pathname;
        let foundActive = false;

        menuItems.forEach(item => {
            const itemUrl = item.getAttribute('href');

            if (itemUrl === currentUrl) {
                item.classList.add('bg-gray-100', 'text-gray-800');
                localStorage.setItem('activeMenu', itemUrl);
                foundActive = true;
            } else {
                item.classList.remove('bg-gray-100', 'text-gray-800');
            }
        });

        // Jika halaman sekarang bukan salah satu menu sidebar, hapus activeMenu
        if (!foundActive) {
            localStorage.removeItem('activeMenu');
        }

            requestAnimationFrame(() => sidebar.classList.remove('invisible'));
    })();
    
});
