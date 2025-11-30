<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - Modern UI</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Custom Scrollbar for sleek look */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .slide-in-bottom {
            animation: slideIn 0.3s ease-out forwards;
        }
        @keyframes slideIn {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        success: '#10b981',
                        warning: '#f59e0b',
                        info: '#3b82f6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen pb-20">

    <nav class="bg-white shadow-sm sticky top-0 z-30">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="text-primary w-6 h-6"></i>
                    <h1 class="text-xl font-bold text-gray-900">Pesanan Saya</h1>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=User+Demo&background=random" alt="User">
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        
        <div class="flex overflow-x-auto gap-2 pb-4 mb-4 no-scrollbar" id="status-tabs">
            </div>

        <div id="order-list" class="space-y-4">
            </div>

        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 text-center">
            <div class="bg-gray-100 p-6 rounded-full mb-4">
                <i data-lucide="package-open" class="w-12 h-12 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Belum ada pesanan</h3>
            <p class="text-gray-500 mt-1">Pesanan Anda akan muncul di sini.</p>
        </div>
    </main>

    <div id="order-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        
        <div class="absolute inset-x-0 bottom-0 md:inset-0 md:flex md:items-center md:justify-center pointer-events-none">
            <div class="bg-white w-full md:w-[600px] md:h-[85vh] h-[90vh] md:rounded-2xl rounded-t-2xl shadow-2xl flex flex-col pointer-events-auto slide-in-bottom">
                
                <div class="px-6 py-4 border-b flex justify-between items-center bg-white rounded-t-2xl sticky top-0">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Detail Pesanan</h2>
                        <p class="text-xs text-gray-500" id="modal-order-id">#ID</p>
                    </div>
                    <button onclick="closeModal()" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50">
                    
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-semibold mb-4 text-gray-900">Status Pengiriman</h3>
                        <div class="relative pl-4 border-l-2 border-gray-200 space-y-6" id="modal-timeline">
                            </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-semibold mb-4 text-gray-900">Produk Dibeli</h3>
                        <div class="space-y-4" id="modal-items">
                            </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                        <h3 class="text-sm font-semibold mb-3 text-gray-900">Rincian Pembayaran</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal Produk</span>
                                <span id="modal-subtotal">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Ongkos Kirim</span>
                                <span id="modal-shipping">Rp 0</span>
                            </div>
                            <div class="border-t border-dashed my-2 pt-2 flex justify-between font-bold text-gray-900 text-base">
                                <span>Total Belanja</span>
                                <span id="modal-total" class="text-primary">Rp 0</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="p-4 border-t bg-white md:rounded-b-2xl flex gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                    <button class="flex-1 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-sm">
                        Bantuan
                    </button>
                    <button class="flex-1 px-4 py-2.5 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition shadow-lg shadow-primary/30 text-sm">
                        Lacak Paket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- 1. DATA DUMMY (Simulasi Response JSON dari PHP Backend) ---
        const ordersData = [
            {
                id: "ORD-2025-001",
                date: "2025-11-30 08:30",
                status: "dikemas", // 'dikemas', 'dikirim', 'selesai'
                total: 2500000,
                shipping_cost: 25000,
                items: [
                    { name: "Mechanical Keyboard Keychron K2", qty: 1, price: 1800000, img: "https://images.unsplash.com/photo-1595225476474-87563907a212?w=150&q=80" },
                    { name: "Logitech MX Master 3S", qty: 1, price: 700000, img: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=150&q=80" }
                ],
                timeline: [
                    { title: "Pesanan Dibuat", time: "30 Nov, 08:30", active: true },
                    { title: "Pembayaran Dikonfirmasi", time: "30 Nov, 08:35", active: true },
                    { title: "Sedang Dikemas Penjual", time: "Sedang diproses", active: true },
                    { title: "Masuk Logistik", time: "-", active: false },
                ]
            },
            {
                id: "ORD-2025-002",
                date: "2025-11-28 14:20",
                status: "dikirim",
                total: 450000,
                shipping_cost: 15000,
                items: [
                    { name: "Sony WH-1000XM4 Earpads Replacement", qty: 2, price: 225000, img: "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=150&q=80" }
                ],
                timeline: [
                    { title: "Pesanan Dibuat", time: "28 Nov, 14:20", active: true },
                    { title: "Dikemas", time: "28 Nov, 16:00", active: true },
                    { title: "Diserahkan ke Kurir", time: "28 Nov, 19:00", active: true },
                    { title: "Dalam Perjalanan (Jakarta DC)", time: "29 Nov, 10:00", active: true },
                ]
            },
            {
                id: "ORD-2025-003",
                date: "2025-11-20 09:15",
                status: "selesai",
                total: 125000,
                shipping_cost: 10000,
                items: [
                    { name: "USB-C Fast Charging Cable 2m", qty: 1, price: 125000, img: "https://images.unsplash.com/photo-1562858712-07eb78619374?w=150&q=80" }
                ],
                timeline: [
                    { title: "Pesanan Dibuat", time: "20 Nov", active: true },
                    { title: "Dikirim", time: "21 Nov", active: true },
                    { title: "Paket Diterima", time: "23 Nov", active: true },
                    { title: "Selesai", time: "23 Nov", active: true },
                ]
            }
        ];

        // --- 2. LOGIC JAVASCRIPT ---

        // Konfigurasi Tampilan Status
        const statusConfig = {
            'dikemas': { label: 'Dikemas', color: 'bg-warning/10 text-warning border-warning/20', icon: 'package' },
            'dikirim': { label: 'Dikirim', color: 'bg-info/10 text-info border-info/20', icon: 'truck' },
            'selesai': { label: 'Selesai', color: 'bg-success/10 text-success border-success/20', icon: 'check-circle-2' }
        };

        const formatRupiah = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);

        let currentFilter = 'all';

        // Render Tabs
        function renderTabs() {
            const tabs = ['all', 'dikemas', 'dikirim', 'selesai'];
            const container = document.getElementById('status-tabs');
            
            container.innerHTML = tabs.map(tab => {
                const isActive = currentFilter === tab;
                const label = tab === 'all' ? 'Semua' : statusConfig[tab].label;
                const activeClass = isActive 
                    ? 'bg-gray-900 text-white shadow-md' 
                    : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50';
                
                return `
                    <button onclick="setFilter('${tab}')" 
                        class="px-5 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 ${activeClass}">
                        ${label}
                    </button>
                `;
            }).join('');
        }

        // Render Order List
        function renderOrders() {
            const container = document.getElementById('order-list');
            const emptyState = document.getElementById('empty-state');
            
            // Filter Data
            const filteredOrders = currentFilter === 'all' 
                ? ordersData 
                : ordersData.filter(o => o.status === currentFilter);

            if (filteredOrders.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            } else {
                emptyState.classList.add('hidden');
            }

            container.innerHTML = filteredOrders.map(order => {
                const config = statusConfig[order.status];
                const firstItem = order.items[0];
                const otherItemsCount = order.items.length - 1;

                return `
                    <div onclick="openModal('${order.id}')" 
                        class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-pointer hover:shadow-md transition-shadow group">
                        
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-100 p-1.5 rounded-lg">
                                    <i data-lucide="shopping-bag" class="w-4 h-4 text-gray-600"></i>
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500">Belanja</p>
                                    <p class="text-xs text-gray-400">${order.date}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border flex items-center gap-1.5 ${config.color}">
                                <i data-lucide="${config.icon}" class="w-3 h-3"></i> ${config.label}
                            </span>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-20 h-20 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                <img src="${firstItem.img}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${firstItem.name}">
                            </div>
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 line-clamp-2 leading-snug">
                                        ${firstItem.name}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        ${firstItem.qty} Barang ${otherItemsCount > 0 ? `(+${otherItemsCount} lainnya)` : ''}
                                    </p>
                                </div>
                                <div class="flex justify-between items-end mt-2">
                                    <div class="text-xs text-gray-400">Total Pesanan</div>
                                    <div class="text-base font-bold text-gray-900">${formatRupiah(order.total)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            lucide.createIcons();
        }

        // Logic Filter
        function setFilter(status) {
            currentFilter = status;
            renderTabs();
            renderOrders();
        }

        // --- 3. MODAL LOGIC ---

        function openModal(orderId) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order) return;

            // Populate Data
            document.getElementById('modal-order-id').innerText = order.id;
            
            // Populate Timeline
            const timelineContainer = document.getElementById('modal-timeline');
            timelineContainer.innerHTML = order.timeline.map((log, index) => {
                const colorClass = log.active ? 'bg-primary border-primary' : 'bg-gray-300 border-gray-300';
                const textClass = log.active ? 'text-gray-900 font-medium' : 'text-gray-400';
                const lineClass = index === order.timeline.length - 1 ? 'hidden' : ''; // Hide line for last item
                
                return `
                    <div class="relative mb-0 last:mb-0">
                        <div class="absolute -left-[21px] mt-1.5 w-2.5 h-2.5 rounded-full border-2 border-white ring-1 ring-gray-200 ${colorClass}"></div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm ${textClass}">${log.title}</p>
                            </div>
                            <span class="text-xs text-gray-400 text-right">${log.time}</span>
                        </div>
                    </div>
                `;
            }).join('');

            // Populate Items
            const itemsContainer = document.getElementById('modal-items');
            itemsContainer.innerHTML = order.items.map(item => `
                <div class="flex gap-3">
                    <img src="${item.img}" class="w-14 h-14 rounded-md object-cover bg-gray-100">
                    <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900 line-clamp-2">${item.name}</h4>
                        <div class="flex justify-between mt-1">
                            <span class="text-xs text-gray-500">x${item.qty}</span>
                            <span class="text-sm font-medium text-gray-700">${formatRupiah(item.price)}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            // Populate Prices
            const subtotal = order.total - order.shipping_cost;
            document.getElementById('modal-subtotal').innerText = formatRupiah(subtotal);
            document.getElementById('modal-shipping').innerText = formatRupiah(order.shipping_cost);
            document.getElementById('modal-total').innerText = formatRupiah(order.total);

            // Show Modal
            const modal = document.getElementById('order-modal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeModal() {
            const modal = document.getElementById('order-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Initialize
        renderTabs();
        renderOrders();
    </script>
</body>
</html>