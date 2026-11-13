<?php
session_start();

// Base URL
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login');
        exit;
    }
}

function logout() {
    session_destroy();
    header('Location: login');
    exit;
}

function redirect($path) {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function getUser() {
    return $_SESSION['username'] ?? null;
}
