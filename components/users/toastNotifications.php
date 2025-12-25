<?php
$toastSuccess = $_SESSION['toast_success'] ?? null;
$toastError = $_SESSION['toast_error'] ?? null;
$toastWarning = $_SESSION['toast_warning'] ?? null;
$toastInfo = $_SESSION['toast_info'] ?? null;

unset($_SESSION['toast_success'], $_SESSION['toast_error'], $_SESSION['toast_warning'], $_SESSION['toast_info']);

$hasToast = $toastSuccess || $toastError || $toastWarning || $toastInfo;
?>

<?php if ($hasToast): ?>
    <script>
        console.log('🔔 Toast notification pending:', {
            success: <?= json_encode($toastSuccess) ?>,
            error: <?= json_encode($toastError) ?>,
            warning: <?= json_encode($toastWarning) ?>,
            info: <?= json_encode($toastInfo) ?>
        });
    </script>
<?php endif; ?>

<div x-data="{
        notifications: [],
        displayDuration: 6000,
        soundEffect: false,

        init() {
            console.log('🔔 Alpine Toast Component initialized');
            <?php if ($toastSuccess): ?>
            console.log('🔔 Adding success toast');
            this.addNotification({ variant: 'success', title: 'Berhasil!', message: '<?= addslashes($toastSuccess) ?>' });
            <?php endif; ?>
            <?php if ($toastError): ?>
            console.log('🔔 Adding error toast');
            this.addNotification({ variant: 'danger', title: 'Gagal!', message: '<?= addslashes($toastError) ?>' });
            <?php endif; ?>
            <?php if ($toastWarning): ?>
            console.log('🔔 Adding warning toast');
            this.addNotification({ variant: 'warning', title: 'Perhatian!', message: '<?= addslashes($toastWarning) ?>' });
            <?php endif; ?>
            <?php if ($toastInfo): ?>
            console.log('🔔 Adding info toast');
            this.addNotification({ variant: 'info', title: 'Info', message: '<?= addslashes($toastInfo) ?>' });
            <?php endif; ?>
        },

        addNotification({ variant = 'info', title = null, message = null }) {
            const id = Date.now()
            const notification = { id, variant, title, message }

            if (this.notifications.length >= 20) {
                this.notifications.splice(0, this.notifications.length - 19)
            }

            this.notifications.push(notification)

            if (this.soundEffect) {
                const notificationSound = new Audio('https://res.cloudinary.com/ds8pgw1pf/video/upload/v1728571480/penguinui/component-assets/sounds/ding.mp3')
                notificationSound.play().catch((error) => {
                    console.error('Error playing the sound:', error)
                })
            }
        },
        removeNotification(id) {
            setTimeout(() => {
                this.notifications = this.notifications.filter(
                    (notification) => notification.id !== id,
                )
            }, 400);
        },
    }"
    x-on:notify.window="addNotification({
        variant: $event.detail.variant,
        title: $event.detail.title,
        message: $event.detail.message,
    })">

    <div x-on:mouseenter="$dispatch('pause-auto-dismiss')"
        x-on:mouseleave="$dispatch('resume-auto-dismiss')"
        class="group pointer-events-none fixed inset-x-0 top-0 z-[9999] flex max-w-full flex-col gap-2 bg-transparent px-4 py-4 sm:inset-x-auto sm:right-0 sm:top-auto sm:bottom-0 sm:max-w-sm sm:px-6 sm:py-6">

        <template x-for="(notification, index) in notifications" x-bind:key="notification.id">
            <div>
                <!-- Info Notification -->
                <template x-if="notification.variant === 'info'">
                    <div x-data="{ isVisible: false, timeout: null }"
                        x-cloak
                        x-show="isVisible"
                        class="pointer-events-auto relative overflow-hidden rounded-lg border border-sky-400 bg-white shadow-lg"
                        role="alert"
                        x-on:pause-auto-dismiss.window="clearTimeout(timeout)"
                        x-on:resume-auto-dismiss.window="timeout = setTimeout(() => {(isVisible = false), removeNotification(notification.id) }, displayDuration)"
                        x-init="$nextTick(() => { isVisible = true }), (timeout = setTimeout(() => { isVisible = false, removeNotification(notification.id)}, displayDuration))"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:enter-start="translate-y-8 opacity-0"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-end="translate-x-24 opacity-0"
                        x-transition:leave-start="translate-x-0 opacity-100">
                        <div class="flex w-full items-center gap-3 bg-sky-50 rounded-lg p-4 transition-all duration-300">
                            <div class="rounded-full bg-sky-100 p-1 text-sky-500 flex-shrink-0" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1 flex-1 min-w-0">
                                <h3 x-cloak x-show="notification.title" class="text-sm font-semibold text-sky-600" x-text="notification.title"></h3>
                                <p x-cloak x-show="notification.message" class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2" aria-label="dismiss notification" x-on:click="(isVisible = false), removeNotification(notification.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="h-1 w-full bg-sky-200 overflow-hidden">
                            <div class="h-full bg-sky-500 animate-progress-bar" :style="`animation-duration: ${displayDuration}ms`"></div>
                        </div>
                    </div>
                </template>

                <!-- Success Notification -->
                <template x-if="notification.variant === 'success'">
                    <div x-data="{ isVisible: false, timeout: null }"
                        x-cloak
                        x-show="isVisible"
                        class="pointer-events-auto relative overflow-hidden rounded-lg border border-green-400 bg-white shadow-lg"
                        role="alert"
                        x-on:pause-auto-dismiss.window="clearTimeout(timeout)"
                        x-on:resume-auto-dismiss.window="timeout = setTimeout(() => {(isVisible = false), removeNotification(notification.id) }, displayDuration)"
                        x-init="$nextTick(() => { isVisible = true }), (timeout = setTimeout(() => { isVisible = false, removeNotification(notification.id)}, displayDuration))"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:enter-start="translate-y-8 opacity-0"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-end="translate-x-24 opacity-0"
                        x-transition:leave-start="translate-x-0 opacity-100">
                        <div class="flex w-full items-center gap-3 bg-green-50 rounded-lg p-4 transition-all duration-300">
                            <div class="rounded-full bg-green-100 p-1 text-green-500 flex-shrink-0" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1 flex-1 min-w-0">
                                <h3 x-cloak x-show="notification.title" class="text-sm font-semibold text-green-600" x-text="notification.title"></h3>
                                <p x-cloak x-show="notification.message" class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2" aria-label="dismiss notification" x-on:click="(isVisible = false), removeNotification(notification.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="h-1 w-full bg-green-200 overflow-hidden">
                            <div class="h-full bg-green-500 animate-progress-bar" :style="`animation-duration: ${displayDuration}ms`"></div>
                        </div>
                    </div>
                </template>

                <!-- Warning Notification -->
                <template x-if="notification.variant === 'warning'">
                    <div x-data="{ isVisible: false, timeout: null }"
                        x-cloak
                        x-show="isVisible"
                        class="pointer-events-auto relative overflow-hidden rounded-lg border border-amber-400 bg-white shadow-lg"
                        role="alert"
                        x-on:pause-auto-dismiss.window="clearTimeout(timeout)"
                        x-on:resume-auto-dismiss.window="timeout = setTimeout(() => {(isVisible = false), removeNotification(notification.id) }, displayDuration)"
                        x-init="$nextTick(() => { isVisible = true }), (timeout = setTimeout(() => { isVisible = false, removeNotification(notification.id)}, displayDuration))"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:enter-start="translate-y-8 opacity-0"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-end="translate-x-24 opacity-0"
                        x-transition:leave-start="translate-x-0 opacity-100">
                        <div class="flex w-full items-center gap-3 bg-amber-50 rounded-lg p-4 transition-all duration-300">
                            <div class="rounded-full bg-amber-100 p-1 text-amber-500 flex-shrink-0" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1 flex-1 min-w-0">
                                <h3 x-cloak x-show="notification.title" class="text-sm font-semibold text-amber-600" x-text="notification.title"></h3>
                                <p x-cloak x-show="notification.message" class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2" aria-label="dismiss notification" x-on:click="(isVisible = false), removeNotification(notification.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="h-1 w-full bg-amber-200 overflow-hidden">
                            <div class="h-full bg-amber-500 animate-progress-bar" :style="`animation-duration: ${displayDuration}ms`"></div>
                        </div>
                    </div>
                </template>

                <!-- Danger Notification -->
                <template x-if="notification.variant === 'danger'">
                    <div x-data="{ isVisible: false, timeout: null }"
                        x-cloak
                        x-show="isVisible"
                        class="pointer-events-auto relative overflow-hidden rounded-lg border border-red-400 bg-white shadow-lg"
                        role="alert"
                        x-on:pause-auto-dismiss.window="clearTimeout(timeout)"
                        x-on:resume-auto-dismiss.window="timeout = setTimeout(() => {(isVisible = false), removeNotification(notification.id) }, displayDuration)"
                        x-init="$nextTick(() => { isVisible = true }), (timeout = setTimeout(() => { isVisible = false, removeNotification(notification.id)}, displayDuration))"
                        x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-end="translate-y-0 opacity-100"
                        x-transition:enter-start="translate-y-8 opacity-0"
                        x-transition:leave="transition duration-300 ease-in"
                        x-transition:leave-end="translate-x-24 opacity-0"
                        x-transition:leave-start="translate-x-0 opacity-100">
                        <div class="flex w-full items-center gap-3 bg-red-50 rounded-lg p-4 transition-all duration-300">
                            <div class="rounded-full bg-red-100 p-1 text-red-500 flex-shrink-0" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1 flex-1 min-w-0">
                                <h3 x-cloak x-show="notification.title" class="text-sm font-semibold text-red-600" x-text="notification.title"></h3>
                                <p x-cloak x-show="notification.message" class="text-sm text-gray-600 line-clamp-2" x-text="notification.message"></p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600 flex-shrink-0 ml-2" aria-label="dismiss notification" x-on:click="(isVisible = false), removeNotification(notification.id)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2" class="w-5 h-5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="h-1 w-full bg-red-200 overflow-hidden">
                            <div class="h-full bg-red-500 animate-progress-bar" :style="`animation-duration: ${displayDuration}ms`"></div>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>
</div>

<style>
    @keyframes progressBar {
        from {
            width: 100%;
        }

        to {
            width: 0%;
        }
    }

    .animate-progress-bar {
        animation: progressBar linear forwards;
    }
</style>