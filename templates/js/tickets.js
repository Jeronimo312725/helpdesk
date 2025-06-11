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

            // Lógica botones de acción
            const isAssigned = !!ticket.usuario_atencion && ticket.usuario_atencion !== '-' && ticket.usuario_atencion !== 'Sin asignar';
            const cargo = (window.usuarioCargo || '').toUpperCase();
            const isAnalista = cargo === 'ANALISTA';
            const isDinamizador = cargo === 'DINAMIZADOR';
            const isAssignedToMe = isAnalista && window.usuarioNombre && (ticket.usuario_atencion === window.usuarioNombre);

            // Botón persona
            let assignBtnClass = 'action-btn';
            let assignBtnStyle = '';
            let assignBtnDisabled = '';
            let assignBtnTitle = 'Asignar ticket';
            if (isAssigned) {
                assignBtnClass += ' disabled';
                assignBtnStyle = 'color: #bdbdbd; cursor: not-allowed;';
                assignBtnDisabled = 'tabindex="-1"';
                assignBtnTitle = 'Ya asignado';
            }

            // Botón mano (reasignar)
            let handBtnHtml = '';
            if (isAssigned && (isDinamizador || isAssignedToMe)) {
                handBtnHtml = `
                  <span class="action-btn" title="Reasignar ticket" onclick="reasignarTicket2(${ticket.id_ticket}); event.stopPropagation();">
                    <ion-icon id="icono_asig" name="hand-left-outline"></ion-icon>
                  </span>
                `;
            }

            // Celda de acciones (¡NO clickeable!)
            const actionCell = `
                <td>
                    <span 
                        class="${assignBtnClass}" 
                        style="${assignBtnStyle}" 
                        ${assignBtnDisabled} 
                        title="${assignBtnTitle}"
                        ${!isAssigned ? `onclick="editTicket2(${ticket.id_ticket}); event.stopPropagation();"` : ''}
                    >
                        <ion-icon name="person-add-outline"></ion-icon>
                    </span>
                    ${handBtnHtml}
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
            const isAssigned = !!ticket.usuario_atencion && ticket.usuario_atencion !== '-' && ticket.usuario_atencion !== 'Sin asignar';
            const cargo = (window.usuarioCargo || '').toUpperCase();
            const isAnalista = cargo === 'ANALISTA';
            const isDinamizador = cargo === 'DINAMIZADOR';
            const isAssignedToMe = isAnalista && window.usuarioNombre && (ticket.usuario_atencion === window.usuarioNombre);

            // Botón persona (gestionar)
            let assignBtnDisabled = isAssigned ? 'disabled' : '';
            let assignBtnStyle = isAssigned ? 'background: #eee; color: #bdbdbd; cursor: not-allowed;' : '';
            let assignBtnOnclick = !isAssigned ? `onclick="editTicket2(${ticket.id_ticket})"` : '';

            // Botón mano (reasignar)
            let handBtnHtml = '';
            if (isAssigned && (isDinamizador || isAssignedToMe)) {
                handBtnHtml = `
                  <button class="card-action-btn" onclick="reasignarTicket2(${ticket.id_ticket})">
                    <ion-icon name="hand-left-outline"></ion-icon>
                    Reasignar
                  </button>
                `;
            }

            const card = document.createElement('div');
            card.className = 'ticket-card';
            card.innerHTML = `
                <div class="card-header" onclick="toggleCard(this)">
                    <h3>${ticket.codigo_ticket}</h3>
                    <span class="card-value-cabecera">${ticket.ubicacion}</span>
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
                            <button class="card-action-btn" style="${assignBtnStyle}" ${assignBtnDisabled} ${assignBtnOnclick}>
                                <ion-icon name="person-add-outline"></ion-icon>
                                Gestionar
                            </button>
                            ${handBtnHtml}
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

// Función global para editar un ticket (ASIGNAR)
function editTicket(ticketId) {
    window.location.href = `../../analista/analista.php?id=${ticketId}`;
}

// --- MODAL DE ASIGNACIÓN Y REASIGNACIÓN ---

// Variables globales para el ticket actual
let currentTicketId = null;
let currentTicketCode = null;

// Función para abrir el modal de asignación/reasignación
function editTicket2(ticketId, modoReasignacion = false) {
    currentTicketId = ticketId;

    // Cargar datos del ticket para mostrar el código
    const formData = new FormData();
    formData.append('action', 'getTicketInfo');
    formData.append('ticketId', ticketId);

    fetch('asignacion_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al cargar información del ticket'
            });
            return;
        }

        currentTicketCode = data.ticket.codigo_ticket;
        const cargoUsuario = (window.usuarioCargo || '').toUpperCase();

        // Mostrar modal para seleccionar analista (asignar o reasignar) para DINAMIZADOR o ANALISTA en modo reasignación
        if (modoReasignacion || cargoUsuario === 'DINAMIZADOR') {
            createAsignacionModal();
            cargarAnalistas();
            asignacionModal.style.display = 'block';
            const asignarBtn = document.getElementById('asignarBtn');
            asignarBtn.onclick = asignarTicket;

            // Cambiar título según acción
            const modalTitle = document.querySelector('#asignacionModal h2');
            if (modalTitle) {
                modalTitle.textContent = modoReasignacion ? 'REASIGNACIÓN DE TICKET' : 'ASIGNACION DE TICKET';
            }
        } else if (cargoUsuario === 'ANALISTA') {
            // Proceso directo solo si NO es modo reasignación
            const miDocumento = window.usuarioDocumento;
            const miNombre = window.usuarioNombre;
            Swal.fire({
                title: `¿Deseas asignarte el ticket ${currentTicketCode}?`,
                text: `El ticket será asignado directamente a ti (${miNombre}).`,
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No, cancelar',
                confirmButtonText: 'Sí, continuar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceder con la asignación automática
                    const formData2 = new FormData();
                    formData2.append('action', 'asignarTicket');
                    formData2.append('ticketId', currentTicketId);
                    formData2.append('analistaId', miDocumento);

                    fetch('asignacion_handler.php', {
                        method: 'POST',
                        body: formData2
                    })
                    .then(response2 => response2.json())
                    .then(data2 => {
                        if (data2.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ticket asignado correctamente',
                                text: 'El ticket ha sido asignado a ti mismo.'
                            }).then(() => {
                                if (typeof loadTickets === 'function') loadTickets();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data2.message || 'Error al asignar el ticket'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión. Por favor, intente nuevamente.'
                        });
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Sin permisos',
                text: 'Solo dinamizadores o analistas pueden asignar tickets.'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión. Por favor, intente nuevamente.'
        });
    });
}

// Función global para reasignar un ticket (abre modal de asignación)
function reasignarTicket2(ticketId) {
    editTicket2(ticketId, true);
}

// MODAL helpers (debes tener el HTML de la modal en tu base, o crearla dinámicamente)
function createAsignacionModal() {
    if (document.getElementById('asignacionModal')) {
        asignacionModal = document.getElementById('asignacionModal');
        return;
    }

    const modalHTML = `
    <div id="asignacionModal" class="modal_asignacion">
        <div class="modal-content_asignacion">
            <span class="modal-close-asignacion">&times;</span>
            <h2>ASIGNACION DE TICKET</h2>
            <div id="anaslista_titulo">
                <label for="analistaSelect"><b>Analistas</b></label>
            </div>
            <div class="form-group-asignacion">
                <select id="analistaSelect" class="form-control-asignacion">
                    <option value="">Seleccione</option>
                </select>
            </div>
            <div class="form-group-asignacion" style="margin-top: 20px;">
                <button id="asignarBtn" class="btn-asignar">ASIGNAR</button>
            </div>
        </div>
    </div>
    `;

    // Agregar estilos para el modal si no existen
    if (!document.getElementById('asignacionModalStyle')) {
        const modalStyle = document.createElement('style');
        modalStyle.id = 'asignacionModalStyle';
        modalStyle.textContent = `
        .modal_asignacion { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content_asignacion { background-color: #fff; margin: 8% auto; padding: 22px 20px 18px 20px; border-radius: 7px; width: 400px; position: relative; box-shadow: 0 4px 16px rgba(0,0,0,0.18); max-width: 90vw; }
        .modal-close-asignacion { position: absolute; right: 14px; top: 10px; font-size: 26px; font-weight: bold; cursor: pointer; color: #333; }
        .form-group-asignacion { margin: 15px 0px 15px 0px; }
        .form-control-asignacion { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        .btn-asignar { width: 100%; padding: 12px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: background 0.2s; }
        .btn-asignar:hover, .btn-asignar:focus { background-color: #45a049; outline: none; }
        @media (max-width: 600px) {
            .modal-content_asignacion { width: 87vw; margin: 122px auto; padding: 16px 8px 14px 8px; min-width: unset; max-width: 99vw; border-radius: 5px; }
            .modal-close-asignacion { right: 10px; top: 7px; font-size: 24px; }
            .btn-asignar { padding: 10px; font-size: 0.98rem; }
            .form-control-asignacion { font-size: 0.98rem; }
        }
        `;
        document.head.appendChild(modalStyle);
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    asignacionModal = document.getElementById('asignacionModal');
    const closeBtn = asignacionModal.querySelector('.modal-close-asignacion');

    closeBtn.addEventListener('click', () => {
        asignacionModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === asignacionModal) {
            asignacionModal.style.display = 'none';
        }
    });
}

// Cargar analistas en el select
function cargarAnalistas() {
    const select = document.getElementById('analistaSelect');
    if (!select) return;

    // Limpiar opciones excepto la primera
    while (select.options.length > 1) {
        select.remove(1);
    }

    const formData = new FormData();
    formData.append('action', 'getAnalistas');

    fetch('asignacion_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            data.analistas.forEach(analista => {
                const option = document.createElement('option');
                option.value = analista.documento;
                option.textContent = analista.nombre;
                select.appendChild(option);
            });
        } else {
            console.error('Error al cargar analistas:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
    });
}

// Asignar el ticket (DINAMIZADOR o ANALISTA en modo reasignación)
function asignarTicket() {
    const analistaSelect = document.getElementById('analistaSelect');
    const analistaId = analistaSelect.value;
    const analistaNombre = analistaSelect.options[analistaSelect.selectedIndex].text;

    if (!analistaId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar un analista'
        });
        return;
    }

    Swal.fire({
        title: '¿Deseas ' + (document.querySelector('#asignacionModal h2').textContent.includes('REASIGNACIÓN') ? 'reasignar' : 'asignar') + ' el ticket ' + currentTicketCode,
        text: 'al analista ' + analistaNombre + '?',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'No, cancelar',
        confirmButtonText: 'Sí, continuar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('action', 'asignarTicket');
            formData.append('ticketId', currentTicketId);
            formData.append('analistaId', analistaId);

            fetch('asignacion_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    asignacionModal.style.display = 'none';
                    Swal.fire({
                        icon: 'success',
                        title: 'Ticket ' + (document.querySelector('#asignacionModal h2').textContent.includes('REASIGNACIÓN') ? 'reasignado' : 'asignado') + ' correctamente',
                        text: 'El ticket ha sido ' + (document.querySelector('#asignacionModal h2').textContent.includes('REASIGNACIÓN') ? 'reasignado' : 'asignado') + ' a ' + analistaNombre
                    }).then(() => {
                        if (typeof loadTickets === 'function') loadTickets();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al asignar el ticket'
                    });
                }
            })
            .catch(error => {
                console.error('Error en la petición:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión. Por favor, intente nuevamente.'
                });
            });
        }
    });
}

// Hacer las funciones accesibles globalmente
window.editTicket2 = editTicket2;
window.reasignarTicket2 = reasignarTicket2;