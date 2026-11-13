<?php
// Router for pondok keuangan

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DashboardController.php';
require_once __DIR__ . '/../controllers/OperationalController.php';
require_once __DIR__ . '/../controllers/ProgramController.php';
require_once __DIR__ . '/../controllers/AlumniController.php';

// Get request path
$path = $_GET['url'] ?? '';
$path = trim($path, '/');

// Default route
if ($path === '' || $path === 'dashboard') {
    $controller = new DashboardController();
    $controller->index();
    exit;
}

// Auth routes
if ($path === 'login') {
    $controller = new AuthController();
    $controller->login();
    exit;
}

if ($path === 'logout') {
    $controller = new AuthController();
    $controller->logout();
    exit;
}

// Route mapping
$routes = [
    'income' => ['OperationalController', 'incomeList'],
    'income/create' => ['OperationalController', 'createIncome'],
    'income/delete/(\d+)' => ['OperationalController', 'deleteIncome'],
    'expense' => ['OperationalController', 'expenseList'],
    'expense/create' => ['OperationalController', 'createExpense'],
    'expense/delete/(\d+)' => ['OperationalController', 'deleteExpense'],
    'category' => ['OperationalController', 'categoryList'],
    'category/create' => ['OperationalController', 'createCategory'],
    'category/delete/(\d+)' => ['OperationalController', 'deleteCategory'],
    'infaq' => ['OperationalController', 'infaqList'],
    'infaq/save' => ['OperationalController', 'saveInfaq'],
    'report' => ['OperationalController', 'monthlyReport'],
    'report/yearly' => ['OperationalController', 'yearlyReport'],
    'program' => ['ProgramController', 'index'],
    'program/create' => ['ProgramController', 'create'],
    'program/update/(\d+)' => ['ProgramController', 'update'],
    'program/delete/(\d+)' => ['ProgramController', 'delete'],
    'program/detail/(\d+)' => ['ProgramController', 'detail'],
    'program/manage/(\d+)' => ['ProgramController', 'manageAlumni'],
    'program/participant/add/(\d+)/(\d+)' => ['ProgramController', 'addParticipant'],
    'program/participant/remove/(\d+)/(\d+)' => ['ProgramController', 'removeParticipant'],
    'program/payment/(\d+)/(\d+)' => ['ProgramController', 'updatePayment'],
    'program/pay-installment/(\d+)/(\d+)/(\d+)' => ['ProgramController', 'payInstallment'],
    'program/export/(\d+)' => ['ProgramController', 'export'],
    'alumni' => ['AlumniController', 'index'],
    'alumni/create' => ['AlumniController', 'create'],
    'alumni/update' => ['AlumniController', 'update'],
    'alumni/delete/(\d+)' => ['AlumniController', 'delete'],
    'program/alumni/(\d+)' => ['ProgramController', 'alumniDetail'],
];

// Match routes
foreach ($routes as $pattern => $handler) {
    if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
        array_shift($matches); // Remove full match
        
        list($controllerClass, $method) = $handler;
        $controller = new $controllerClass();
        
        call_user_func_array([$controller, $method], $matches);
        exit;
    }
}

// 404
http_response_code(404);
echo "404 - Page not found";
