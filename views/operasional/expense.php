<?php
$title = 'Pengeluaran';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-receipt text-danger me-2"></i>
            Pengeluaran Operasional
        </h1>
        <div class="d-flex gap-2">
            <a href="category" class="btn btn-outline-secondary">
                <i class="fas fa-tags me-2"></i>Kelola Kategori
            </a>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-2"></i>Tambah Pengeluaran
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Bulan Ini</small>
                        <h4 class="mb-0 text-danger"><?= number_format(array_sum(array_column($expenses, 'amount')), 0, ',', '.') ?></h4>
                    </div>
                    <i class="fas fa-calendar fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header bg-white py-3 px-4">
            <h6 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Daftar Pengeluaran
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover" id="expenseTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Belum ada data pengeluaran</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($expense['tanggal'])) ?></td>
                            <td><span class="badge bg-danger"><?= htmlspecialchars($expense['category_name']) ?></span></td>
                            <td class="fw-bold text-danger"><?= number_format($expense['amount'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($expense['notes'] ?? '-') ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteExpense(<?= $expense['id'] ?>)"
                                        data-confirm="Yakin hapus pengeluaran ini?">
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
                    <h5 class="modal-title">Tambah Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="expense/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nominal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount" class="form-control" required min="1" 
                                       placeholder="Masukkan nominal">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" required 
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="notes" class="form-control" rows="2" 
                                      placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function deleteExpense(id) {
        if (confirm('Yakin hapus pengeluaran ini?')) {
            window.location.href = 'expense/delete/' + id;
        }
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>