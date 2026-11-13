<?php
$title = 'Data Alumni';
require_once __DIR__ . '/../layouts/header.php';
?>
<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users text-success me-2"></i>
            Data Alumni
        </h1>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus me-2"></i>Tambah Alumni
        </button>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 px-4">
            <h6 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Daftar Alumni
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alumni)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>Belum ada data alumni</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($alumni as $index => $alumni): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($alumni['name']) ?></td>
                            <td><?= htmlspecialchars($alumni['contact'] ?? '-') ?></td>
                            <td><?= date('d M Y', strtotime($alumni['created_at'])) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="editAlumni(<?= $alumni['id'] ?>, '<?= htmlspecialchars($alumni['name']) ?>', '<?= htmlspecialchars($alumni['contact'] ?? '') ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteAlumni(<?= $alumni['id'] ?>)"
                                        data-confirm="Yakin hapus alumni ini?">
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

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Alumni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="alumni/create" id="alumniForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required 
                                   id="alumniName" placeholder="Nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kontak</label>
                            <input type="text" name="contact" class="form-control" 
                                   id="alumniContact" placeholder="Telepon, email, dll">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function editAlumni(id, name, contact) {
        const modal = new bootstrap.Modal(document.getElementById('addModal'));
        document.getElementById('modalTitle').textContent = 'Edit Alumni';
        document.getElementById('alumniForm').action = '/alumni/update?id=' + id;
        document.getElementById('alumniName').value = name;
        document.getElementById('alumniContact').value = contact;
        modal.show();
    }

    function deleteAlumni(id) {
        if (confirm('Yakin hapus alumni ini?')) {
            window.location.href = 'alumni/delete/' + id;
        }
    }
    </script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>