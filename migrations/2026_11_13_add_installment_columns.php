<?php
/**
 * Migration: Add installment support columns
 *
 * This script adds missing columns to an existing database, if they do not already exist.
 * Run via: php migrations/2026_11_13_add_installment_columns.php
 */

require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    // 1. Add installment_count to programs if missing
    $check = $db->query("SHOW COLUMNS FROM programs LIKE 'installment_count'");
    if (!$check->fetch()) {
        $db->exec("ALTER TABLE programs ADD COLUMN installment_count INT NOT NULL DEFAULT 1 AFTER deadline;");
        echo "Column 'installment_count' added to 'programs'.\n";
    } else {
        echo "Column 'installment_count' already exists in 'programs'.\n";
    }

    // 2. Add paid_installments to alumni_payments if missing
    $check = $db->query("SHOW COLUMNS FROM alumni_payments LIKE 'paid_installments'");
    if (!$check->fetch()) {
        $db->exec("ALTER TABLE alumni_payments ADD COLUMN paid_installments INT NOT NULL DEFAULT 0 AFTER amount_paid;");
        echo "Column 'paid_installments' added to 'alumni_payments'.\n";
    } else {
        echo "Column 'paid_installments' already exists in 'alumni_payments'.\n";
    }
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Migration completed successfully.\n";