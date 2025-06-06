<?php
// Configuración de la página
$pageTitle = 'Gestión de Tickets';
$basePath = '../'; // Ruta relativa a la raíz del proyecto
$extraCSS = '<link rel="stylesheet" href="../templates/css/tickets.css">'; // CSS adicional si es necesario



// Si no es una solicitud AJAX, continuar con la renderización de la página

// Incluir el encabezado
include_once('../templates/header.php');

// Incluir el sidebar
include_once('../templates/sidebar.php');

// Abrir el contenedor principal
include_once('../templates/main-container-begin.php');
?>
<script src="../templates/js/tickets.js"></script>
<div class="tickets-container">
    <h1>GESTIÓN DE TICKETS</h1>
    
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
            <div class="state-filter" data-state="CERRADOS">CERRADOS</div>
            <div class="state-filter" data-state="EN ESPERA">EN ESPERA</div>
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



<?php
// Cerrar el contenedor principal
include_once('../templates/main-container-end.php');

// Incluir el pie de página
include_once('../templates/footer.php');
?>