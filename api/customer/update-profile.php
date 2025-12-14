<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/config.php';

use App\Auth\CustomerAuthMiddleware;
use App\Auth\SessionManager;

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    if (!CustomerAuthMiddleware::isLoggedIn()) {
        $response['require_login'] = true;
        throw new Exception('Silakan login terlebih dahulu');
    }

    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $nama = trim($input['nama'] ?? $input['nama_lengkap'] ?? '');
        $email = trim($input['email'] ?? '');
        $noTelp = trim($input['no_telp'] ?? $input['no_telepon'] ?? '');
    } else {
        $nama = trim($_POST['nama'] ?? $_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $noTelp = trim($_POST['no_telp'] ?? $_POST['no_telepon'] ?? '');
    }

    if (empty($nama)) {
        throw new Exception('Nama lengkap tidak boleh kosong');
    }

    if (empty($email)) {
        throw new Exception('Email tidak boleh kosong');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid');
    }

    if (!empty($noTelp) && !preg_match('/^[0-9]{10,15}$/', $noTelp)) {
        throw new Exception('Format nomor telepon tidak valid (10-15 digit angka)');
    }

    $customerId = CustomerAuthMiddleware::getCustomerId();
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    $stmtCheck = $db->prepare("SELECT id_customer FROM customers WHERE email = :email AND id_customer != :id");
    $stmtCheck->execute([':email' => $email, ':id' => $customerId]);
    if ($stmtCheck->fetch()) {
        throw new Exception('Email sudah digunakan oleh akun lain');
    }

    if (!empty($noTelp)) {
        $stmtCheckPhone = $db->prepare("SELECT id_customer FROM customers WHERE no_telp = :phone AND id_customer != :id");
        $stmtCheckPhone->execute([':phone' => $noTelp, ':id' => $customerId]);
        if ($stmtCheckPhone->fetch()) {
            throw new Exception('Nomor telepon sudah digunakan oleh akun lain');
        }
    }

    $photoFilename = null;
    $deletePhoto = false;

    if (strpos($contentType, 'application/json') !== false) {
        $deletePhoto = !empty($input['delete_photo']);
    } else {
        $deletePhoto = !empty($_POST['delete_photo']) && $_POST['delete_photo'] === 'true';
    }

    if ($deletePhoto) {
        $stmt = $db->prepare("SELECT profile_image FROM customers WHERE id_customer = :id");
        $stmt->execute([':id' => $customerId]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentData && !empty($currentData['profile_image'])) {
            $uploadDir = __DIR__ . '/../../uploads/customers/';
            $oldFile = $uploadDir . $currentData['profile_image'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $stmtUpdate = $db->prepare("
            UPDATE customers 
            SET nama_lengkap = :nama, 
                email = :email, 
                no_telp = :no_telp,
                profile_image = NULL,
                updated_at = NOW()
            WHERE id_customer = :id
        ");

        $stmtUpdate->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':no_telp' => $noTelp ?: null,
            ':id' => $customerId
        ]);

        $_SESSION['name'] = $nama;
        $_SESSION['email'] = $email;

        $response['success'] = true;
        $response['message'] = 'Profil berhasil diperbarui dan foto dihapus';
        $response['photo_deleted'] = true;
        $response['data'] = [
            'nama_lengkap' => $nama,
            'email' => $email,
            'no_telp' => $noTelp
        ];

        echo json_encode($response);
        exit;
    }

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Format file tidak valid. Gunakan JPG, PNG, atau GIF.');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('File bukan gambar yang valid');
        }

        if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
            throw new Exception('Resolusi gambar terlalu kecil. Minimal 100x100 piksel.');
        }

        $uploadDir = __DIR__ . '/../../uploads/customers/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $stmt = $db->prepare("SELECT profile_image FROM customers WHERE id_customer = :id");
        $stmt->execute([':id' => $customerId]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentData && !empty($currentData['profile_image'])) {
            $oldFile = $uploadDir . $currentData['profile_image'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => 'jpg'
        };

        $photoFilename = 'customer_' . $customerId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destination = $uploadDir . $photoFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Gagal menyimpan file foto');
        }
    }

    if ($photoFilename) {
        $stmtUpdate = $db->prepare("
            UPDATE customers 
            SET nama_lengkap = :nama, 
                email = :email, 
                no_telp = :no_telp,
                profile_image = :photo,
                updated_at = NOW()
            WHERE id_customer = :id
        ");

        $stmtUpdate->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':no_telp' => $noTelp ?: null,
            ':photo' => $photoFilename,
            ':id' => $customerId
        ]);
    } else {
        $stmtUpdate = $db->prepare("
            UPDATE customers 
            SET nama_lengkap = :nama, 
                email = :email, 
                no_telp = :no_telp,
                updated_at = NOW()
            WHERE id_customer = :id
        ");

        $stmtUpdate->execute([
            ':nama' => $nama,
            ':email' => $email,
            ':no_telp' => $noTelp ?: null,
            ':id' => $customerId
        ]);
    }

    $_SESSION['name'] = $nama;
    $_SESSION['email'] = $email;

    $response['success'] = true;
    $response['message'] = 'Profil berhasil diperbarui';
    $response['data'] = [
        'nama_lengkap' => $nama,
        'email' => $email,
        'no_telp' => $noTelp
    ];

    if ($photoFilename) {
        $response['photo_url'] = '../../uploads/customers/' . $photoFilename;
        $response['filename'] = $photoFilename;
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
