document.addEventListener("DOMContentLoaded", () => {
    // ===================
    // Pembuka halaman Hak Akses ERP
    // ===================
    // untuk form tambah atau edit USERS
    // diterapkan untuk di halaman HakAksesAdmin.php
    
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
})