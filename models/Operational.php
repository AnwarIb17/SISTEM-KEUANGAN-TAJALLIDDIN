<?php
require_once __DIR__ . '/../config/database.php';

class Operational {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Income
    public function getAllIncome($limit = 50) {
        $stmt = $this->db->query("SELECT * FROM operational_income ORDER BY tanggal DESC LIMIT $limit");
        return $stmt->fetchAll();
    }

    public function createIncome($data) {
        $stmt = $this->db->prepare("INSERT INTO operational_income (category, amount, tanggal, notes) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['category'], $data['amount'], $data['tanggal'], $data['notes'] ?? null]);
    }

    public function deleteIncome($id) {
        $stmt = $this->db->prepare("DELETE FROM operational_income WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Expense
    public function getAllExpense($limit = 50) {
        $stmt = $this->db->query("
            SELECT oe.*, ec.name as category_name 
            FROM operational_expense oe 
            JOIN expense_categories ec ON oe.category_id = ec.id 
            ORDER BY oe.tanggal DESC 
            LIMIT $limit
        ");
        return $stmt->fetchAll();
    }

    public function createExpense($data) {
        $stmt = $this->db->prepare("INSERT INTO operational_expense (category_id, amount, tanggal, notes) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['category_id'], $data['amount'], $data['tanggal'], $data['notes'] ?? null]);
    }

    public function deleteExpense($id) {
        $stmt = $this->db->prepare("DELETE FROM operational_expense WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Categories
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM expense_categories ORDER BY name");
        return $stmt->fetchAll();
    }

    public function createCategory($name) {
        $stmt = $this->db->prepare("INSERT INTO expense_categories (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function deleteCategory($id) {
        $stmt = $this->db->prepare("DELETE FROM expense_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Summary
    public function getSummary($bulanTahun = null) {
        $bulanTahun = $bulanTahun ?? date('Y-m');
        
        // Total infaq
        $infaqStmt = $this->db->prepare("SELECT SUM(amount) as total FROM infaq_monthly WHERE DATE_FORMAT(bulan_tahun, '%Y-%m') = ?");
        $infaqStmt->execute([$bulanTahun]);
        $infaq = $infaqStmt->fetch()['total'] ?? 0;

        // Total income
        $incomeStmt = $this->db->prepare("SELECT SUM(amount) as total FROM operational_income WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?");
        $incomeStmt->execute([$bulanTahun]);
        $income = $incomeStmt->fetch()['total'] ?? 0;

        // Total expense
        $expenseStmt = $this->db->prepare("SELECT SUM(amount) as total FROM operational_expense WHERE DATE_FORMAT(tanggal, '%Y-%m') = ?");
        $expenseStmt->execute([$bulanTahun]);
        $expense = $expenseStmt->fetch()['total'] ?? 0;

        return [
            'infaq' => $infaq,
            'income' => $income,
            'expense' => $expense,
            'balance' => ($infaq + $income) - $expense
        ];
    }

    public function getTotalInfaq() {
        $stmt = $this->db->query("SELECT COALESCE(SUM(amount),0) as total FROM infaq_monthly");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getTotalAlumniPayments() {
        $stmt = $this->db->query("SELECT COALESCE(SUM(amount_paid),0) as total FROM alumni_payments");
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getMonthlyReport($year, $month) {
        $bulanTahun = "$year-$month";
        
        $infaqStmt = $this->db->prepare("
            SELECT c.name as class_name, i.amount 
            FROM infaq_monthly i 
            JOIN classes c ON i.class_id = c.id 
            WHERE DATE_FORMAT(i.bulan_tahun, '%Y-%m') = ?
            ORDER BY c.id
        ");
        $infaqStmt->execute([$bulanTahun]);
        
        $incomeStmt = $this->db->prepare("SELECT * FROM operational_income WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? ORDER BY tanggal");
        $incomeStmt->execute([$bulanTahun]);
        
        $expenseStmt = $this->db->prepare("
            SELECT oe.*, ec.name as category_name 
            FROM operational_expense oe 
            JOIN expense_categories ec ON oe.category_id = ec.id 
            WHERE DATE_FORMAT(oe.tanggal, '%Y-%m') = ? 
            ORDER BY oe.tanggal
        ");
        $expenseStmt->execute([$bulanTahun]);

        return [
            'infaq' => $infaqStmt->fetchAll(),
            'income' => $incomeStmt->fetchAll(),
            'expense' => $expenseStmt->fetchAll()
        ];
    }

    public function getExpenseByCategory($month) {
        $stmt = $this->db->prepare("
            SELECT ec.name, SUM(oe.amount) as total
            FROM expense_categories ec
            LEFT JOIN operational_expense oe ON oe.category_id = ec.id AND DATE_FORMAT(oe.tanggal, '%Y-%m') = ?
            GROUP BY ec.id
            HAVING total > 0
            ORDER BY total DESC
        ");
        $stmt->execute([$month]);
        return $stmt->fetchAll();
    }

    public function getYearlyReport($year) {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $month = str_pad($m, 2, '0', STR_PAD_LEFT);
            $months[$m] = $this->getSummary("$year-$month");
        }
        return $months;
    }
}
