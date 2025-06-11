let currentTicketId = null;
let currentTicketCode = null;

function reasignarTicket2(ticketId) {
    currentTicketId = ticketId;
    obtenerCodigoTicket(ticketId, function(codigo) {
        currentTicketCode = codigo;
        cargarAnalistas();
        abrirModalReasignar();
    });
}

// Obtiene el código del ticket por AJAX
function obtenerCodigoTicket(ticketId, callback) {
    fetch('../dinamizador/asignacion_handler.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'getTicketInfo', ticketId })
    })
    .then(r => r.json())
    .then(data => {
        if(data.success && data.ticket && data.ticket.codigo_ticket) {
            callback(data.ticket.codigo_ticket);
        } else {
            callback('[Desconocido]');
        }
    }).catch(() => callback('[Desconocido]'));
}

function abrirModalReasignar() {
    let modal = document.getElementById('asignacionModal');
    if (!modal) {
        const modalHTML = `
        <div id="asignacionModal" class="modal_asignacion" style="display: none;">
            <div class="modal-content_asignacion">
                <span class="modal-close-asignacion">&times;</span>
                <h2>REASIGNACIÓN DE TICKET</h2>
                <div class="form-group-asignacion">
                    <label for="analistaSelect"><b>Analistas</b></label>
                    <select id="analistaSelect" class="form-control-asignacion">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div class="form-group-asignacion" style="margin-top: 20px;">
                    <button id="reasignarBtn" class="btn-asignar">REASIGNAR</button>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        if (!document.getElementById('asignacionModalStyle')) {
            const style = document.createElement('style');
            style.id = 'asignacionModalStyle';
            style.innerHTML = `
            .modal_asignacion { display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items:center; justify-content:center;}
            .modal-content_asignacion { background-color: #fff; margin: 8% auto; padding: 22px 20px 18px 20px; border-radius: 7px; width: 400px; position: relative; box-shadow: 0 4px 16px rgba(0,0,0,0.18); max-width: 90vw; }
            .modal-close-asignacion { position: absolute; right: 14px; top: 10px; font-size: 26px; font-weight: bold; cursor: pointer; color: #333; }
            .form-group-asignacion { margin: 15px 0px 15px 0px; }
            .form-control-asignacion { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
            .btn-asignar { width: 100%; padding: 12px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1rem; transition: background 0.2s; }
            .btn-asignar:hover, .btn-asignar:focus { background-color: #45a049; outline: none; }
            @media (max-width: 600px) {
                .modal-content_asignacion { width: 87vw; margin: 122px auto; padding: 16px 8px 14px 8px; min-width: unset; max-width: 99vw; border-radius: 5px;}
                .modal-close-asignacion { right: 10px; top: 7px; font-size: 24px;}
                .btn-asignar { padding: 10px; font-size: 0.98rem;}
                .form-control-asignacion { font-size: 0.98rem;}
            }
            `;
            document.head.appendChild(style);
        }
        document.querySelector('.modal-close-asignacion').onclick = cerrarModalReasignar;
        window.onclick = function(e) {
            if (e.target === document.getElementById('asignacionModal')) cerrarModalReasignar();
        }
        document.getElementById('reasignarBtn').onclick = confirmarReasignacion;
    }
    document.getElementById('asignacionModal').style.display = 'flex';
}

function cerrarModalReasignar() {
    let modal = document.getElementById('asignacionModal');
    if (modal) modal.style.display = 'none';
}

function cargarAnalistas() {
    fetch('../dinamizador/asignacion_handler.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'getAnalistas' })
    })
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('analistaSelect');
        select.innerHTML = '<option value="">Seleccione</option>';
        if (data.success && Array.isArray(data.analistas)) {
            data.analistas.forEach(a => {
                const o = document.createElement('option');
                o.value = a.documento;
                o.textContent = a.nombre;
                select.appendChild(o);
            });
        }
    });
}

function confirmarReasignacion() {
    const select = document.getElementById('analistaSelect');
    const analistaId = select.value;
    const analistaNombre = select.options[select.selectedIndex].text;
    if (!analistaId) {
        Swal.fire({ icon: 'error', title: '¡Debes seleccionar un analista!' });
        return;
    }
    Swal.fire({
        title: `¿Reasignar el ticket <b>${currentTicketCode}</b> a ${analistaNombre}?`,
        html: `¿Seguro que deseas reasignar el ticket <b>${currentTicketCode}</b> a <b>${analistaNombre}</b>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, reasignar',
        cancelButtonText: 'No'
    }).then(result => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'asignarTicket');
            fd.append('ticketId', currentTicketId);
            fd.append('analistaId', analistaId);
            fetch('../dinamizador/asignacion_handler.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                cerrarModalReasignar();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: '¡Ticket reasignado!', text: `El ticket ${currentTicketCode} fue reasignado correctamente.` })
                    .then(()=>location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Error al reasignar' });
                }
            })
            .catch(() => {
                cerrarModalReasignar();
                Swal.fire({ icon: 'error', title: 'Error de conexión' });
            });
        }
    });
}