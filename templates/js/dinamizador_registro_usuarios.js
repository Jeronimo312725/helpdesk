document.addEventListener('DOMContentLoaded', function() {
    // Mostrar alertas si hay respuesta del servidor
    if (typeof response !== "undefined" && response) {
        if (error) {
            Swal.fire({
                title: 'Error',
                text: response,
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        } else if (registro_exitoso) {
            // Si es edición exitosa y está la variable de redirección, redirigir después del alert
            if (typeof redirigir_ver_usuarios !== "undefined" && redirigir_ver_usuarios) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: response,
                    icon: 'success',
                    confirmButtonText: 'CONTINUAR'
                }).then(() => {
                    window.location.href = "dinamizador_ver_usuarios.php";
                });
            } else {
                Swal.fire({
                    title: '¡Éxito!',
                    text: response,
                    icon: 'success',
                    confirmButtonText: 'CONTINUAR'
                });
            }
        }
    }

    // Toggle para mostrar/ocultar contraseña
    const passwordInput = document.getElementById('clave');
    const togglePassword = document.getElementById('togglePassword');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    }

    // Validación en tiempo real de la contraseña
    if (passwordInput) {
        passwordInput.addEventListener('input', validarContrasena);
    }

    // Validación en tiempo real para correo institucional SENA
    const correoInput = document.getElementById('correo');
    const correoError = document.getElementById('correo-error');

    if (correoInput) {
        correoInput.addEventListener('input', function() {
            const correoVal = correoInput.value.trim().toLowerCase();
            const regex = /^[a-z0-9._%+-]+@sena\.edu\.co$/;
            if (correoVal === "") {
                correoError.style.display = "none";
                correoInput.setCustomValidity('');
            } else if (!regex.test(correoVal)) {
                correoError.textContent = "El correo debe ser institucional (@sena.edu.co)";
                correoError.style.display = "block";
                correoInput.setCustomValidity("El correo debe ser institucional (@sena.edu.co)");
            } else {
                correoError.style.display = "none";
                correoInput.setCustomValidity('');
            }
        });
    }

    // Mostrar/ocultar campo clave según cargo seleccionado (solo en registro)
    const cargoInput = document.getElementById('cargo');
    function toggleClavePorCargo() {
        const formGroupClave = document.querySelector('.form-group[for-clave]');
        if (!cargoInput || !formGroupClave) return;
        if (cargoInput.value === "ANALISTA" || cargoInput.value === "DINAMIZADOR") {
            formGroupClave.style.display = "";
            if (passwordInput) passwordInput.required = true;
        } else {
            formGroupClave.style.display = "none";
            if (passwordInput) {
                passwordInput.value = "";
                passwordInput.required = false;
                passwordInput.setCustomValidity("");
            }
        }
    }
    if (cargoInput) {
        cargoInput.addEventListener('change', toggleClavePorCargo);
        // Ejecutar una vez al cargar
        toggleClavePorCargo();
    }

    // Si está en edición y el usuario es FUNCIONARIO o VIGILANTE, bloquear el select de cargo para que no sea editable
    if (typeof esEdicion !== "undefined" && esEdicion && (cargoInput && (cargoInput.value === "FUNCIONARIO" || cargoInput.value === "VIGILANTE"))) {
        cargoInput.addEventListener('mousedown', function(e){ e.preventDefault(); });
        cargoInput.addEventListener('keydown', function(e){ e.preventDefault(); });
        cargoInput.addEventListener('change', function(e){ this.value = cargoInput.value; });
    }

    // Validaciones del formulario antes del submit
    const form = document.getElementById('formulario_registro');
    if (form) {
        form.addEventListener('submit', function(event) {
            // Campos a validar
            const documento = document.getElementById('documento');
            const nombre = document.getElementById('nombre');
            const correo = document.getElementById('correo');
            const clave = document.getElementById('clave');
            const cargo = document.getElementById('cargo');

            // Validar documento (solo números entre 6 y 15 dígitos)
            if (!/^[0-9]{6,15}$/.test(documento.value)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'El documento debe contener entre 6 y 15 dígitos numéricos',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            // Validar nombre (solo letras y espacios)
            if (!/^[A-Za-zÀ-ÿ\s]+$/.test(nombre.value)) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'El nombre solo debe contener letras y espacios',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
            // Validar correo institucional SENA
            if (!/^[a-z0-9._%+-]+@sena\.edu\.co$/.test(correo.value.trim().toLowerCase())) {
                event.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'El correo debe ser institucional (@sena.edu.co)',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }

            // Validar contraseña SOLO si el campo existe, SOLO si está visible y SOLO si es requerida
            if (clave && clave.required && clave.value.trim() !== "") {
                if (clave.value.length < 8 || clave.value.length > 15
                    || !/[A-Z]/.test(clave.value)
                    || !/[a-z]/.test(clave.value)
                    || !/[0-9]/.test(clave.value)
                    || !/[@$!%?#&.]/.test(clave.value)) {
                    event.preventDefault();
                    Swal.fire({
                        title: 'Error',
                        text: 'La contraseña debe tener entre 8 y 15 caracteres, al menos una mayúscula, una minúscula, un número y un carácter especial (@$!%?#&.)',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                    return;
                }
            }

            // Validar que se seleccione un cargo
            if (cargo && cargo.value === "") {
                event.preventDefault();
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un cargo',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
                return;
            }
        });
    }
});

// Función para validar la contraseña en tiempo real
function validarContrasena() {
    const passwordInput = document.getElementById('clave');
    const password = passwordInput.value;
    const msgElement = document.getElementById("password-requirements");

    // Requisitos de la contraseña
    const tieneMayuscula = /[A-Z]/.test(password);
    const tieneMinuscula = /[a-z]/.test(password);
    const tieneNumero = /[0-9]/.test(password);
    const tieneEspecial = /[@$!%?#&.]/.test(password);
    const longitudCorrecta = password.length >= 8 && password.length <= 15;

    let mensaje = "";
    if (!longitudCorrecta) mensaje += "• Entre 8 y 15 caracteres.\n";
    if (!tieneMayuscula) mensaje += "• Al menos una letra mayúscula.\n";
    if (!tieneMinuscula) mensaje += "• Al menos una letra minúscula.\n";
    if (!tieneNumero) mensaje += "• Al menos un número.\n";
    if (!tieneEspecial) mensaje += "• Al menos un carácter especial (@$!%?#&.).\n";

    if (mensaje) {
        msgElement.textContent = mensaje;
        msgElement.style.display = "block";
        passwordInput.setCustomValidity(mensaje);
    } else {
        msgElement.style.display = "none";
        passwordInput.setCustomValidity("");
    }
}