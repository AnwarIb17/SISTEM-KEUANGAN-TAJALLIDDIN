<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Infaq.php';
require_once __DIR__ . '/../models/Operational.php';

class DashboardController {
    public function index() {
        requireAuth();

        $year = (int)($_GET['year'] ?? date('Y'));
        if ($year < 2000 || $year > 2100) $year = (int)date('Y');

        $infaqModel = new Infaq();
        $operationalModel = new Operational();

        $currentMonth = date('Y-m');
        $summary = $operationalModel->getSummary($currentMonth);

        // 6 cards
        $infaqBulanan = $summary['infaq'];                                   // 1. Infaq bulan ini
        $infaqTotal = $operationalModel->getTotalInfaq();                    // 2. Total infaq keseluruhan
        $alumniTotal = $operationalModel->getTotalAlumniPayments();           // 3. Total keuangan alumni
        $pemasukkan = $summary['income'];                                     // 4. Pemasukkan
        $pengeluaran = $summary['expense'];                                   // 5. Pengeluaran
        $sisa = $summary['balance'];                                          // 6. Sisa (infaq + income - expense)

        // Prepare chart data for infaq
        $infaqChart = $infaqModel->getMonthlySummary($year);
        $chartLabels = array_column($infaqChart, 'month');
        $chartData = array_column($infaqChart, 'total');

        // Prepare expense categories for chart
        $expenseByCategory = $operationalModel->getExpenseByCategory($currentMonth);

        // expose selected year to view
        $selectedYear = $year;

        require __DIR__ . '/../views/dashboard/index.php';
    }
}