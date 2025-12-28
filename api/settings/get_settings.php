<?php 
header('Content-Type: application/json');
require_once __DIR__ . '/../../app/Database/DatabaseConnection.php';
require_once __DIR__ . '/../../app/Services/SiteSetting.php';

use App\Services\SiteSetting;
use App\Database\DatabaseConnection;

try {
    $db = DatabaseConnection::getInstance()->getConnection();
    $setting = new SiteSetting($db);
    $allSettings = $setting->getAllSettings();
    echo json_encode(['status' => 'success', 'data' => $allSettings]);
    

    // $siteSettingService = new SiteSetting(DatabaseConnection::getInstance()->getConnection());
    // $pdo = $dbInstance->getConnection();
    
    // $setting = new SiteSetting($pdo);
    // $allSettings = $setting->getAllSettings();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    exit;
}


?>