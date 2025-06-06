// Notification System for Ticket Management
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const notificationBell = document.getElementById('camp_noti');
    const modal = document.getElementById('modal');
    const modalContent = document.querySelector('.modal-content');
    
    // Check for notifications every minute (60000ms)
    checkNotifications();
    setInterval(checkNotifications, 60000);
    
    // Function to check for notifications
    function checkNotifications() {
        const formData = new FormData();
        formData.append('action', 'checkNotifications');
        
        fetch('notificacion.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ya viene ordenado desde el backend
                const sortedNotifications = data.notifications;
                updateNotificationBell(sortedNotifications);
                updateNotificationModal(sortedNotifications);
            } else {
                console.error('Error checking notifications:', data.message);
            }
        });
    }
    
    // Update notification bell appearance
    function updateNotificationBell(notifications) {
        if (notifications.length > 0) {
            notificationBell.classList.add('has-notifications');
            let badge = document.getElementById('notification-badge');
            if (!badge) {
                badge = document.createElement('span');
                badge.id = 'notification-badge';
                badge.classList.add('notification-badge');
                notificationBell.parentNode.appendChild(badge);
            }
            badge.textContent = notifications.length;
        } else {
            notificationBell.classList.remove('has-notifications');
            const badge = document.getElementById('notification-badge');
            if (badge) {
                badge.remove();
            }
        }
    }

    function updateNotificationModal(notifications) {
        modalContent.innerHTML = '<span class="close" onclick="window.ocultarModal()">&times;</span>';
        const header = document.createElement('div');
        header.className = 'notification-header';
        header.innerHTML = `<h2>Notificaciones (${notifications.length})</h2>`;
        modalContent.appendChild(header);

        if (notifications.length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'empty-notifications';
            emptyMessage.textContent = 'No hay notificaciones pendientes';
            modalContent.appendChild(emptyMessage);
        } else {
            const notificationsContainer = document.createElement('div');
            notificationsContainer.className = 'notifications-container';
            notifications.forEach(notification => {
                const notificationItem = document.createElement('div');
                notificationItem.className = 'notification-item';
                const timeDisplay = formatTimeDifference(notification.dias_transcurridos);
                notificationItem.innerHTML = `
                    <div class="notification-item-header">
                        <span class="notification-code">${notification.codigo_ticket}</span>
                        <span class="notification-time">${timeDisplay}</span>
                    </div>
                    <div class="notification-item-body">
                        <div>
                            <span class="notification-label">Ubicación:</span>
                            <span>${notification.ubicacion}</span>
                        </div>
                        <div>
                            <span class="notification-label">Prioridad:</span>
                            <span>
                                <span class="priority-indicator ${getPriorityClass(notification.prioridad)}"></span>
                                <span class="priority-text">${notification.prioridad}</span>
                            </span>
                        </div>
                        <div>
                            <span class="notification-label">Estado:</span>
                            <span class="estado-tag ${getStatusClass(notification.estado)}">${notification.estado}</span>
                        </div>
                        <div>
                            <span class="notification-label">Caso:</span>
                            <span class="estado-tag" id="noti_caso" ${getStatusClass(notification.tipo_caso)}>${notification.tipo_caso}</span>
                        </div>
                    </div>
                    <div class="notification-item-footer">
                        <button class="btn-action" onclick="editTicket(${notification.id_ticket})">
                            Ver Ticket
                        </button>
                    </div>
                `;
                notificationsContainer.appendChild(notificationItem);
            });
            modalContent.appendChild(notificationsContainer);
        }

        // Add close button at the bottom
        const closeButton = document.createElement('button');
        closeButton.className = 'btn-close';
        closeButton.textContent = 'Cerrar';
        closeButton.onclick = window.ocultarModal;
        modalContent.appendChild(closeButton);
    }

    // Helper function to get priority class
    function getPriorityClass(priority) {
        switch(priority.toLowerCase()) {
            case 'alta':
                return 'alta';
            case 'media':
                return 'media';
            case 'baja':
                return 'baja';
            default:
                return '';
        }
    }

    // Helper function to get status class
    function getStatusClass(status) {
        switch(status.toLowerCase()) {
            case 'abierto':
                return 'abierto';
            case 'en proceso':
                return 'proceso';
            case 'en pausa':
                return 'pausa';
            case 'solucionado':
                return 'solucionado';
            default:
                return '';
        }
    }

    // Helper function to format time difference
    function formatTimeDifference(days) {
        days = parseInt(days);
        if (days < 1) {
            return 'Hoy';
        } else if (days === 1) {
            return 'Hace 1 día';
        } else {
            return `Hace ${days} días`;
        }
    }
});

// Función para mostrar el modal (ámbito global)
function mostrarModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.style.display = 'block';
    }
}

// Función para ocultar el modal (ámbito global)
function ocultarModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Hacer las funciones accesibles globalmente
window.mostrarModal = mostrarModal;
window.ocultarModal = ocultarModal;

// Función para redirigir al historial del ticket seleccionado
function editTicket(ticketId) {
    // Redirige codificando el id en base64 (igual que desde la tabla)
    window.location.href = `historial_ticket.php?id=${btoa(ticketId)}`;
}