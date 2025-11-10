
document.addEventListener("DOMContentLoaded", () => {    
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

    (function () {
        const openBtns = document.querySelectorAll(".openReview");
        const modal = document.getElementById("modal-review");
        if (!modal) return;

        const backdrop = modal.querySelector("#lb-backdrop");
        const modalCard = modal.querySelector("#modal-card");
        const closeBtns = modal.querySelectorAll(".cancel");

        const orderIdEl = modal.querySelector("#review-orderid");
        const produkEl = modal.querySelector("#review-produk");
        const userEl = modal.querySelector("#review-user");
        const ratingEl = modal.querySelector("#review-rating");
        const commentEl = modal.querySelector("#review-comment");

        const replyBtn = modal.querySelector("#reply-btn");
        const replyBox = modal.querySelector("#reply-box");
        const replyText = modal.querySelector("#reply-text");
        const sendReply = modal.querySelector("#send-reply");

        // --- fungsi open dan close modal
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
            replyBox.classList.add("hidden");
            replyText.value = "";
        }

        // --- tombol untuk membuka detail review
        openBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const orderId = btn.getAttribute("data-orderid");
                const produk = btn.getAttribute("data-produk");
                const user = btn.getAttribute("data-user");
                const rating = btn.getAttribute("data-rating");
                const comment = btn.getAttribute("data-comment");

                orderIdEl.textContent = orderId || "-";
                produkEl.textContent = produk || "-";
                userEl.textContent = user || "-";
                ratingEl.textContent = rating || "-";
                commentEl.textContent = comment || "-";

                openModal();
            });
        });

        // --- btn close nya
        closeBtns.forEach(btn => btn.addEventListener("click", closeModal));
        if (backdrop) backdrop.addEventListener("click", closeModal);
        document.addEventListener("keydown", e => {
            if (e.key === "Escape") closeModal();
        });

        // --- btn balas komentar
        replyBtn.addEventListener("click", () => {
            replyBox.classList.toggle("hidden");
            replyText.focus();
        });

        // --- tombol kirim balasan
        sendReply.addEventListener("click", () => {
            const reply = replyText.value.trim();
            if (!reply) {
                alert("Balasan tidak boleh kosong.");
                return;
            }

            console.log("Balasan terkirim:", reply);
            replyText.value = "";
            replyBox.classList.add("hidden");
        });
    })();

});


//Login toogle
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');

    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });

    // Sign In Toggle
    const toggleSigninPassword = document.getElementById("toggleSigninPassword");
    const signinPassword = document.getElementById("signinPassword");

    if (toggleSigninPassword && signinPassword) {
        toggleSigninPassword.addEventListener("click", function () {
            const type = signinPassword.getAttribute("type") === "password" ? "text" : "password";
            signinPassword.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
            this.classList.toggle("fa-eye");
        });
    }

    // Sign Up Toggle
    const toggleSignupPassword = document.getElementById("toggleSignupPassword");
    const signupPassword = document.getElementById("signupPassword");

    if (toggleSignupPassword && signupPassword) {
        toggleSignupPassword.addEventListener("click", function () {
            const type = signupPassword.getAttribute("type") === "password" ? "text" : "password";
            signupPassword.setAttribute("type", type);
            this.classList.toggle("fa-eye-slash");
            this.classList.toggle("fa-eye");
        });
    }
});