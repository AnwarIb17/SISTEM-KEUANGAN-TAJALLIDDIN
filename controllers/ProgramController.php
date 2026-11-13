<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Program.php';
require_once __DIR__ . '/../models/Alumni.php';

class ProgramController {
    private $programModel;
    private $alumniModel;

    public function __construct() {
        $this->programModel = new Program();
        $this->alumniModel = new Alumni();
    }

    public function index() {
        requireAuth();
        $programs = $this->programModel->allWithPaidTotal();
        require __DIR__ . '/../views/alumni/program_list.php';
    }

    public function create() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->programModel->create($_POST)) {
                $_SESSION['success'] = 'Program baru berhasil dibuat';
            } else {
                $_SESSION['error'] = 'Gagal membuat program';
            }
        }
        redirect('program');
        exit;
    }

    public function update($id) {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->programModel->update($id, $_POST)) {
                $_SESSION['success'] = 'Program berhasil diupdate';
            } else {
                $_SESSION['error'] = 'Gagal mengupdate program';
            }
        }
        redirect('program');
        exit;
    }

    public function delete($id) {
        requireAuth();
        if ($this->programModel->delete($id)) {
            $_SESSION['success'] = 'Program berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus program';
        }
        redirect('program');
        exit;
    }

    public function detail($id) {
        requireAuth();
        $program = $this->programModel->find($id);
        $program['installment_count'] = $program['installment_count'] ?? 1;
        $payments = $this->programModel->getPayments($id);

        // filter by status
        $statusFilter = $_GET['status'] ?? null;
        if ($statusFilter) {
            $payments = array_values(array_filter($payments, fn($p) => $p['status'] === $statusFilter));
        }

        $allPayments = $this->programModel->getPayments($id);
        $totalTarget = count($allPayments) * ($program['nominal_per_alumni'] ?? 0);
        $totalPaid = array_sum(array_column($allPayments, 'amount_paid'));
        $balance = $totalTarget - $totalPaid;

        $lunas = count(array_filter($allPayments, fn($p) => $p['status'] === 'lunas'));
        $belum = count(array_filter($allPayments, fn($p) => $p['status'] === 'belum'));
        $minus = count(array_filter($allPayments, fn($p) => $p['status'] === 'minus'));

        require __DIR__ . '/../views/alumni/program_detail.php';
    }

    public function alumniDetail($id) {
        requireAuth();
        $alumni = $this->alumniModel->find($id);
        $payments = $this->programModel->getAlumniPayments($id);

        // Fetch installment payment history for each program
        $historyMap = [];
        foreach ($payments as $p) {
            $historyMap[$p['program_id']] = $this->programModel->getProgramInstallmentHistory($id, $p['program_id']);
        }

        $totalTarget = 0;
        $totalPaid = 0;
        $totalOverdue = 0;
        foreach ($payments as $p) {
            $totalTarget += $p['nominal_per_alumni'] ?? 0;
            $totalPaid += (float)($p['amount_paid'] ?? 0);
            $totalOverdue += max(0, ($p['nominal_per_alumni'] ?? 0) - ($p['amount_paid'] ?? 0));
        }
        
        $totalOverdue = max(0, $totalOverdue);
        $status = $totalPaid >= $totalTarget ? 'lunas' : ($totalPaid > 0 ? 'minus' : 'belum');

        require __DIR__ . '/../views/alumni/alumni_payment_detail.php';
    }

    public function payInstallment($alumniId, $programId, $installmentNumber) {
        requireAuth();
        $amount = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
            $amount = floatval($_POST['amount']);
        } elseif (isset($_GET['amount'])) {
            $amount = floatval($_GET['amount']);
        }
        $program = $this->programModel->find($programId);
        $nominalPerAlumni = $program['nominal_per_alumni'] ?? 0;
        if ($this->programModel->payInstallment($alumniId, $programId, (int)$installmentNumber, $amount, $nominalPerAlumni)) {
            $_SESSION['success'] = 'Pembayaran cicilan berhasil dicatat';
        } else {
            $_SESSION['error'] = 'Jumlah pembayaran tidak valid atau melebihi cicilan';
        }
        redirect("program/detail/$programId");
        exit;
    }

    public function updatePayment($alumniId, $programId) {
        requireAuth();
        $amount = 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
            $amount = floatval($_POST['amount']);
        } elseif (isset($_GET['amount'])) {
            $amount = floatval($_GET['amount']);
        }
        $program = $this->programModel->find($programId);
        $nominalPerAlumni = $program['nominal_per_alumni'] ?? 0;
        if ($amount > 0 && $amount <= $nominalPerAlumni && $this->programModel->updatePayment($alumniId, $programId, $amount, $nominalPerAlumni)) {
            $_SESSION['success'] = 'Pembayaran berhasil dicatat';
        } else {
            $_SESSION['error'] = 'Pembayaran melebihi target per alumni';
        }
        redirect("program/detail/$programId");
        exit;
    }

    public function manageAlumni($programId) {
        requireAuth();
        $program = $this->programModel->find($programId);
        if (!$program) {
            http_response_code(404);
            echo "Program not found";
            exit;
        }
        $participants = $this->programModel->getPayments($programId);
        $available = $this->programModel->getAvailableAlumni($programId);
        require __DIR__ . '/../views/alumni/program_manage_alumni.php';
    }

    public function addParticipant($programId, $alumniId) {
        requireAuth();
        if ($this->programModel->addParticipant($programId, $alumniId)) {
            $_SESSION['success'] = 'Alumni ditambahkan ke program';
        } else {
            $_SESSION['error'] = 'Gagal menambahkan alumni';
        }
        redirect("program/manage/$programId");
        exit;
    }

    public function removeParticipant($programId, $alumniId) {
        requireAuth();
        if ($this->programModel->removeParticipant($programId, $alumniId)) {
            $_SESSION['success'] = 'Alumni dihapus dari program';
        } else {
            $_SESSION['error'] = 'Tidak bisa menghapus alumni yang sudah membayar';
        }
        redirect("program/manage/$programId");
        exit;
    }

    public function export($programId) {
        requireAuth();
        $program = $this->programModel->find($programId);
        if (!$program) {
            http_response_code(404);
            echo "Program not found";
            exit;
        }
        $payments = $this->programModel->getPayments($programId);
        
        // Export as HTML table (opens in Excel)
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="program_' . preg_replace('/[^a-z0-9]/i', '_', $program['name']) . '.xls"');
        
        echo "<html><head><meta charset='utf-8'></head><body>";
        echo "<h2>" . htmlspecialchars($program['name']) . "</h2>";
        echo "<p>Target per alumni: Rp " . number_format($program['nominal_per_alumni'], 0, ',', '.') . "</p>";
        echo "<p>Tanggal: " . date('d M Y') . "</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>No</th><th>Nama Alumni</th><th>Kontak</th><th>Dibayar</th><th>Sisa</th><th>Status</th></tr>";
        $no = 1;
        foreach ($payments as $p) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($p['alumni_name']) . "</td>";
            echo "<td>" . htmlspecialchars($p['contact'] ?? '-') . "</td>";
            echo "<td>" . number_format($p['amount_paid'], 0, ',', '.') . "</td>";
            echo "<td>" . number_format(max(0, $p['nominal_per_alumni'] - $p['amount_paid']), 0, ',', '.') . "</td>";
            echo "<td>" . strtoupper($p['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table></body></html>";
        exit;
    }
}
