<?php
$title = 'Detail Program - ' . htmlspecialchars($program['name']);
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-project-diagram text-primary me-2"></i>
                <?= htmlspecialchars($program['name']) ?>
            </h1>
            <a href="program" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
        <div class="d-flex gap-2">
            <a href="program/manage/<?= $program['id'] ?>" class="btn btn-outline-primary">
                <i class="fas fa-users-cog me-2"></i>Kelola Alumni
            </a>
            <a href="program/export/<?= $program['id'] ?>?type=excel" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="program/export/<?= $program['id'] ?>?type=pdf" class="btn btn-info">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Target</h6>
                        <h4 class="mb-0"><?= number_format($program['nominal_per_alumni'], 0, ',', '.') ?></h4>
                        <small>Per Alumni</small>
                    </div>
                    <i class="fas fa-bullseye fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Total Keuangan</h6>
                        <h4 class="mb-0"><?= number_format($totalTarget, 0, ',', '.') ?></h4>
                        <small><?= count($payments) ?> alumni</small>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Uang Terkumpul</h6>
                        <h4 class="mb-0"><?= number_format($totalPaid, 0, ',', '.') ?></h4>
                        <small><?= $totalPaid > 0 ? (count($payments) - count(array_filter($payments, fn($p) => $p['status'] === 'belum'))) : '0' ?> alumni</small>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 opacity-75">Keurangan</h6>
                        <h4 class="mb-0"><?= number_format($balance, 0, ',', '.') ?></h4>
                        <small><?= $balance >= 0 ? 'Belum lunas' : 'Telah lunas' ?></small>
                    </div>
                    <i class="fas fa-wallet fa-2x opacity-20"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex gap-2 flex-wrap">
                <a href="program/detail/<?= $program['id'] ?>" class="btn btn-outline-primary btn-sm">
                    Semua (<?= count($payments) ?>)
                </a>
                <a href="program/detail/<?= $program['id'] ?>?status=lunas" class="btn btn-success btn-sm">
                    Lunas (<?= $lunas ?>)
                </a>
                <a href="program/detail/<?= $program['id'] ?>?status=belum" class="btn btn-warning btn-sm">
                    Belum Bayar (<?= $belum ?>)
                </a>
                <a href="program/detail/<?= $program['id'] ?>?status=minus" class="btn btn-danger btn-sm">
                    Kurang (<?= $minus ?>)
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Daftar Alumni & Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($payments)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>Belum ada alumni yang ikut serta dalam program ini</p>
                            <a href="program/manage/<?= $program['id'] ?>" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-users-cog me-2"></i>Kelola Alumni
                            </a>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Alumni</th>
                                    <th>Kontak</th>
                                    <th>Target</th>
                                    <th>Sudah Dibayar</th>
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
                                        <a href="#" class="text-decoration-none fw-bold">
                                            <?= htmlspecialchars($p['alumni_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($p['contact'] ?? '-') ?></td>
                                    <td class="text-center"><?= number_format($p['nominal_per_alumni'], 0, ',', '.') ?></td>
                                    <td class="text-center fw-bold">
                                        <?= $p['amount_paid'] > 0 ? number_format($p['amount_paid'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td class="text-center 
                                        <?= $p['balance'] < 0 ? 'text-danger' : ($p['balance'] > 0 ? 'text-warning' : 'text-success') ?>">
                                        <?= $p['balance'] != 0 ? number_format($p['balance'], 0, ',', '.') : '-' ?>
                                    </td>
                                    <td>
                                        <span class="badge status-<?= $p['status'] === 'lunas' ? 'lunas' : ($p['status'] === 'minus' ? 'minus' : 'belum') ?>">
                                            <?= $p['status'] === 'lunas' ? 'LUNAS' : ($p['status'] === 'minus' ? 'KURANG' : 'BELUM') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($p['amount_paid'] < $p['nominal_per_alumni']): ?>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="recordPayment(<?= $p['alumni_id'] ?>, <?= $program['id'] ?>, <?= $p['nominal_per_alumni'] - $p['amount_paid'] ?>)">
                                            <i class="fas fa-check"></i> Bayar
                                        </button>
                                        <?php else: ?>
                                        <span class="text-success small fw-bold">
                                            <i class="fas fa-check-circle me-1"></i> Lunas
                                        </span>
                                        <?php endif; ?>
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
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Info Program
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <dt class="fw-bold mb-1">Target per alumni</dt>
                        <dd><?= number_format($program['nominal_per_alumni'], 0, ',', '.') ?></dd>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <dt class="fw-bold mb-1">Deadline</dt>
                        <dd><?= $program['deadline'] ? date('d M Y', strtotime($program['deadline'])) : '-' ?></dd>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <dt class="fw-bold mb-1">Total alumni</dt>
                        <dd><?= count($payments) ?></dd>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <dt class="fw-bold mb-1">Total Target</dt>
                        <dd><?= number_format($totalTarget, 0, ',', '.') ?></dd>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <dt class="fw-bold mb-1">Total yang masih kurang</dt>
                        <dd><?= number_format($balance, 0, ',', '.') ?></dd>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>

<!-- Modal Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fas fa-coins me-2"></i>Pembayaran Program
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center mb-3" style="color:var(--muted);font-size:.9rem;">
                    Masukkan jumlah yang akan dibayar (maksimal <strong id="maxAmountDisplay" style="color:var(--ink);"></strong>)
                </p>
                <div class="mb-3">
                    <input type="number" class="form-control" id="paymentAmount" min="1" step="1" 
                           placeholder="Masukkan nominal">
                </div>
                <div id="paymentError" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>Jumlah melebihi sisa target!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-success btn-sm" id="confirmPayment">
                    <i class="fas fa-check me-1"></i>Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAlumniId = null;
let currentProgramId = null;
let currentMaxAmount = 0;

function recordPayment(alumniId, programId, maxAmount) {
    currentAlumniId = alumniId;
    currentProgramId = programId;
    currentMaxAmount = parseInt(maxAmount) || 0;
    document.getElementById('maxAmountDisplay').textContent = currentMaxAmount.toLocaleString('id-ID');
    document.getElementById('paymentAmount').value = '';
    document.getElementById('paymentAmount').max = currentMaxAmount;
    document.getElementById('paymentError').classList.add('d-none');

    const modalEl = document.getElementById('paymentModal');
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function') {
        new bootstrap.Modal(modalEl).show();
    } else {
        // Fallback: open modal manually (Bootstrap JS missing)
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.removeAttribute('aria-hidden');
        if (!document.getElementById('modal-backdrop')) {
            const bk = document.createElement('div');
            bk.id = 'modal-backdrop';
            bk.className = 'modal-backdrop fade show';
            bk.style.cssText = 'position:fixed;top:0;left:0;z-index:1040;width:100vw;height:100vh;';
            document.body.appendChild(bk);
        }
        // Close button fallback
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                const b = document.getElementById('modal-backdrop');
                if (b) b.remove();
            };
        });
    }
}

document.getElementById('paymentAmount').addEventListener('input', function() {
    const val = parseInt(this.value) || 0;
    const errorEl = document.getElementById('paymentError');
    if (val > currentMaxAmount) {
        errorEl.classList.remove('d-none');
    } else {
        errorEl.classList.add('d-none');
    }
});

document.getElementById('confirmPayment').addEventListener('click', function() {
    const val = parseInt(document.getElementById('paymentAmount').value) || 0;
    if (val <= 0) {
        document.getElementById('paymentAmount').focus();
        return;
    }
    if (val > currentMaxAmount) {
        document.getElementById('paymentError').classList.remove('d-none');
        return;
    }
    window.location.href = 'program/payment/' + currentAlumniId + '/' + currentProgramId + '?amount=' + val;
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>