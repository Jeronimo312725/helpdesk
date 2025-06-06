

function validaNumericos(event) {
    if(event.charCode >= 48 && event.charCode <= 57){
      return true;
     }
     return false;        
}
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar alertas si hay respuesta del servidor
    if (response) {
        if (error) {
            Swal.fire({
                title: 'Error',
                text: response,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        } else if (registro_exitoso) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'Registro completado correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.html';
                }
            });
        }
    }

    // Validaciones de formulario
    const form = document.querySelector('form');
    const numDocumento = document.getElementById('numero_documento');
    const nombreCompleto = document.getElementById('nombre_completo');
    const correo = document.getElementById('correo');
    const telefono = document.getElementById('telefono');

    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Validar número de documento (solo números entre 6 y 15 dígitos)
        if (!/^[0-9]{6,15}$/.test(numDocumento.value)) {
            isValid = false;
            Swal.fire({
                title: 'Error',
                text: 'El documento debe contener entre 6 y 15 dígitos numéricos',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }

        // Validar nombre completo (solo letras y espacios)
        if (!/^[A-Za-zÀ-ÿ\s]+$/.test(nombreCompleto.value)) {
            isValid = false;
            Swal.fire({
                title: 'Error',
                text: 'El nombre solo debe contener letras y espacios',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }

        // Validar correo electrónico
        if (!/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(correo.value)) {
            isValid = false;
            Swal.fire({
                title: 'Error',
                text: 'Ingrese un correo electrónico válido',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }

        // Validar teléfono (solo números entre 7 y 10 dígitos)
        if (!/^[0-9]{7,10}$/.test(telefono.value)) {
            isValid = false;
            Swal.fire({
                title: 'Error',
                text: 'El teléfono debe contener entre 7 y 10 dígitos numéricos',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }

        if (!isValid) {
            event.preventDefault();
        }
    });
});