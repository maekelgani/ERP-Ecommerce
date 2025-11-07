document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // Pembuka halaman Category
    // ===================
    // untuk form tambah atau edit category
    // diterapkan di CategoryAdmin.php
    
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
})