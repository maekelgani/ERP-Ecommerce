<?php

namespace App\Auth;

/**
 * Google OAuth Handler - Menangani Google Authentication Flow
 * Mendukung pengambilan profil, foto, dan nomor telepon dari Google
 */
class GoogleOAuthHandler
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private const GOOGLE_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const GOOGLE_TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    private const GOOGLE_USERINFO_URL = 'https://www.googleapis.com/oauth2/v2/userinfo';
    private const GOOGLE_PEOPLE_API_URL = 'https://people.googleapis.com/v1/people/me';

    public function __construct(string $clientId, string $clientSecret, string $redirectUri)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Generate Google OAuth authorization URL
     * Hanya menggunakan basic scopes (tidak perlu verifikasi Google)
     */
    public function getAuthorizationUrl(string $state = ''): string
    {
        $scopes = [
            'openid',
            'profile',
            'email'
        ];

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $scopes),
            'state' => $state ?: uniqid('oauth_', true),
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];

        return self::GOOGLE_AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function getAccessToken(string $code): ?array
    {
        $params = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $response = $this->makeRequest(self::GOOGLE_TOKEN_URL, $params, true);

        if ($response && isset($response['access_token'])) {
            return $response;
        }

        return null;
    }

    /**
     * Get user info dari Google using access token
     * Mengembalikan data termasuk foto profil (picture)
     */
    public function getUserInfo(string $accessToken): ?array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer $accessToken\r\n",
                'timeout' => 10
            ]
        ]);

        $response = @file_get_contents(self::GOOGLE_USERINFO_URL, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['id'], $data['email'])) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Get phone number dari Google People API
     * Memerlukan scope: https://www.googleapis.com/auth/user.phonenumbers.read
     * 
     * @param string $accessToken Access token dari Google
     * @return string|null Nomor telepon atau null jika tidak tersedia
     */
    public function getPhoneNumber(string $accessToken): ?string
    {
        $url = self::GOOGLE_PEOPLE_API_URL . '?personFields=phoneNumbers';

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: Bearer $accessToken\r\n",
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response) {
            $data = json_decode($response, true);

            if (isset($data['phoneNumbers']) && is_array($data['phoneNumbers']) && count($data['phoneNumbers']) > 0) {
                $phone = $data['phoneNumbers'][0]['value'] ?? null;

                if ($phone) {
                    return $this->normalizePhoneNumber($phone);
                }
            }
        }

        return null;
    }

    /**
     * Normalize phone number - hapus karakter non-digit dan format
     */
    private function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (strpos($phone, '+62') === 0) {
            $phone = '0' . substr($phone, 3);
        } elseif (strpos($phone, '62') === 0 && strlen($phone) > 10) {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }

    /**
     * Download dan simpan foto profil Google ke server lokal
     * 
     * @param string $pictureUrl URL foto profil dari Google
     * @param string $uploadDir Direktori upload
     * @return string|null Path file yang tersimpan atau null jika gagal
     */
    public function downloadProfilePicture(string $pictureUrl, string $uploadDir = 'uploads/customers/'): ?string
    {
        if (empty($pictureUrl)) {
            return null;
        }

        $pictureUrl = str_replace('=s96-c', '=s400-c', $pictureUrl);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 15,
                'header' => "User-Agent: Mozilla/5.0\r\n"
            ]
        ]);

        $imageContent = @file_get_contents($pictureUrl, false, $context);

        if (!$imageContent) {
            return null;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageContent);

        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        $extension = $extensions[$mimeType] ?? 'jpg';

        $fileName = 'google_profile_' . uniqid() . '_' . time() . '.' . $extension;

        $baseDir = dirname(dirname(__DIR__));
        $fullUploadDir = $baseDir . '/' . $uploadDir;

        if (!is_dir($fullUploadDir)) {
            mkdir($fullUploadDir, 0755, true);
        }

        $filePath = $fullUploadDir . $fileName;

        if (file_put_contents($filePath, $imageContent)) {
            return $fileName;
        }

        return null;
    }

    /**
     * Get complete user data (info + profile picture path)
     * Menggabungkan semua data yang bisa diambil dari Google
     * Note: Phone number tidak diambil dari Google (memerlukan verifikasi)
     *       User akan diminta mengisi phone number manual di completeProfile.php
     */
    public function getCompleteUserData(string $accessToken, string $uploadDir = 'uploads/customers/'): ?array
    {
        $userInfo = $this->getUserInfo($accessToken);

        if (!$userInfo) {
            return null;
        }

        $profileImagePath = null;
        if (!empty($userInfo['picture'])) {
            $profileImagePath = $this->downloadProfilePicture($userInfo['picture'], $uploadDir);
        }

        return [
            'id' => $userInfo['id'],
            'email' => $userInfo['email'],
            'name' => $userInfo['name'] ?? $userInfo['email'],
            'given_name' => $userInfo['given_name'] ?? null,
            'family_name' => $userInfo['family_name'] ?? null,
            'picture_url' => $userInfo['picture'] ?? null,
            'profile_image' => $profileImagePath,
            'phone' => null,
            'verified_email' => $userInfo['verified_email'] ?? false
        ];
    }

    /**
     * Make HTTP request
     */
    private function makeRequest(string $url, array $data = [], bool $isPost = false): ?array
    {
        $options = [
            'http' => [
                'method' => $isPost ? 'POST' : 'GET',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query($data),
                'timeout' => 10
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response) {
            return json_decode($response, true);
        }

        return null;
    }
}
