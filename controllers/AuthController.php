<?php
require_once __DIR__ . '/../config/auth.php';

class AuthController {
    public function login() {
        if (isLoggedIn()) {
            redirect('');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($username === 'admin' && password_verify($password, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')) {
                $_SESSION['user_id'] = 1;
                $_SESSION['username'] = 'admin';
                $_SESSION['success'] = 'Login berhasil';
                redirect('');
                exit;
            }
            
            $_SESSION['error'] = 'Username atau password salah';
        }
        
        require __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        logout();
    }
}
