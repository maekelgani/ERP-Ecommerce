<?php

// Di file login handler Anda (misal: GoogleLoginController.php)

use App\Auth\GoogleOAuthHandler;
use App\Auth\CustomerRepository;

class GoogleLoginController
{
    private GoogleOAuthHandler $oauth;
    private CustomerRepository $customerRepo;

    public function __construct()
    {
        $this->oauth = new GoogleOAuthHandler(
            $_ENV['GOOGLE_CLIENT_ID'],
            $_ENV['GOOGLE_CLIENT_SECRET'],
            $_ENV['GOOGLE_REDIRECT_URI']
        );
        $this->customerRepo = new CustomerRepository();
    }

    /**
     * Handle callback dari Google
     */
    public function handleCallback()
    {
        $code = $_GET['code'] ?? null;

        if (!$code) {
            return $this->redirectWithError('Invalid authorization code');
        }

        // 1. Tukar code dengan access token
        $tokenData = $this->oauth->getAccessToken($code);

        if (!$tokenData) {
            return $this->redirectWithError('Failed to get access token');
        }

        $accessToken = $tokenData['access_token'];

        // 2. Dapatkan user info dari Google
        $googleUser = $this->oauth->getUserInfo($accessToken);

        if (!$googleUser) {
            return $this->redirectWithError('Failed to get user info');
        }

        // 3. Download foto profil (jika ada)
        $profileImagePath = null;
        if (isset($googleUser['picture'])) {
            // Generate temporary customer ID untuk nama file
            $tempId = uniqid('temp_');
            $profileImagePath = $this->oauth->downloadProfileImage(
                $googleUser['picture'],
                $tempId
            );
        }

        // 4. Siapkan data untuk disimpan
        $customerData = [
            'id' => $googleUser['id'],
            'name' => $googleUser['name'] ?? '',
            'email' => $googleUser['email'] ?? '',
            'phone_number' => $googleUser['phone_number'] ?? null,
            'profile_image' => $profileImagePath
        ];

        // 5. Simpan/update customer di database
        $customerId = $this->customerRepo->upsertGoogleCustomer($customerData);

        // 6. Jika ada foto profil dan customer baru dibuat, rename file
        if ($profileImagePath && strpos($profileImagePath, 'temp_') !== false) {
            $this->renameProfileImage($profileImagePath, $customerId);
        }

        // 7. Set session
        $_SESSION['user_id'] = $customerId;
        $_SESSION['login_type'] = 'google';

        // 8. Redirect ke dashboard
        header('Location: /dashboard');
        exit;
    }

    /**
     * Rename foto profil setelah customer ID diketahui
     */
    private function renameProfileImage(string $oldPath, int $customerId): void
    {
        $uploadDir = __DIR__ . '/../../public/';
        $oldFile = $uploadDir . $oldPath;

        if (file_exists($oldFile)) {
            $extension = pathinfo($oldFile, PATHINFO_EXTENSION);
            $newFilename = 'profile_' . $customerId . '_' . time() . '.' . $extension;
            $newPath = 'uploads/profiles/' . $newFilename;
            $newFile = $uploadDir . $newPath;

            if (rename($oldFile, $newFile)) {
                // Update database dengan path baru
                $this->customerRepo->updateProfileImage($customerId, $newPath);
            }
        }
    }

    private function redirectWithError(string $message): void
    {
        $_SESSION['error'] = $message;
        header('Location: /login');
        exit;
    }
}
