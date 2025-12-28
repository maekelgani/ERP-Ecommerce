<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class DashboardRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function getMonthlyRevenue(): array
    {
        $currentMonth = date('Y-m-01');
        $previousMonth = date('Y-m-01', strtotime('-1 month'));

        $sql = "SELECT COALESCE(SUM(o.total_bayar), 0) as total
                FROM orders o
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'
                AND o.tanggal_order >= :start_date";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start_date' => $currentMonth]);
        $current = (float) $stmt->fetchColumn();

        $sqlPrev = "SELECT COALESCE(SUM(o.total_bayar), 0) as total
                    FROM orders o
                    JOIN payment p ON o.id_order = p.id_order
                    WHERE o.status_order NOT IN ('dibatalkan')
                    AND p.status_pembayaran = 'berhasil'
                    AND o.tanggal_order >= :prev_start AND o.tanggal_order < :prev_end";

        $stmtPrev = $this->db->prepare($sqlPrev);
        $stmtPrev->execute(['prev_start' => $previousMonth, 'prev_end' => $currentMonth]);
        $previous = (float) $stmtPrev->fetchColumn();

        $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getTotalCustomers(): array
    {
        $currentMonth = date('Y-m-01');
        $previousMonth = date('Y-m-01', strtotime('-1 month'));

        $sql = "SELECT COUNT(*) FROM customers WHERE is_active = true";
        $stmt = $this->db->query($sql);
        $current = (int) $stmt->fetchColumn();

        $sqlPrev = "SELECT COUNT(*) FROM customers WHERE is_active = true AND created_at < :prev_end";
        $stmtPrev = $this->db->prepare($sqlPrev);
        $stmtPrev->execute(['prev_end' => $currentMonth]);
        $previous = (int) $stmtPrev->fetchColumn();

        $newThisMonth = $current - $previous;
        $change = $previous > 0 ? ($newThisMonth / $previous) * 100 : ($newThisMonth > 0 ? 100 : 0);

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getTotalOrders(): array
    {
        $currentMonth = date('Y-m-01');
        $previousMonth = date('Y-m-01', strtotime('-1 month'));

        $sql = "SELECT COUNT(*) FROM orders WHERE tanggal_order >= :start_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start_date' => $currentMonth]);
        $current = (int) $stmt->fetchColumn();

        $sqlPrev = "SELECT COUNT(*) FROM orders WHERE tanggal_order >= :prev_start AND tanggal_order < :prev_end";
        $stmtPrev = $this->db->prepare($sqlPrev);
        $stmtPrev->execute(['prev_start' => $previousMonth, 'prev_end' => $currentMonth]);
        $previous = (int) $stmtPrev->fetchColumn();

        $sqlTotal = "SELECT COUNT(*) FROM orders";
        $total = (int) $this->db->query($sqlTotal)->fetchColumn();

        return [
            'value' => $total,
            'thisMonth' => $current,
            'change' => $current - $previous,
            'isIncrease' => $current >= $previous
        ];
    }

    public function getPendingReturns(): int
    {
        $sql = "SELECT COUNT(*) FROM return_request WHERE status_return IN ('pending', 'diproses')";
        $stmt = $this->db->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function getRevenueChart(int $months = 6): array
    {
        $labels = [];
        $data = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = strtotime("-{$i} months");
            $monthStart = date('Y-m-01', $date);
            $monthEnd = date('Y-m-t 23:59:59', $date);
            $labels[] = $monthNames[date('n', $date) - 1] . ' ' . date('Y', $date);

            $sql = "SELECT COALESCE(SUM(o.total_bayar), 0) as total
                    FROM orders o
                    JOIN payment p ON o.id_order = p.id_order
                    WHERE o.status_order NOT IN ('dibatalkan')
                    AND p.status_pembayaran = 'berhasil'
                    AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['start_date' => $monthStart, 'end_date' => $monthEnd]);
            $data[] = (float) $stmt->fetchColumn();
        }

        return ['labels' => $labels, 'data' => $data];
    }
    public function getExpensesChart(int $months = 6): array
    {
        $labels = [];
        $data = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = strtotime("-{$i} months");
            $monthStart = date('Y-m-01', $date);
            $monthEnd = date('Y-m-t 23:59:59', $date);

            $labels[] = $monthNames[date('n', $date) - 1] . ' ' . date('Y', $date);

            $sql = "SELECT COALESCE(SUM(jumlah), 0) as total
                    FROM pengeluaran 
                    WHERE tgl_pengeluaran >= :start_date 
                    AND tgl_pengeluaran <= :end_date";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'start_date' => $monthStart,
                'end_date' => $monthEnd
            ]);

            $data[] = (float) $stmt->fetchColumn();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getOrdersChart(int $months = 6): array
    {
        $labels = [];
        $data = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = strtotime("-{$i} months");
            $monthStart = date('Y-m-01', $date);
            $monthEnd = date('Y-m-t 23:59:59', $date);
            $labels[] = $monthNames[date('n', $date) - 1];

            $sql = "SELECT COUNT(*) FROM orders 
                    WHERE tanggal_order >= :start_date AND tanggal_order <= :end_date";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['start_date' => $monthStart, 'end_date' => $monthEnd]);
            $data[] = (int) $stmt->fetchColumn();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getRecentOrders(int $limit = 5): array
    {
        $sql = "
            SELECT 
                o.id_order,
                o.tanggal_order,
                o.total_bayar,
                o.status_order,
                c.nama_lengkap AS customer_name,
                p.status_pembayaran,
                p.metode_pembayaran,
    
                -- total item dalam order
                COALESCE(SUM(od.jumlah), 0) AS item_count,
    
                -- daftar produk
                GROUP_CONCAT(pr.nama_product SEPARATOR ', ') AS product_names
    
            FROM orders o
            JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            LEFT JOIN order_detail od ON o.id_order = od.id_order
            LEFT JOIN products pr ON od.id_product = pr.id_product
    
            GROUP BY 
                o.id_order,
                o.tanggal_order,
                o.total_bayar,
                o.status_order,
                c.nama_lengkap,
                p.status_pembayaran,
                p.metode_pembayaran
    
            ORDER BY o.tanggal_order DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function getOrderStatusDistribution(): array
    {
        $sql = "SELECT status_order, COUNT(*) as count 
                FROM orders 
                GROUP BY status_order";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        $statusLabels = [
            'pending' => 'Pending',
            'dikonfirmasi' => 'Dikonfirmasi',
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
        ];

        foreach ($results as $row) {
            $labels[] = $statusLabels[$row['status_order']] ?? $row['status_order'];
            $data[] = (int) $row['count'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getTopSellingProducts(int $limit = 5): array
    {
        $sql = "SELECT p.id_product, p.nama_product, p.gambar, p.harga,
                       COALESCE(SUM(od.jumlah), 0) as total_sold,
                       COALESCE(SUM(od.subtotal), 0) as total_revenue
                FROM products p
                LEFT JOIN order_detail od ON p.id_product = od.id_product
                LEFT JOIN orders o ON od.id_order = o.id_order AND o.status_order NOT IN ('dibatalkan')
                GROUP BY p.id_product, p.nama_product, p.gambar, p.harga
                ORDER BY total_sold DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPaymentMethodDistribution(): array
    {
        $sql = "SELECT metode_pembayaran, COUNT(*) as count 
                FROM payment 
                WHERE status_pembayaran = 'berhasil'
                GROUP BY metode_pembayaran";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        $methodLabels = [
            'transfer_bank' => 'Transfer Bank',
            'ewallet' => 'E-Wallet',
            'cod' => 'COD',
            'kartu_kredit' => 'Kartu Kredit'
        ];

        foreach ($results as $row) {
            $labels[] = $methodLabels[$row['metode_pembayaran']] ?? $row['metode_pembayaran'];
            $data[] = (int) $row['count'];
        }

        return ['labels' => $labels, 'data' => $data];
    }

    public function getTodayStats(): array
    {
        $today = date('Y-m-d');

        $sqlOrders = "SELECT COUNT(*) FROM orders WHERE DATE(tanggal_order) = :today";
        $stmt = $this->db->prepare($sqlOrders);
        $stmt->execute(['today' => $today]);
        $todayOrders = (int) $stmt->fetchColumn();

        $sqlRevenue = "SELECT COALESCE(SUM(o.total_bayar), 0) 
                       FROM orders o
                       JOIN payment p ON o.id_order = p.id_order
                       WHERE DATE(o.tanggal_order) = :today
                       AND p.status_pembayaran = 'berhasil'";
        $stmt = $this->db->prepare($sqlRevenue);
        $stmt->execute(['today' => $today]);
        $todayRevenue = (float) $stmt->fetchColumn();

        $sqlCustomers = "SELECT COUNT(*) FROM customers WHERE DATE(created_at) = :today";
        $stmt = $this->db->prepare($sqlCustomers);
        $stmt->execute(['today' => $today]);
        $newCustomers = (int) $stmt->fetchColumn();

        return [
            'orders' => $todayOrders,
            'revenue' => $todayRevenue,
            'newCustomers' => $newCustomers
        ];
    }
}
