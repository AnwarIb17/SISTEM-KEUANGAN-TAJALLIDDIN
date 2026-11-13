<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Operational.php';
require_once __DIR__ . '/../models/Infaq.php';

class OperationalController {
    private $model;

    public function __construct() {
        $this->model = new Operational();
    }

    public function incomeList() {
        requireAuth();
        $incomes = $this->model->getAllIncome();
        require __DIR__ . '/../views/operasional/income.php';
    }

    public function createIncome() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->createIncome($_POST)) {
                $_SESSION['success'] = 'Pemasukkan berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan pemasukkan';
            }
        }
        redirect('income');
        exit;
    }

    public function deleteIncome($id) {
        requireAuth();
        if ($this->model->deleteIncome($id)) {
            $_SESSION['success'] = 'Pemasukkan berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus pemasukkan';
        }
        redirect('income');
        exit;
    }

    public function expenseList() {
        requireAuth();
        $expenses = $this->model->getAllExpense();
        $categories = $this->model->getAllCategories();
        require __DIR__ . '/../views/operasional/expense.php';
    }

    public function createExpense() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->createExpense($_POST)) {
                $_SESSION['success'] = 'Pengeluaran berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan pengeluaran';
            }
        }
        redirect('expense');
        exit;
    }

    public function deleteExpense($id) {
        requireAuth();
        if ($this->model->deleteExpense($id)) {
            $_SESSION['success'] = 'Pengeluaran berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus pengeluaran';
        }
        redirect('expense');
        exit;
    }

    public function categoryList() {
        requireAuth();
        $categories = $this->model->getAllCategories();
        require __DIR__ . '/../views/operasional/category.php';
    }

    public function createCategory() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            if ($this->model->createCategory($_POST['name'])) {
                $_SESSION['success'] = 'Kategori pengeluaran berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan kategori';
            }
        }
        redirect('category');
        exit;
    }

    public function deleteCategory($id) {
        requireAuth();
        if ($this->model->deleteCategory($id)) {
            $_SESSION['success'] = 'Kategori pengeluaran berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus kategori';
        }
        redirect('category');
        exit;
    }

    public function infaqList() {
        requireAuth();
        $infaqModel = new Infaq();
        $classes = $infaqModel->getClasses();
        $bulanTahun = $_GET['month'] ?? date('Y-m-01');
        // normalize: if passed YYYY-MM, append -01
        if (strlen($bulanTahun) === 7) $bulanTahun .= '-01';
        $infaqData = $infaqModel->getByMonth($bulanTahun);
        $amounts = [];
        foreach ($infaqData as $row) {
            $amounts[$row['class_id']] = $row['amount'];
        }
        $total = $infaqModel->getTotalByMonth($bulanTahun);

// Chart data
$selectedClass = $_GET['class'] ?? '';
$year = $_GET['year'] ?? date('Y', strtotime($bulanTahun));
$chartData = [];
if ($selectedClass !== '') {
    $chartData = $infaqModel->getMonthlySummaryByClass($selectedClass, $year);
} else {
    $chartData = $infaqModel->getMonthlySummary($year);
}

$selectedClassName = '';
if ($selectedClass !== '') {
    foreach ($classes as $class) {
        if ($class['id'] == $selectedClass) {
            $selectedClassName = $class['name'];
            break;
        }
    }
}

$monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$chartLabels = [];
$chartValues = [];
foreach ($chartData as $row) {
    $monthKey = $row['month'];
    $monthNum = (int)substr($monthKey, 5, 2);
    $chartLabels[] = $monthNames[$monthNum - 1];
    $chartValues[] = (float)($row['total'] ?? 0);
}

require __DIR__ . '/../views/operasional/infaq.php';
    }

    public function saveInfaq() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $infaqModel = new Infaq();
            $bulanTahun = $_POST['bulan_tahun'] ?? date('Y-m-01');
            $success = true;
            foreach ($_POST['amount'] as $classId => $amount) {
                if ($amount !== '' && !$infaqModel->createOrUpdate($classId, $bulanTahun, floatval($amount))) {
                    $success = false;
                }
            }
            if ($success) {
                $_SESSION['success'] = 'Data infaq berhasil disimpan';
            } else {
                $_SESSION['error'] = 'Gagal menyimpan data infaq';
            }
        }
        redirect('infaq');
        exit;
    }

    public function monthlyReport() {
        requireAuth();
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');
        
        $report = $this->model->getMonthlyReport($year, $month);
        
        require __DIR__ . '/../views/operasional/report.php';
    }

    public function yearlyReport() {
        requireAuth();
        $year = $_GET['year'] ?? date('Y');
        
        $summary = [];
        for ($m = 1; $m <= 12; $m++) {
            $summary[$m] = $this->model->getSummary("$year-" . str_pad($m, 2, '0', STR_PAD_LEFT));
        }
        
        require __DIR__ . '/../views/operasional/yearly_report.php';
    }
}
