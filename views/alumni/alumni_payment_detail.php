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
                                    <th>Tanggal Bayar</th>
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
                                    <td class="text-center">
                                        <?= !empty($p['tanggal_bayar']) ? date('d M Y', strtotime($p['tanggal_bayer'])) : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($p['balance'] ?? 0) < 0 || ($p['amount_paid'] ?? 0) >= ($p['nominal_per_alumni'] ?? 0) ? 'success' : (($p['amount_paid'] ?? 0) > 0 ? 'warning' : 'secondary') ?>">
                                            <?= ($p['amount_paid'] ?? 0) >= ($p['nominal_per_alumni'] ?? 0) ? 'LUNAS' : (($p['amount_paid'] ?? 0) > 0 ? 'KURANG' : 'BELUM') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="program/detail/<?= $p['program_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
