<?php

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

// Configuración de la aplicación

define('APP_NAME', 'Sistema de Tickets - Total Ground');
define('APP_VERSION', '1.0.0');

// Rutas
define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH.'/data');
define('DATA_FILE', DATA_PATH.'/data.json');

// Credenciales admin (hardcodeadas como se solicita)
define('ADMIN_USERS', [
    'Roman' => 'Ground2025.h',
    'Hugo' => 'Ground2025.h',
    'Angel' => 'Ground2026.h',
]);

// Departamentos disponibles
define('DEPARTMENTS', [
    'Administración' => 'Administración',
    'Torno' => 'Torno',
    'Producción' => 'Producción',
    'Almacén de materia prima' => 'Almacén de materia prima',
    'Almacén de instalaciones' => 'Almacén de instalaciones',
    'Almacén de producto terminado' => 'Almacén de producto terminado',
    'Logística' => 'Logística',
    'Sistemas - Diseño - Multimedia' => 'Sistemas - Diseño - Multimedia',
    'Electrónica' => 'Electrónica',
    'Dirección Comercial / Mercadotecnia' => 'Dirección Comercial / Mercadotecnia',
    'Ventas Internas' => 'Ventas Internas',
    'Ventas Externas' => 'Ventas Externas',
    'Occidente y Bajío (ZMG y León, Gto.)' => 'Occidente y Bajío (ZMG y León, Gto.)',
    'Norte (La Paz, Tijuana, Monterrey, Hermosillo)' => 'Norte (La Paz, Tijuana, Monterrey, Hermosillo)',
    'CDMX' => 'CDMX',
    'Sureste (Mérida)' => 'Sureste (Mérida)',
    'Recepción' => 'Recepción',
    'Proyectos Especiales' => 'Proyectos Especiales',
    'Grafito' => 'Grafito',
]);

// Prioridades
define('PRIORITIES', [
    'baja' => 'Baja',
    'media' => 'Media',
    'alta' => 'Alta',
    'urgente' => 'Urgente',
]);

// Estados de ticket
define('STATUSES', [
    'abierto' => 'Abierto',
    'en_proceso' => 'En Proceso',
    'cerrado' => 'Cerrado',
]);

// Configuración de email
define(
    'ADMIN_EMAIL',
    'roman.n@totalground.com, daniels@totalground.com, angel.dominguez@totalground.com'
);

define('FROM_EMAIL', 'tickets@totalground.com');
define('FROM_NAME', 'Sistema de Tickets');

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Crear archivo de datos si no existe
if (! is_dir(DATA_PATH)) {
    mkdir(DATA_PATH, 0755, true);
}
if (! file_exists(DATA_FILE)) {
    $initial_data = [
        'tickets' => [],
        'last_id' => 0,
        'last_cleanup' => null,
    ];
    file_put_contents(DATA_FILE, json_encode($initial_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Auto-disparador de limpieza mensual (Día 1)
if (date('d') === '01') {
    $last_cleanup = get_last_cleanup();
    $current_month = date('Y-m');
    if (! $last_cleanup || strpos($last_cleanup, $current_month) !== 0) {
        // Ejecutar limpieza (incluimos el script directamente)
        // Definimos una constante para evitar redirecciones o salidas prematuras si fuera necesario
        define('AUTO_CLEANUP', true);
        ob_start();
        include_once BASE_PATH.'/api/monthly_cleanup.php';
        ob_clean();
    }
}

// Funciones helper

function get_all_tickets()
{
    if (! file_exists(DATA_FILE)) {
        return [];
    }
    $content = file_get_contents(DATA_FILE);
    if ($content === false) {
        return [];
    }
    $data = json_decode($content, true);
    if (! isset($data['tickets']) || ! is_array($data['tickets'])) {
        return [];
    }

    return $data['tickets'];
}

function save_tickets($tickets)
{
    if (! is_array($tickets)) {
        return false;
    }

    $content = file_exists(DATA_FILE) ? file_get_contents(DATA_FILE) : '{}';
    $data = json_decode($content, true);

    $data['tickets'] = $tickets;
    $data['last_id'] = count($tickets) > 0 ? (int) max(array_column($tickets, 'id')) : ($data['last_id'] ?? 0);

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return file_put_contents(DATA_FILE, $json) !== false;
}

function add_notification($message, $ticket_id = null)
{
    $content = file_exists(DATA_FILE) ? file_get_contents(DATA_FILE) : '{}';
    $data = json_decode($content, true) ?: [];
    $data['notifications'] = $data['notifications'] ?? [];
    array_unshift($data['notifications'], ['id' => bin2hex(random_bytes(6)), 'message' => sanitize($message), 'ticket_id' => $ticket_id, 'at' => date('Y-m-d H:i:s')]);
    $data['notifications'] = array_slice($data['notifications'], 0, 100);

    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function get_notifications()
{
    $data = json_decode(file_get_contents(DATA_FILE), true) ?: [];

    return $data['notifications'] ?? [];
}

function update_last_cleanup($date)
{
    if (! file_exists(DATA_FILE)) {
        return false;
    }
    $content = file_get_contents(DATA_FILE);
    $data = json_decode($content, true);
    $data['last_cleanup'] = $date;

    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function get_last_cleanup()
{
    if (! file_exists(DATA_FILE)) {
        return null;
    }
    $content = file_get_contents(DATA_FILE);
    $data = json_decode($content, true);

    return $data['last_cleanup'] ?? null;
}

function get_ticket_by_id($id)
{
    $tickets = get_all_tickets();
    foreach ($tickets as $ticket) {
        if ($ticket['id'] == $id) {
            return $ticket;
        }
    }

    return null;
}

function create_ticket($title, $description, $department, $priority, $creator, $due_date = null)
{
    $tickets = get_all_tickets();
    $data = json_decode(file_get_contents(DATA_FILE), true);
    $new_id = ($data['last_id'] ?? 0) + 1;

    $ticket = [
        'id' => $new_id,
        'folio' => 'TK-'.date('Y').'-'.str_pad((string) $new_id, 4, '0', STR_PAD_LEFT),
        'title' => sanitize($title),
        'description' => sanitize($description),
        'department' => sanitize($department),
        'priority' => sanitize($priority),
        'creator' => sanitize($creator),
        'status' => 'abierto',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'assigned_to' => null,
        'due_date' => $due_date ? sanitize($due_date) : null,
    ];

    $tickets[] = $ticket;
    save_tickets($tickets);

    return $ticket;
}

function update_ticket($id, $updates)
{
    $tickets = get_all_tickets();
    $found = false;

    foreach ($tickets as &$ticket) {
        if ($ticket['id'] == $id) {
            $actor = $_SESSION['creator_name'] ?? 'Sistema';
            $changes = [];
            foreach ($updates as $key => $value) {
                if (in_array($key, ['title', 'description', 'department', 'priority', 'status', 'assigned_to'])) {
                    $changes[] = $key.': '.(string) $value;
                    $ticket[$key] = sanitize($value);
                }
            }
            $ticket['updated_at'] = date('Y-m-d H:i:s');
            $ticket['history'] = $ticket['history'] ?? [];
            $ticket['history'][] = ['at' => $ticket['updated_at'], 'actor' => $actor, 'action' => 'Actualización', 'detail' => implode(', ', $changes)];
            $found = true;
            break;
        }
    }

    if ($found) {
        save_tickets($tickets);

        return true;
    }

    return false;
}

function add_ticket_comment($id, $message, $actor)
{
    $tickets = get_all_tickets();
    foreach ($tickets as &$ticket) {
        if ($ticket['id'] == $id) {
            $ticket['comments'] = $ticket['comments'] ?? [];
            $now = date('Y-m-d H:i:s');
            $ticket['comments'][] = ['at' => $now, 'actor' => sanitize($actor), 'message' => sanitize($message)];
            $ticket['history'] = $ticket['history'] ?? [];
            $ticket['history'][] = ['at' => $now, 'actor' => sanitize($actor), 'action' => 'Comentario agregado', 'detail' => ''];
            $ticket['updated_at'] = $now;
            save_tickets($tickets);

            return true;
        }
    }

    return false;
}

function close_ticket($id)
{
    return update_ticket($id, ['status' => 'cerrado']);
}

function delete_ticket($id)
{
    $tickets = get_all_tickets();
    $initial_count = count($tickets);

    $tickets = array_filter($tickets, function ($ticket) use ($id) {
        return $ticket['id'] != $id;
    });

    if (count($tickets) < $initial_count) {
        save_tickets(array_values($tickets));

        return true;
    }

    return false;
}

function sanitize($value)
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function get_tickets_by_department($department)
{
    $tickets = get_all_tickets();
    $filtered = [];

    foreach ($tickets as $ticket) {
        if ($ticket['department'] === $department && $ticket['status'] !== 'cerrado') {
            $filtered[] = $ticket;
        }
    }

    usort($filtered, function ($a, $b) {
        $priority_order = ['urgente' => 1, 'alta' => 2, 'media' => 3, 'baja' => 4];

        return $priority_order[$a['priority']] - $priority_order[$b['priority']];
    });

    return $filtered;
}

function send_email($to, $subject, $message)
{
    $envFile = dirname(__DIR__, 2).'/.env';
    $mail = [];
    if (is_file($envFile)) {
        foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $mail[trim($key)] = trim(trim($value), "'\"");
        }
    }
    $autoload = dirname(__DIR__, 2).'/vendor/autoload.php';
    if (PHP_VERSION_ID < 80200 || ! is_file($autoload)) {
        $headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: ".FROM_NAME.' <'.FROM_EMAIL.">\r\nReply-To: ".FROM_EMAIL."\r\n";

        return mail($to, $subject, $message, $headers);
    }
    require_once $autoload;
    try {
        $scheme = ($mail['MAIL_SCHEME'] ?? 'smtp') === 'smtps' ? 'smtps' : 'smtp';
        $host = $mail['MAIL_HOST'] ?? '';
        $port = $mail['MAIL_PORT'] ?? 587;
        $user = rawurlencode($mail['MAIL_USERNAME'] ?? '');
        $pass = rawurlencode($mail['MAIL_PASSWORD'] ?? '');
        $dsn = sprintf('%s://%s:%s@%s:%s', $scheme, $user, $pass, $host, $port);
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);
        $email = (new Email)->from($mail['MAIL_FROM_ADDRESS'] ?? FROM_EMAIL)->to(...array_map('trim', explode(',', $to)))->subject($subject)->html($message);
        $mailer->send($email);

        return true;
    } catch (Throwable $e) {
        error_log('Ticket mail error: '.$e->getMessage());

        return false;
    }
}

function notify_new_ticket($ticket)
{
    $subject = '[NUEVO TICKET #'.$ticket['id'].'] '.$ticket['title'];
    $priority_colors = [
        'urgente' => '#DC2626',
        'alta' => '#EA580C',
        'media' => '#EAB308',
        'baja' => '#16A34A',
    ];
    $priority_color = isset($priority_colors[$ticket['priority']]) ? $priority_colors[$ticket['priority']] : '#3B82F6';

    $message = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            .email-container {
                font-family: 'Segoe UI', Arial, sans-serif;
                max-width: 600px;
                margin: 0 auto;
                background-color: #f9f9f9;
                padding: 20px;
            }
            .header {
                background-color: #1A1A1A;
                padding: 30px;
                text-align: center;
                border-radius: 8px 8px 0 0;
            }
            .content {
                background-color: white;
                padding: 30px;
                border-radius: 0 0 8px 8px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            }
            .ticket-id {
                color: #E31E24;
                font-weight: bold;
                font-size: 1.2em;
            }
            .priority-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 4px;
                color: white;
                font-size: 0.9em;
                font-weight: bold;
                text-transform: uppercase;
                margin-left: 10px;
            }
            .detail-row {
                margin: 15px 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .label {
                color: #666;
                font-size: 0.9em;
                display: block;
                margin-bottom: 5px;
            }
            .value {
                color: #1A1A1A;
                font-weight: 500;
                font-size: 1.05em;
            }
            .description-box {
                background-color: #f4f4f4;
                padding: 15px;
                border-radius: 6px;
                color: #333;
                margin-top: 10px;
                white-space: pre-wrap;
            }
            .btn-container {
                text-align: center;
                margin-top: 30px;
            }
            .btn {
                background-color: #E31E24;
                color: white !important;
                padding: 12px 25px;
                text-decoration: none;
                border-radius: 6px;
                font-weight: bold;
                display: inline-block;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                color: #999;
                font-size: 0.8em;
            }
        </style>
    </head>
    <body style='margin: 0; padding: 0;'>
        <div class='email-container'>
            <div class='header' style='background:#111827;padding:28px 20px;text-align:center;border-radius:8px 8px 0 0;'>
                <div style='font-family:Arial,sans-serif;font-size:22px;font-weight:700;letter-spacing:1px;color:#ffffff;'>TOTAL <span style='color:#ef1b2d;'>GROUND</span></div>
                <div style='margin-top:6px;font-family:Arial,sans-serif;font-size:12px;letter-spacing:3px;color:#cbd5e1;'>SISTEMA DE TICKETS</div>
            </div>
            <div class='content'>
                <h2 style='margin-top: 0; color: #1A1A1A;'>Nuevo Ticket Registrado</h2>
                
                <div style='margin-bottom: 25px;'>
                    <span class='ticket-id'>#{$ticket['id']}</span>
                    <span class='priority-badge' style='background-color: {$priority_color}'>{$ticket['priority']}</span>
                </div>

                <div class='detail-row'>
                    <span class='label'>Título</span>
                    <span class='value'>{$ticket['title']}</span>
                </div>

                <div class='detail-row'>
                    <span class='label'>Departamento</span>
                    <span class='value'>".DEPARTMENTS[$ticket['department']]."</span>
                </div>

                <div class='detail-row'>
                    <span class='label'>Creado por</span>
                    <span class='value'>{$ticket['creator']}</span>
                </div>

                <div class='detail-row' style='border-bottom: none;'>
                    <span class='label'>Descripción</span>
                    <div class='description-box'>".nl2br($ticket['description'])."</div>
                </div>

                <div class='btn-container'>
                    <a href='http://{$_SERVER['HTTP_HOST']}/tickets/index.php?page=ticket&id={$ticket['id']}' class='btn'>Ver Detalle del Ticket</a>
                </div>
            </div>
            <div class='footer'>
                <p>Este es un correo automático del Sistema de Tickets de Total Ground.</p>
                <p>© ".date('Y').' Total Ground - Todos los derechos reservados.</p>
            </div>
        </div>
    </body>
    </html>
    ';

    send_email(ADMIN_EMAIL, $subject, $message);
}
