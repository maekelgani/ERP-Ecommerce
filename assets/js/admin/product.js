document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // Halaman product
    // ===================
    // Untuk Buka form add product
    (function () {
        const modal = document.getElementById("form-product");
        if (!modal) return; // Jika bukan di halaman Product, langsung cabut
        
        // per btn an
        const openBtns = document.querySelectorAll(".openForm");
        const editBtns = document.querySelectorAll(".editForm");

        const backdrop = modal?.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal?.querySelector(".cancel");
        const title = modal.querySelector("h2");
        const subtitle = modal.querySelector("p.text-sm");
        const submitBtn = modal.querySelector("button[type='submit']"); // HATI-Hati pake button yang sama kayak tambah produk 

        const inputId = modal.querySelector("input[disabled]");
        const inputName = modal.querySelector("input[required]");
        const selCategory = modal.querySelector("select:nth-child(1)");
        const selBrand = modal.querySelector("select:nth-child(2)");
        const inputPrice = modal.querySelector("input[placeholder]");
        const inputStock = modal.querySelector("input[type='number']");

        function openModal() {
            modal.classList.remove("hidden");
            requestAnimationFrame(() => {
                backdrop.classList.remove("opacity-0");
                modalCard.classList.remove("opacity-0", "scale-95", "translate-y-4");
            });
            document.documentElement.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        }

        function closeModal() {
            backdrop.classList.add("opacity-0");
            modalCard.classList.add("opacity-0", "scale-95", "translate-y-4");

            setTimeout(() => {
                modal.classList.add("hidden");
            }, 300);
            document.documentElement.style.overflow = "";
            document.body.style.overflow = "";
        }

        // FORM TAMBAH PRODUK
        openBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Tambah Produk";
                subtitle.textContent = "Masukkan detail produk ke persediaan";
                submitBtn.textContent = "Tambah";
                inputId.value = "";
                inputName.value = "";
                selCategory.value = "";
                selBrand.value = "";
                inputPrice.value = "";
                inputStock.value = "";
            });
        });

        // openBtns.forEach((btn) => {
        //     btn.addEventListener("click", openModal);
        // });

        // editBtns.forEach((btn) => {
        //     btn.addEventListener("click", openModal);
        // });

        // FORM EDIT PRODUK
        editBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Ubah produk";
                subtitle.textContent = "Perbarui informasi produk";
                submitBtn.textContent = "Edit";
                inputId.value = "";
                inputName.value = "";
                selCategory.value = "";
                selBrand.value = "";
                inputPrice.value = "";
                inputStock.value = "";
            });
        });

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();

    // ===================
    // Halaman product
    // ===================
    // Product Img Opener
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

    // FUNGSI: Increment dan Decrement stok produk
    (function () {
        const plusButtons = document.querySelectorAll('.btn-plus');
        const minusButtons = document.querySelectorAll('.btn-minus');

        if (!plusButtons.length && !minusButtons.length) return;

        function changeStock(inputEl, delta) {
            if (!inputEl) return;
            let cur = parseInt(inputEl.value, 10) || 0;
            let next = cur + delta;
            if (next < 0) next = 0;
            inputEl.value = next;
        }

        plusButtons.forEach(btn => {
            btn.addEventListener('click', () => {
            const input = btn.closest('td')?.querySelector('.produkstok');
            changeStock(input, 1);
            });
        });

        minusButtons.forEach(btn => {
            btn.addEventListener('click', () => {
            const input = btn.closest('td')?.querySelector('.produkstok');
            changeStock(input, -1);
            });
        });
    })();

})