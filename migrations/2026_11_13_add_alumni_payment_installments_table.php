<?php
/**
 * Migration: Create tables for installment schedule and payment history
 *
 * This script creates two tables:
 * 1. program_installments - stores schedule for each program (due dates per installment)
 * 2. alumni_installment_payments - stores payment history for each participant per installment
 *
 * Run via: php migrations/2026_11_13_add_installment_tables.php
 */
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    // ---- Create program_installments table ----
    $checkPrograms = $db->query("SHOW TABLES LIKE 'program_installments'");
    if ($checkPrograms->fetch()) {
        echo "Table 'program_installments' already exists.\n";
    } else {
        $sql = "CREATE TABLE program_installments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            program_id INT NOT NULL,
            installment_number INT NOT NULL,
            nominal DECIMAL(15,2) NOT NULL,
            due_date DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_program_installment (program_id, installment_number),
            FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB";
        $db->exec($sql);
        echo "Table 'program_installments' created.\n";
    }

    // ---- Create alumni_installment_payments table ----
    $checkAlumni = $db->query("SHOW TABLES LIKE 'alumni_installment_payments'");
    if ($checkAlumni->fetch()) {
        echo "Table 'alumni_installment_payments' already exists.\n";
    } else {
        $sql = "CREATE TABLE alumni_installment_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            alumni_payment_id INT NOT NULL,
            program_id INT NOT NULL,
            installment_number INT NOT NULL,
            due_date DATE NOT NULL,
            paid_date DATE NULL,
            paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_alumni_installment (alumni_payment_id, installment_number),
            FOREIGN KEY (alumni_payment_id) REFERENCES alumni_payments(id) ON DELETE CASCADE,
            FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
        ) ENGINE=InnoDB";
        $db->exec($sql);
        echo "Table 'alumni_installment_payments' created.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Migration completed successfully.\n";