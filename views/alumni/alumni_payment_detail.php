<?php
$programName = 'Semua Program';
if (!empty($program_name)) {
    $programName = htmlspecialchars($program_name);
}
$title = 'Detail Pembayaran Alumni - ' . htmlspecialchars($alumni['name']);
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-graduate text-primary me-2"></i>
                <?= htmlspecialchars($alumni['name']) ?>
            </h1>
            <a href="program" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <div class="d-flex gap-2">
            <a href="program" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-list me-2"></i>Daftar Program
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total Target</h6>
                        <h4 class="mb-0"><?= number_format($totalTarget, 0, ',', '.') ?></h4>
                        <small>Total seluruh program</small>
                    </div>
                    <i class="fas fa-bullseye fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total Terbayar</h6>
                        <h4 class="mb-0"><?= number_format($totalPaid, 0, ',', '.') ?></h4>
                        <small>Status: <?= strtoupper($status) ?></small>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Tunggakan</h6>
                        <h4 class="mb-0 text-danger"><?= number_format($totalOverdue, 0, ',', '.') ?></h4>
                        <small>Belum dibayar</small>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Status</h6>
                        <h4 class="mb-0">
                            <?= $status === 'lunas' ? 'LUNAS' : ($status === 'minus' ? 'KURANG' : 'BELUM') ?>
                        </h4>
                        <small>Semua program</small>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        Detail Pembayaran Berdasarkan Program
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($payments)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p>Alumni ini belum berpartisipasi dalam program manapun.</p>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Program</th>
                                    <th>Target/Alumni</th>
                                    <th>Cicilan/Alumni</th>
                                    <th>Dibayar</th>
                                    <th>Sisa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $index => $p): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <a href="program/detail/<?= $p['program_id'] ?>" class="text-decoration-none fw-bold">
                                            <?= htmlspecialchars($p['program_name']) ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?= number_format($p['nominal_per_alumni'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php
                                            $installmentCount = (int)($p['installment_count'] ?? 1);
                                            if ($installmentCount > 1) {
                                                echo $installmentCount . ' x ' . number_format($p['nominal_per_alumni'] / max(1, $installmentCount), 0, ',', '.');
                                            } else {
                                                echo '1 x ' . number_format($p['nominal_per_alumni'], 0, ',', '.');
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center fw-bold">
                                        <?= $p['amount_paid'] > 0 ? number_format($p['amount_paid'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-center <?= ($p['balance'] ?? 0) < 0 ? 'text-danger' : (($p['balance'] ?? 0) > 0 ? 'text-warning' : 'text-success') ?>">
                                        <?= ($p['balance'] ?? 0) != 0 ? number_format($p['balance'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($p['balance'] ?? 0) < 0 || ($p['amount_paid'] ?? 0) >= ($p['nominal_per_alumni'] ?? 0) ? 'success' : (($p['amount_paid'] ?? 0) > 0 ? 'warning' : 'secondary') ?>">
                                            <?= ($p['amount_paid'] ?? 0) >= ($p['nominal_per_alumni'] ?? 0) ? 'LUNAS' : (($p['amount_paid'] ?? 0) > 0 ? 'KURANG' : 'BELUM') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#payDetail_<?= $p['program_id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php foreach ($payments as $p):
                        $pid = $p['program_id'];
                        $sched = [];
                        foreach (($scheduleMap[$pid] ?? []) as $s) {
                            $sched[(int)$s['installment_number']] = $s;
                        }
                        $hist = [];
                        foreach (($historyMap[$pid] ?? []) as $h) {
                            $hist[(int)$h['installment_number']] = $h;
                        }
                        $rowCount = max(1, (int)($p['installment_count'] ?? 1), $sched ? max(array_keys($sched)) : 0, $hist ? max(array_keys($hist)) : 0);
                        $defaultNominal = $p['nominal_per_alumni'] / $rowCount;
                        $legacy = !$sched && !$hist && $p['amount_paid'] > 0;
                        if ($legacy) {
                            $rowCount = 1;
                        }
                        $totalPaid = 0;
                        foreach ($hist as $h) {
                            $totalPaid += (float)$h['paid_amount'];
                        }
                        if (!$hist) {
                            $totalPaid = (float)$p['amount_paid'];
                        }
                    ?>
                    <div class="modal fade" id="payDetail_<?= $pid ?>" tabindex="-1" aria-labelledby="payDetailLabel_<?= $pid ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="payDetailLabel_<?= $pid ?>">
                                        <i class="fas fa-receipt me-2"></i>Detail Pembayaran: <?= htmlspecialchars($p['program_name']) ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Cicilan</th>
                                                    <th>Jatuh Tempo</th>
                                                    <th class="text-end">Nominal</th>
                                                    <th class="text-end">Dibayar</th>
                                                    <th>Tanggal Bayar</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if ($legacy): ?>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td class="text-center"><?= $p['payment_date'] ? date('d M Y', strtotime($p['payment_date'])) : '-' ?></td>
                                                    <td class="text-end"><?= number_format($p['nominal_per_alumni'], 0, ',', '.') ?></td>
                                                    <td class="text-end"><?= number_format($p['amount_paid'], 0, ',', '.') ?></td>
                                                    <td class="text-center"><?= $p['payment_date'] ? date('d M Y', strtotime($p['payment_date'])) : '-' ?></td>
                                                    <td class="text-center">
                                                        <?php $legacyPaid = $p['amount_paid'] >= $p['nominal_per_alumni']; ?>
                                                        <span class="badge bg-<?= $legacyPaid ? 'success' : 'warning' ?>"><?= $legacyPaid ? 'LUNAS' : 'PARTIAL' ?></span>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php for ($i = 1; $i <= $rowCount; $i++):
                                                    $nominal = isset($sched[$i]) ? (float)$sched[$i]['nominal'] : $defaultNominal;
                                                    $dueDate = $sched[$i]['due_date'] ?? ($hist[$i]['due_date'] ?? null);
                                                    $paid = isset($hist[$i]) ? (float)$hist[$i]['paid_amount'] : 0;
                                                    $paidDate = $hist[$i]['paid_date'] ?? null;
                                                    $isPaid = $paid >= $nominal;
                                                    $isPartial = !$isPaid && $paid > 0;
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?= $i ?></td>
                                                    <td class="text-center"><?= $dueDate ? date('d M Y', strtotime($dueDate)) : '-' ?></td>
                                                    <td class="text-end"><?= number_format($nominal, 0, ',', '.') ?></td>
                                                    <td class="text-end"><?= $paid > 0 ? number_format($paid, 0, ',', '.') : '-' ?></td>
                                                    <td class="text-center"><?= $paidDate ? date('d M Y', strtotime($paidDate)) : '-' ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?= $isPaid ? 'success' : ($isPartial ? 'warning' : 'secondary') ?>">
                                                            <?= $isPaid ? 'LUNAS' : ($isPartial ? 'PARTIAL' : 'BELUM') ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-end">Total Dibayar</th>
                                                    <th class="text-end"><?= number_format($totalPaid, 0, ',', '.') ?></th>
                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
