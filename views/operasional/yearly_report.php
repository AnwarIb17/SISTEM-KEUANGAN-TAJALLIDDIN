<?php
$title = 'Laporan Tahunan';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-calendar-alt text-primary me-2"></i>
            Laporan Tahunan - <?= date('Y', strtotime($year)) ?>
        </h1>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 px-4">
            <h6 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Ringkasan Tahunan
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Infaq</th>
                            <th>Pemasukkan</th>
                            <th>Pengeluaran</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <?php $monthData = $summary[$m] ?? null; ?>
                        <?php if ($monthData): ?>
                        <tr>
                            <td><?= date('F', mktime(0, 0, 0, $m, 1)) ?></td>
                            <td><?= number_format($monthData['infaq'], 0, ',', '.') ?></td>
                            <td><?= number_format($monthData['income'], 0, ',', '.') ?></td>
                            <td><?= number_format($monthData['expense'], 0, ',', '.') ?></td>
                            <td class="text-success fw-bold">
                                <?= number_format($monthData['balance'], 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><b>Total Tahunan</b></td>
                            <td><b><?= number_format(array_sum(array_column($summary, 'infaq')), 0, ',', '.') ?></b></td>
                            <td><b><?= number_format(array_sum(array_column($summary, 'income')), 0, ',', '.') ?></b></td>
                            <td><b><?= number_format(array_sum(array_column($summary, 'expense')), 0, ',', '.') ?></b></td>
                            <td><b><?= number_format(array_sum(array_column($summary, 'balance')), 0, ',', '.') ?></b></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>