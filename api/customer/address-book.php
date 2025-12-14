<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

use App\Auth\CustomerAuthMiddleware;

if (!CustomerAuthMiddleware::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customerId = CustomerAuthMiddleware::getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    $db = \App\Database\DatabaseConnection::getInstance()->getConnection();

    switch ($method) {
        case 'GET':
            $stmt = $db->prepare("
                SELECT * FROM address_book 
                WHERE id_customer = :id 
                ORDER BY default_alamat DESC, id_alamat ASC
            ");
            $stmt->execute([':id' => $customerId]);
            $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $addresses]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);

            if ($action === 'add') {
                if (
                    empty($data['nama_penerima']) || empty($data['nomor_hp']) ||
                    empty($data['alamat_lengkap']) || empty($data['kota']) || empty($data['kode_pos'])
                ) {
                    throw new Exception('Field wajib harus diisi');
                }

                $nama_penerima = trim($data['nama_penerima']);
                $nomor_hp = preg_replace('/[^0-9+]/', '', $data['nomor_hp']);
                $alamat_lengkap = trim($data['alamat_lengkap']);
                $kota = trim($data['kota']);
                $kode_pos = preg_replace('/[^0-9]/', '', $data['kode_pos']);

                if (strlen($nama_penerima) < 3 || strlen($nama_penerima) > 100) {
                    throw new Exception('Nama penerima harus antara 3-100 karakter');
                }
                if (strlen($nomor_hp) < 10 || strlen($nomor_hp) > 15) {
                    throw new Exception('Nomor HP tidak valid (10-15 digit)');
                }
                if (strlen($kode_pos) !== 5) {
                    throw new Exception('Kode pos harus 5 digit');
                }

                $db->beginTransaction();

                try {
                    $stmtCount = $db->prepare("SELECT COUNT(*) FROM address_book WHERE id_customer = :id FOR UPDATE");
                    $stmtCount->execute([':id' => $customerId]);
                    $count = (int)$stmtCount->fetchColumn();

                    $isDefault = $count === 0 ? 1 : (int)($data['default_alamat'] ?? 0);

                    if ($isDefault && $count > 0) {
                        $stmtReset = $db->prepare("UPDATE address_book SET default_alamat = 0 WHERE id_customer = :id");
                        $stmtReset->execute([':id' => $customerId]);
                    }

                    $stmt = $db->prepare("
                        INSERT INTO address_book 
                        (id_customer, label_alamat, nama_penerima, nomor_hp, alamat_lengkap, 
                         kelurahan, kecamatan, kota, provinsi, kode_pos, default_alamat)
                        VALUES 
                        (:id_customer, :label, :nama, :hp, :alamat, 
                         :kelurahan, :kecamatan, :kota, :provinsi, :kode_pos, :default_alamat)
                    ");

                    $stmt->execute([
                        ':id_customer' => $customerId,
                        ':label' => trim($data['label_alamat'] ?? 'Rumah'),
                        ':nama' => $nama_penerima,
                        ':hp' => $nomor_hp,
                        ':alamat' => $alamat_lengkap,
                        ':kelurahan' => isset($data['kelurahan']) ? trim($data['kelurahan']) : null,
                        ':kecamatan' => isset($data['kecamatan']) ? trim($data['kecamatan']) : null,
                        ':kota' => $kota,
                        ':provinsi' => isset($data['provinsi']) ? trim($data['provinsi']) : null,
                        ':kode_pos' => $kode_pos,
                        ':default_alamat' => $isDefault
                    ]);

                    $newId = $db->lastInsertId();

                    $db->commit();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Alamat berhasil ditambahkan',
                        'id' => (int)$newId
                    ]);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'update') {
                if (empty($data['id_alamat'])) {
                    throw new Exception('ID alamat diperlukan');
                }

                $idAlamat = (int)$data['id_alamat'];

                $stmtCheck = $db->prepare("SELECT id_alamat FROM address_book WHERE id_alamat = :id AND id_customer = :cid");
                $stmtCheck->execute([':id' => $idAlamat, ':cid' => $customerId]);
                if (!$stmtCheck->fetch()) {
                    throw new Exception('Alamat tidak ditemukan');
                }

                $db->beginTransaction();

                try {
                    if (!empty($data['default_alamat'])) {
                        $stmtReset = $db->prepare("UPDATE address_book SET default_alamat = 0 WHERE id_customer = :id");
                        $stmtReset->execute([':id' => $customerId]);
                    }

                    $stmt = $db->prepare("
                        UPDATE address_book SET
                            label_alamat = :label,
                            nama_penerima = :nama,
                            nomor_hp = :hp,
                            alamat_lengkap = :alamat,
                            kelurahan = :kelurahan,
                            kecamatan = :kecamatan,
                            kota = :kota,
                            provinsi = :provinsi,
                            kode_pos = :kode_pos,
                            default_alamat = :default_alamat
                        WHERE id_alamat = :id_alamat AND id_customer = :id_customer
                    ");

                    $stmt->execute([
                        ':label' => trim($data['label_alamat'] ?? 'Rumah'),
                        ':nama' => trim($data['nama_penerima']),
                        ':hp' => preg_replace('/[^0-9+]/', '', $data['nomor_hp']),
                        ':alamat' => trim($data['alamat_lengkap']),
                        ':kelurahan' => isset($data['kelurahan']) ? trim($data['kelurahan']) : null,
                        ':kecamatan' => isset($data['kecamatan']) ? trim($data['kecamatan']) : null,
                        ':kota' => trim($data['kota']),
                        ':provinsi' => isset($data['provinsi']) ? trim($data['provinsi']) : null,
                        ':kode_pos' => preg_replace('/[^0-9]/', '', $data['kode_pos']),
                        ':default_alamat' => (int)($data['default_alamat'] ?? 0),
                        ':id_alamat' => $idAlamat,
                        ':id_customer' => $customerId
                    ]);

                    $db->commit();

                    echo json_encode(['success' => true, 'message' => 'Alamat berhasil diperbarui']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'delete') {
                if (empty($data['id_alamat'])) {
                    throw new Exception('ID alamat diperlukan');
                }

                $idAlamat = (int)$data['id_alamat'];

                $stmtCheck = $db->prepare("SELECT default_alamat FROM address_book WHERE id_alamat = :id AND id_customer = :cid");
                $stmtCheck->execute([':id' => $idAlamat, ':cid' => $customerId]);
                $address = $stmtCheck->fetch();

                if (!$address) {
                    throw new Exception('Alamat tidak ditemukan');
                }

                $db->beginTransaction();

                try {
                    $stmt = $db->prepare("DELETE FROM address_book WHERE id_alamat = :id AND id_customer = :cid");
                    $stmt->execute([':id' => $idAlamat, ':cid' => $customerId]);

                    if ($address['default_alamat']) {
                        $stmtGetFirst = $db->prepare("
                            SELECT id_alamat FROM address_book 
                            WHERE id_customer = :id 
                            ORDER BY id_alamat ASC 
                            LIMIT 1
                        ");
                        $stmtGetFirst->execute([':id' => $customerId]);
                        $firstAddress = $stmtGetFirst->fetch();

                        if ($firstAddress) {
                            $stmtUpdate = $db->prepare("
                                UPDATE address_book 
                                SET default_alamat = 1 
                                WHERE id_alamat = :id_alamat AND id_customer = :id_customer
                            ");
                            $stmtUpdate->execute([
                                ':id_alamat' => $firstAddress['id_alamat'],
                                ':id_customer' => $customerId
                            ]);
                        }
                    }

                    $db->commit();

                    echo json_encode(['success' => true, 'message' => 'Alamat berhasil dihapus']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } elseif ($action === 'set-default') {
                if (empty($data['id_alamat'])) {
                    throw new Exception('ID alamat diperlukan');
                }

                $idAlamat = (int)$data['id_alamat'];

                $stmtCheck = $db->prepare("SELECT id_alamat FROM address_book WHERE id_alamat = :id AND id_customer = :cid");
                $stmtCheck->execute([':id' => $idAlamat, ':cid' => $customerId]);
                if (!$stmtCheck->fetch()) {
                    throw new Exception('Alamat tidak ditemukan');
                }

                $db->beginTransaction();

                try {
                    $stmtReset = $db->prepare("UPDATE address_book SET default_alamat = 0 WHERE id_customer = :id");
                    $stmtReset->execute([':id' => $customerId]);

                    $stmt = $db->prepare("UPDATE address_book SET default_alamat = 1 WHERE id_alamat = :id AND id_customer = :cid");
                    $stmt->execute([':id' => $idAlamat, ':cid' => $customerId]);

                    $db->commit();

                    echo json_encode(['success' => true, 'message' => 'Alamat utama berhasil diubah']);
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
            } else {
                throw new Exception('Action tidak valid');
            }
            break;

        default:
            throw new Exception('Method tidak didukung');
    }
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Database Error in address-book.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
