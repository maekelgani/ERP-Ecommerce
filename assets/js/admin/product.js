(function() {
    'use strict';

    function initProductAdmin() {
        console.log('[ProductAdmin] Initializing...');

        // ===================
        // Modal Form Tambah/Edit Produk
        // ===================
        try {
            const modal = document.getElementById("form-product");
            if (modal) {
                console.log('[ProductAdmin] Modal form found');
                
                const openBtns = document.querySelectorAll(".openForm");
                const editBtns = document.querySelectorAll(".editForm");
                const backdrop = modal.querySelector("#modal-backdrop");
                const modalCard = modal.querySelector("#modal-card");
                const closeBtns = modal.querySelectorAll(".cancel");
                const title = modal.querySelector("h2");
                const subtitle = modal.querySelector("p.text-sm");
                const submitBtn = modal.querySelector("button[type='submit']");

                function openModal() {
                    modal.classList.remove("hidden");
                    requestAnimationFrame(function() {
                        if (backdrop) {
                            backdrop.classList.remove("opacity-0");
                            backdrop.classList.add("opacity-100");
                        }
                        if (modalCard) {
                            modalCard.classList.remove("opacity-0", "scale-95", "translate-y-4");
                            modalCard.classList.add("opacity-100", "scale-100", "translate-y-0");
                        }
                    });
                    document.documentElement.style.overflow = "hidden";
                    document.body.style.overflow = "hidden";
                }

                function closeModal() {
                    if (backdrop) {
                        backdrop.classList.remove("opacity-100");
                        backdrop.classList.add("opacity-0");
                    }
                    if (modalCard) {
                        modalCard.classList.remove("opacity-100", "scale-100", "translate-y-0");
                        modalCard.classList.add("opacity-0", "scale-95", "translate-y-4");
                    }
                    setTimeout(function() {
                        modal.classList.add("hidden");
                    }, 300);
                    document.documentElement.style.overflow = "";
                    document.body.style.overflow = "";
                }

                openBtns.forEach(function(btn) {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openModal();
                        if (title) title.textContent = "Tambah Produk";
                        if (subtitle) subtitle.textContent = "Masukkan detail produk ke inventori";
                        if (submitBtn) submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span> Simpan Produk';
                    });
                });

                editBtns.forEach(function(btn) {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openModal();
                        if (title) title.textContent = "Edit Produk";
                        if (subtitle) subtitle.textContent = "Perbarui informasi produk";
                        if (submitBtn) submitBtn.innerHTML = '<span class="material-symbols-outlined text-lg">save</span> Update Produk';
                    });
                });

                if (backdrop) {
                    backdrop.addEventListener("click", closeModal);
                }

                closeBtns.forEach(function(btn) {
                    btn.addEventListener("click", closeModal);
                });

                document.addEventListener("keydown", function(e) {
                    if (e.key === "Escape" && !modal.classList.contains("hidden")) {
                        closeModal();
                    }
                });

                console.log('[ProductAdmin] Modal initialized with', openBtns.length, 'open buttons');
            }
        } catch (e) {
            console.error('[ProductAdmin] Modal error:', e);
        }

        // ===================
        // Lightbox untuk Gambar Produk
        // ===================
        try {
            const triggers = document.querySelectorAll('.lightbox-trigger');
            const lightbox = document.getElementById('lightbox');
            const lbImage = document.getElementById('lb-image');
            const lbClose = document.getElementById('lb-close');
            const lbBackdrop = document.getElementById('lb-backdrop');

            if (lightbox && lbImage) {
                console.log('[ProductAdmin] Lightbox found with', triggers.length, 'triggers');

                function openLightbox(imgEl) {
                    if (!imgEl) return;
                    lbImage.src = imgEl.src;
                    lbImage.alt = imgEl.alt || '';
                    lightbox.classList.remove('hidden');

                    requestAnimationFrame(function() {
                        if (lbBackdrop) {
                            lbBackdrop.classList.remove('opacity-0');
                            lbBackdrop.classList.add('opacity-100');
                        }
                        lbImage.classList.remove('opacity-0', 'scale-95');
                        lbImage.classList.add('opacity-100', 'scale-100');
                    });

                    document.documentElement.style.overflow = 'hidden';
                    document.body.style.overflow = 'hidden';
                }

                function closeLightbox() {
                    if (lbBackdrop) {
                        lbBackdrop.classList.remove('opacity-100');
                        lbBackdrop.classList.add('opacity-0');
                    }
                    lbImage.classList.remove('opacity-100', 'scale-100');
                    lbImage.classList.add('opacity-0', 'scale-95');

                    setTimeout(function() {
                        lightbox.classList.add('hidden');
                        lbImage.src = '';
                    }, 300);

                    document.documentElement.style.overflow = '';
                    document.body.style.overflow = '';
                }

                triggers.forEach(function(img) {
                    img.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openLightbox(img);
                    });
                });

                if (lbBackdrop) {
                    lbBackdrop.addEventListener('click', closeLightbox);
                }
                if (lbClose) {
                    lbClose.addEventListener('click', closeLightbox);
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
                        closeLightbox();
                    }
                });

                console.log('[ProductAdmin] Lightbox initialized');
            }
        } catch (e) {
            console.error('[ProductAdmin] Lightbox error:', e);
        }

        // ===================
        // Increment dan Decrement Stok Produk
        // ===================
        try {
            const plusButtons = document.querySelectorAll('.btn-plus');
            const minusButtons = document.querySelectorAll('.btn-minus');

            console.log('[ProductAdmin] Stock buttons:', plusButtons.length, 'plus,', minusButtons.length, 'minus');

            function updateStockColor(inputEl) {
                if (!inputEl) return;
                var value = parseInt(inputEl.value, 10) || 0;
                inputEl.classList.remove('text-red-600', 'text-amber-600', 'text-gray-800');

                if (value === 0) {
                    inputEl.classList.add('text-red-600');
                } else if (value <= 5) {
                    inputEl.classList.add('text-amber-600');
                } else {
                    inputEl.classList.add('text-gray-800');
                }
            }

            function changeStock(inputEl, delta) {
                if (!inputEl) return;
                var cur = parseInt(inputEl.value, 10) || 0;
                var next = cur + delta;
                if (next < 0) next = 0;
                inputEl.value = next;
                updateStockColor(inputEl);
            }

            function findStockInput(btn) {
                var td = btn.closest('td');
                if (td) {
                    var input = td.querySelector('.produkstok');
                    if (input) return input;
                }
                var container = btn.closest('.flex');
                if (container) {
                    var input = container.querySelector('.produkstok');
                    if (input) return input;
                }
                var parent = btn.parentElement;
                if (parent) {
                    return parent.querySelector('.produkstok');
                }
                return null;
            }

            plusButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var input = findStockInput(btn);
                    changeStock(input, 1);
                });
            });

            minusButtons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var input = findStockInput(btn);
                    changeStock(input, -1);
                });
            });

            console.log('[ProductAdmin] Stock controls initialized');
        } catch (e) {
            console.error('[ProductAdmin] Stock controls error:', e);
        }

        // ===================
        // View Toggle (Table / Grid)
        // ===================
        try {
            var tableViewBtn = document.getElementById('view-table');
            var gridViewBtn = document.getElementById('view-grid');
            var tableView = document.getElementById('table-view');
            var gridView = document.getElementById('grid-view');

            if (tableViewBtn && gridViewBtn && tableView && gridView) {
                console.log('[ProductAdmin] View toggle found');

                function setActiveView(activeBtn, inactiveBtn) {
                    activeBtn.style.background = '#882426';
                    activeBtn.style.color = 'white';
                    activeBtn.classList.add('active');

                    inactiveBtn.style.background = 'transparent';
                    inactiveBtn.style.color = '#4b5563';
                    inactiveBtn.classList.remove('active');
                }

                tableViewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    tableView.classList.remove('hidden');
                    gridView.classList.add('hidden');
                    setActiveView(tableViewBtn, gridViewBtn);
                    try { localStorage.setItem('product-view', 'table'); } catch(ex) {}
                });

                gridViewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    gridView.classList.remove('hidden');
                    tableView.classList.add('hidden');
                    setActiveView(gridViewBtn, tableViewBtn);
                    try { localStorage.setItem('product-view', 'grid'); } catch(ex) {}
                });

                try {
                    var savedView = localStorage.getItem('product-view');
                    if (savedView === 'grid') {
                        gridViewBtn.click();
                    }
                } catch (e) {
                    console.log('[ProductAdmin] LocalStorage not available');
                }

                console.log('[ProductAdmin] View toggle initialized');
            }
        } catch (e) {
            console.error('[ProductAdmin] View toggle error:', e);
        }

        // ===================
        // Search & Filter Functionality
        // ===================
        try {
            var searchInput = document.getElementById('search-product');
            var applyBtn = document.getElementById('apply-filter');
            var resetBtn = document.getElementById('reset-filter');
            var filterCategory = document.getElementById('filter-category');
            var filterBrand = document.getElementById('filter-brand');
            var filterStatus = document.getElementById('filter-status');
            var filterSort = document.getElementById('filter-sort');
            var tableViewEl = document.getElementById('table-view');
            var gridViewEl = document.getElementById('grid-view');

            if (tableViewEl) {
                console.log('[ProductAdmin] Filter section found');

                var tableRows = Array.from(tableViewEl.querySelectorAll('tbody tr'));
                var gridCards = gridViewEl ? Array.from(gridViewEl.querySelectorAll('.product-card')) : [];

                function getTableRowData(row) {
                    var productName = '';
                    var brand = '';
                    var sku = '';
                    var category = '';
                    var stock = 0;
                    var price = 0;
                    var status = 'active';

                    try {
                        var nameEl = row.querySelector('td:nth-child(2) p.font-semibold');
                        if (nameEl) productName = nameEl.textContent.toLowerCase();

                        var brandEl = row.querySelector('td:nth-child(2) p.text-xs');
                        if (brandEl) brand = brandEl.textContent.toLowerCase();

                        var skuEl = row.querySelector('td:nth-child(3) span');
                        if (skuEl) sku = skuEl.textContent.toLowerCase();

                        var catEl = row.querySelector('td:nth-child(4) span');
                        if (catEl) category = catEl.textContent.toLowerCase();

                        var stockInput = row.querySelector('.produkstok');
                        if (stockInput) stock = parseInt(stockInput.value, 10) || 0;

                        var priceEl = row.querySelector('td:nth-child(5) p.font-semibold');
                        if (priceEl) price = parseInt(priceEl.textContent.replace(/[^\d]/g, ''), 10) || 0;

                        var statusBadge = row.querySelector('td:nth-child(7) span');
                        if (statusBadge) {
                            var statusText = statusBadge.textContent.toLowerCase();
                            if (statusText.indexOf('menipis') >= 0) status = 'lowstock';
                            if (statusText.indexOf('habis') >= 0) status = 'outstock';
                        }
                    } catch (ex) {}

                    return { productName: productName, brand: brand, sku: sku, category: category, stock: stock, price: price, status: status };
                }

                function getGridCardData(card) {
                    return {
                        productName: (card.dataset.name || '').toLowerCase(),
                        brand: (card.dataset.brand || '').toLowerCase(),
                        category: (card.dataset.category || '').toLowerCase(),
                        sku: (card.dataset.sku || '').toLowerCase(),
                        status: card.dataset.status || 'active',
                        stock: parseInt(card.dataset.stock, 10) || 0,
                        price: parseInt(card.dataset.price, 10) || 0
                    };
                }

                function matchesFilters(data) {
                    var searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                    var categoryFilter = filterCategory ? filterCategory.value.toLowerCase() : '';
                    var brandFilter = filterBrand ? filterBrand.value.toLowerCase() : '';
                    var statusFilter = filterStatus ? filterStatus.value.toLowerCase() : '';

                    var matchesSearch = !searchTerm ||
                        data.productName.indexOf(searchTerm) >= 0 ||
                        data.brand.indexOf(searchTerm) >= 0 ||
                        (data.sku && data.sku.indexOf(searchTerm) >= 0);

                    var matchesCategory = !categoryFilter || data.category.indexOf(categoryFilter) >= 0;
                    var matchesBrand = !brandFilter || data.brand.indexOf(brandFilter) >= 0;
                    var matchesStatus = !statusFilter || data.status === statusFilter;

                    return matchesSearch && matchesCategory && matchesBrand && matchesStatus;
                }

                function sortData(items, getData) {
                    var sortValue = filterSort ? filterSort.value : 'newest';
                    
                    return items.slice().sort(function(a, b) {
                        var dataA = getData(a);
                        var dataB = getData(b);

                        switch (sortValue) {
                            case 'price-asc':
                                return dataA.price - dataB.price;
                            case 'price-desc':
                                return dataB.price - dataA.price;
                            case 'stock-asc':
                                return dataA.stock - dataB.stock;
                            case 'stock-desc':
                                return dataB.stock - dataA.stock;
                            case 'oldest':
                                return 1;
                            default:
                                return -1;
                        }
                    });
                }

                function filterProducts() {
                    var tableVisibleCount = 0;
                    var gridVisibleCount = 0;

                    tableRows.forEach(function(row) {
                        var data = getTableRowData(row);
                        if (matchesFilters(data)) {
                            row.style.display = '';
                            tableVisibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    gridCards.forEach(function(card) {
                        var data = getGridCardData(card);
                        if (matchesFilters(data)) {
                            card.style.display = '';
                            gridVisibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    console.log('[ProductAdmin] Filtered:', tableVisibleCount, 'table rows,', gridVisibleCount, 'grid cards visible');
                }

                function resetFilters() {
                    if (searchInput) searchInput.value = '';
                    if (filterCategory) filterCategory.selectedIndex = 0;
                    if (filterBrand) filterBrand.selectedIndex = 0;
                    if (filterStatus) filterStatus.selectedIndex = 0;
                    if (filterSort) filterSort.selectedIndex = 0;

                    tableRows.forEach(function(row) {
                        row.style.display = '';
                    });

                    gridCards.forEach(function(card) {
                        card.style.display = '';
                    });
                }

                if (applyBtn) {
                    applyBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        filterProducts();
                    });
                }

                if (resetBtn) {
                    resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        resetFilters();
                    });
                }

                if (searchInput) {
                    var debounceTimer;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(filterProducts, 300);
                    });

                    searchInput.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            filterProducts();
                        }
                    });
                }

                console.log('[ProductAdmin] Filter initialized with', tableRows.length, 'table rows,', gridCards.length, 'grid cards');
            }
        } catch (e) {
            console.error('[ProductAdmin] Filter error:', e);
        }

        console.log('[ProductAdmin] Initialization complete');
    }

    document.addEventListener('DOMContentLoaded', initProductAdmin);
})();
