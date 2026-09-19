<?php

header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'login-collaborator':
        handle_login_collaborator();
        break;
    case 'login-admin':
        handle_login_admin();
        break;
    case 'logout':
        handle_logout();
        break;
    case 'check-session':
        handle_check_session();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
}

function handle_login_collaborator()
{
    $data = json_decode(file_get_contents('php://input'), true);
    $department = $data['department'] ?? '';

    // Compatibilidad con los nombres antiguos guardados con una codificacion distinta.
    if ($department && ! isset(DEPARTMENTS[$department]) && function_exists('mb_convert_encoding')) {
        $legacyDepartment = mb_convert_encoding($department, 'UTF-8', 'ISO-8859-1');
        if (isset(DEPARTMENTS[$legacyDepartment])) {
            $department = $legacyDepartment;
        }
    }

    if (! $department || ! isset(DEPARTMENTS[$department])) {
        http_response_code(400);
        echo json_encode(['error' => 'Departamento inválido']);
        exit;
    }

    $_SESSION['user_type'] = 'collaborator';
    $_SESSION['department'] = $department;
    $_SESSION['creator_name'] = DEPARTMENTS[$department];

    echo json_encode([
        'success' => true,
        'message' => 'Sesión iniciada como colaborador',
        'user_type' => 'collaborator',
        'department' => $department,
    ]);
}

function handle_login_admin()
{
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (! isset(ADMIN_USERS[$username]) || ADMIN_USERS[$username] !== $password) {
        http_response_code(401);
        echo json_encode(['error' => 'Usuario o contrase�a incorrectos']);
        exit;
    }

    $_SESSION['user_type'] = 'admin';
    $_SESSION['creator_name'] = $username;

    echo json_encode([
        'success' => true,
        'message' => 'Sesi�n iniciada como administrador',
        'user_type' => 'admin',
        'nombre' => $username,
    ]);
}

function handle_logout()
{
    session_destroy();
    echo json_encode([
        'success' => true,
        'message' => 'Sesi�n cerrada',
    ]);
}

function handle_check_session()
{
    if (isset($_SESSION['user_type'])) {
        echo json_encode([
            'authenticated' => true,
            'user_type' => $_SESSION['user_type'],
            'name' => $_SESSION['creator_name'] ?? 'Usuario',
            'department' => $_SESSION['department'] ?? null,
        ]);
    } else {
        echo json_encode([
            'authenticated' => false,
        ]);
    }
}
