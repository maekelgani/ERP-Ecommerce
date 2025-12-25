<?php

namespace App\Repository;

use App\Database\DatabaseConnection;

class ReportRepository
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    private function getDateRange(string $period): array
    {
        $now = new \DateTime();
        $start = null;
        $end = $now->format('Y-m-d 23:59:59');

        switch ($period) {
            case 'today':
                $start = $now->format('Y-m-d 00:00:00');
                break;
            case 'week':
                $start = (clone $now)->modify('monday this week')->format('Y-m-d 00:00:00');
                break;
            case 'month':
                $start = $now->format('Y-m-01 00:00:00');
                break;
            case 'last_month':
                $start = (clone $now)->modify('first day of last month')->format('Y-m-d 00:00:00');
                $end = (clone $now)->modify('last day of last month')->format('Y-m-d 23:59:59');
                break;
            case '6months':
                $start = (clone $now)->modify('-6 months')->format('Y-m-d 00:00:00');
                break;
            case 'year':
                $start = $now->format('Y-01-01 00:00:00');
                break;
            default:
                $start = null;
                $end = null;
        }

        return ['start' => $start, 'end' => $end];
    }

    public function getSalesReport(string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);

        $summaryParams = [];
        $summarySql = "SELECT 
                COALESCE(COUNT(o.id_order), 0) as total_orders,
                COALESCE(SUM(o.total_bayar), 0) as total_revenue,
                COALESCE(SUM(o.total_diskon), 0) as total_discount,
                COALESCE(AVG(o.total_bayar), 0) as avg_order_value
            FROM orders o
            JOIN payment p ON o.id_order = p.id_order
            WHERE o.status_order NOT IN ('dibatalkan')
            AND p.status_pembayaran = 'berhasil'";

        if ($dateRange['start'] && $dateRange['end']) {
            $summarySql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $summaryParams['start_date'] = $dateRange['start'];
            $summaryParams['end_date'] = $dateRange['end'];
        }

        $stmtSummary = $this->db->prepare($summarySql);
        $stmtSummary->execute($summaryParams);
        $summary = $stmtSummary->fetch(\PDO::FETCH_ASSOC);

        $detailParams = [];
        $detailSql = "SELECT 
                o.id_order,
                o.tanggal_order,
                c.nama_lengkap as customer_name,
                o.total_harga,
                o.total_diskon,
                o.total_ongkir,
                o.total_bayar,
                o.status_order,
                p.metode_pembayaran,
                p.nama_bank
            FROM orders o
            JOIN customers c ON o.id_customer = c.id_customer
            JOIN payment p ON o.id_order = p.id_order
            WHERE o.status_order NOT IN ('dibatalkan')
            AND p.status_pembayaran = 'berhasil'";

        if ($dateRange['start'] && $dateRange['end']) {
            $detailSql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $detailParams['start_date'] = $dateRange['start'];
            $detailParams['end_date'] = $dateRange['end'];
        }

        $detailSql .= " ORDER BY o.tanggal_order DESC";

        $stmtDetail = $this->db->prepare($detailSql);
        $stmtDetail->execute($detailParams);
        $orders = $stmtDetail->fetchAll(\PDO::FETCH_ASSOC);

        $categoryParams = [];
        $categorySql = "SELECT 
                k.nama_kategori,
                COALESCE(SUM(od.jumlah), 0) as qty_sold,
                COALESCE(SUM(od.subtotal), 0) as revenue
            FROM kategori k
            LEFT JOIN products pr ON k.id_kategori = pr.id_kategori
            LEFT JOIN order_detail od ON pr.id_product = od.id_product
            LEFT JOIN orders o ON od.id_order = o.id_order
            LEFT JOIN payment p ON o.id_order = p.id_order
            WHERE (o.status_order NOT IN ('dibatalkan') OR o.status_order IS NULL)
            AND (p.status_pembayaran = 'berhasil' OR p.status_pembayaran IS NULL)";

        if ($dateRange['start'] && $dateRange['end']) {
            $categorySql .= " AND (o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date OR o.tanggal_order IS NULL)";
            $categoryParams['start_date'] = $dateRange['start'];
            $categoryParams['end_date'] = $dateRange['end'];
        }

        $categorySql .= " GROUP BY k.id_kategori, k.nama_kategori ORDER BY revenue DESC LIMIT 10";

        $stmtCategory = $this->db->prepare($categorySql);
        $stmtCategory->execute($categoryParams);
        $categories = $stmtCategory->fetchAll(\PDO::FETCH_ASSOC);

        $paymentParams = [];
        $paymentSql = "SELECT 
                p.metode_pembayaran,
                COALESCE(p.nama_bank, p.metode_pembayaran) as bank_name,
                COUNT(*) as count,
                COALESCE(SUM(p.total_bayar), 0) as total
            FROM payment p
            JOIN orders o ON p.id_order = o.id_order
            WHERE p.status_pembayaran = 'berhasil'";

        if ($dateRange['start'] && $dateRange['end']) {
            $paymentSql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $paymentParams['start_date'] = $dateRange['start'];
            $paymentParams['end_date'] = $dateRange['end'];
        }

        $paymentSql .= " GROUP BY p.metode_pembayaran, p.nama_bank ORDER BY total DESC";

        $stmtPayment = $this->db->prepare($paymentSql);
        $stmtPayment->execute($paymentParams);
        $payments = $stmtPayment->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'summary' => $summary,
            'orders' => $orders,
            'categories' => $categories,
            'payments' => $payments
        ];
    }

    public function getOrdersReport(string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);

        $params = [];
        $sql = "SELECT 
                o.id_order,
                o.tanggal_order,
                c.nama_lengkap as customer_name,
                c.email as customer_email,
                c.no_telp as customer_phone,
                o.total_harga,
                o.total_diskon,
                o.total_ongkir,
                o.biaya_packing,
                o.total_bayar,
                o.status_order,
                o.shipping_method,
                o.catatan_order,
                p.metode_pembayaran,
                p.nama_bank,
                p.status_pembayaran,
                -- s.nama_kurir,
                s.nama_layanan,
                s.no_resi
            FROM orders o
            JOIN customers c ON o.id_customer = c.id_customer
            LEFT JOIN payment p ON o.id_order = p.id_order
            LEFT JOIN shipment s ON o.id_order = s.id_order
            WHERE 1=1";

        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }

        $sql .= " ORDER BY o.tanggal_order DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $statusParams = [];
        $statusSql = "SELECT 
                status_order,
                COUNT(*) as count
            FROM orders
            WHERE 1=1";

        if ($dateRange['start'] && $dateRange['end']) {
            $statusSql .= " AND tanggal_order >= :start_date AND tanggal_order <= :end_date";
            $statusParams['start_date'] = $dateRange['start'];
            $statusParams['end_date'] = $dateRange['end'];
        }

        $statusSql .= " GROUP BY status_order";

        $stmtStatus = $this->db->prepare($statusSql);
        $stmtStatus->execute($statusParams);
        $statusBreakdown = $stmtStatus->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'orders' => $orders,
            'status_breakdown' => $statusBreakdown
        ];
    }

    public function getCustomersReport(string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);

        $params = [];
        $sql = "SELECT 
                c.id_customer,
                c.nama_lengkap,
                c.email,
                c.no_telp,
                c.login_type,
                c.is_active,
                c.created_at,
                COUNT(DISTINCT o.id_order) as total_orders,
                COALESCE(SUM(o.total_bayar), 0) as total_spent
            FROM customers c
            LEFT JOIN orders o ON c.id_customer = o.id_customer 
                AND o.status_order NOT IN ('dibatalkan')
            WHERE 1=1";

        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND c.created_at >= :start_date AND c.created_at <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }

        $sql .= " GROUP BY c.id_customer, c.nama_lengkap, c.email, c.no_telp, c.login_type, c.is_active, c.created_at
                  ORDER BY total_spent DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $summaryParams = [];
        $summarySql = "SELECT 
                COUNT(*) as total_customers,
                COUNT(CASE WHEN login_type = 'google' THEN 1 END) as google_users,
                COUNT(CASE WHEN login_type = 'regular' THEN 1 END) as regular_users,
                COUNT(CASE WHEN is_active = true THEN 1 END) as active_users
            FROM customers
            WHERE 1=1";

        if ($dateRange['start'] && $dateRange['end']) {
            $summarySql .= " AND created_at >= :start_date AND created_at <= :end_date";
            $summaryParams['start_date'] = $dateRange['start'];
            $summaryParams['end_date'] = $dateRange['end'];
        }

        $stmtSummary = $this->db->prepare($summarySql);
        $stmtSummary->execute($summaryParams);
        $summary = $stmtSummary->fetch(\PDO::FETCH_ASSOC);

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'customers' => $customers,
            'summary' => $summary
        ];
    }

    public function getInventoryReport(): array
    {
        $sql = "SELECT 
                p.id_product,
                p.nama_product,
                p.harga,
                p.stok,
                p.status_produk,
                k.nama_kategori,
                b.nama_brand,
                COALESCE(SUM(od.jumlah), 0) as total_sold,
                (p.stok * p.harga) as stock_value
            FROM products p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN brand b ON p.id_brand = b.id_brand
            LEFT JOIN order_detail od ON p.id_product = od.id_product
            LEFT JOIN orders o ON od.id_order = o.id_order 
                AND o.status_order NOT IN ('dibatalkan')
            GROUP BY p.id_product, p.nama_product, p.harga, p.stok, p.status_produk, 
                     k.nama_kategori, b.nama_brand
            ORDER BY p.stok ASC";

        $stmt = $this->db->query($sql);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $summarySql = "SELECT 
                COUNT(*) as total_products,
                SUM(stok) as total_stock,
                SUM(stok * harga) as total_stock_value,
                COUNT(CASE WHEN stok = 0 THEN 1 END) as out_of_stock,
                COUNT(CASE WHEN stok > 0 AND stok <= 10 THEN 1 END) as low_stock,
                COUNT(CASE WHEN status_produk = 'tersedia' THEN 1 END) as available_products
            FROM products";

        $stmtSummary = $this->db->query($summarySql);
        $summary = $stmtSummary->fetch(\PDO::FETCH_ASSOC);

        $categorySql = "SELECT 
                k.nama_kategori,
                COUNT(p.id_product) as product_count,
                COALESCE(SUM(p.stok), 0) as total_stock,
                COALESCE(SUM(p.stok * p.harga), 0) as stock_value
            FROM kategori k
            LEFT JOIN products p ON k.id_kategori = p.id_kategori
            GROUP BY k.id_kategori, k.nama_kategori
            ORDER BY stock_value DESC";

        $stmtCategory = $this->db->query($categorySql);
        $byCategory = $stmtCategory->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'products' => $products,
            'summary' => $summary,
            'by_category' => $byCategory
        ];
    }

    public function getDailySales(string $period = 'month'): array
    {
        $dateRange = $this->getDateRange($period);

        $params = [];
        $sql = "SELECT 
                DATE(o.tanggal_order) as sale_date,
                COUNT(o.id_order) as order_count,
                COALESCE(SUM(o.total_bayar), 0) as revenue
            FROM orders o
            JOIN payment p ON o.id_order = p.id_order
            WHERE o.status_order NOT IN ('dibatalkan')
            AND p.status_pembayaran = 'berhasil'";

        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }

        $sql .= " GROUP BY DATE(o.tanggal_order) ORDER BY sale_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTopProducts(string $period = 'month', int $limit = 10): array
    {
        $dateRange = $this->getDateRange($period);

        $params = [];
        $sql = "SELECT 
                p.id_product,
                p.nama_product,
                p.harga,
                k.nama_kategori,
                COALESCE(SUM(od.jumlah), 0) as qty_sold,
                COALESCE(SUM(od.subtotal), 0) as revenue
            FROM products p
            LEFT JOIN kategori k ON p.id_kategori = k.id_kategori
            LEFT JOIN order_detail od ON p.id_product = od.id_product
            LEFT JOIN orders o ON od.id_order = o.id_order
            LEFT JOIN payment pay ON o.id_order = pay.id_order
            WHERE (o.status_order NOT IN ('dibatalkan') OR o.status_order IS NULL)
            AND (pay.status_pembayaran = 'berhasil' OR pay.status_pembayaran IS NULL)";

        if ($dateRange['start'] && $dateRange['end']) {
            $sql .= " AND (o.tanggal_order >= :start_date AND o.tanggal_order <= :end_date OR o.tanggal_order IS NULL)";
            $params['start_date'] = $dateRange['start'];
            $params['end_date'] = $dateRange['end'];
        }

        $sql .= " GROUP BY p.id_product, p.nama_product, p.harga, k.nama_kategori
                  ORDER BY qty_sold DESC
                  LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
