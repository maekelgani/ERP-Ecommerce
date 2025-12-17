function getApiUrl(endpoint) {
    if (window.APP_CONFIG && window.APP_CONFIG.apiUrl) {
        return window.APP_CONFIG.apiUrl + '/' + endpoint;
    }
    return '../../api/' + endpoint;
}

function showNotification(message, type = 'success') {
    const existing = document.querySelector('.notification-toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = `notification-toast fixed top-24 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 flex items-center gap-3 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;

    const icon = type === 'success' 
        ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
        : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';

    toast.innerHTML = icon + '<span>' + message + '</span>';
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full');
    });

    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function checkLoginRequired(actionType = 'default') {
    const isLoggedIn = document.body.dataset.customerLoggedIn === 'true';
    if (!isLoggedIn) {
        if (typeof showLoginModal === 'function') {
            showLoginModal(actionType);
        } else if (typeof showLoginRequiredModal === 'function') {
            showLoginRequiredModal();
        } else {
            window.location.href = 'customerLogin.php';
        }
        return false;
    }
    return true;
}

function addToCart(productId, quantity = 1) {
    if (!checkLoginRequired('cart')) return;

    const qtyInput = document.getElementById('quantity');
    const qty = qtyInput ? parseInt(qtyInput.value) : quantity;

    fetch(getApiUrl('cart/add.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: qty })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            if (data.cart_count !== undefined) {
                updateCartCount(data.cart_count);
            }
        } else {
            if (data.require_login) {
                if (typeof showLoginRequiredModal === 'function') {
                    showLoginRequiredModal();
                }
            } else {
                showNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    });
}

function updateWishlistButtonState(btn, isWishlisted) {
    const svg = btn.querySelector('svg');
    if (!svg) return;
    
    if (!btn.dataset.originalClasses) {
        btn.dataset.originalClasses = btn.className;
    }
    
    if (isWishlisted) {
        svg.setAttribute('fill', 'currentColor');
        svg.classList.add('text-red-500');
        svg.classList.remove('text-gray-600', 'text-gray-700');
        
        const currentBgClasses = Array.from(btn.classList).filter(c => c.startsWith('bg-'));
        currentBgClasses.forEach(c => btn.classList.remove(c));
        btn.classList.add('bg-red-50');
        
        btn.classList.add('wishlist-active');
        btn.dataset.wishlisted = 'true';
    } else {
        svg.setAttribute('fill', 'none');
        svg.classList.remove('text-red-500');
        svg.classList.add('text-gray-700');
        
        btn.classList.remove('bg-red-50', 'wishlist-active');
        
        const originalClasses = btn.dataset.originalClasses.split(' ');
        const originalBgClasses = originalClasses.filter(c => c.startsWith('bg-'));
        originalBgClasses.forEach(c => {
            if (!btn.classList.contains(c)) {
                btn.classList.add(c);
            }
        });
        
        btn.dataset.wishlisted = 'false';
    }
}

function addToWishlist(productId) {
    if (!checkLoginRequired('wishlist')) return;
    
    const allBtns = document.querySelectorAll(`[data-wishlist-product="${productId}"]`);
    const firstBtn = allBtns[0];
    const wasWishlisted = firstBtn?.dataset.wishlisted === 'true';
    const newState = !wasWishlisted;
    
    allBtns.forEach(btn => {
        btn.style.pointerEvents = 'none';
        updateWishlistButtonState(btn, newState);
    });
    
    const currentBadge = document.querySelector('.wishlist-count-badge');
    const currentCount = currentBadge ? parseInt(currentBadge.textContent) || 0 : 0;
    const optimisticCount = newState ? currentCount + 1 : Math.max(0, currentCount - 1);
    updateWishlistCount(optimisticCount, newState);

    fetch(getApiUrl('wishlist/add.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        allBtns.forEach(btn => btn.style.pointerEvents = '');
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            const confirmedState = data.action === 'added';
            
            if (confirmedState !== newState) {
                allBtns.forEach(btn => {
                    updateWishlistButtonState(btn, confirmedState);
                });
            }
            
            if (data.wishlist_count !== undefined) {
                updateWishlistCount(data.wishlist_count, false);
            }
        } else {
            allBtns.forEach(btn => {
                updateWishlistButtonState(btn, wasWishlisted);
            });
            updateWishlistCount(currentCount, false);
            
            if (data.require_login) {
                if (typeof showLoginRequiredModal === 'function') {
                    showLoginRequiredModal();
                }
            } else {
                showNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        allBtns.forEach(btn => {
            btn.style.pointerEvents = '';
            updateWishlistButtonState(btn, wasWishlisted);
        });
        updateWishlistCount(currentCount, false);
        showNotification('Terjadi kesalahan', 'error');
    });
}

function buyNow(productId, quantity = 1) {
    if (!checkLoginRequired('checkout')) return;

    const qtyInput = document.getElementById('quantity');
    const qty = qtyInput ? parseInt(qtyInput.value) : quantity;

    window.location.href = 'productCheckout.php?from=buynow&product=' + encodeURIComponent(productId) + '&qty=' + qty;
}

function updateCartCount(count, animate = true) {
    const cartBadges = document.querySelectorAll('.cart-count-badge');
    cartBadges.forEach(badge => {
        badge.classList.remove('animate-pulse-scale');
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
            
            if (animate) {
                void badge.offsetWidth;
                badge.classList.add('animate-pulse-scale');
            }
        } else {
            badge.classList.add('hidden');
        }
    });
}

function updateWishlistCount(count, animate = false) {
    const wishlistBadges = document.querySelectorAll('.wishlist-count-badge');
    wishlistBadges.forEach(badge => {
        badge.classList.remove('animate-pulse-scale');
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
            
            if (animate) {
                void badge.offsetWidth;
                badge.classList.add('animate-pulse-scale');
            }
        } else {
            badge.classList.add('hidden');
        }
    });
}

function initWishlistButtons() {
    const isLoggedIn = document.body.dataset.customerLoggedIn === 'true';
    if (!isLoggedIn) {
        return;
    }

    const wishlistBtns = document.querySelectorAll('[data-wishlist-product]');
    if (wishlistBtns.length === 0) {
        return;
    }

    const productIds = Array.from(wishlistBtns).map(btn => btn.dataset.wishlistProduct);
    const uniqueIds = [...new Set(productIds)].filter(id => id && id.trim() !== '');

    if (uniqueIds.length === 0) {
        return;
    }

    const apiUrl = getApiUrl('wishlist/check.php');
    
    fetch(apiUrl, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ product_ids: uniqueIds }),
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && Array.isArray(data.wishlisted)) {
            const wishlistedSet = new Set(data.wishlisted.map(id => String(id)));
            
            wishlistBtns.forEach(btn => {
                const productId = String(btn.dataset.wishlistProduct);
                const isWishlisted = wishlistedSet.has(productId);
                
                btn.dataset.wishlisted = isWishlisted ? 'true' : 'false';
                updateWishlistButtonState(btn, isWishlisted);
            });
        }
    })
    .catch(error => {
        console.error('Error checking wishlist status:', error);
    });
}

function runInitWishlistButtons() {
    if (typeof window.APP_CONFIG !== 'undefined') {
        initWishlistButtons();
    } else {
        setTimeout(runInitWishlistButtons, 50);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', runInitWishlistButtons);
} else {
    runInitWishlistButtons();
}
