<?php
// Configuración de la página
$pageTitle = 'Gestión de Tickets';
$basePath = '../'; // Ruta relativa a la raíz del proyecto
$extraCSS = '<link rel="stylesheet" href="../templates/css/tickets.css">'; // CSS adicional si es necesario


// Configuración de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requiere el archivo de conexión
require '../conexion.php';
require_once 'notificacion.php';

// Establecer zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar la sesión
session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html'); // Ajusta la ruta según corresponda
    exit;
}

// Procesar solicitudes AJAX
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Procesar según la acción solicitada
    switch ($action) {
        case 'getTickets':
            // Obtener parámetros de filtrado
            $estado = $_POST['estado'] ?? 'Abierto';
            $search = $_POST['search'] ?? '';
            $fecha_desde = $_POST['fecha_desde'] ?? '';
            $fecha_hasta = $_POST['fecha_hasta'] ?? '';
            
            // Consulta base con JOIN a usuarios y funcionarios para traer nombre del creador
            $sql = "SELECT t.id_ticket, t.codigo_ticket, t.tipo_caso, t.prioridad, t.ubicacion, 
                           t.estado, t.fecha_creacion, t.doc_usuario_creador, t.doc_usuario_atencion, t.fecha_actualizacion,
                           COALESCE(u.nombre, f.nombre, 'Sistema') AS usuario_creador,
                           COALESCE(u2.nombre, f2.nombre, '') AS usuario_atencion
                    FROM tickets t
                    LEFT JOIN usuarios u ON t.doc_usuario_creador = u.documento
                    LEFT JOIN funcionarios f ON t.doc_usuario_creador = f.numero_documento
                    LEFT JOIN usuarios u2 ON t.doc_usuario_atencion = u2.documento
                    LEFT JOIN funcionarios f2 ON t.doc_usuario_atencion = f2.numero_documento
                    WHERE 1=1";
            
            // Agregar condiciones según filtros
            if (!empty($estado)) {
                $sql .= " AND t.estado = '" . $conexion->real_escape_string($estado) . "'";
            }
            
            if (!empty($search)) {
                $sql .= " AND (
                    t.codigo_ticket LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                    t.tipo_caso LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                    t.ubicacion LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                    COALESCE(u.nombre, f.nombre, 'Sistema') LIKE '%" . $conexion->real_escape_string($search) . "%' OR
                    COALESCE(u2.nombre, f2.nombre, '') LIKE '%" . $conexion->real_escape_string($search) . "%'
                )";
            }
            
            if (!empty($fecha_desde)) {
                $sql .= " AND DATE(t.fecha_actualizacion) >= '" . $conexion->real_escape_string($fecha_desde) . "'";
            }
            
            if (!empty($fecha_hasta)) {
                $sql .= " AND DATE(t.fecha_actualizacion) <= '" . $conexion->real_escape_string($fecha_hasta) . "'";
            }
            
            // Ordenar por fecha de creación descendente (más recientes primero)
            $sql .= " ORDER BY t.fecha_actualizacion DESC";
            
            // Ejecutar la consulta
            $result = $conexion->query($sql);
            
            if ($result === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error en la consulta: ' . $conexion->error
                ]);
                exit;
            }
            
            // Recopilar los resultados
            $tickets = [];
            while ($row = $result->fetch_assoc()) {
                $tickets[] = $row;
            }
            
            // Devolver los resultados como JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tickets' => $tickets
            ]);
            exit;
            break;
            
        case 'exportTickets':
            // Configurar cabeceras para descargar un archivo CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=tickets_export_' . date('Ymd') . '.csv');
            
            // Crear un recurso de salida
            $output = fopen('php://output', 'w');
            
            // Añadir BOM para UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Definir las cabeceras del CSV
            fputcsv($output, [
                'Código Ticket',
                'Tipo de Caso',
                'Prioridad',
                'Ubicación',
                'Estado',
                'Fecha de Creación',
                'Creado Por',
                'Asignado a',
                'Fecha de ultima actualizacion',
            ]);
            
            // Consulta para obtener todos los tickets con nombres de usuario
            $sql = "SELECT t.codigo_ticket, t.tipo_caso, t.prioridad, t.ubicacion, t.estado, t.fecha_creacion, 
                        COALESCE(u.nombre, f.nombre, 'Sistema') AS usuario_creador,
                        COALESCE(u2.nombre, f2.nombre, '') AS usuario_atencion,
                        t.fecha_actualizacion
                    FROM tickets t
                    LEFT JOIN usuarios u ON t.doc_usuario_creador = u.documento
                    LEFT JOIN funcionarios f ON t.doc_usuario_creador = f.numero_documento
                    LEFT JOIN usuarios u2 ON t.doc_usuario_atencion = u2.documento
                    LEFT JOIN funcionarios f2 ON t.doc_usuario_atencion = f2.numero_documento
                    ORDER BY t.fecha_actualizacion DESC";
            
            $result = $conexion->query($sql);
            
            if ($result === false) {
                fclose($output);
                exit;
            }
            
            // Añadir cada registro al CSV
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            exit;
            break;
    }
    exit; // Asegurarnos de que el script termine aquí para solicitudes AJAX
}

// Si no es una solicitud AJAX, continuar con la renderización de la página

// Incluir el encabezado
include_once('../templates/header.php');

// Incluir el sidebar
include_once('../templates/sidebar.php');

// Abrir el contenedor principal
include_once('../templates/main-container-begin.php');
?>
<script src="../templates/js/tickets.js"></script>

<!-- Sistema de notificaciones -->
<link rel="stylesheet" href="../templates/css/notificaciones.css">
<script src="../templates/js/notificaciones.js"></script>
<script src="../templates/js/asignacion_analistas.js"></script>
 

<div class="tickets-container">
    
    <div id="contenedor_campanita">
        <div><h1>GESTIÓN DE TICKETS  -   <?php echo htmlspecialchars($_SESSION['sesion']['nombre'] ?? 'Usuario'); ?></h1></div> 
        <div><ion-icon id="camp_noti" onclick="mostrarModal()" name="notifications-outline"></ion-icon>
        </div></div>
       
   
    <div class="filters-container">
        <div id="primer_contenedor">
        <div id="contenedor_buscar" class="search-filter">
            <input type="text" id="searchInput" placeholder="Buscar Tikets por Codigo ,Ubicacion , Tipo de caso o Creador"   >
            <div id="contenedor_logo"></div>
        </div>
        
        <div class="date-filters">
            <div class="date-filter">
                <label for="dateFrom">Desde:</label>
                <input type="date" id="dateFrom">
            </div>
            <div class="date-filter">
                <label for="dateTo">Hasta:</label>
                <input type="date" id="dateTo">
            </div>
        </div>
        </div>
     
        <div class="export-filter">
            <button id="downloadBtn" class="btn-download">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
        <div class="state-filters">
    <div class="state-filter" data-state="ABIERTOS">ABIERTOS</div>
    <div class="state-filter" data-state="EN PROCESO">EN PROCESO</div>
    <div class="state-filter" data-state="SOLUCIONADOS">SOLUCIONADOS</div>
    <div class="state-filter" data-state="EN PAUSA">EN PAUSA</div>
</div>
    </div>
    
    <div class="tickets-table-container">
        <table id="ticketsTable" class="tickets-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo de Caso</th>
                    <th>Ubicación</th>
                    <th>Creado Por</th>
                    <th>Fecha Creación</th>
                    <th>Asignado a</th>
                    <th>Fecha Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los tickets se cargarán dinámicamente aquí -->
                <tr>
                    <td colspan="8" style="text-align: center;">Cargando tickets...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="ocultarModal()">&times;</span>
    <div class="notification-header">
      <h2>Notificaciones</h2>
    </div>
    <div class="notifications-container">
      <div class="empty-notifications">
        Cargando notificaciones...
      </div>
    </div>
    <button class="btn-close" onclick="ocultarModal()">Cerrar</button>
  </div>
</div>

<?php
// Cerrar el contenedor principal
include_once('../templates/main-container-end.php');

// Incluir el pie de página
include_once('../templates/footer.php');
?>