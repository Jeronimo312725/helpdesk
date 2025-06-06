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

// Establecer zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar la sesión
session_start();

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
            
            // Construir la consulta base
            $sql = "SELECT t.id_ticket, t.codigo_ticket, t.tipo_caso, t.prioridad, t.ubicacion, 
                      t.estado, t.fecha_creacion, t.usuario_creador 
               FROM tickets t 
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
                    t.usuario_creador LIKE '%" . $conexion->real_escape_string($search) . "%'
                )";
            }
            
            if (!empty($fecha_desde)) {
                $sql .= " AND DATE(t.fecha_creacion) >= '" . $conexion->real_escape_string($fecha_desde) . "'";
            }
            
            if (!empty($fecha_hasta)) {
                $sql .= " AND DATE(t.fecha_creacion) <= '" . $conexion->real_escape_string($fecha_hasta) . "'";
            }
            
            // Ordenar por fecha de creación descendente (más recientes primero)
            $sql .= " ORDER BY t.fecha_creacion DESC";
            
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
                'Usuario Creador'
            ]);
            
            // Consulta para obtener todos los tickets
            $sql = "SELECT codigo_ticket, tipo_caso, prioridad, ubicacion, estado, fecha_creacion, usuario_creador 
                    FROM tickets 
                    ORDER BY fecha_creacion DESC";
            
            $result = $conexion->query($sql);
            
            if ($result === false) {
                // No podemos usar json_encode aquí porque ya configuramos las cabeceras de CSV
                // Solo cerramos el archivo y salimos
                fclose($output);
                exit;
            }
            
            // Añadir cada registro al CSV
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            
            // Cerrar el recurso de salida
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

<div class="tickets-container">
    <h1>GESTIÓN DE TICKETS</h1>
    
    <div class="filters-container">
        <div class="search-filter">
            <input type="text" id="searchInput" placeholder="Buscar tickets...">
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
        
        <div class="state-filters">
            <div class="state-filter" data-state="ABIERTOS">ABIERTOS</div>
            <div class="state-filter" data-state="EN PROCESO">EN PROCESO</div>
            <div class="state-filter" data-state="CERRADOS">CERRADOS</div>
            <div class="state-filter" data-state="EN ESPERA">EN ESPERA</div>
        </div>
        
        <div class="export-filter">
            <button id="downloadBtn" class="btn-download">
                <i class="fas fa-download"></i> Exportar
            </button>
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
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los tickets se cargarán dinámicamente aquí -->
                <tr>
                    <td colspan="6" style="text-align: center;">Cargando tickets...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- JavaScript para la gestión de tickets -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para elementos del DOM
    const searchInput = document.getElementById('searchInput');
    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');
    const stateFilters = document.querySelectorAll('.state-filter');
    const ticketsTable = document.getElementById('ticketsTable');
    const downloadBtn = document.getElementById('downloadBtn');
    
    // Estado actual del filtro
    let currentFilter = 'ABIERTOS';
    
    // Activar el filtro "ABIERTOS" por defecto
    document.querySelector('.state-filter[data-state="ABIERTOS"]').classList.add('active');
    
    // Cargar tickets iniciales con el filtro por defecto
    loadTickets();
    
    // Event listeners
    
    // Búsqueda en tiempo real
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            loadTickets();
        });
    }
    
    // Filtro por fechas
    if (dateFromInput) {
        dateFromInput.addEventListener('change', function() {
            loadTickets();
        });
    }
    
    if (dateToInput) {
        dateToInput.addEventListener('change', function() {
            loadTickets();
        });
    }
    
    // Filtros de estado
    stateFilters.forEach(filter => {
        filter.addEventListener('click', function() {
            // Quitar clase activa de todos los filtros
            stateFilters.forEach(f => f.classList.remove('active'));
            
            // Añadir clase activa al filtro seleccionado
            this.classList.add('active');
            
            // Actualizar filtro actual
            currentFilter = this.getAttribute('data-state');
            
            // Cargar tickets con el nuevo filtro
            loadTickets();
        });
    });
    
    // Botón de descarga
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            exportToCSV();
        });
    }
    
    // Función para cargar tickets
    function loadTickets() {
        const searchTerm = searchInput ? searchInput.value : '';
        const dateFrom = dateFromInput ? dateFromInput.value : '';
        const dateTo = dateToInput ? dateToInput.value : '';
        
        // Mostrar indicador de carga
        if (ticketsTable) {
            const tbody = ticketsTable.querySelector('tbody');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Cargando tickets...</td></tr>';
        }
        
        // Crear objeto FormData para enviar los datos
        const formData = new FormData();
        formData.append('action', 'getTickets');
        formData.append('estado', getEstadoValue(currentFilter));
        formData.append('search', searchTerm);
        
        if (dateFrom) {
            formData.append('fecha_desde', dateFrom);
        }
        
        if (dateTo) {
            formData.append('fecha_hasta', dateTo);
        }
        
        // Realizar petición AJAX al mismo archivo
        fetch('dinamizador.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderTickets(data.tickets);
            } else {
                console.error('Error al cargar tickets:', data.message);
                // Mostrar mensaje de error en la tabla
                if (ticketsTable) {
                    const tbody = ticketsTable.querySelector('tbody');
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align: center;">Error: ${data.message}</td></tr>`;
                }
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            // Mostrar mensaje de error en la tabla
            if (ticketsTable) {
                const tbody = ticketsTable.querySelector('tbody');
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center;">Error de conexión. Por favor, intente nuevamente.</td></tr>`;
            }
        });
    }
    
    // Función para renderizar tickets en la tabla
    function renderTickets(tickets) {
        if (!ticketsTable) return;
        
        // Limpiar tabla excepto la cabecera
        const tbody = ticketsTable.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (tickets.length === 0) {
            // Si no hay tickets, mostrar mensaje
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="6" style="text-align: center;">No se encontraron tickets.</td>`;
            tbody.appendChild(row);
            return;
        }
        
        // Añadir cada ticket a la tabla
        tickets.forEach(ticket => {
            const row = document.createElement('tr');
            
            // Determinar clase de prioridad
            const priorityClass = `priority-${ticket.prioridad.toLowerCase()}`;
            
            row.innerHTML = `
                <td>${ticket.codigo_ticket}</td>
                <td>${ticket.tipo_caso} <span class="priority-indicator ${priorityClass}"></span></td>
                <td>${ticket.ubicacion}</td>
                <td>${ticket.usuario_creador || 'Sistema'}</td>
                <td>${formatDate(ticket.fecha_creacion)}</td>
                <td>
                    <span class="action-btn" title="Editar ticket" onclick="editTicket(${ticket.id_ticket})">
                        <i class="fas fa-edit"></i>
                    </span>
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
    
    // Función para exportar a CSV
    function exportToCSV() {
        // Redirigir directamente para descargar el archivo
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'dinamizador.php';  // El mismo archivo
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'exportTickets';
        
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
        
        // Eliminar el formulario después de enviarlo
        setTimeout(() => {
            document.body.removeChild(form);
        }, 1000);
    }
    
    // Función auxiliar para convertir estado visual a valor de BD
    function getEstadoValue(filterName) {
        const estadoMap = {
            'ABIERTOS': 'Abierto',
            'EN PROCESO': 'En proceso',
            'CERRADOS': 'Solucionado',
            'EN ESPERA': 'En pausa'
        };
        
        return estadoMap[filterName] || 'Abierto';
    }
    
    // Función para formatear fechas
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        // Verificar si la fecha es válida
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString('es-ES');
    }
});

// Función global para editar un ticket
function editTicket(ticketId) {
    // Redirigir a la página de edición
    window.location.href = `editar_ticket.php?id=${ticketId}`;
}
</script>

<style>
/* Estilos para la gestión de tickets */
.tickets-container {
    padding: 20px;
}

.tickets-container h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

/* Contenedor de filtros */
.filters-container {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;
    gap: 15px;
    justify-content: space-between;
    align-items: center;
}

/* Filtro de búsqueda */
.search-filter {
    flex: 1;
    min-width: 200px;
}

.search-filter input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

/* Filtros de fecha */
.date-filters {
    display: flex;
    gap: 10px;
}

.date-filter {
    display: flex;
    align-items: center;
    gap: 5px;
}

.date-filter label {
    font-size: 14px;
    white-space: nowrap;
}

.date-filter input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

/* Filtros de estado */
.state-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.state-filter {
    padding: 8px 15px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
}

.state-filter:hover {
    background-color: #e0e0e0;
}

.state-filter.active {
    background-color: #4a6fdc;
    color: white;
    border-color: #4a6fdc;
}

/* Botón de exportar */
.export-filter {
    margin-left: auto;
}

.btn-download {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-download:hover {
    background-color: #218838;
}

.btn-download i {
    font-size: 16px;
}

/* Tabla de tickets */
.tickets-table-container {
    overflow-x: auto;
}

.tickets-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.tickets-table th,
.tickets-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.tickets-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.tickets-table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Indicadores de prioridad */
.priority-indicator {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-left: 5px;
}

.priority-alta {
    background-color: #dc3545;
}

.priority-media {
    background-color: #ffc107;
}

.priority-baja {
    background-color: #28a745;
}

/* Botón de acción */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e9ecef;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.action-btn:hover {
    background-color: #ced4da;
}

.action-btn i {
    color: #495057;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .filters-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .date-filters {
        flex-direction: column;
    }
    
    .state-filters {
        justify-content: center;
    }
    
    .export-filter {
        margin-left: 0;
        display: flex;
        justify-content: center;
    }
}
</style>

<?php
// Cerrar el contenedor principal
include_once('../templates/main-container-end.php');

// Incluir el pie de página
include_once('../templates/footer.php');
?>