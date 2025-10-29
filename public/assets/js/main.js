
document.addEventListener("DOMContentLoaded", () => {    

    // ===================
    // Pembuka gambar halaman product
    // ===================
    // Untuk Product, menampilkan Modal gambar prodyct di Product Admin ketika di klik gambarnya
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
    // Sidebar
    // ===================
    // 
    (function () {
        const dropdownSections = document.querySelectorAll('.dropdown-section');
        const sidebar = document.getElementById('sidebar');
        if (!sidebar || dropdownSections.length === 0) return;

        // === 1. mulihin status setiap dropdown ===
        dropdownSections.forEach(dropdownSections => {
            const sectionName = dropdownSections.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();
            const savedState = localStorage.getItem(`dropdown-${sectionName}`) === 'open';
            const menu = dropdownSections.querySelector('.dropdown-menu');
            const arrow = dropdownSections.querySelector('.arrow-icon');
            if (savedState) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        });
        // === 2. Event buka/tutup ===
        dropdownSections.forEach(dropdownSections => {
            const btn = dropdownSections.querySelector('.toggle-btn');
            const menu = dropdownSections.querySelector('.dropdown-menu');
            const arrow = dropdownSections.querySelector('.arrow-icon');
            const sectionName = dropdownSections.querySelector('.toggle-btn span:nth-child(2)').textContent.trim().toLowerCase();
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

    // ===================
    // Pembuka halaman Category
    // ===================
    // untuk form tambah atau edit category
    (function () {
        const openBtns = document.querySelectorAll(".openCategory");
        const editBtns = document.querySelectorAll(".editCategory");
        const modal = document.getElementById("form-category");
        if (!modal) return; // Keluar jika bukan di halaman Category

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal.querySelector(".cancel");

        const title = modal.querySelector("h2");
        const subtitle = modal.querySelector("p.text-sm");
        const submitBtn = modal.querySelector("button[type='submit']");
        const inputId = modal.querySelector("input[disabled]");
        const inputName = modal.querySelector("input[required]");
        const inputDesc = modal.querySelector("textarea");

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

        // FORM TAMBAH
        openBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Tambah Kategori";
                subtitle.textContent = "Masukkan detail mengenai kategori ini";
                submitBtn.textContent = "Tambah";
                inputId.value = "";
                inputName.value = "";
                inputDesc.value = "";
            });
        });

        // FORM EDIT
        editBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Edit Kategori";
                subtitle.textContent = "Perbarui detail kategori";
                submitBtn.textContent = "Simpan";

                // Ambil data dari attribute tombol
                inputId.value = btn.getAttribute("data-id") || "";
                inputName.value = btn.getAttribute("data-name") || "";
                inputDesc.value = btn.getAttribute("data-desc") || "";
            });
        });

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();


    // ===================
    // Pembuka LIHAT DETAIL DI INORDER DAN ORDER
    // ===================
    // untuk modal lihat detail dari Orderan
    (function () {
        const detailBtns = document.querySelectorAll(".detailOrder");
        const modal = document.getElementById("order-details");
        if (!modal) return;

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal.querySelector(".cancel");

        const elOrderId = modal.querySelector("#d-order-id");
        const elName = modal.querySelector("#d-name");
        const elAddress = modal.querySelector("#d-address");
        const elItems = modal.querySelector("#d-items");
        const elDate = modal.querySelector("#d-date");
        const elPayment = modal.querySelector("#d-payment");
        const elMethod = modal.querySelector("#d-method");
        const elStatus = modal.querySelector("#d-status");
        const elTotal = modal.querySelector("#d-total");

        // Elemen Progress Bar
        const stepPacked = modal.querySelector("#step-packed");
        const stepShipped = modal.querySelector("#step-shipped");
        const stepDone = modal.querySelector("#step-done");

        function resetProgress() {
            [stepPacked, stepShipped, stepDone].forEach(step => {
                step.classList.remove("bg-green-600");
                step.classList.add("bg-gray-300");
            });
        }

        function updateShippingProgress(status) {
            resetProgress();
            if (status === "dikemas") {
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
            } else if (status === "dikirim") {
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
                stepShipped.classList.replace("bg-gray-300", "bg-green-600");
            } else if (status === "selesai") {
                stepPacked.classList.replace("bg-gray-300", "bg-green-600");
                stepShipped.classList.replace("bg-gray-300", "bg-green-600");
                stepDone.classList.replace("bg-gray-300", "bg-green-600");
            }
        }

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
            setTimeout(() => modal.classList.add("hidden"), 300);
            document.documentElement.style.overflow = "";
            document.body.style.overflow = "";
        }

        detailBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();

                const itemsRaw = btn.getAttribute("data-items") || "";
                const itemsArr = itemsRaw.split(";").filter(item => item.trim());
                elItems.innerHTML = "";
                itemsArr.forEach((item) => {
                    const [name, qty, price] = item.split("|");
                    const li = document.createElement("li");
                    li.textContent = `${name} — ${qty} pcs — Rp ${price}`;
                    elItems.appendChild(li);
                });

                const status = btn.dataset.status;

                elOrderId.textContent = btn.dataset.orderId;
                elName.textContent = btn.dataset.name;
                elAddress.textContent = btn.dataset.address;
                elDate.textContent = btn.dataset.date;
                elPayment.textContent = btn.dataset.payment;
                elMethod.textContent = btn.dataset.method;
                elStatus.textContent = status;
                elTotal.textContent = btn.dataset.total;

                // Update progress bar berdasarkan status
                updateShippingProgress(status);
            });
        });

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();

    // ===================
    // Pembuka halaman Hak Akses ERP
    // ===================
    // untuk form tambah atau edit USERS
    (function () {
        const openBtns = document.querySelectorAll(".openUsers");
        const editBtns = document.querySelectorAll(".editUsers");
        const modal = document.getElementById("form-users");
        if (!modal) return;

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal.querySelector(".cancel");

        const title = modal.querySelector("h2");
        const subtitle = modal.querySelector("p.text-sm");
        const submitBtn = modal.querySelector("button[type='submit']");

        const inputId = modal.querySelector("input[disabled]");
        const inputName = modal.querySelector("input[required]");
        const inputEmail = modal.querySelector("input:nth-of-type(2)");
        const inputPhone = modal.querySelector("input:nth-of-type(3)");
        const selectRole = modal.querySelector("select:nth-of-type(1)");
        const selectStatus = modal.querySelector("select:nth-of-type(2)");

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
            setTimeout(() => modal.classList.add("hidden"), 300);
            document.documentElement.style.overflow = "";
            document.body.style.overflow = "";
        }

        // Fungsi Reset Form
        function resetForm() {
            inputId.value = "";
            inputName.value = "";
            inputEmail.value = "";
            inputPhone.value = "";
            selectRole.value = "";
            selectStatus.value = "";
        }

        // TAMBAH USERS
        openBtns.forEach((btn) =>
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Tambah Users";
                subtitle.textContent = "Masukkan detail data diri users";
                submitBtn.textContent = "Tambah";
                resetForm();
            })
        );

        // EDIT USERS
        editBtns.forEach((btn) =>
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Edit Users";
                subtitle.textContent = "Perbarui data pengguna";
                submitBtn.textContent = "Simpan";

                inputId.value = btn.getAttribute("data-id") || "";
                inputName.value = btn.getAttribute("data-name") || "";
                inputEmail.value = btn.getAttribute("data-email") || "";
                inputPhone.value = btn.getAttribute("data-phone") || "";
                selectRole.value = btn.getAttribute("data-role") || "";
                selectStatus.value = btn.getAttribute("data-status") || "";
            })
        );

        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        document.addEventListener("keydown", (e) => { 
            if (e.key === "Escape") closeModal();
        });
    })();


    // ===================
    // Tab switching halaman management WEB
    // ===================
    // Fungsi untuk mengubah opsi UMUM KONTAK dan SOSIAL MEDIA
    (function () {
        const tabBtns = document.querySelectorAll(".tab-btn-managementweb");
        const tabContents = document.querySelectorAll(".tab-content-managementweb");
        if (!tabBtns.length || !tabContents.length) return;

        let activeTabIndex = parseInt(localStorage.getItem("tab_management_index")) || 0;

        function setActiveTab(index) {
            tabBtns.forEach((btn, i) => {
                btn.classList.toggle("bg-white", i === index);
                btn.classList.toggle("text-black", i === index);
                btn.classList.toggle("text-gray-400", i !== index);
            });

            tabContents.forEach((content, i) => {
                content.classList.toggle("hidden", i !== index);
            });

            localStorage.setItem("tab_management_index", index);
        }

        tabBtns.forEach((btn, index) => {
            btn.addEventListener("click", () => setActiveTab(index));
        });

        setActiveTab(activeTabIndex);
    })();

    // ===================
    // Tab switching halaman analitik 
    // ===================
    // Fungsi untuk mengubah opsi di halaman analitik
    (function () {
        const tabBtns = document.querySelectorAll(".tab-btn-analitik");
        const tabContents = document.querySelectorAll(".tab-content-analitik");
        if (!tabBtns.length || !tabContents.length) return;

        let activeTabIndex = parseInt(localStorage.getItem("tab_analitik_index")) || 0;

        function setActiveTab(index) {
            tabBtns.forEach((btn, i) => {
                btn.classList.toggle("bg-white", i === index);
                btn.classList.toggle("text-black", i === index);
                btn.classList.toggle("text-gray-400", i !== index);
            });

            tabContents.forEach((content, i) => {
                content.classList.toggle("hidden", i !== index);
            });

            localStorage.setItem("tab_management_index", index);
        }

        tabBtns.forEach((btn, index) => {
            btn.addEventListener("click", () => setActiveTab(index));
        });

        setActiveTab(activeTabIndex);
    })();

});
