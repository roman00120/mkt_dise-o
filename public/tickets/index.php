<?php
require_once 'config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Si est� autenticado, mostrar dashboard por defecto
if (isset($_SESSION['user_type'])) {
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
}

// Routing simple
switch ($page) {
    case 'login':
        include 'views/login.html';
        break;
    case 'dashboard':
        if (!isset($_SESSION['user_type'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include 'views/dashboard.html';
        break;
    case 'create-ticket':
        if (!isset($_SESSION['user_type'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include 'views/create-ticket.html';
        break;
    case 'ticket':
        if (!isset($_SESSION['user_type'])) {
            header('Location: index.php?page=login');
            exit;
        }
        include 'views/ticket-detail.html';
        break;
    case 'logout':
        session_destroy();
        header('Location: /login');
        exit;
    default:
        include 'views/login.html';
}

