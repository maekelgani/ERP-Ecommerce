<?php 

namespace App\Services;

use App\Database\DatabaseConnection;
use PDO;

class PengeluaranServices {
    private \PDO $db;

        public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getPaginated($filter = 'this_month', $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        // 1. Bangun WHERE Clause dasar (dipakai untuk hitung total & ambil data)
        $whereClause = "";
        if ($filter === 'this_month') {
            $whereClause = "WHERE MONTH(tgl_pengeluaran) = MONTH(CURRENT_DATE()) AND YEAR(tgl_pengeluaran) = YEAR(CURRENT_DATE())";
        } elseif ($filter === 'last_month') {
            $whereClause = "WHERE MONTH(tgl_pengeluaran) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
                            AND YEAR(tgl_pengeluaran) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
        }
        // 'all' tidak butuh WHERE clause tambahan

        // 2. Query untuk menghitung TOTAL data (tanpa limit)
        $countSql = "SELECT COUNT(*) as total FROM pengeluaran $whereClause";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute();
        $totalRecords = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

        // 3. Query untuk mengambil DATA (dengan limit & offset)
        $dataSql = "SELECT * FROM pengeluaran $whereClause 
                    ORDER BY tgl_pengeluaran DESC, created_at DESC 
                    LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($dataSql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll();

        // 4. Return paket lengkap
        return [
            'data' => $data,
            'pagination' => [
                'total_records' => $totalRecords,
                'total_pages'   => ceil($totalRecords / $limit),
                'current_page'  => $page,
                'limit'         => $limit
            ]
        ];
    }

// public function getAll($filter = 'this_month') {
//         // SELECT semua kolom
//         $sql = "SELECT * FROM pengeluaran";
        
//         // Perhatikan kolom 'tgl_pengeluaran'
//         if ($filter === 'this_month') {
//             $sql .= " WHERE MONTH(tgl_pengeluaran) = MONTH(CURRENT_DATE()) AND YEAR(tgl_pengeluaran) = YEAR(CURRENT_DATE())";
//         } elseif ($filter === 'last_month') {
//             $sql .= " WHERE MONTH(tgl_pengeluaran) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
//                       AND YEAR(tgl_pengeluaran) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
//         }

//         // Order by tgl_pengeluaran
//         $sql .= " ORDER BY tgl_pengeluaran DESC, created_at DESC";

//         $stmt = $this->db->prepare($sql);
//         $stmt->execute();
//         return $stmt->fetchAll();
//     }

    public function create($data) {
        $generatedId = 'EXP-' . date('Ymd') . '-' . rand(1000, 9999);

        // 2. Masukkan ID ke query SQL
        $sql = "INSERT INTO pengeluaran (id, tgl_pengeluaran, kategori, description, jumlah) 
                VALUES (:id, :tgl_pengeluaran, :kategori, :description, :jumlah)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            ':id'              => $generatedId, // <--- ID manual dimasukkan disini
            ':tgl_pengeluaran' => $data['tgl_pengeluaran'],
            ':kategori'        => $data['kategori'],
            ':description'     => $data['description'],
            ':jumlah'          => $data['jumlah']
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM pengeluaran WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function getTotalByPeriod($period = 'month') {
    $sql = "SELECT SUM(jumlah) as total FROM pengeluaran";
    
    // Logika Filter Waktu (Sama seperti filter report lainnya)
    switch ($period) {
        case 'today':
            $sql .= " WHERE DATE(tgl_pengeluaran) = CURDATE()";
            break;
        case 'week':
            $sql .= " WHERE YEARWEEK(tgl_pengeluaran, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $sql .= " WHERE MONTH(tgl_pengeluaran) = MONTH(CURDATE()) AND YEAR(tgl_pengeluaran) = YEAR(CURDATE())";
            break;
        case 'last_month':
            $sql .= " WHERE MONTH(tgl_pengeluaran) = MONTH(CURDATE() - INTERVAL 1 MONTH) 
                      AND YEAR(tgl_pengeluaran) = YEAR(CURDATE() - INTERVAL 1 MONTH)";
            break;
        case 'year':
            $sql .= " WHERE YEAR(tgl_pengeluaran) = YEAR(CURDATE())";
            break;
        // 'all' tidak perlu WHERE
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Kembalikan 0 jika null (belum ada pengeluaran)
    return $result['total'] ?? 0;
}
}


?>