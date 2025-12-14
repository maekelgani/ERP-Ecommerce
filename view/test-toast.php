<?php
require_once __DIR__ . '/../config/config.php';

if (isset($_GET['trigger'])) {
    $type = $_GET['trigger'];
    switch ($type) {
        case 'success':
            $_SESSION['toast_success'] = 'Ini adalah pesan sukses test!';
            break;
        case 'error':
            $_SESSION['toast_error'] = 'Ini adalah pesan error test!';
            break;
        case 'warning':
            $_SESSION['toast_warning'] = 'Ini adalah pesan warning test!';
            break;
        case 'info':
            $_SESSION['toast_info'] = 'Ini adalah pesan info test!';
            break;
    }
    header('Location: test-toast.php');
    exit;
}

$pageTitle = "Test Toast";
include '../components/users/head.php';
?>

<body class="bg-gray-100 min-h-screen p-8">
    <?php include '../components/users/toastNotifications.php'; ?>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Test Toast Notifications</h1>

        <div class="grid grid-cols-2 gap-4">
            <a href="?trigger=success" class="bg-green-500 text-white px-6 py-3 rounded-lg text-center hover:bg-green-600 transition">
                Test Success Toast
            </a>
            <a href="?trigger=error" class="bg-red-500 text-white px-6 py-3 rounded-lg text-center hover:bg-red-600 transition">
                Test Error Toast
            </a>
            <a href="?trigger=warning" class="bg-amber-500 text-white px-6 py-3 rounded-lg text-center hover:bg-amber-600 transition">
                Test Warning Toast
            </a>
            <a href="?trigger=info" class="bg-sky-500 text-white px-6 py-3 rounded-lg text-center hover:bg-sky-600 transition">
                Test Info Toast
            </a>
        </div>

        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Test JavaScript Trigger</h2>
            <button onclick="window.dispatchEvent(new CustomEvent('notify', { detail: { variant: 'success', title: 'JavaScript Test!', message: 'Toast triggered from JavaScript!' } }))"
                class="bg-primary text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
                Trigger Toast via JavaScript
            </button>
        </div>

        <div class="mt-4 text-gray-500 text-sm">
            <p>Session data:</p>
            <pre class="bg-gray-800 text-green-400 p-4 rounded mt-2 overflow-x-auto"><?php print_r($_SESSION); ?></pre>
        </div>

        <a href="users/landingPage.php" class="inline-block mt-6 text-primary hover:underline">
            &larr; Kembali ke Landing Page
        </a>
    </div>
</body>

</html>