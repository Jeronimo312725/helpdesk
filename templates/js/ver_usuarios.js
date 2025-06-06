document.addEventListener('DOMContentLoaded', function() {
    // Variables para elementos del DOM
    const inputBusqueda = document.getElementById('searchInput');
    const tablaUsuarios = document.getElementById('usuariosTable');

    // Cargar usuarios al iniciar
    cargarUsuarios();

    // Búsqueda en tiempo real
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            cargarUsuarios();
        });
    }

    // Función para cargar usuarios
    function cargarUsuarios() {
        const terminoBusqueda = inputBusqueda ? inputBusqueda.value : '';
        if (tablaUsuarios) {
            const cuerpoTabla = tablaUsuarios.querySelector('tbody');
            cuerpoTabla.innerHTML = '<tr><td colspan="8" style="text-align: center;">Cargando usuarios...</td></tr>';
        }
        const datos = new FormData();
        datos.append('action', 'getUsuarios');
        datos.append('search', terminoBusqueda);
        fetch('dinamizador_ver_usuarios.php', {
            method: 'POST',
            body: datos
        })
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.success) {
                renderizarUsuarios(datos.usuarios);
            } else {
                if (tablaUsuarios) {
                    const cuerpoTabla = tablaUsuarios.querySelector('tbody');
                    cuerpoTabla.innerHTML = `<tr><td colspan="8" style="text-align: center;">Error: ${datos.message}</td></tr>`;
                }
            }
        })
        .catch(() => {
            if (tablaUsuarios) {
                const cuerpoTabla = tablaUsuarios.querySelector('tbody');
                cuerpoTabla.innerHTML = `<tr><td colspan="8" style="text-align: center;">Error de conexión. Por favor, intente nuevamente.</td></tr>`;
            }
        });
    }

    // Renderizar usuarios en la tabla
    function renderizarUsuarios(usuarios) {
        if (!tablaUsuarios) return;
        const cuerpoTabla = tablaUsuarios.querySelector('tbody');
        cuerpoTabla.innerHTML = '';
        if (usuarios.length === 0) {
            const fila = document.createElement('tr');
            fila.innerHTML = `<td colspan="8" style="text-align: center;">No se encontraron usuarios.</td>`;
            cuerpoTabla.appendChild(fila);
            return;
        }
        usuarios.forEach(usuario => {
            const fila = document.createElement('tr');
            const id = usuario.id_funcionario || usuario.id_usuario;
            const tipo = usuario.tipo;
            fila.innerHTML = `
                <td>${usuario.numero_documento}</td>
                <td>${usuario.nombre}</td>
                <td>${usuario.correo}</td>
                <td>${usuario.telefono}</td>
                <td>${usuario.cargo}</td>
                <td>${usuario.clave ? usuario.clave : 'N/A'}</td>
                <td>${formatearFecha(usuario.fecha_ultima_sesion)}</td>
                <td>
                    <span class="action-btn edit-btn" title="Editar usuario" onclick="editarUsuario(${id}, '${tipo}')">
                        <ion-icon name="create-outline"></ion-icon>
                    </span>
                    <span class="action-btn delete-btn" title="Inhabilitar usuario" onclick="inhabilitarUsuario(${id}, '${tipo}')">
                      <ion-icon name="trash-outline"></ion-icon>
                    </span>
                </td>
            `;
            cuerpoTabla.appendChild(fila);
        });
    }

    // Función para formatear fechas
    window.formatearFecha = function(fecha) {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        if (isNaN(date.getTime())) return fecha;
        const dia = String(date.getDate()).padStart(2, '0');
        const mes = String(date.getMonth() + 1).padStart(2, '0');
        const anio = date.getFullYear();
        const horas = String(date.getHours()).padStart(2, '0');
        const minutos = String(date.getMinutes()).padStart(2, '0');
        return `${dia}/${mes}/${anio} ${horas}:${minutos}`;
    };

    // Exponer la función para recarga tras editar/inhabilitar
    window.cargarUsuarios = cargarUsuarios;
});

// Editar usuario: redirige al formulario de registro para editar
function editarUsuario(id, tipo) {
    // Codifica los parámetros en base64 para mayor seguridad en la URL
    const idEncoded = btoa(id.toString());
    const tipoEncoded = btoa(tipo);
    const url = `dinamizador_registro_usuarios.php?modo=editar&id=${encodeURIComponent(idEncoded)}&tipo=${encodeURIComponent(tipoEncoded)}`;
    window.location.href = url;
}

// Inhabilitar usuario y refrescar la tabla automáticamente
function inhabilitarUsuario(id, tipo) {
    Swal.fire({
        title: '¿Está seguro?',
        text: `¿Desea inhabilitar este ${tipo.toLowerCase()}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, inhabilitar',
        cancelButtonText: 'Cancelar'
    }).then((resultado) => {
        if (resultado.isConfirmed) {
            const datos = new FormData();
            datos.append('action', 'inhabilitarUsuario');
            datos.append('id', id);
            datos.append('tipo', tipo);
            fetch('dinamizador_ver_usuarios.php', {
                method: 'POST',
                body: datos
            })
            .then(respuesta => respuesta.json())
            .then(datos => {
                if (datos.success) {
                    Swal.fire({
                        title: '¡Inhabilitado!',
                        text: datos.message,
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.cargarUsuarios();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: datos.message,
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    title: 'Error',
                    text: 'Error de conexión. Por favor, intente nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        }
    });
}