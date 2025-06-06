document.addEventListener('DOMContentLoaded', function () {
    // Ocultar "Cuarto Vigilancia" si cargo es FUNCIONARIO
    const formContainer = document.getElementById('contenedor-formulario');
    if (formContainer) {
        const cargo = (formContainer.dataset.cargo || '').toUpperCase();
        if (cargo === 'FUNCIONARIO') {
            const ubicacionSelect = document.getElementById('ubicacion');
            if (ubicacionSelect) {
                for (let i = ubicacionSelect.options.length - 1; i >= 0; i--) {
                    if (ubicacionSelect.options[i].value.trim().toUpperCase() === 'CUARTO VIGILANCIA') {
                        ubicacionSelect.remove(i);
                    }
                }
            }
        }
    }

    // Validación de campos obligatorios antes de enviar
    const ticketForm = document.getElementById('ticketForm');
    if (ticketForm) {
        ticketForm.addEventListener('submit', function (e) {
            let tipoCase = document.getElementById('tipo_caso').value;
            let ubicacion = document.getElementById('ubicacion').value;
            let observaciones = document.getElementById('observaciones').value.trim();
            let isValid = true;

            // Validar tipo de caso
            if (!tipoCase) {
                document.getElementById('tipo_caso_error').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('tipo_caso_error').style.display = 'none';
            }

            // Validar ubicación
            if (!ubicacion) {
                document.getElementById('ubicacion_error').style.display = 'block';
                isValid = false;
            } else {
                document.getElementById('ubicacion_error').style.display = 'none';
            }

            // Validar observaciones SOLO si ubicación es Ambientes
            const obsError = document.getElementById('observaciones_error');
            if (ubicacion === 'Ambientes' && observaciones === '') {
                if (obsError) obsError.style.display = 'block';
                isValid = false;
            } else {
                if (obsError) obsError.style.display = 'none';
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Mostrar/ocultar el error en tiempo real al cambiar ubicación
        const ubicacionSelect = document.getElementById('ubicacion');
        if (ubicacionSelect) {
            ubicacionSelect.addEventListener('change', function () {
                let obsError = document.getElementById('observaciones_error');
                let observaciones = document.getElementById('observaciones').value.trim();
                if (this.value === 'Ambientes' && observaciones === '') {
                    if (obsError) obsError.style.display = 'block';
                } else {
                    if (obsError) obsError.style.display = 'none';
                }
            });
        }
    }

    // Mensajes SweetAlert (éxito/error)
    if (window.ticketMessageType && window.ticketMessage) {
        if (window.ticketMessageType === 'exito') {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Ticket creado exitosamente. Código: ' + (window.ticketCode || ''),
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Limpiar selects y textarea después del éxito
                if (document.getElementById('tipo_caso')) document.getElementById('tipo_caso').value = '';
                if (document.getElementById('ubicacion')) document.getElementById('ubicacion').value = '';
                if (document.getElementById('observaciones')) document.getElementById('observaciones').value = '';
                if (document.getElementById('adjuntos')) document.getElementById('adjuntos').value = '';
            });
        } else if (window.ticketMessageType === 'error') {
            Swal.fire({
                title: 'Error',
                text: window.ticketMessage,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }
    }
});