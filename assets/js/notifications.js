/**
 * Notification Handler for Admin Dashboard
 * Mengelola fetch, display, dan interaksi dengan notifikasi
 */

class NotificationManager {
    constructor() {
        this.notificationBtn = document.getElementById('notification-btn');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.notificationBadge = document.getElementById('notification-badge');
        this.apiEndpoint = '../../api/admin/get-notifications.php';
        this.autoRefreshInterval = 30000; // 30 detik
        this.isLoading = false;

        this.init();
    }

    init() {
        if (this.notificationBtn && this.notificationDropdown) {
            this.notificationBtn.addEventListener('click', () => this.toggleDropdown());
            this.loadNotifications();
            this.setupAutoRefresh();
        }
    }

    /**
     * Toggle notification dropdown
     */
    toggleDropdown() {
        const isHidden = this.notificationDropdown.classList.contains('hidden');
        if (isHidden) {
            this.notificationDropdown.classList.remove('hidden');
            this.loadNotifications();
        } else {
            this.notificationDropdown.classList.add('hidden');
        }
    }

    /**
     * Load notifications dari server
     */
    async loadNotifications() {
        if (this.isLoading) return;

        this.isLoading = true;
        try {
            const response = await fetch(`${this.apiEndpoint}?limit=10&offset=0`);
            const result = await response.json();

            if (result.success) {
                this.renderNotifications(result.data);
                this.updateBadgeCount(result.unread_count);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        } finally {
            this.isLoading = false;
        }
    }

    /**
     * Render notifikasi ke DOM
     */
    renderNotifications(notifications) {
        const container = this.notificationDropdown.querySelector('.max-h-64');
        
        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-500">
                    <span class="material-symbols-outlined block text-3xl mb-2">inbox</span>
                    <p class="text-sm">Tidak ada notifikasi</p>
                </div>
            `;
            return;
        }

        container.innerHTML = notifications.map(notif => this.createNotificationElement(notif)).join('');

        // Add event listeners to notification items
        container.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => this.handleNotificationClick(item));
        });
    }

    /**
     * Buat elemen notifikasi HTML
     */
    createNotificationElement(notif) {
        const isUnread = notif.status_baca === 'belum_dibaca';
        const colorMap = {
            'orange': '#f97316',
            'green': '#22c55e',
            'blue': '#3b82f6',
            'purple': '#a855f7',
            'gray': '#6b7280'
        };

        return `
            <div class="notification-item ${isUnread ? 'unread' : ''}" data-notification-id="${notif.id_notifikasi}">
                <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 transition-colors">
                    <span class="material-symbols-outlined flex-shrink-0" style="color: ${colorMap[notif.color] || colorMap['gray']}; font-size: 20px;">
                        ${notif.icon}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            ${notif.judul_pesan}
                        </p>
                        <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">
                            ${notif.isi_pesan}
                        </p>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs text-gray-400">${notif.waktu_relatif}</span>
                            ${isUnread ? '<span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>' : ''}
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-2">
                        <button class="text-gray-400 hover:text-gray-600 close-btn" data-notification-id="${notif.id_notifikasi}" title="Hapus">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            `;
    }

    /**
     * Handle notification item click
     */
    async handleNotificationClick(item) {
        const notificationId = item.dataset.notificationId;
        
        if (item.classList.contains('unread')) {
            // Mark as read
            await this.markAsRead(notificationId);
            item.classList.remove('unread');
        }

        // Bisa ditambah untuk navigate ke detail order/payment dll
        // const orderId = item.dataset.orderId;
        // if (orderId) {
        //     window.location.href = `../../view/admin/OrderAdmin.php?id=${orderId}`;
        // }
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    async markAsRead(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            const response = await fetch(`${this.apiEndpoint}?action=mark-as-read`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                this.updateBadgeCount(result.unread_count);
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    async markAllAsRead() {
        try {
            const response = await fetch(`${this.apiEndpoint}?action=mark-all-as-read`, {
                method: 'POST'
            });

            const result = await response.json();
            if (result.success) {
                this.updateBadgeCount(0);
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    }

    /**
     * Hapus notifikasi
     */
    async deleteNotification(notificationId) {
        try {
            const formData = new FormData();
            formData.append('notification_id', notificationId);

            const response = await fetch(`${this.apiEndpoint}?action=delete`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.success) {
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }

    /**
     * Update badge count
     */
    updateBadgeCount(count) {
        if (this.notificationBadge) {
            if (count > 0) {
                this.notificationBadge.textContent = count > 99 ? '99+' : count;
                this.notificationBadge.classList.remove('hidden');
            } else {
                this.notificationBadge.classList.add('hidden');
            }
        }
    }

    /**
     * Setup auto refresh
     */
    setupAutoRefresh() {
        setInterval(() => {
            // Hanya refresh jika dropdown tertutup
            if (!this.notificationDropdown.classList.contains('hidden')) {
                this.loadNotifications();
            }
        }, this.autoRefreshInterval);
    }
}

// Initialize notification manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const notificationManager = new NotificationManager();

    // Handle close button pada notification items
    document.addEventListener('click', function(e) {
        if (e.target.closest('.close-btn')) {
            const btn = e.target.closest('.close-btn');
            const notificationId = btn.dataset.notificationId;
            notificationManager.deleteNotification(notificationId);
        }
    });

    // Handle "Mark all as read" button jika ada
    const markAllBtn = document.getElementById('mark-all-as-read-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', () => notificationManager.markAllAsRead());
    }
});
