<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class AnalyticsRepository
{
    private \PDO $db;
    
    private const VALID_PERIODS = ['today', '7', '30', '90', 'all', 'this_month', 'this_year'];
    private const INDONESIAN_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    private function validatePeriod(string $period): string
    {
        return in_array($period, self::VALID_PERIODS) ? $period : 'all';
    }

    public function getTotalRevenue(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $sql = "SELECT COALESCE(SUM(o.total_bayar), 0) as total
                FROM orders o
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $current = (float) $stmt->fetch()['total'];

        $previous = 0.0;
        $change = 0.0;
        
        if ($previousRange['start'] && $previousRange['end']) {
            $sqlPrevious = "SELECT COALESCE(SUM(o.total_bayar), 0) as total
                            FROM orders o
                            JOIN payment p ON o.id_order = p.id_order
                            WHERE o.status_order NOT IN ('dibatalkan')
                            AND p.status_pembayaran = 'berhasil'
                            AND o.tanggal_order >= :prev_start AND o.tanggal_order < :prev_end";
            
            $stmtPrevious = $this->db->prepare($sqlPrevious);
            $stmtPrevious->execute([
                'prev_start' => $previousRange['start'],
                'prev_end' => $previousRange['end']
            ]);
            $previous = (float) $stmtPrevious->fetch()['total'];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);
        }

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0,
            'previous' => $previous
        ];
    }

    public function getAveragePurchasePrice(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $sql = "SELECT COALESCE(AVG(o.total_bayar), 0) as average
                FROM orders o
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $current = (float) $stmt->fetch()['average'];

        $previous = 0.0;
        $change = 0.0;
        
        if ($previousRange['start'] && $previousRange['end']) {
            $sqlPrevious = "SELECT COALESCE(AVG(o.total_bayar), 0) as average
                            FROM orders o
                            JOIN payment p ON o.id_order = p.id_order
                            WHERE o.status_order NOT IN ('dibatalkan')
                            AND p.status_pembayaran = 'berhasil'
                            AND o.tanggal_order >= :prev_start AND o.tanggal_order < :prev_end";
            
            $stmtPrevious = $this->db->prepare($sqlPrevious);
            $stmtPrevious->execute([
                'prev_start' => $previousRange['start'],
                'prev_end' => $previousRange['end']
            ]);
            $previous = (float) $stmtPrevious->fetch()['average'];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);
        }

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getProductsSold(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $sql = "SELECT COALESCE(SUM(od.jumlah), 0) as total
                FROM order_detail od
                JOIN orders o ON od.id_order = o.id_order
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $current = (int) $stmt->fetch()['total'];

        $previous = 0;
        $change = 0.0;
        
        if ($previousRange['start'] && $previousRange['end']) {
            $sqlPrevious = "SELECT COALESCE(SUM(od.jumlah), 0) as total
                            FROM order_detail od
                            JOIN orders o ON od.id_order = o.id_order
                            JOIN payment p ON o.id_order = p.id_order
                            WHERE o.status_order NOT IN ('dibatalkan')
                            AND p.status_pembayaran = 'berhasil'
                            AND o.tanggal_order >= :prev_start AND o.tanggal_order < :prev_end";
            
            $stmtPrevious = $this->db->prepare($sqlPrevious);
            $stmtPrevious->execute([
                'prev_start' => $previousRange['start'],
                'prev_end' => $previousRange['end']
            ]);
            $previous = (int) $stmtPrevious->fetch()['total'];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);
        }

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getAverageProductRating(): array
    {
        $sql = "SELECT 
                    COALESCE(AVG(rating), 0) as average_rating,
                    COUNT(*) as total_reviews
                FROM review 
                WHERE status_review = 'approved'";
        
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();

        return [
            'value' => round((float) $result['average_rating'], 1),
            'total_reviews' => (int) $result['total_reviews']
        ];
    }

    public function getMonthlyRevenue(int $months = 6): array
    {
        $months = max(1, min(12, $months));
        
        $labels = [];
        $data = [];
        $monthDataMap = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-{$i} months");
            $monthNum = (int) $date->format('n');
            $year = (int) $date->format('Y');
            $key = $year . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT);
            
            $labels[] = self::INDONESIAN_MONTHS[$monthNum - 1];
            $monthDataMap[$key] = count($labels) - 1;
            $data[] = 0;
        }

        $startDate = (new \DateTime())->modify("-" . ($months - 1) . " months")->format('Y-m-01');
        $endDate = (new \DateTime())->format('Y-m-t');

        $sql = "SELECT 
                    EXTRACT(YEAR FROM o.tanggal_order) as year,
                    EXTRACT(MONTH FROM o.tanggal_order) as month_num,
                    COALESCE(SUM(o.total_bayar), 0) as total_revenue
                FROM orders o
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'
                AND o.tanggal_order >= :start_date
                AND o.tanggal_order <= :end_date
                GROUP BY EXTRACT(YEAR FROM o.tanggal_order), EXTRACT(MONTH FROM o.tanggal_order)
                ORDER BY year, month_num";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        $results = $stmt->fetchAll();

        foreach ($results as $row) {
            $key = $row['year'] . '-' . str_pad($row['month_num'], 2, '0', STR_PAD_LEFT);
            if (isset($monthDataMap[$key])) {
                $data[$monthDataMap[$key]] = (float) $row['total_revenue'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getMonthlyOrdersAndUsers(int $months = 6): array
    {
        $months = max(1, min(12, $months));
        
        $labels = [];
        $ordersData = [];
        $usersData = [];
        $monthDataMap = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-{$i} months");
            $monthNum = (int) $date->format('n');
            $year = (int) $date->format('Y');
            $key = $year . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT);
            
            $labels[] = self::INDONESIAN_MONTHS[$monthNum - 1];
            $monthDataMap[$key] = count($labels) - 1;
            $ordersData[] = 0;
            $usersData[] = 0;
        }

        $startDate = (new \DateTime())->modify("-" . ($months - 1) . " months")->format('Y-m-01');
        $endDate = (new \DateTime())->format('Y-m-t');

        $sqlOrders = "SELECT 
                        EXTRACT(YEAR FROM tanggal_order) as year,
                        EXTRACT(MONTH FROM tanggal_order) as month_num,
                        COUNT(*) as total_orders
                      FROM orders
                      WHERE status_order NOT IN ('dibatalkan')
                      AND tanggal_order >= :start_date
                      AND tanggal_order <= :end_date
                      GROUP BY EXTRACT(YEAR FROM tanggal_order), EXTRACT(MONTH FROM tanggal_order)
                      ORDER BY year, month_num";
        
        $stmtOrders = $this->db->prepare($sqlOrders);
        $stmtOrders->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        $ordersResults = $stmtOrders->fetchAll();

        foreach ($ordersResults as $row) {
            $key = $row['year'] . '-' . str_pad($row['month_num'], 2, '0', STR_PAD_LEFT);
            if (isset($monthDataMap[$key])) {
                $ordersData[$monthDataMap[$key]] = (int) $row['total_orders'];
            }
        }

        $sqlUsers = "SELECT 
                        EXTRACT(YEAR FROM created_at) as year,
                        EXTRACT(MONTH FROM created_at) as month_num,
                        COUNT(*) as total_users
                     FROM customers
                     WHERE created_at >= :start_date
                     AND created_at <= :end_date
                     GROUP BY EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)
                     ORDER BY year, month_num";
        
        $stmtUsers = $this->db->prepare($sqlUsers);
        $stmtUsers->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        $usersResults = $stmtUsers->fetchAll();

        foreach ($usersResults as $row) {
            $key = $row['year'] . '-' . str_pad($row['month_num'], 2, '0', STR_PAD_LEFT);
            if (isset($monthDataMap[$key])) {
                $usersData[$monthDataMap[$key]] = (int) $row['total_users'];
            }
        }

        return [
            'labels' => $labels,
            'orders' => $ordersData,
            'users' => $usersData
        ];
    }

    public function getCategorySales(string $period = 'all', int $limit = 5): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $limit = max(1, min(20, $limit));

        $sql = "SELECT 
                    k.nama_kategori,
                    COALESCE(SUM(od.jumlah), 0) as total_sold
                FROM kategori k
                LEFT JOIN products p ON k.id_kategori = p.id_kategori
                LEFT JOIN order_detail od ON p.id_product = od.id_product
                LEFT JOIN orders o ON od.id_order = o.id_order
                LEFT JOIN payment pay ON o.id_order = pay.id_order
                WHERE (o.status_order NOT IN ('dibatalkan') OR o.status_order IS NULL)
                AND (pay.status_pembayaran = 'berhasil' OR pay.status_pembayaran IS NULL)";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND (o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date OR o.tanggal_order IS NULL)";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $sql .= " GROUP BY k.id_kategori, k.nama_kategori
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $labels = [];
        $data = [];
        
        foreach ($results as $row) {
            $labels[] = $row['nama_kategori'];
            $data[] = (int) $row['total_sold'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function getTopSellingProducts(string $period = 'all', int $limit = 5): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $limit = max(1, min(20, $limit));

        $sql = "SELECT 
                    p.id_product,
                    p.nama_product,
                    p.gambar,
                    COALESCE(SUM(od.jumlah), 0) as total_sold,
                    COALESCE(SUM(od.subtotal), 0) as total_revenue
                FROM products p
                JOIN order_detail od ON p.id_product = od.id_product
                JOIN orders o ON od.id_order = o.id_order
                JOIN payment pay ON o.id_order = pay.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND pay.status_pembayaran = 'berhasil'";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $sql .= " GROUP BY p.id_product, p.nama_product, p.gambar
                  ORDER BY total_sold DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalOrders(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $sql = "SELECT COUNT(*) as total
                FROM orders
                WHERE status_order NOT IN ('dibatalkan')";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND tanggal_order >= :start_date AND tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $current = (int) $stmt->fetch()['total'];

        $previous = 0;
        $change = 0.0;
        
        if ($previousRange['start'] && $previousRange['end']) {
            $sqlPrevious = "SELECT COUNT(*) as total
                            FROM orders
                            WHERE status_order NOT IN ('dibatalkan')
                            AND tanggal_order >= :prev_start AND tanggal_order < :prev_end";
            
            $stmtPrevious = $this->db->prepare($sqlPrevious);
            $stmtPrevious->execute([
                'prev_start' => $previousRange['start'],
                'prev_end' => $previousRange['end']
            ]);
            $previous = (int) $stmtPrevious->fetch()['total'];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);
        }

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getTotalCustomers(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);
        $previousRange = $this->getPreviousDateRange($period);

        $sql = "SELECT COUNT(*) as total
                FROM customers
                WHERE is_active = true";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND created_at >= :start_date AND created_at <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $current = (int) $stmt->fetch()['total'];

        $previous = 0;
        $change = 0.0;
        
        if ($previousRange['start'] && $previousRange['end']) {
            $sqlPrevious = "SELECT COUNT(*) as total
                            FROM customers
                            WHERE is_active = true
                            AND created_at >= :prev_start AND created_at < :prev_end";
            
            $stmtPrevious = $this->db->prepare($sqlPrevious);
            $stmtPrevious->execute([
                'prev_start' => $previousRange['start'],
                'prev_end' => $previousRange['end']
            ]);
            $previous = (int) $stmtPrevious->fetch()['total'];
            $change = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);
        }

        return [
            'value' => $current,
            'change' => round($change, 1),
            'isIncrease' => $change >= 0
        ];
    }

    public function getOrdersByStatus(): array
    {
        $sql = "SELECT 
                    status_order,
                    COUNT(*) as count
                FROM orders
                GROUP BY status_order";
        
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();

        $statusCount = [];
        foreach ($results as $row) {
            $statusCount[$row['status_order']] = (int) $row['count'];
        }

        return $statusCount;
    }

    public function getPaymentMethodDistribution(string $period = 'all'): array
    {
        $period = $this->validatePeriod($period);
        $dateRange = $this->getDateRange($period);

        $sql = "SELECT 
                    p.metode_pembayaran,
                    COUNT(*) as count,
                    SUM(p.total_bayar) as total
                FROM payment p
                JOIN orders o ON p.id_order = o.id_order
                WHERE p.status_pembayaran = 'berhasil'";
        
        $params = [];
        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }
        
        $sql .= " GROUP BY p.metode_pembayaran
                  ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDailyRevenue(int $days = 30): array
    {
        $days = max(1, min(365, $days));
        $startDate = (new \DateTime())->modify("-{$days} days")->format('Y-m-d');
        $endDate = (new \DateTime())->format('Y-m-d');

        $sql = "SELECT 
                    DATE(o.tanggal_order) as date,
                    COALESCE(SUM(o.total_bayar), 0) as revenue
                FROM orders o
                JOIN payment p ON o.id_order = p.id_order
                WHERE o.status_order NOT IN ('dibatalkan')
                AND p.status_pembayaran = 'berhasil'
                AND o.tanggal_order >= :start_date
                AND o.tanggal_order <= :end_date
                GROUP BY DATE(o.tanggal_order)
                ORDER BY date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        return $stmt->fetchAll();
    }

    public function getRevenueGrowth(string $period = '30'): array
    {
        $days = is_numeric($period) ? max(1, min(365, (int) $period)) : 30;
        
        $currentStart = (new \DateTime())->modify("-{$days} days")->format('Y-m-d');
        $currentEnd = (new \DateTime())->format('Y-m-d');
        $previousStart = (new \DateTime())->modify("-" . ($days * 2) . " days")->format('Y-m-d');
        $previousEnd = $currentStart;
        
        $sqlCurrent = "SELECT COALESCE(SUM(o.total_bayar), 0) as current_revenue
                       FROM orders o
                       JOIN payment p ON o.id_order = p.id_order
                       WHERE o.status_order NOT IN ('dibatalkan')
                       AND p.status_pembayaran = 'berhasil'
                       AND o.tanggal_order >= :start_date
                       AND o.tanggal_order <= :end_date";
        
        $stmt = $this->db->prepare($sqlCurrent);
        $stmt->execute(['start_date' => $currentStart, 'end_date' => $currentEnd]);
        $current = (float) $stmt->fetch()['current_revenue'];

        $sqlPrevious = "SELECT COALESCE(SUM(o.total_bayar), 0) as previous_revenue
                        FROM orders o
                        JOIN payment p ON o.id_order = p.id_order
                        WHERE o.status_order NOT IN ('dibatalkan')
                        AND p.status_pembayaran = 'berhasil'
                        AND o.tanggal_order >= :start_date
                        AND o.tanggal_order < :end_date";
        
        $stmtPrevious = $this->db->prepare($sqlPrevious);
        $stmtPrevious->execute(['start_date' => $previousStart, 'end_date' => $previousEnd]);
        $previous = (float) $stmtPrevious->fetch()['previous_revenue'];

        $growth = $previous > 0 ? (($current - $previous) / $previous) * 100 : ($current > 0 ? 100 : 0);

        return [
            'current' => $current,
            'previous' => $previous,
            'growth' => round($growth, 1),
            'isPositive' => $growth >= 0
        ];
    }

    private function getDateRange(string $period): array
    {
        $now = new \DateTime();
        $start = null;
        $end = $now->format('Y-m-d H:i:s');

        switch ($period) {
            case 'today':
                $start = $now->format('Y-m-d 00:00:00');
                $end = $now->format('Y-m-d 23:59:59');
                break;
            case '7':
                $start = (clone $now)->modify('-7 days')->format('Y-m-d 00:00:00');
                break;
            case '30':
                $start = (clone $now)->modify('-30 days')->format('Y-m-d 00:00:00');
                break;
            case '90':
                $start = (clone $now)->modify('-90 days')->format('Y-m-d 00:00:00');
                break;
            case 'this_month':
                $start = $now->format('Y-m-01 00:00:00');
                break;
            case 'this_year':
                $start = $now->format('Y-01-01 00:00:00');
                break;
            case 'all':
            default:
                return ['start' => null, 'end' => null];
        }

        return ['start' => $start, 'end' => $end];
    }

    private function getPreviousDateRange(string $period): array
    {
        $now = new \DateTime();
        
        switch ($period) {
            case 'today':
                $start = (clone $now)->modify('-1 day')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('-1 day')->format('Y-m-d 23:59:59');
                break;
            case '7':
                $start = (clone $now)->modify('-14 days')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('-7 days')->format('Y-m-d 00:00:00');
                break;
            case '30':
                $start = (clone $now)->modify('-60 days')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('-30 days')->format('Y-m-d 00:00:00');
                break;
            case '90':
                $start = (clone $now)->modify('-180 days')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('-90 days')->format('Y-m-d 00:00:00');
                break;
            case 'this_month':
                $start = (clone $now)->modify('first day of last month')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('last day of last month')->format('Y-m-d 23:59:59');
                break;
            case 'this_year':
                $start = (clone $now)->modify('first day of january last year')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('last day of december last year')->format('Y-m-d 23:59:59');
                break;
            case 'all':
            default:
                return ['start' => null, 'end' => null];
        }

        return ['start' => $start, 'end' => $end];
    }
}
