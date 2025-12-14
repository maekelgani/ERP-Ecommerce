<?php

/**
 * Google OAuth Redirect Handler
 * Menangani callback dari Google OAuth dan membuat/update customer
 * Mendukung pengambilan foto profil dari Google
 */

require_once __DIR__ . '/../config/config.php';

use App\Auth\CustomerRepository;
use App\Auth\GoogleOAuthHandler;
use App\Auth\SessionManager;

if (isset($_GET['error'])) {
    $errorMsg = htmlspecialchars($_GET['error']);
    $errorDesc = isset($_GET['error_description']) ? htmlspecialchars($_GET['error_description']) : '';
    $_SESSION['oauth_error'] = "Google OAuth Error: $errorMsg. $errorDesc";
    header('Location: login.php');
    exit;
}

if (!isset($_GET['code'])) {
    $_SESSION['oauth_error'] = 'Kode otorisasi tidak diterima dari Google.';
    header('Location: login.php');
    exit;
}

$customerRepo = new CustomerRepository();
$googleOAuth = new GoogleOAuthHandler(GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI);

$authCode = $_GET['code'];

try {
    $tokenData = $googleOAuth->getAccessToken($authCode);

    if (!$tokenData || !isset($tokenData['access_token'])) {
        throw new \Exception('Gagal mendapatkan access token dari Google.');
    }

    $accessToken = $tokenData['access_token'];

    $googleUserData = $googleOAuth->getCompleteUserData($accessToken);

    if (!$googleUserData) {
        throw new \Exception('Gagal mengambil informasi pengguna dari Google.');
    }

    if (!isset($googleUserData['id']) || !isset($googleUserData['email'])) {
        throw new \Exception('Data pengguna dari Google tidak lengkap.');
    }

    $result = $customerRepo->upsertGoogleCustomer([
        'id' => $googleUserData['id'],
        'email' => $googleUserData['email'],
        'name' => $googleUserData['name'],
        'profile_image' => $googleUserData['profile_image'],
        'phone' => $googleUserData['phone']
    ]);

    $customerId = $result['id'];
    $isNewUser = $result['is_new'] ?? false;
    $needsPhone = $result['needs_phone'] ?? false;
    $wasLinked = $result['linked'] ?? false;

    $customer = $customerRepo->getById($customerId);

    if (!$customer) {
        throw new \Exception('Gagal mengambil data customer.');
    }

    if (!$customer['is_active']) {
        $_SESSION['deactivated_account'] = true;
        $_SESSION['oauth_error'] = 'Akun Anda telah dinonaktifkan. Anda tidak dapat login sampai akun diaktifkan kembali. Hubungi tim support kami di support@nanokomputer.com atau WhatsApp 0812-3456-7890 untuk informasi lebih lanjut.';
        header('Location: login.php');
        exit;
    }

    SessionManager::createCustomerSession($customer, true);

    SessionManager::setCustomerRememberMe($customerId, $customerRepo);

    if ($isNewUser) {
        $_SESSION['google_welcome'] = true;
        $_SESSION['google_welcome_name'] = $googleUserData['name'];
        $_SESSION['toast_success'] = 'Selamat datang, ' . htmlspecialchars($googleUserData['name']) . '! Akun Anda berhasil dibuat.';
    } elseif ($wasLinked) {
        $_SESSION['google_linked'] = true;
        $_SESSION['toast_success'] = 'Akun Google berhasil dihubungkan dengan akun Anda!';
    } else {
        $_SESSION['toast_success'] = 'Selamat datang kembali, ' . htmlspecialchars($googleUserData['name']) . '!';
    }

    if ($needsPhone) {
        $_SESSION['needs_phone_completion'] = true;
        header('Location: users/completeProfile.php');
        exit;
    }

    header('Location: users/landingPage.php');
    exit;
} catch (\Exception $e) {
    error_log('Google OAuth Error: ' . $e->getMessage());

    $_SESSION['oauth_error'] = APP_DEBUG
        ? 'Error: ' . htmlspecialchars($e->getMessage())
        : 'Terjadi kesalahan saat autentikasi dengan Google. Silakan coba lagi.';

    header('Location: login.php');
    exit;
}
