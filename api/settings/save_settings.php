<?php
// untuk debugging doang, tolong di comment/hapus kalo ini kelupaan
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/SiteSetting.php';

use App\Database\DatabaseConnection;
use App\Services\SiteSetting;
// use App\Auth\AuthMiddleware;

// AuthMiddleware::requireAdminLoginFromView();


/**
 * Helper Upload Gambar
 * @param array $file File dari $_FILES
 * @param string $targetName Folder tujuan dalam /assets/img/
 */
function uploadImage($file, $targetName) {
    $allowedTypes = ['jpg', 'jpeg', 'png', 'svg', 'ico', 'webp'];
    $fileName     = $file['name'];
    $fileTmp      = $file['tmp_name'];
    $fileSize     = $file['size'];
    $fileExt      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExt, $allowedTypes)) return false;
    if ($fileSize > 2097152) return false; // Max 2MB

    $destinationPath = __DIR__ . '/../../assets/img/';
    
    // Pastikan folder ada
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    $existingFiles = glob($destinationPath . $targetName . '.*');
    foreach ($existingFiles as $existingFile) {
        if (is_file($existingFile)) {
            unlink($existingFile); // Hapus file lama
        }
    }

    $newFileName = $targetName . '.' . $fileExt;

    if (move_uploaded_file($fileTmp, $destinationPath . $newFileName)) {
        return 'assets/img/' . $newFileName;
    }
    
    return false;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $dbInstance = DatabaseConnection::getInstance();
    $db = $dbInstance->getConnection();
    
    $setting = new SiteSetting($db);

    $settingsToSave = [
        'site_title'       => $_POST['site_title'] ?? '',
        'site_description' => $_POST['site_description'] ?? '',
        'contact_email'    => $_POST['contact_email'] ?? '',
        'contact_phone'    => $_POST['contact_phone'] ?? '',
        'facebook_url'     => $_POST['facebook_url'] ?? '',
        'instagram_url'    => $_POST['instagram_url'] ?? '',
        'tiktok_url'       => $_POST['tiktok_url'] ?? '',
        'youtube_url'      => $_POST['youtube_url'] ?? '',
        'x_url'            => $_POST['x_url'] ?? '',
    ];

    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        // Parameter kedua adalah nama file yang diinginkan: 'mainicon'
        $path = uploadImage($_FILES['site_logo'], 'mainicon');
        
        if ($path) {
            $settingsToSave['site_logo'] = $path;
        } else {
            throw new Exception("Gagal upload Logo. Pastikan format gambar valid (Max 2MB).");
        }
    }

    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
        // Parameter kedua adalah nama file yang diinginkan: 'favicon'
        $path = uploadImage($_FILES['site_favicon'], 'favicon');
        
        if ($path) {
            $settingsToSave['site_favicon'] = $path;
        } else {
            throw new Exception("Gagal upload Favicon. Pastikan format gambar valid (Max 2MB).");
        }
    }

    if ($setting->updateBatchSettings($settingsToSave)) {
        echo json_encode(['status' => 'success', 'message' => 'Pengaturan berhasil disimpan']);
    } else {
        throw new Exception('Gagal menyimpan perubahan ke database');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}


// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $database = new DatabaseConnection();
//     $db = $database->getConnection();
//     $setting = new SiteSetting($db);

//     // Ambil data text
// $settingsToSave = [
//     'site_title'       => $_POST['site_title'] ?? '',
//     'site_description' => $_POST['site_description'] ?? '',
//     'contact_email'    => $_POST['contact_email'] ?? '',
//     'contact_phone'    => $_POST['contact_phone'] ?? '',
//     'facebook_url'     => $_POST['facebook_url'] ?? '',
//     'instagram_url'    => $_POST['instagram_url'] ?? '',
//     'tiktok_url'       => $_POST['tiktok_url'] ?? '',
//     'x_url'            => $_POST['x_url'] ?? '',
// ];

// // 2. Handle Upload (Sama seperti sebelumnya, tapi simpan path-nya ke array)
// if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
//     $path = uploadImage($_FILES['site_logo'], 'logos');
//     if ($path) $settingsToSave['site_logo'] = $path; // Masukkan ke array update
// }

// if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
//     $path = uploadImage($_FILES['site_favicon'], 'favicons');
//     if ($path) $settingsToSave['site_favicon'] = $path;
// }

// // 3. Simpan Batch (Sekaligus)
// if ($setting->updateBatchSettings($settingsToSave)) {
//     echo json_encode(['status' => 'success', 'message' => 'Pengaturan berhasil disimpan']);
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan']);
// }
// }