<?php

/**
 * Application Configuration
 * Load environment variables dan initialize necessary services
 */

// Start output buffering to prevent headers already sent errors
ob_start();

// Load environment variables
require_once __DIR__ . '/env-loader.php';
loadEnv(__DIR__ . '/../.env');

// Include Database Classes
require_once __DIR__ . '/../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../app/Database/BaseRepository.php';
require_once __DIR__ . '/../app/Repository/ProductRepository.php';

// Include Auth Classes
require_once __DIR__ . '/../app/Auth/CustomerRepository.php';
require_once __DIR__ . '/../app/Auth/AdminRepository.php';
require_once __DIR__ . '/../app/Auth/GoogleOAuthHandler.php';
require_once __DIR__ . '/../app/Auth/SessionManager.php';
require_once __DIR__ . '/../app/Auth/AuthMiddleware.php';
require_once __DIR__ . '/../app/Auth/ValidationHelper.php';
require_once __DIR__ . '/../app/Auth/PermissionHelper.php';
require_once __DIR__ . '/../app/Auth/CustomerAuthMiddleware.php';
require_once __DIR__ . '/../app/Repository/RoleRepository.php';
require_once __DIR__ . '/../app/Repository/CategoryRepository.php';
require_once __DIR__ . '/../app/Repository/AnalyticsRepository.php';
require_once __DIR__ . '/../app/Repository/DiskonRepository.php';
require_once __DIR__ . '/../app/Repository/PromoCampaignRepository.php';
require_once __DIR__ . '/../app/Repository/CampaignProductRepository.php';
require_once __DIR__ . '/../app/Repository/VoucherRepository.php';
require_once __DIR__ . '/../app/Repository/VoucherUsageRepository.php';
require_once __DIR__ . '/../app/Repository/StoreLocationRepository.php';
require_once __DIR__ . '/../app/Helper/AdminProfileHelper.php';
require_once __DIR__ . '/../app/Helper/CategoryLandingHelper.php';
require_once __DIR__ . '/../app/Helper/ProductLandingHelper.php';
require_once __DIR__ . '/../app/Helper/BrandLandingHelper.php';
require_once __DIR__ . '/../app/Repository/BlogCategoryRepository.php';
require_once __DIR__ . '/../app/Repository/BlogPostRepository.php';

// Google OAuth Configuration
define('GOOGLE_CLIENT_ID', $_ENV['GOOGLE_CLIENT_ID'] ?? '');
define('GOOGLE_CLIENT_SECRET', $_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
define('GOOGLE_REDIRECT_URI', $_ENV['GOOGLE_REDIRECT_URI'] ?? '');

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USERNAME', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'nanocomp_db');

// Application Configuration
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', $_ENV['APP_DEBUG'] === 'true');
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Nano Komputer');

// Session Configuration
define('SESSION_LIFETIME', 86400); // 24 hours
define('REMEMBER_ME_LIFETIME', 2592000); // 30 days

if (session_status() === PHP_SESSION_NONE) {
    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Security headers
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
}

// Error handling (development only)
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Validate required variables
if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
    error_log('Warning: Google OAuth credentials not configured in .env file!');
}

// Database connection test (optional) - only test if not doing auth redirect
// This prevents headers already sent errors when redirecting
if (!isset($_GET['session']) && !isset($_GET['logout']) && !isset($_GET['login'])) {
    try {
        $db = \App\Database\DatabaseConnection::getInstance();
    } catch (\Exception $e) {
        error_log('Database Connection Error: ' . $e->getMessage());
        // Don't die here, let the application handle the error gracefully
    }
}
