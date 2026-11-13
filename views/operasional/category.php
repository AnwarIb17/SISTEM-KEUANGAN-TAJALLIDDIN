<?php
$title = 'Kategori Pengeluaran';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-tags text-primary me-2"></i>
            Kelola Kategori Pengeluaran
        </h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-2"></i>Tambah Kategori
        </button>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 px-4">
            <h6 class="mb-0">
                <i class="fas fa-tags me-2"></i>
                Daftar Kategori
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="fas fa-tags fa-3x mb-3"></i>
                                <p>Belum ada kategori</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($categories as $index => $cat): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= date('d M Y', strtotime($cat['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteCategory(<?= $cat['id'] ?>)"
                                        data-confirm="Yakin hapus kategori ini?">
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
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="category/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="name" class="form-control" required 
                                   placeholder="Contoh: Makanan, Listrik, Transportasi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function deleteCategory(id) {
        if (confirm('Yakin hapus kategori ini? Semua pengeluaran dengan kategori ini mungkin tidak bisa ditampilkan.')) {
            window.location.href = 'category/delete/' + id;
        }
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>