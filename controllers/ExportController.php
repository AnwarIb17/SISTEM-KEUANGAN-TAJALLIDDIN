<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Program.php';
require_once __DIR__ . '/../models/Operational.php';

class ExportController {
    private $programModel;
    private $operationalModel;

    public function __construct() {
        $this->programModel = new Program();
        $this->operationalModel = new Operational();
    }

    public function monthlyExcel() {
        requireAuth();
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');
        
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Laporan Operasional');
        $sheet->setCellValue('B1', date('F Y'));
        $sheet->mergeCells('A1:D1');
        
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Infaq');
        $row++;
        
        foreach ($this->operationalModel->getMonthlyReport($year, $month)['infaq'] as $item) {
            $sheet->setCellValue('A' . $row, $item['class_name']);
            $sheet->setCellValue('B' . $row, $item['amount']);
            $row++;
        }
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Pemasukkan');
        $row++;
        
        foreach ($this->operationalModel->getMonthlyReport($year, $month)['income'] as $item) {
            $sheet->setCellValue('A' . $row, $item['category']);
            $sheet->setCellValue('B' . $row, $item['amount']);
            $row++;
        }
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Pengeluaran');
        $row++;
        
        foreach ($this->operationalModel->getMonthlyReport($year, $month)['expense'] as $item) {
            $sheet->setCellValue('A' . $row, $item['category_name']);
            $sheet->setCellValue('B' . $row, $item['amount']);
            $row++;
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="laporan_' . $year . '_' . $month . '.xlsx"');
        
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function programExcel($programId) {
        requireAuth();
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $program = $this->programModel->find($programId);
        $payments = $this->programModel->getPayments($programId);
        
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', $program['name']);
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A2', 'Tanggal: ' . date('d M Y'));
        $sheet->setCellValue('A3', 'Target per alumni: ' . number_format($program['nominal_per_alumni'], 0, ',', '.'));
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama Alumni');
        $sheet->setCellValue('C5', 'Kontak');
        $sheet->setCellValue('D5', 'Dibayar');
        $sheet->setCellValue('E5', 'Sisa');
        $sheet->setCellValue('F5', 'Status');
        
        $row = 6;
        foreach ($payments as $i => $p) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p['alumni_name']);
            $sheet->setCellValue('C' . $row, $p['contact'] ?? '-');
            $sheet->setCellValue('D' . $row, $p['amount_paid']);
            $sheet->setCellValue('E' . $row, $p['nominal_per_alumni'] - $p['amount_paid']);
            $sheet->setCellValue('F' . $row, strtoupper($p['status']));
            $row++;
        }
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="program_' . $program['name'] . '.xlsx"');
        
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
