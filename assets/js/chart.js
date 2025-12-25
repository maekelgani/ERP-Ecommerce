(function() {
    document.addEventListener('DOMContentLoaded', function() {
        initCustomToast();
        initCartOperations();
    });

    function initCustomToast() {
        if (document.getElementById('customToastContainer')) return;
        
        const container = document.createElement('div');
        container.id = 'customToastContainer';
        document.body.appendChild(container);

        window.showCustomToast = function(message, type = 'success', title = null, duration = 4000) {
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
    }

    function initCartOperations() {
        window.updateQuantity = function(cartId, change, maxStock) {
            const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
            if (!input) return;

            let newQty = parseInt(input.value) + change;
            if (newQty < 1) newQty = 1;
            if (newQty > maxStock) newQty = maxStock;

            input.value = newQty;
            updateCartQuantity(cartId, newQty);
        };

        window.updateQuantityDirect = function(cartId, value, maxStock) {
            let qty = parseInt(value);
            if (isNaN(qty) || qty < 1) {
                window.showCustomToast('Jumlah tidak valid', 'error');
                return;
            }
            if (qty > maxStock) {
                window.showCustomToast(`Stok maksimal ${maxStock} item`, 'warning');
                return;
            }
            updateCartQuantity(cartId, qty);
        };

        window.moveToWishlist = function(productId, cartId, isInWishlist) {
            const btn = document.querySelector(`[data-product-id="${productId}"][data-in-wishlist]`);
            
            if (isInWishlist) {
                removeFromWishlist(productId, btn);
            } else {
                addToWishlist(productId, cartId, btn);
            }
        };

        window.removeFromCart = function(cartId) {
            fetch('../../ajax/cart/remove-item.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart_id: cartId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`[data-cart-id="${cartId}"]`);
                    if (item) {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(-100%)';
                        setTimeout(() => item.remove(), 300);
                    }
                    window.showCustomToast('Produk dihapus dari keranjang', 'success', 'Dihapus');
                    setTimeout(() => location.reload(), 500);
                } else {
                    window.showCustomToast(data.message || 'Gagal menghapus produk', 'error');
                }
            })
            .catch(() => {
                window.showCustomToast('Terjadi kesalahan saat menghapus', 'error');
            });
        };

        window.removeAllOutOfStock = function() {
            fetch('../../ajax/cart/remove-all-unavailable.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.showCustomToast('Semua produk tidak tersedia telah dihapus', 'success', 'Dihapus');
                    setTimeout(() => location.reload(), 500);
                } else {
                    window.showCustomToast(data.message || 'Gagal menghapus produk', 'error');
                }
            })
            .catch(() => {
                window.showCustomToast('Terjadi kesalahan saat menghapus', 'error');
            });
        };
    }

    function updateCartQuantity(cartId, newQty) {
        fetch('../../ajax/cart/update-quantity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId, quantity: newQty })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-cart-id="${cartId}"]`);
                if (item) {
                    const pricePerItem = parseFloat(item.dataset.price);
                    const newTotal = pricePerItem * newQty;
                    item.querySelector('.item-total').textContent = formatPrice(newTotal);
                }
                window.showCustomToast('Jumlah produk diperbarui', 'info');
            } else {
                window.showCustomToast(data.message || 'Gagal mengupdate jumlah', 'error');
            }
        })
        .catch(() => {
            window.showCustomToast('Terjadi kesalahan saat mengupdate', 'error');
        });
    }

    function addToWishlist(productId, cartId, btn) {
        fetch('../../ajax/wishlist/add-wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.setAttribute('data-in-wishlist', 'true');
                btn.classList.add('text-[#882426]');
                btn.classList.remove('text-gray-600', 'hover:text-[#882426]');
                btn.querySelector('svg').classList.add('fill-[#882426]');
                window.showCustomToast('Produk ditambahkan ke wishlist', 'success', 'Ditambahkan');
            } else {
                window.showCustomToast(data.message || 'Gagal menambahkan ke wishlist', 'error');
            }
        })
        .catch(() => {
            window.showCustomToast('Terjadi kesalahan saat menambahkan ke wishlist', 'error');
        });
    }

    function removeFromWishlist(productId, btn) {
        fetch('../../ajax/wishlist/remove-wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.setAttribute('data-in-wishlist', 'false');
                btn.classList.remove('text-[#882426]');
                btn.classList.add('text-gray-600', 'hover:text-[#882426]');
                btn.querySelector('svg').classList.remove('fill-[#882426]');
                window.showCustomToast('Produk dihapus dari wishlist', 'success', 'Dihapus');
            } else {
                window.showCustomToast(data.message || 'Gagal menghapus dari wishlist', 'error');
            }
        })
        .catch(() => {
            window.showCustomToast('Terjadi kesalahan saat menghapus dari wishlist', 'error');
        });
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(price);
    }
})();
