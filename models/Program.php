<?php
require_once __DIR__ . '/../config/database.php';

class Program {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function allWithPaidTotal() {
        $sql = "SELECT p.*, COALESCE(SUM(ap.amount_paid),0) AS paid_total,
                       COUNT(ap.id) AS alumni_count
                FROM programs p
                LEFT JOIN alumni_payments ap ON ap.program_id = p.id
                GROUP BY p.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM programs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO programs (name, nominal_per_alumni, deadline, installment_count) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['nominal_per_alumni'], $data['deadline'] ?? null, (int)($data['installment_count'] ?? 1)]);
    }

    // Add an alumni to a program (participation)
    public function addParticipant($programId, $alumniId) {
        // prevent duplicate
        $chk = $this->db->prepare("SELECT id FROM alumni_payments WHERE program_id = ? AND alumni_id = ?");
        $chk->execute([$programId, $alumniId]);
        if ($chk->fetch()) {
            return true; // already exists
        }
        $stmt = $this->db->prepare("INSERT INTO alumni_payments (alumni_id, program_id, amount_paid) VALUES (?, ?, 0)");
        return $stmt->execute([$alumniId, $programId]);
    }

    // Remove an alumni from a program (only if unpaid)
    public function removeParticipant($programId, $alumniId) {
        $chk = $this->db->prepare("SELECT amount_paid FROM alumni_payments WHERE program_id = ? AND alumni_id = ?");
        $chk->execute([$programId, $alumniId]);
        $row = $chk->fetch();
        if (!$row) return true; // not a participant
        if ((float)$row['amount_paid'] > 0) {
            return false; // cannot remove if already paid
        }
        $stmt = $this->db->prepare("DELETE FROM alumni_payments WHERE program_id = ? AND alumni_id = ?");
        return $stmt->execute([$programId, $alumniId]);
    }

    // Get alumni payments (including installment info)
    public function getPayments($programId) {
        $stmt = $this->db->prepare("
            SELECT 
                a.id as alumni_id,
                a.name as alumni_name,
                a.contact,
                ap.tanggal_bayar,
                COALESCE(ap.amount_paid, 0) as amount_paid,
                COALESCE(ap.paid_installments, 0) as paid_installments,
                p.nominal_per_alumni,
                (COALESCE(ap.amount_paid, 0) - p.nominal_per_alumni) as balance,
                CASE 
                    WHEN COALESCE(ap.amount_paid, 0) >= p.nominal_per_alumni THEN 'lunas'
                    WHEN COALESCE(ap.amount_paid, 0) = 0 THEN 'belum'
                    ELSE 'minus'
                END as status,
                p.installment_count,
                ap.tanggal_bayar as payment_date
            FROM alumni a
            INNER JOIN alumni_payments ap ON a.id = ap.alumni_id AND ap.program_id = ?
            LEFT JOIN programs p ON p.id = ?
            ORDER BY a.name
        ");
        $stmt->execute([$programId, $programId]);
        return $stmt->fetchAll();
    }

    // Get alumni payments across all programs for a specific alumni
    public function getAlumniPayments($alumniId) {
        $stmt = $this->db->prepare("
            SELECT 
                ap.id as payment_id,
                ap.program_id,
                ap.amount_paid,
                ap.paid_installments,
                ap.tanggal_bayar,
                ap.tanggal_bayar as payment_date,
                p.name as program_name,
                p.nominal_per_alumni,
                p.installment_count,
                (COALESCE(ap.amount_paid, 0) - p.nominal_per_alumni) as balance
            FROM alumni_payments ap
            INNER JOIN programs p ON ap.program_id = p.id
            WHERE ap.alumni_id = ?
            ORDER BY ap.program_id DESC
        ");
        $stmt->execute([$alumniId]);
        return $stmt->fetchAll();
    }

    // Get installment schedule for a program
    public function getInstallmentSchedule($programId) {
        $stmt = $this->db->prepare("
            SELECT installment_number, nominal, due_date
            FROM program_installments
            WHERE program_id = ?
            ORDER BY installment_number
        ");
        $stmt->execute([$programId]);
        return $stmt->fetchAll();
    }

    // Get payment per installment for a specific alumni & program
    public function getProgramInstallmentHistory($alumniId, $programId) {
        $stmt = $this->db->prepare("
            SELECT aip.installment_number, aip.due_date, aip.paid_amount, aip.paid_date
            FROM alumni_installment_payments aip
            INNER JOIN alumni_payments ap ON aip.alumni_payment_id = ap.id
            WHERE ap.alumni_id = ? AND aip.program_id = ?
            ORDER BY aip.installment_number
        ");
        $stmt->execute([$alumniId, $programId]);
        return $stmt->fetchAll();
    }

    // Get available alumni NOT participating in a program
    public function getAvailableAlumni($programId) {
        $stmt = $this->db->prepare("
            SELECT a.* FROM alumni a
            WHERE a.id NOT IN (SELECT alumni_id FROM alumni_payments WHERE program_id = ?)
            ORDER BY a.name
        ");
        $stmt->execute([$programId]);
        return $stmt->fetchAll();
    }

    // Check if alumni is a participant
    public function isParticipant($programId, $alumniId) {
        $stmt = $this->db->prepare("SELECT id FROM alumni_payments WHERE program_id = ? AND alumni_id = ?");
        $stmt->execute([$programId, $alumniId]);
        return (bool)$stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE programs SET name = ?, nominal_per_alumni = ?, deadline = ?, installment_count = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['nominal_per_alumni'], $data['deadline'] ?? null, (int)($data['installment_count'] ?? 1), $id]);
    }

    // Pay an installment (add amount to total paid and update installment count)
    public function payInstallment($alumniId, $programId, $installmentNumber, $amount, $nominalPerAlumni) {
        $program = $this->find($programId);
        if (!$program || $amount <= 0) return false;
        $installmentCount = max(1, (int)($program['installment_count'] ?? 1));
        $installmentAmount = round($nominalPerAlumni / $installmentCount, 2);

        $stmt = $this->db->prepare("SELECT id, amount_paid, paid_installments FROM alumni_payments WHERE alumni_id = ? AND program_id = ?");
        $stmt->execute([$alumniId, $programId]);
        $row = $stmt->fetch();
        if (!$row) return false;

        $paymentId = (int)$row['id'];
        $newAmount = (float)$row['amount_paid'] + $amount;
        if ($newAmount > $nominalPerAlumni) return false;
        if ((int)$row['paid_installments'] >= $installmentNumber) return false;

        // Check existing payment for this installment
        $stmt = $this->db->prepare("SELECT id, paid_amount FROM alumni_installment_payments WHERE alumni_payment_id = ? AND installment_number = ?");
        $stmt->execute([$paymentId, $installmentNumber]);
        $hist = $stmt->fetch();
        $paidForInstallment = $hist ? (float)$hist['paid_amount'] : 0;
        if ($paidForInstallment + $amount > $installmentAmount) return false;

        $dueDate = $program['deadline'] ?? date('Y-m-d');
        $stmt = $this->db->prepare("SELECT due_date FROM program_installments WHERE program_id = ? AND installment_number = ?");
        $stmt->execute([$programId, $installmentNumber]);
        $sched = $stmt->fetch();
        if ($sched && $sched['due_date']) {
            $dueDate = $sched['due_date'];
        }

        $this->db->beginTransaction();
        try {
            if ($hist) {
                $stmt = $this->db->prepare("UPDATE alumni_installment_payments SET paid_amount = ?, due_date = ?, paid_date = CURDATE(), updated_at = NOW() WHERE id = ?");
                $stmt->execute([$paidForInstallment + $amount, $dueDate, $hist['id']]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO alumni_installment_payments (alumni_payment_id, program_id, installment_number, due_date, paid_date, paid_amount) VALUES (?, ?, ?, ?, CURDATE(), ?)");
                $stmt->execute([$paymentId, $programId, $installmentNumber, $dueDate, $amount]);
            }

            $newPaid = (int)$row['paid_installments'];
            if ($paidForInstallment + $amount >= $installmentAmount) {
                $newPaid = max($newPaid, (int)$installmentNumber);
            }

            $stmt = $this->db->prepare("UPDATE alumni_payments SET amount_paid = ?, paid_installments = ?, tanggal_bayar = CURDATE(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newAmount, $newPaid, $paymentId]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Single payment (adds to existing amount_paid, for non-installment or full payment)
    public function updatePayment($alumniId, $programId, $amount, $nominalPerAlumni) {
        if ($amount < 0) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT amount_paid FROM alumni_payments WHERE alumni_id = ? AND program_id = ?");
        $stmt->execute([$alumniId, $programId]);
        $row = $stmt->fetch();
        $currentPaid = $row ? (float)$row['amount_paid'] : 0;
        $newAmount = $currentPaid + $amount;
        if ($newAmount > $nominalPerAlumni) {
            return false; // cannot exceed target per alumni
        }
        $stmt = $this->db->prepare("
            UPDATE alumni_payments 
            SET amount_paid = ?, tanggal_bayar = CURDATE() 
            WHERE alumni_id = ? AND program_id = ?
        ");
        return $stmt->execute([$newAmount, $alumniId, $programId]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM programs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}