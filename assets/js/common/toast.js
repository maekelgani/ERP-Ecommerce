/**
 * Toast Notification System - Reusable Component
 * Digunakan di seluruh aplikasi untuk konsistensi alert/toast messages
 * Tipe: success, error, warning, info
 */

(function() {
    // Inisialisasi container jika belum ada
    function initToastContainer() {
        if (!document.getElementById('customToastContainer')) {
            const container = document.createElement('div');
            container.id = 'customToastContainer';
            document.body.appendChild(container);
        }
    }

    // Buat toast dengan styling profesional
    window.showCustomToast = function(message, type = 'success', title = null, duration = 4000) {
        initToastContainer();
        
        const container = document.getElementById('customToastContainer');
        const toast = document.createElement('div');
        toast.className = `custom-toast ${type}`;

        const icons = {
            success: 'check_circle',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        const titles = {
            success: 'Berhasil!',
            error: 'Gagal!',
            warning: 'Perhatian!',
            info: 'Informasi'
        };

        toast.innerHTML = `
            <div class="custom-toast-icon">
                <span class="material-symbols-outlined">${icons[type]}</span>
            </div>
            <div class="custom-toast-content">
                <div class="custom-toast-title">${title || titles[type]}</div>
                <div class="custom-toast-message">${message}</div>
            </div>
            <button class="custom-toast-close">
                <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
            </button>
            <div class="custom-toast-progress" style="animation-duration: ${duration}ms;"></div>
        `;

        const closeBtn = toast.querySelector('.custom-toast-close');
        closeBtn.addEventListener('click', () => removeToast(toast));

        container.appendChild(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });
        });

        const timeoutId = setTimeout(() => removeToast(toast), duration);
        toast.dataset.timeoutId = timeoutId;

        // Auto-remove progress bar elements if toast is already hiding
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class' && toast.classList.contains('hiding')) {
                    const progress = toast.querySelector('.custom-toast-progress');
                    if (progress) progress.style.display = 'none';
                }
            });
        });
        observer.observe(toast, { attributes: true });

        function removeToast(toastElement) {
            if (toastElement.classList.contains('hiding')) return;

            clearTimeout(parseInt(toastElement.dataset.timeoutId));
            toastElement.classList.add('hiding');
            toastElement.classList.remove('show');

            setTimeout(() => {
                if (toastElement.parentNode) {
                    toastElement.remove();
                }
            }, 500);
        }

        return toast;
    };

    // Shortcut methods untuk kemudahan penggunaan
    window.Toast = {
        success: (message, title = 'Berhasil!') => window.showCustomToast(message, 'success', title),
        error: (message, title = 'Gagal!') => window.showCustomToast(message, 'error', title),
        warning: (message, title = 'Perhatian!') => window.showCustomToast(message, 'warning', title),
        info: (message, title = 'Informasi') => window.showCustomToast(message, 'info', title)
    };
})();
