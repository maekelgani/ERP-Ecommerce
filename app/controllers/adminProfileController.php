<?php
require_once __DIR__ . '/../../config/config.php';

use App\Auth\AdminRepository;
use App\Auth\SessionManager;
use App\Helper\AdminProfileHelper;

// Ensure session is started before any other operations
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set CORS headers BEFORE any output
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));

// Disable error display, log to file instead
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

try {
    // Validate session
    if (!SessionManager::isAdminLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Sesi tidak valid. Silakan login kembali.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $adminRepo = new AdminRepository();
    $profileHelper = new AdminProfileHelper();
    $currentAdmin = SessionManager::getCurrentAdmin();

    if (!$currentAdmin || !isset($currentAdmin['id_admin'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Sesi tidak valid'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $adminId = (int) $currentAdmin['id_admin'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'get_profile':
            handleGetProfile($adminRepo, $adminId);
            break;
        case 'update_profile':
            handleUpdateProfile($adminRepo, $profileHelper, $adminId);
            break;
        case 'change_password':
            handleChangePassword($adminRepo, $profileHelper, $adminId);
            break;
        case 'upload_photo':
            handleUploadPhoto($adminRepo, $profileHelper, $adminId);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Aksi tidak valid'], JSON_UNESCAPED_UNICODE);
            exit();
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

function handleGetProfile($adminRepo, $adminId)
{
    try {
        $admin = $adminRepo->getAdminWithPhoto($adminId);

        if ($admin) {
            unset($admin['password_hash']);
            unset($admin['remember_token']);
            unset($admin['remember_expires']);
            echo json_encode(['success' => true, 'data' => $admin], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Data admin tidak ditemukan'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

function handleUpdateProfile($adminRepo, $profileHelper, $adminId)
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Validate input
        $namaLengkap = $profileHelper->sanitizeInput($_POST['nama_lengkap'] ?? '');
        $email = $profileHelper->sanitizeInput($_POST['email'] ?? '');
        $username = $profileHelper->sanitizeInput($_POST['username'] ?? '');
        $phone = $profileHelper->sanitizeInput($_POST['phone'] ?? '');

        $errors = [];

        if (empty($namaLengkap)) {
            $errors[] = 'Nama lengkap wajib diisi';
        }

        if (empty($email)) {
            $errors[] = 'Email wajib diisi';
        } elseif (!$profileHelper->validateEmail($email)) {
            $errors[] = 'Format email tidak valid';
        } elseif ($adminRepo->emailExistsExcept($email, $adminId)) {
            $errors[] = 'Email sudah digunakan oleh admin lain';
        }

        if (!empty($username)) {
            if (!$profileHelper->validateUsername($username)) {
                $errors[] = 'Username hanya boleh huruf, angka, dan underscore (3-30 karakter)';
            } elseif ($adminRepo->usernameExists($username, $adminId)) {
                $errors[] = 'Username sudah digunakan';
            }
        }

        if (!empty($phone) && !$profileHelper->validatePhone($phone)) {
            $errors[] = 'Format nomor telepon tidak valid';
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Prepare update data
        $updateData = [
            'nama_lengkap' => $namaLengkap,
            'email' => $email,
            'username' => !empty($username) ? $username : null,
            'phone' => !empty($phone) ? $phone : null
        ];

        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $oldAdmin = $adminRepo->getById($adminId);
            $uploadResult = $profileHelper->uploadProfileImage($_FILES['photo']);

            if ($uploadResult['success']) {
                if (!empty($oldAdmin['photo'])) {
                    $profileHelper->deleteProfileImage(basename($oldAdmin['photo']));
                }
                $updateData['photo'] = $uploadResult['path'];
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $uploadResult['message']], JSON_UNESCAPED_UNICODE);
                exit();
            }
        }

        // Update profile in database
        $result = $adminRepo->updateProfile($adminId, $updateData);

        if ($result) {
            // Update session
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['name'] = $namaLengkap;
                $_SESSION['email'] = $email;
            }

            // Get updated admin data
            $updatedAdmin = $adminRepo->getAdminWithPhoto($adminId);
            unset($updatedAdmin['password_hash']);
            unset($updatedAdmin['remember_token']);
            unset($updatedAdmin['remember_expires']);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $updatedAdmin
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil di database'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

function handleChangePassword($adminRepo, $profileHelper, $adminId)
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Get password fields
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Semua field password wajib diisi'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Verify current password
        $currentHash = $adminRepo->getPasswordHash($adminId);
        if (!$adminRepo->verifyPassword($currentPassword, $currentHash)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Password lama tidak sesuai'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Check password match
        if ($newPassword !== $confirmPassword) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak sesuai'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Validate password strength
        $passwordErrors = $profileHelper->validatePassword($newPassword);
        if (!empty($passwordErrors)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => implode(', ', $passwordErrors)], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $adminRepo->updatePassword($adminId, $newHash);

        if ($result) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal mengubah password'], JSON_UNESCAPED_UNICODE);
        }
        exit();
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit();
    }
}

function handleUploadPhoto($adminRepo, $profileHelper, $adminId)
{
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File foto tidak valid'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $oldAdmin = $adminRepo->getById($adminId);
        $uploadResult = $profileHelper->uploadProfileImage($_FILES['photo']);

        if ($uploadResult['success']) {
            if (!empty($oldAdmin['photo'])) {
                $profileHelper->deleteProfileImage(basename($oldAdmin['photo']));
            }

            $result = $adminRepo->updateProfile($adminId, ['photo' => $uploadResult['path']]);

            if ($result) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Foto profil berhasil diupload',
                    'photo_path' => $uploadResult['path']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Gagal menyimpan path foto'], JSON_UNESCAPED_UNICODE);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $uploadResult['message']], JSON_UNESCAPED_UNICODE);
        }
        exit();
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        exit();
    }
}
