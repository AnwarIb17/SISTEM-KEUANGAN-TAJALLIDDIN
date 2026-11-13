<?php
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();
$sql = "SELECT COLUMN_NAME, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='pondok_keuangan' AND TABLE_NAME='programs' ORDER BY ORDINAL_POSITION";
$stmt = $db->query($sql);
echo "programs columns:\n";
foreach ($stmt as $row) {
    echo $row['COLUMN_NAME'] . ' -> ' . $row['COLUMN_TYPE'] . "\n";
}
echo "\nalumni_payments columns:\n";
$sql2 = "SELECT COLUMN_NAME, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='pondok_keuangan' AND TABLE_NAME='alumni_payments' ORDER BY ORDINAL_POSITION";
$stmt2 = $db->query($sql2);
foreach ($stmt2 as $row) {
    echo $row['COLUMN_NAME'] . ' -> ' . $row['COLUMN_TYPE'] . "\n";
}