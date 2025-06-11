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
    let currentTickets = []; // Almacenar tickets actuales
    
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
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center;">Cargando tickets...</td></tr>';
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
                currentTickets = data.tickets; // Guardar tickets actuales
                renderTickets(data.tickets);
                renderCards(data.tickets); // Renderizar cards también
            } else {
                console.error('Error al cargar tickets:', data.message);
                // Mostrar mensaje de error en la tabla
                if (ticketsTable) {
                    const tbody = ticketsTable.querySelector('tbody');
                    tbody.innerHTML = `<tr><td colspan="8" style="text-align: center;">Error: ${data.message}</td></tr>`;
                }
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            // Mostrar mensaje de error en la tabla
            if (ticketsTable) {
                const tbody = ticketsTable.querySelector('tbody');
                tbody.innerHTML = `<tr><td colspan="8" style="text-align: center;">Error de conexión. Por favor, intente nuevamente.</td></tr>`;
            }
        });
    }
    
    // Función para renderizar tickets en la tabla
    function renderTickets(tickets) {
        if (!ticketsTable) return;

        const tbody = ticketsTable.querySelector('tbody');
        tbody.innerHTML = '';

        if (tickets.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="8" style="text-align: center;">No se encontraron tickets.</td>`;
            tbody.appendChild(row);
            return;
        }

        tickets.forEach(ticket => {
            const row = document.createElement('tr');
            const priorityClass = `priority-${ticket.prioridad.toLowerCase()}`;

            // Celdas normales (clickeables)
            const cells = [
                `<td class="clickable-cell">${ticket.codigo_ticket}</td>`,
                `<td class="clickable-cell">${ticket.tipo_caso} <span class="priority-indicator ${priorityClass}"></span></td>`,
                `<td class="clickable-cell">${ticket.ubicacion}</td>`,
                `<td class="clickable-cell">${ticket.usuario_creador || 'Sistema'}</td>`,
                `<td class="clickable-cell">${formatDateTime(ticket.fecha_creacion)}</td>`,
                `<td class="clickable-cell">${ticket.usuario_atencion || '-'}</td>`,
                `<td class="clickable-cell">${formatDateTime(ticket.fecha_actualizacion)}</td>`
            ];

            // Celda de acciones (¡NO clickeable!)
            const actionCell = `
                <td>
                    <span class="action-btn" title="Editar ticket" onclick="editTicket2(${ticket.id_ticket}); event.stopPropagation();">

                        <ion-icon name="person-add-outline"></ion-icon>
                    </span>


                    <span class="action-btn" title=""">
                        <ion-icon name="hand-left-outline"></ion-icon>
                    </span>


                       

                </td>
            `;

            row.innerHTML = cells.join('') + actionCell;

            // SOLO las celdas antes de acciones serán clickeables:
            const clickableCells = row.querySelectorAll('.clickable-cell');
            clickableCells.forEach(cell => {
                cell.style.cursor = "pointer";
               cell.addEventListener('click', function(e) {
    const encodedId = btoa(ticket.id_ticket.toString());
    window.location.href = `historial_ticket.php?id=${encodedId}`;
});
            });

            tbody.appendChild(row);
        });
    }
    
    // Función para renderizar cards en dispositivos móviles
    function renderCards(tickets) {
        // Crear contenedor de cards si no existe
        let cardsContainer = document.querySelector('.tickets-cards-container');
        if (!cardsContainer) {
            cardsContainer = document.createElement('div');
            cardsContainer.className = 'tickets-cards-container';
            
            // Insertar después del contenedor de la tabla
            const tableContainer = document.querySelector('.tickets-table-container');
            if (tableContainer) {
                tableContainer.parentNode.insertBefore(cardsContainer, tableContainer.nextSibling);
            }
        }
        
        // Limpiar contenedor
        cardsContainer.innerHTML = '';
        
        if (tickets.length === 0) {
            cardsContainer.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">No se encontraron tickets.</div>';
            return;
        }
        
        tickets.forEach(ticket => {
            const priorityClass = `priority-${ticket.prioridad.toLowerCase()}`;
            
            const card = document.createElement('div');
            card.className = 'ticket-card';
            card.innerHTML = `
                <div class="card-header" onclick="toggleCard(this)">
                    <h3>${ticket.codigo_ticket}</h3>
                    <span class="card-value-cabecera">${ticket.ubicacion}
                        </span>
                    <span class="card-toggle"><ion-icon name="chevron-down-outline"></ion-icon></span>
                </div>
                <div class="card-content">
                    
                    <div class="card-row">
                        <span class="card-label">Tipo de Caso:</span>
                       ${ticket.tipo_caso}
                            <span class="priority-indicator ${priorityClass}"></span>
                    </div>
                    
                    <div class="card-details">
                        <div class="card-row">
                            <span class="card-label">Creado Por:</span>
                            <span class="card-value">${ticket.usuario_creador || 'Sistema'}</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Fecha Creación:</span>
                            <span class="card-value">${formatDateTime(ticket.fecha_creacion)}</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Asignado a:</span>
                            <span class="card-value">${ticket.usuario_atencion || 'Sin asignar'}</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">Fecha Actualización:</span>
                            <span class="card-value">${formatDateTime(ticket.fecha_actualizacion)}</span>
                        </div>
                         <div class="card-actions">
                    <button class="card-action-btn" onclick="editTicket2(${ticket.id_ticket})">

                      <ion-icon name="person-add-outline"></ion-icon>
                        Gestionar
                    </button>
                     <button class="card-action-btn">
                       <ion-icon name="hand-left-outline"></ion-icon>
                       Reasignar
                    </button>

                        <ion-icon name="hand-left-outline"></ion-icon>
                        Gestionar
                    </button>
</div>
                    </div>
                </div>
               
            `;
            
            // Agregar click para ir al historial (excepto en botones)
           card.addEventListener('click', function(e) {
    if (e.target.closest('.card-action-btn') || e.target.closest('.card-header')) {
        return;
    }
    const encodedId = btoa(ticket.id_ticket.toString());
    window.location.href = `historial_ticket.php?id=${encodedId}`;
});
            
            cardsContainer.appendChild(card);
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
        'SOLUCIONADOS': 'Solucionado',
        'EN PAUSA': 'En pausa'
    };
    return estadoMap[filterName] || 'Abierto';
}

    
    // Función para formatear fechas con hora completa
    function formatDateTime(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        // Verificar si la fecha es válida
        if (isNaN(date.getTime())) return dateString;
        
        // Formatear fecha y hora completa: DD/MM/YYYY HH:MM:SS
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');
        
        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    }
    
    // Mantener la función original formatDate por si se necesita en otro lugar
    function formatDate(dateString) {
        if (!dateString) return '-';
        
        const date = new Date(dateString);
        // Verificar si la fecha es válida
        if (isNaN(date.getTime())) return dateString;
        
        return date.toLocaleDateString('es-ES');
    }
});

// Función global para alternar el desplegado de las cards
function toggleCard(headerElement) {
    const card = headerElement.closest('.ticket-card');
    const details = card.querySelector('.card-details');
    const toggle = headerElement.querySelector('.card-toggle');
    
    if (details.classList.contains('show')) {
        details.classList.remove('show');
        toggle.classList.remove('rotated');
        card.classList.remove('expanded');
    } else {
        details.classList.add('show');
        toggle.classList.add('rotated');
        card.classList.add('expanded');
    }
}

// Función global para editar un ticket
function editTicket(ticketId) {
    // Redirigir a la página de edición
    window.location.href = `../../analista/analista.php?id=${ticketId}`;
}

