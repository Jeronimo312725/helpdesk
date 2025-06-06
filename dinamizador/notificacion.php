<?php
// notificacion.php - Procesamiento de notificaciones
date_default_timezone_set('America/Bogota');

if (isset($_POST['action']) && $_POST['action'] === 'checkNotifications') {
    if (!isset($conexion)) {
        require '../conexion.php';
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // actualice la consulta al final se lograba hacer menos codigo
    $sql = "SELECT 
                t.id_ticket, 
                t.codigo_ticket, 
                t.tipo_caso, 
                t.prioridad, 
                t.ubicacion, 
                t.estado, 
                t.fecha_actualizacion
            FROM tickets t 
            WHERE t.estado = 'Abierto' OR t.estado = 'En proceso'";

    $result = $conexion->query($sql);

    if ($result === false) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en la consulta: ' . $conexion->error
        ]);
        exit;
    }

    $notifications = [];
    $now = new DateTime();

    while ($row = $result->fetch_assoc()) {
        $fecha_actualizacion = new DateTime($row['fecha_actualizacion']);
        $dias_transcurridos = $fecha_actualizacion->diff($now)->days;

        // Filtrar por días según el estado
        if (
            ($row['estado'] == 'Abierto' && $dias_transcurridos >= 3) ||
            ($row['estado'] == 'En proceso' && $dias_transcurridos >= 1)
        ) {
            $row['dias_transcurridos'] = $dias_transcurridos;
            $notifications[] = $row;
        }
    }

    // Ordenar por días transcurridos de mayor a menor (más días arriba)
    usort($notifications, function ($a, $b) {
        return intval($b['dias_transcurridos']) - intval($a['dias_transcurridos']);
    });

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
    exit;
}
?>