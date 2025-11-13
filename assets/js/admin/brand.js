document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // FORM BRAND (Tambah & Edit)
    // ===================
    // diterapkan di BrandAdmin.php

    (function () {
        const openBtns = document.querySelectorAll(".openBrand");
        const editBtns = document.querySelectorAll(".editBrand");
        const modal = document.getElementById("form-brand");
        if (!modal) return; // kalau bukan di halaman brand, keluar

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtn = modal.querySelector(".cancel");

        const title = modal.querySelector("h2");
        const subtitle = modal.querySelector("p.text-sm");
        const submitBtn = modal.querySelector("button[type='submit']");
        const inputId = modal.querySelector("input[disabled]");
        const inputNama = modal.querySelectorAll("input[type='text']")[0];
        const inputWeb = modal.querySelectorAll("input[type='text']")[1];
        const textarea = modal.querySelector("textarea");
        const fileInput = modal.querySelector("#file-upload");

        // === Buka modal dengan animasi ===
        function openModal() {
            modal.classList.remove("hidden");
            requestAnimationFrame(() => {
                backdrop.classList.remove("opacity-0");
                modalCard.classList.remove("opacity-0", "scale-95", "translate-y-4");
            });
            document.documentElement.style.overflow = "hidden";
            document.body.style.overflow = "hidden";
        }

        // === Tutup modal dengan animasi ===
        function closeModal() {
            backdrop.classList.add("opacity-0");
            modalCard.classList.add("opacity-0", "scale-95", "translate-y-4");

            setTimeout(() => {
                modal.classList.add("hidden");
            }, 300);

            document.documentElement.style.overflow = "";
            document.body.style.overflow = "";
        }

        // === FORM TAMBAH ===
        openBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Tambah Brand";
                subtitle.textContent = "Masukkan detail mengenai brand ini";
                submitBtn.textContent = "Tambah";
                inputId.value = "";
                inputNama.value = "";
                inputWeb.value = "";
                textarea.value = "";
                fileInput.value = ""; // reset file input
            });
        });

        // === FORM EDIT ===
        editBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                openModal();
                title.textContent = "Edit Brand";
                subtitle.textContent = "Perbarui informasi brand";
                submitBtn.textContent = "Simpan";

                // Ambil data dari atribut tombol edit
                inputId.value = btn.getAttribute("data-id") || "";
                inputNama.value = btn.getAttribute("data-nama") || "";
                inputWeb.value = btn.getAttribute("data-web") || "";
                textarea.value = btn.getAttribute("data-desc") || "";

                // Jika kamu ingin menampilkan nama file sebelumnya di placeholder
                const fileName = btn.getAttribute("data-logo") || "";
                if (fileName) {
                    fileInput.setAttribute("data-previous", fileName);
                    fileInput.classList.add("border-blue-400");
                } else {
                    fileInput.removeAttribute("data-previous");
                    fileInput.classList.remove("border-blue-400");
                }
            });
        });

        // === Preview nama file saat user upload ===
        fileInput?.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                console.log("File dipilih:", file.name);
            } else {
                console.log("Tidak ada file dipilih");
            }
        });

        // === Tutup modal ===
        if (backdrop) backdrop.addEventListener("click", closeModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") closeModal();
        });
    })();
});
