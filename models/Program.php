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
        $stmt = $this->db->prepare("INSERT INTO programs (name, nominal_per_alumni, deadline) VALUES (?, ?, ?)");
        return $stmt->execute([$data['name'], $data['nominal_per_alumni'], $data['deadline'] ?? null]);
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

    // Get alumni NOT participating in a program
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
        $stmt = $this->db->prepare("UPDATE programs SET name = ?, nominal_per_alumni = ?, deadline = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['nominal_per_alumni'], $data['deadline'] ?? null, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM programs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getPayments($programId) {
        $stmt = $this->db->prepare("
            SELECT 
                a.id as alumni_id,
                a.name as alumni_name,
                a.contact,
                COALESCE(ap.amount_paid, 0) as amount_paid,
                p.nominal_per_alumni,
                (COALESCE(ap.amount_paid, 0) - p.nominal_per_alumni) as balance,
                CASE 
                    WHEN COALESCE(ap.amount_paid, 0) >= p.nominal_per_alumni THEN 'lunas'
                    WHEN COALESCE(ap.amount_paid, 0) = 0 THEN 'belum'
                    ELSE 'minus'
                END as status
            FROM alumni a
            INNER JOIN alumni_payments ap ON a.id = ap.alumni_id AND ap.program_id = ?
            LEFT JOIN programs p ON p.id = ?
            ORDER BY a.name
        ");
        $stmt->execute([$programId, $programId]);
        return $stmt->fetchAll();
    }

    public function updatePayment($alumniId, $programId, $amount, $nominalPerAlumni) {
        if ($amount < 0 || $amount > $nominalPerAlumni) {
            return false;
        }
        $stmt = $this->db->prepare("
            UPDATE alumni_payments 
            SET amount_paid = ?, tanggal_bayar = CURDATE() 
            WHERE alumni_id = ? AND program_id = ?
        ");
        return $stmt->execute([$amount, $alumniId, $programId]);
    }
}
