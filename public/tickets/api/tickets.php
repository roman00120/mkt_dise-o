<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// Verificar autenticaci�n
if (!isset($_SESSION['user_type'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $sameOrigin = ($origin && parse_url($origin, PHP_URL_HOST) === (parse_url('http://' . $host, PHP_URL_HOST))) || ($referer && parse_url($referer, PHP_URL_HOST) === parse_url('http://' . $host, PHP_URL_HOST));
    if (!$sameOrigin && (isset($_SERVER['HTTP_ORIGIN']) || isset($_SERVER['HTTP_REFERER']))) {
        http_response_code(403);
        echo json_encode(['error' => 'Solicitud no autorizada']);
        exit;
    }
}

switch ($action) {
    case 'export':
        handle_export();
        break;
    case 'list':
        handle_list();
        break;
    case 'notifications':
        echo json_encode(['success' => true, 'notifications' => get_notifications()]);
        break;
    case 'get':
        handle_get();
        break;
    case 'create':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'M�todo no permitido']);
            exit;
        }
        handle_create();
        break;
    case 'update':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'M�todo no permitido']);
            exit;
        }
        handle_update();
        break;
    case 'close':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'M�todo no permitido']);
            exit;
        }
        handle_close();
        break;
    case 'delete':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'M�todo no permitido']);
            exit;
        }
        handle_delete();
        break;
    case 'comment':
        if ($method !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Método no permitido']); exit; }
        handle_comment();
        break;
    case 'upload':
        if ($method !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Método no permitido']); exit; }
        handle_upload();
        break;
    case 'download':
        handle_download();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acci�n no v�lida']);
}

function handle_list() {
    $user_type = $_SESSION['user_type'];
    $department = $_SESSION['department'] ?? null;

    if ($user_type === 'admin') {
        $tickets = get_all_tickets();
    } else {
        $tickets = get_tickets_by_department($department);
    }

    echo json_encode([
        'success' => true,
        'tickets' => $tickets
    ]);
}

function handle_get() {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID requerido']);
        exit;
    }

    $ticket = get_ticket_by_id($id);

    if (!$ticket) {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket no encontrado']);
        exit;
    }

    // Verificar permisos
    $user_type = $_SESSION['user_type'];
    $department = $_SESSION['department'] ?? null;

    if ($user_type !== 'admin' && $ticket['department'] !== $department) {
        http_response_code(403);
        echo json_encode(['error' => 'No tiene permiso para ver este ticket']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'ticket' => $ticket
    ]);
}

function handle_create() {
    $data = json_decode(file_get_contents('php://input'), true);

    $title = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $department = $data['department'] ?? '';
    $priority = $data['priority'] ?? 'media';
    $creator = $data['creator_name'] ?? ($_SESSION['creator_name'] ?? 'An�nimo');
    $due_date = $data['due_date'] ?? null;

    if (!$title || !$description) {
        http_response_code(400);
        echo json_encode(['error' => 'T�tulo y descripci�n requeridos']);
        exit;
    }

    // Validar departamento y prioridad
    if (!isset(DEPARTMENTS[$department]) || !isset(PRIORITIES[$priority])) {
        http_response_code(400);
        echo json_encode(['error' => 'Departamento o prioridad inv�lidos']);
        exit;
    }

    $ticket = create_ticket($title, $description, $department, $priority, $creator, $due_date);
    add_notification('Nuevo ticket creado: ' . ($ticket['folio'] ?? $ticket['id']), $ticket['id']);

    // Enviar notificaci�n (ignorar errores de correo para no detener el proceso)
    try {
        @notify_new_ticket($ticket);
    } catch (Exception $e) {
        // Log error if needed, but don't stop execution
    }

    echo json_encode([
        'success' => true,
        'message' => 'Ticket creado exitosamente',
        'ticket' => $ticket
    ]);
}

function handle_update() {
    $user_type = $_SESSION['user_type'];

    if ($user_type !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Solo administradores pueden actualizar tickets']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $updates = $data['updates'] ?? [];

    if (array_key_exists('assigned_to', $updates) && $updates['assigned_to'] !== '' && !in_array($updates['assigned_to'], ['Roman', 'Angel'], true)) {
        http_response_code(422);
        echo json_encode(['error' => 'El responsable debe ser Roman o Angel']);
        exit;
    }

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID requerido']);
        exit;
    }

    if (update_ticket($id, $updates)) {
        add_notification('Ticket actualizado: TK-' . date('Y') . '-' . str_pad((string)$id, 4, '0', STR_PAD_LEFT), $id);
        echo json_encode([
            'success' => true,
            'message' => 'Ticket actualizado'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket no encontrado']);
    }
}

function handle_close() {
    $user_type = $_SESSION['user_type'];

    if ($user_type !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Solo administradores pueden cerrar tickets']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID requerido']);
        exit;
    }

    if (close_ticket($id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket cerrado'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket no encontrado']);
    }
}

function handle_export() {
    $user_type = $_SESSION['user_type'];
    if ($user_type !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Solo administradores pueden generar reportes']);
        exit;
    }

    $tickets = get_all_tickets();
    
    // Calcular KPIs
    $total = count($tickets);
    $status_counts = ['abierto' => 0, 'en_proceso' => 0, 'cerrado' => 0];
    $priority_counts = ['baja' => 0, 'media' => 0, 'alta' => 0, 'urgente' => 0];
    $dept_counts = [];
    $total_res_time = 0;
    $closed_count = 0;

    foreach ($tickets as $t) {
        $status_counts[$t['status']]++;
        $priority_counts[$t['priority']]++;
        
        $dept = $t['department'];
        $dept_counts[$dept] = ($dept_counts[$dept] ?? 0) + 1;

        if ($t['status'] === 'cerrado' && !empty($t['created_at']) && !empty($t['updated_at'])) {
            $start = strtotime($t['created_at']);
            $end = strtotime($t['updated_at']);
            $total_res_time += ($end - $start);
            $closed_count++;
        }
    }

    $avg_res_time = $closed_count > 0 ? round($total_res_time / $closed_count / 3600, 2) : 0; // en horas

    // Preparar CSV
    $filename = "reporte_tickets_" . date('Y-m-d_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    // BOM para Excel UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Secci�n de KPIs
    fputcsv($output, ['SISTEMA DE TICKETS - REPORTE DE KPIs']);
    fputcsv($output, ['Fecha de generaci�n', date('Y-m-d H:i:s')]);
    fputcsv($output, []);
    fputcsv($output, ['RESUMEN GENERAL']);
    fputcsv($output, ['KPI', 'Valor']);
    fputcsv($output, ['Total de Tickets', $total]);
    fputcsv($output, ['Tickets Cerrados', $status_counts['cerrado']]);
    fputcsv($output, ['Tickets en Proceso', $status_counts['en_proceso']]);
    fputcsv($output, ['Tickets Abiertos', $status_counts['abierto']]);
    fputcsv($output, ['Tiempo Promedio Res. (Horas)', $avg_res_time]);
    fputcsv($output, []);

    fputcsv($output, ['DISTRIBUCI�N POR PRIORIDAD']);
    foreach ($priority_counts as $p => $count) {
        fputcsv($output, [ucfirst($p), $count]);
    }
    fputcsv($output, []);

    fputcsv($output, ['DISTRIBUCI�N POR DEPARTAMENTO']);
    foreach ($dept_counts as $d => $count) {
        fputcsv($output, [$d, $count]);
    }
    fputcsv($output, []);

    // Detalle de Tickets
    fputcsv($output, ['DETALLE DE TICKETS']);
    fputcsv($output, ['ID', 'Titulo', 'Descripcion', 'Departamento', 'Prioridad', 'Estado', 'Creador', 'Creado', 'Actualizado']);
    
    foreach ($tickets as $t) {
        fputcsv($output, [
            $t['id'],
            $t['title'],
            $t['description'],
            $t['department'],
            $t['priority'],
            $t['status'],
            $t['creator'],
            $t['created_at'],
            $t['updated_at']
        ]);
    }

    fclose($output);
    exit;
}

function handle_delete() {
    $user_type = $_SESSION['user_type'];

    if ($user_type !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Solo administradores pueden eliminar tickets']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID requerido']);
        exit;
    }

    if (delete_ticket($id)) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket eliminado'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Ticket no encontrado']);
    }
}

function handle_comment() {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $message = trim($data['message'] ?? '');
    if (!$id || $message === '') { http_response_code(400); echo json_encode(['error' => 'Comentario requerido']); exit; }
    $ticket = get_ticket_by_id($id);
    if (!$ticket) { http_response_code(404); echo json_encode(['error' => 'Ticket no encontrado']); exit; }
    if ($_SESSION['user_type'] !== 'admin' && $ticket['department'] !== ($_SESSION['department'] ?? null)) { http_response_code(403); echo json_encode(['error' => 'Sin permiso']); exit; }
    add_ticket_comment($id, $message, $_SESSION['creator_name'] ?? 'Usuario');
    add_notification('Nuevo comentario en ticket #' . $id, $id);
    echo json_encode(['success' => true, 'message' => 'Comentario agregado']);
}

function handle_upload() {
    $id = intval($_POST['id'] ?? 0);
    $ticket = get_ticket_by_id($id);
    if (!$ticket || empty($_FILES['file'])) { http_response_code(400); echo json_encode(['error' => 'Ticket o archivo requerido']); exit; }
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK || $_FILES['file']['size'] > 100 * 1024 * 1024) { http_response_code(400); echo json_encode(['error' => 'Archivo inválido o mayor a 100 MB']); exit; }
    $allowed = ['pdf','png','jpg','jpeg','gif','webp','zip','doc','docx','xls','xlsx','txt','mp4','mov'];
    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) { http_response_code(400); echo json_encode(['error' => 'Tipo de archivo no permitido']); exit; }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['file']['tmp_name']);
    $mimeAllowed = ['application/pdf','image/png','image/jpeg','image/gif','image/webp','application/zip','application/x-zip-compressed','text/plain','video/mp4','video/quicktime','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if (!in_array($mime, $mimeAllowed, true)) { http_response_code(400); echo json_encode(['error' => 'El contenido del archivo no coincide con un tipo permitido']); exit; }
    $dir = DATA_PATH . '/attachments/' . $id; if (!is_dir($dir)) mkdir($dir, 0750, true);
    $name = bin2hex(random_bytes(12)) . '.' . $ext; $target = $dir . '/' . $name;
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) { http_response_code(500); echo json_encode(['error' => 'No se pudo guardar']); exit; }
    $tickets = get_all_tickets(); foreach ($tickets as &$t) if ($t['id'] == $id) { $t['attachments'] = $t['attachments'] ?? []; $t['attachments'][] = ['name' => basename($_FILES['file']['name']), 'file' => $name, 'size' => $_FILES['file']['size'], 'at' => date('Y-m-d H:i:s')]; $t['updated_at'] = date('Y-m-d H:i:s'); } save_tickets($tickets);
    echo json_encode(['success' => true]);
}

function handle_download() {
    $id = intval($_GET['id'] ?? 0); $file = basename($_GET['file'] ?? ''); $ticket = get_ticket_by_id($id);
    if (!$ticket || !$file || ($_SESSION['user_type'] !== 'admin' && $ticket['department'] !== ($_SESSION['department'] ?? null))) { http_response_code(403); exit; }
    $path = DATA_PATH . '/attachments/' . $id . '/' . $file; if (!is_file($path)) { http_response_code(404); exit; }
    header('Content-Type: application/octet-stream'); header('Content-Disposition: attachment; filename="' . $file . '"'); readfile($path); exit;
}

