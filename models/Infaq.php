<?php
require_once __DIR__ . '/../config/database.php';

class Infaq {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getClasses() {
        $stmt = $this->db->query("SELECT * FROM classes ORDER BY id");
        return $stmt->fetchAll();
    }

    public function getByMonth($bulanTahun) {
        $stmt = $this->db->prepare("
            SELECT i.*, c.name as class_name 
            FROM infaq_monthly i 
            JOIN classes c ON i.class_id = c.id 
            WHERE i.bulan_tahun = ?
            ORDER BY c.id
        ");
        $stmt->execute([$bulanTahun]);
        return $stmt->fetchAll();
    }

    public function createOrUpdate($classId, $bulanTahun, $amount) {
        $stmt = $this->db->prepare("
            INSERT INTO infaq_monthly (class_id, bulan_tahun, amount) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE amount = VALUES(amount)
        ");
        return $stmt->execute([$classId, $bulanTahun, $amount]);
    }

    public function getTotalByMonth($bulanTahun) {
        $month = substr($bulanTahun, 0, 7); // normalise to YYYY-MM
        $stmt = $this->db->prepare("SELECT SUM(amount) as total FROM infaq_monthly WHERE DATE_FORMAT(bulan_tahun, '%Y-%m') = ?");
        $stmt->execute([$month]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getMonthlySummary($year) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(bulan_tahun, '%Y-%m') as month,
                SUM(amount) as total
            FROM infaq_monthly
            WHERE YEAR(bulan_tahun) = ?
            GROUP BY DATE_FORMAT(bulan_tahun, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();
        // Build full 12-month timeline (Jan-Dec) with 0 for missing months
        $map = [];
        foreach ($rows as $r) {
            $map[$r['month']] = (float)$r['total'];
        }
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%s-%02d', $year, $m);
            $result[] = [
                'month' => $key,
                'total' => $map[$key] ?? 0
            ];
        }
        return $result;
    }

    public function getMonthlySummaryByClass($classId, $year = null) {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(bulan_tahun, '%Y-%m') as month,
                SUM(amount) as total
            FROM infaq_monthly
            WHERE class_id = ? AND YEAR(bulan_tahun) = ?
            GROUP BY DATE_FORMAT(bulan_tahun, '%Y-%m')
            ORDER BY month
        ");
        $stmt->execute([$classId, $year]);
        $rows = $stmt->fetchAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['month']] = (float)$r['total'];
        }
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%s-%02d', $year, $m);
            $result[] = [
                'month' => $key,
                'total' => $map[$key] ?? 0
            ];
        }
        return $result;
    }
}
