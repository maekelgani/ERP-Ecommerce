
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
    
});
