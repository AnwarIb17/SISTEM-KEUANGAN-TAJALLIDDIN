<?php
$title = 'Program Kegiatan';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h1 class="mb-0">
            <i class="fas fa-project-diagram"></i>
            Program & Kegiatan
        </h1>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-2"></i>Buat Program Baru
        </button>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Daftar Program
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Program</th>
                            <th>Target/Alumni</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($programs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-calculator fa-3x mb-3"></i>
                                <p>Belum ada program</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($programs as $index => $program): ?>
                        <?php
                            $alumniCount = (int)($program['alumni_count'] ?? 0);
                            $targetTotal = (int)$program['nominal_per_alumni'] * $alumniCount;
                            $isLunas = $alumniCount > 0 && (int)$program['paid_total'] >= $targetTotal;
                            $status = $isLunas ? 'Lunas' : 'Belum';
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <a href="program/detail/<?= $program['id'] ?>" class="text-decoration-none fw-bold">
                                    <?= htmlspecialchars($program['name']) ?>
                                </a>
                            </td>
                            <td><?= number_format($program['nominal_per_alumni'], 0, ',', '.') ?></td>
                            <td><?= $program['deadline'] ? date('d M Y', strtotime($program['deadline'])) : '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $status === 'Lunas' ? 'success' : 'warning' ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                            <td>
                                <a href="program/detail/<?= $program['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="program/manage/<?= $program['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Kelola Alumni">
                                    <i class="fas fa-users-cog"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteProgram(<?= $program['id'] ?>)"
                                        data-confirm="Yakin hapus program ini?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Program Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="program/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Program</label>
                            <input type="text" name="name" class="form-control" required
                                   placeholder="Contoh: Qurban, Isra Mi'raj, Kegiatan Bakti">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target per Alumni (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="nominal_per_alumni" class="form-control" required min="1"
                                       placeholder="Masukkan nominal">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" name="deadline" class="form-control"
                                   value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Cicilan (1 = tunai)</label>
                            <input type="number" name="installment_count" class="form-control" min="1" value="1">
                            <div class="form-text">Jika lebih dari 1, sistem akan membagi nominal menjadi cicilan tetap.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Buat Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function deleteProgram(id) {
        if (confirm('Yakin hapus program ini? Pembayaran terkait akan ikut terhapus.')) {
            window.location.href = 'program/delete/' + id;
        }
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
