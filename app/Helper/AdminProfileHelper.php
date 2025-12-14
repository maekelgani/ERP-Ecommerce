<?php

namespace App\Helper;

class AdminProfileHelper
{
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    private int $maxSize = 5242880; // 5MB
    private string $defaultPhoto = '/assets/img/profil/default-profil.png';

    public function __construct()
    {
        $this->uploadDir = __DIR__ . '/../../uploads/admin/';
        $this->ensureDirectoryExists();
    }

    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function uploadProfileImage(array $file): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'filename' => null,
            'path' => null
        ];

        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $result['message'] = 'Tidak ada file yang diupload';
            return $result;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['message'] = $this->getUploadErrorMessage($file['error']);
            return $result;
        }

        if ($file['size'] > $this->maxSize) {
            $result['message'] = 'Ukuran file terlalu besar. Maksimal 5MB';
            return $result;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedTypes)) {
            $result['message'] = 'Format file tidak diizinkan. Gunakan JPG, PNG, atau WebP';
            return $result;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            $result['message'] = 'Ekstensi file tidak diizinkan';
            return $result;
        }

        if (!$this->isValidImage($file['tmp_name'])) {
            $result['message'] = 'File bukan gambar yang valid';
            return $result;
        }

        $filename = $this->generateSecureFilename($extension);
        $destination = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $result['success'] = true;
            $result['message'] = 'Foto profil berhasil diupload';
            $result['filename'] = $filename;
            $result['path'] = 'uploads/admin/' . $filename;
        } else {
            $result['message'] = 'Gagal menyimpan file';
        }

        return $result;
    }

    public function deleteProfileImage(string $filename): bool
    {
        if (empty($filename) || strpos($filename, 'default') !== false) {
            return false;
        }

        $filepath = $this->uploadDir . basename($filename);
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    private function generateSecureFilename(string $extension): string
    {
        return 'admin_' . date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }

    private function isValidImage(string $filepath): bool
    {
        $imageInfo = @getimagesize($filepath);
        return $imageInfo !== false;
    }

    private function getUploadErrorMessage(int $error): string
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Ukuran file melebihi batas yang diizinkan';
            case UPLOAD_ERR_PARTIAL:
                return 'File hanya terupload sebagian';
            case UPLOAD_ERR_NO_FILE:
                return 'Tidak ada file yang diupload';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Folder temporary tidak ditemukan';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Gagal menulis file ke disk';
            default:
                return 'Terjadi kesalahan saat upload';
        }
    }

    public function getDefaultPhoto(): string
    {
        return $this->defaultPhoto;
    }

    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validateUsername(string $username): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username) === 1;
    }

    public function validatePhone(string $phone): bool
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        return strlen($cleaned) >= 10 && strlen($cleaned) <= 15;
    }

    public function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter';
        }

        if (!preg_match('/[a-zA-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka';
        }

        return $errors;
    }

    public function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
