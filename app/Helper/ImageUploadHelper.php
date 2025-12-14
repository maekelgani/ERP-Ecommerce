<?php

namespace App\Helper;

class ImageUploadHelper
{
    private string $uploadDir;
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private int $maxSize = 5242880; // 5MB

    public function __construct(string $uploadDir = 'uploads/products/')
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';
        $this->ensureDirectoryExists();
    }

    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(array $file): array
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
            $result['message'] = 'Format file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WebP';
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
            $result['message'] = 'File berhasil diupload';
            $result['filename'] = $filename;
            $result['path'] = $destination;
        } else {
            $result['message'] = 'Gagal menyimpan file';
        }

        return $result;
    }

    public function delete(string $filename): bool
    {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    private function generateSecureFilename(string $extension): string
    {
        return 'product_' . date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
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

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    public function setMaxSize(int $bytes): void
    {
        $this->maxSize = $bytes;
    }
}
