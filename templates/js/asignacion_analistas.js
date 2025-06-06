// Modal para asignación de tickets
let asignacionModal = null;

// Función para crear la modal si no existe
function createAsignacionModal() {
    if (document.getElementById('asignacionModal')) return;
    
    // Crear el elemento modal
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
    
    // Agregar estilos para el modal
    const modalStyle = document.createElement('style');
    modalStyle.textContent = `
      .modal_asignacion {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
      }
      #anaslista_titulo {
        margin: 27px 0px 0px 0px;
        font-weight: bold;
      }
      .modal-content_asignacion {
        background-color: #fff;
        margin: 8% auto;
        padding: 22px 20px 18px 20px;
        border-radius: 7px;
        width: 400px;
        position: relative;
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        max-width: 90vw;
        transition: all 0.25s;
      }
      .modal-close-asignacion {
        position: absolute;
        right: 14px;
        top: 10px;
        font-size: 26px;
        font-weight: bold;
        cursor: pointer;
        color: #333;
      }
      .form-group-asignacion {
        margin: 15px 0px 15px 0px;
      }
      .form-control-asignacion {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
      }
      .btn-asignar {
        width: 100%;
        padding: 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        font-size: 1rem;
        transition: background 0.2s;
      }
      .btn-asignar:hover, .btn-asignar:focus {
        background-color: #45a049;
        outline: none;
      }
      /* ---- Responsive ---- */
      @media (max-width: 600px) {
        .modal-content_asignacion {
          width: 87vw;
          margin: 122px auto;
          padding: 16px 8px 14px 8px;
          min-width: unset;
          max-width: 99vw;
          border-radius: 5px;
        }
        .modal-close-asignacion {
          right: 10px;
          top: 7px;
          font-size: 24px;
        }
        .btn-asignar {
          padding: 10px;
          font-size: 0.98rem;
        }
        .form-control-asignacion {
          font-size: 0.98rem;
        }
      }
      @media (max-width: 400px) {
        .modal-content_asignacion {
          width: 90vw;
          border-radius: 10px;
          padding: 12px 10px;
        }
        .form-control-asignacion, .btn-asignar {
          font-size: 0.93rem;
        }
      }
    `;
    
    document.head.appendChild(modalStyle);
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Obtener referencias
    asignacionModal = document.getElementById('asignacionModal');
    const closeBtn = asignacionModal.querySelector('.modal-close-asignacion');
    
    // Configurar eventos
    closeBtn.addEventListener('click', () => {
        asignacionModal.style.display = 'none';
    });
    
    // Cerrar el modal al hacer clic fuera de él
    window.addEventListener('click', (event) => {
        if (event.target === asignacionModal) {
            asignacionModal.style.display = 'none';
        }
    });
}

// Función para cargar los analistas en el select
function cargarAnalistas() {
    const select = document.getElementById('analistaSelect');
    if (!select) return;
    
    // Limpiar opciones existentes excepto la primera
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    // Cargar analistas desde la base de datos
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
            // Agregar opciones al select
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

// Variable para almacenar el ID del ticket actual
let currentTicketId = null;
let currentTicketCode = null;

// Función para abrir el modal de asignación o asignar directo según cargo
function editTicket2(ticketId) {
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

        if (cargoUsuario === 'DINAMIZADOR') {
            // Proceso normal: mostrar modal para seleccionar analista
            createAsignacionModal();
            cargarAnalistas();
            asignacionModal.style.display = 'block';
            const asignarBtn = document.getElementById('asignarBtn');
            asignarBtn.onclick = asignarTicket;
        } else if (cargoUsuario === 'ANALISTA') {
            // Proceso directo: asignar a sí mismo con confirmación
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
                                loadTickets();
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
            // Otros cargos, puedes personalizar si lo deseas
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

// Función para asignar el ticket (DINAMIZADOR selecciona analista)
function asignarTicket() {
    const analistaSelect = document.getElementById('analistaSelect');
    const analistaId = analistaSelect.value;
    const analistaNombre = analistaSelect.options[analistaSelect.selectedIndex].text;
    
    // Validar que se haya seleccionado un analista
    if (!analistaId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar un analista'
        });
        return;
    }
    
    // Mostrar confirmación con SweetAlert
    Swal.fire({
        title: '¿Deseas Asignar el ticket ' + currentTicketCode,
        text: 'al analista ' + analistaNombre + '?',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'No, cancelar',
        confirmButtonText: 'Sí, continuar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceder con la asignación
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
                    // Cerrar modal
                    asignacionModal.style.display = 'none';
                    
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Ticket asignado correctamente',
                        text: 'El ticket ha sido asignado a ' + analistaNombre
                    }).then(() => {
                        // Recargar tickets para reflejar cambios
                        loadTickets();
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

// Asegurarnos de que la función loadTickets esté disponible globalmente
// window.loadTickets = loadTickets; // descomenta si lo necesitas