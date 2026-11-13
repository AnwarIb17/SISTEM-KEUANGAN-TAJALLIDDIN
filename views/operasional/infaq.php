<?php
$title = 'Infaq Bulanan';
require_once __DIR__ . '/../layouts/header.php';

$bulanTahun = $bulanTahun ?? date('Y-m-01');
$bulanText = date('F Y', strtotime($bulanTahun));
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h1 class="h3 mb-0">
            <i class="fas fa-hand-holding-usd text-primary me-2"></i>
            Infaq Bulanan
        </h1>
        <div class="d-flex gap-2 align-items-center">
            <label for="monthPicker" class="form-label mb-0 me-2">Pilih Bulan:</label>
            <input type="month" id="monthPicker" class="form-control" 
                   value="<?= date('Y-m', strtotime($bulanTahun)) ?>" 
                   onchange="window.location.href='infaq?month=' + this.value">
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Input Infaq Per Kelas - <?= $bulanText ?>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="infaq/save">
                        <input type="hidden" name="bulan_tahun" value="<?= $bulanTahun ?>">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kelas</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($class['name']) ?></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="amount[<?= $class['id'] ?>]" 
                                                       class="form-control" 
                                                       value="<?= $amounts[$class['id']] ?? '' ?>" 
                                                       min="0" placeholder="0">
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td><strong class="text-primary"><?= number_format($total ?? 0, 0, ',', '.') ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-success w-100 mt-3">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white py-3 px-4">
                    <h6 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Navigasi Cepat
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="list-group">
                        <?php for ($i = -3; $i <= 3; $i++): ?>
                        <?php $m = date('Y-m', strtotime("$i months")); ?>
                        <a href="infaq?month=<?= $m ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded mb-2">
                            <?= date('F Y', strtotime("$i months")) ?>
                            <?php if ($m === date('Y-m')): ?>
                            <span class="badge bg-primary">Bulan Ini</span>
                            <?php elseif ($i > 0): ?>
                            <span class="badge bg-warning">Mendatang</span>
                            <?php endif; ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Gunakan pemilih bulan di atas untuk memilih bulan/tahun lainnya</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>