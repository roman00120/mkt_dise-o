<?php

require_once __DIR__.'/../config.php';

/**
 * Script de Limpieza Mensual y Reporte de KPIs
 * Este script debe ejecutarse el d�a 1 de cada mes.
 * Puede ser llamado v�a CRON o manualmente con un token de seguridad.
 */

// Simple seguridad por token (opcional, puedes cambiar el valor)
$security_token = 'TotalGround_Monthly_2025';
if (isset($_GET['token']) && $_GET['token'] !== $security_token) {
    exit('Acceso denegado.');
}

// Obtener tickets actuales
$tickets = get_all_tickets();

if (empty($tickets)) {
    // Si no hay tickets, no hay nada que reportar ni borrar
    echo 'No hay tickets para procesar.';
    exit;
}

// 1. Calcular KPIs
$total_tickets = count($tickets);
$stats = [
    'status' => [],
    'department' => [],
    'priority' => [],
];

foreach ($tickets as $ticket) {
    $s = $ticket['status'];
    $d = $ticket['department'];
    $p = $ticket['priority'];

    $stats['status'][$s] = ($stats['status'][$s] ?? 0) + 1;
    $stats['department'][$d] = ($stats['department'][$d] ?? 0) + 1;
    $stats['priority'][$p] = ($stats['priority'][$p] ?? 0) + 1;
}

// 2. Generar Reporte HTML
$month_name = date('F Y', strtotime('last month'));
$subject = '[REPORTE MENSUAL] KPIs de Tickets - '.$month_name;

$message = "
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #004a8c; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .section { margin: 20px 0; }
        .section h3 { border-bottom: 2px solid #004a8c; padding-bottom: 5px; color: #004a8c; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #eee; text-align: left; }
        th { background: #f8f9fa; }
        .footer { text-align: center; font-size: 0.8em; color: #777; margin-top: 30px; }
        .total { font-size: 1.2em; font-weight: bold; color: #e63946; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Reporte Mensual de Tickets</h1>
            <p>Resumen del periodo: ".$month_name."</p>
        </div>

        <div class='section'>
            <h3>Resumen General</h3>
            <p class='total'>Total de tickets generados: ".$total_tickets."</p>
        </div>

        <div class='section'>
            <h3>Distribuci�n por Estado</h3>
            <table>
                <tr><th>Estado</th><th>Cantidad</th></tr>";

foreach (STATUSES as $key => $label) {
    $count = $stats['status'][$key] ?? 0;
    $message .= "<tr><td>$label</td><td>$count</td></tr>";
}

$message .= "
            </table>
        </div>

        <div class='section'>
            <h3>Distribuci�n por Prioridad</h3>
            <table>
                <tr><th>Prioridad</th><th>Cantidad</th></tr>";

foreach (PRIORITIES as $key => $label) {
    $count = $stats['priority'][$key] ?? 0;
    $message .= "<tr><td>$label</td><td>$count</td></tr>";
}

$message .= "
            </table>
        </div>

        <div class='section'>
            <h3>Distribuci�n por Departamento</h3>
            <table>
                <tr><th>Departamento</th><th>Cantidad</th></tr>";

foreach (DEPARTMENTS as $key => $label) {
    $count = $stats['department'][$key] ?? 0;
    if ($count > 0) {
        $message .= "<tr><td>$label</td><td>$count</td></tr>";
    }
}

$message .= "
            </table>
        </div>

        <div class='footer'>
            <p>Este reporte fue generado autom�ticamente por el Sistema de Tickets.</p>
            <p>Los tickets de este periodo han sido eliminados del sistema para el nuevo ciclo.</p>
        </div>
    </div>
</body>
</html>
";

// 3. Enviar correo a los administradores
$sent = send_email(ADMIN_EMAIL, $subject, $message);

$debug_mode = isset($_GET['debug']) || (php_sapi_name() === 'cli');

if ($sent || $debug_mode) {
    // 4. Borrar todos los tickets
    $success = save_tickets([]);
    if ($success) {
        update_last_cleanup(date('Y-m-d'));
        if (! $sent && $debug_mode) {
            echo "Aviso: El correo NO se envi� (error en mail()), pero los tickets se borraron por modo DEBUG/CLI.\n";
        } else {
            echo 'Reporte enviado con �xito y tickets resetados.';
        }
    } else {
        echo 'Reporte enviado, pero hubo un error al borrar los tickets.';
    }
} else {
    echo 'Error al enviar el correo. No se borraron los tickets por seguridad.';
}
