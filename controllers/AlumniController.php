<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Alumni.php';
require_once __DIR__ . '/../models/Program.php';

class AlumniController {
    private $model;

    public function __construct() {
        $this->model = new Alumni();
    }

    public function index() {
        requireAuth();
        $alumni = $this->model->all();
        require __DIR__ . '/../views/alumni/list.php';
    }

    public function create() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->create($_POST)) {
                $_SESSION['success'] = 'Data alumni berhasil ditambahkan';
            } else {
                $_SESSION['error'] = 'Gagal menambahkan data alumni';
            }
        }
        redirect('alumni');
        exit;
    }

    public function update() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $_GET['id'] ?? null;
            if ($id && $this->model->update($id, $_POST)) {
                $_SESSION['success'] = 'Data alumni berhasil diupdate';
            } else {
                $_SESSION['error'] = 'Gagal mengupdate data alumni';
            }
        }
        redirect('alumni');
        exit;
    }

    public function delete($id) {
        requireAuth();
        if ($this->model->delete($id)) {
            $_SESSION['success'] = 'Data alumni berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus data alumni';
        }
        redirect('alumni');
        exit;
    }

    public function export($programId) {
        requireAuth();
        require_once __DIR__ . '/../controllers/ExportController.php';
        $export = new ExportController();
        $export->programExcel($programId);
    }
}
