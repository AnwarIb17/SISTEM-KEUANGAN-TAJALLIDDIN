<?php
$title = 'Kelola Alumni - ' . htmlspecialchars($program['name']);
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-users-cog text-primary me-2"></i>
                Kelola Alumni Program
            </h1>
            <a href="program/detail/<?= $program['id'] ?>" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Detail
            </a>
        </div>
        <div>
            <a href="program" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list me-2"></i>Daftar Program
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Alumni yang Ikut Serta -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        Alumni yang Ikut Serta (<?= count($participants) ?>)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($participants)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-user-plus fa-3x mb-3"></i>
                            <p>Belum ada alumni yang ikut serta</p>
                            <small>Pilih dari daftar alumni tersedia di kanan</small>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($participants as $index => $p): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <span class="fw-bold"><?= htmlspecialchars($p['alumni_name']) ?></span>
                                            <br><small class="text-muted"><?= htmlspecialchars($p['contact'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge status-<?= $p['status'] === 'lunas' ? 'lunas' : ($p['status'] === 'minus' ? 'minus' : 'belum') ?>">
                                                <?= $p['status'] === 'lunas' ? 'LUNAS' : ($p['status'] === 'minus' ? 'KURANG' : 'BELUM') ?>
                                            </span>
                                            <?php if ((float)$p['amount_paid'] > 0): ?>
                                                <br><small class="text-muted">Rp <?= number_format($p['amount_paid'], 0, ',', '.') ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="program/participant/remove/<?= $program['id'] ?>/<?= $p['alumni_id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               data-confirm="Yakin hapus alumni ini dari program?">
                                                <i class="fas fa-user-minus"></i> Hapus
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

        <!-- Alumni Tersedia -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Alumni Tersedia (<?= count($available) ?>)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($available)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3"></i>
                            <p>Semua alumni sudah ikut serta</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Kontak</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($available as $index => $a): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($a['name']) ?></td>
                                        <td><?= htmlspecialchars($a['contact'] ?? '-') ?></td>
                                        <td>
                                            <a href="program/participant/add/<?= $program['id'] ?>/<?= $a['id'] ?>"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-user-plus"></i> Tambah
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

    <!-- Info Program -->
    <div class="card mt-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-4">
                    <dt class="fw-bold mb-1">Program</dt>
                    <dd><?= htmlspecialchars($program['name']) ?></dd>
                </div>
                <div class="col-md-4">
                    <dt class="fw-bold mb-1">Target per alumni</dt>
                    <dd><?= number_format($program['nominal_per_alumni'], 0, ',', '.') ?></dd>
                </div>
                <div class="col-md-4">
                    <dt class="fw-bold mb-1">Deadline</dt>
                    <dd><?= $program['deadline'] ? date('d M Y', strtotime($program['deadline'])) : '-' ?></dd>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>