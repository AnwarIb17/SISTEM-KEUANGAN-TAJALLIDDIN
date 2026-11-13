<?php
$title = 'Laporan Bulanan';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-alt text-primary me-2"></i>
            Laporan Bulanan
        </h1>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form method="GET" action="report" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $year ?? date('Y') == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" 
                                <?= $month ?? date('m') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="report/export" class="btn btn-success w-100">
                        <i class="fas fa-download me-2"></i>Export
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($report)): ?>
    <div class="row g-4 mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h6 class="mb-0">Infaq</h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($report['infaq'])): ?>
                        <p class="text-muted">Belum ada infaq</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['infaq'] as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['class_name']) ?></td>
                                    <td><?= number_format($row['amount'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white py-3 px-4">
                    <h6 class="mb-0">Pemasukkan Lainnya</h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($report['income'])): ?>
                        <p class="text-muted">Belum ada pemasukkan</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['income'] as $row): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td class="text-success fw-bold"><?= number_format($row['amount'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white py-3 px-4">
                    <h6 class="mb-0">Pengeluaran</h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($report['expense'])): ?>
                        <p class="text-muted">Belum ada pengeluaran</p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['expense'] as $row): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($row['category_name']) ?></td>
                                    <td class="text-danger fw-bold"><?= number_format($row['amount'], 0, ',', '.') ?></td>
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
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>