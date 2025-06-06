document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("envia_formulario");

    formulario.addEventListener("submit", function (event){
        event.preventDefault(); // Evita el envío del formulario por defecto
        const formData = new FormData(this);

        fetch('logica_sesion.php',{
            method: 'POST',
            body: formData,
        })
        .then(respuesta=> {
            if (!respuesta.ok) {
                throw new Error('Error en la respuesta de la red');
            }
            return respuesta.text().then(text =>{
                try{
                    return JSON.parse(text);
                } catch (e){
                    throw new Error("La respuesta del servidor no es JSON válido. Respuesta recibida: " + text);
                }
            });
        })
        .then(datos=>{
            const campoClave = document.getElementById("campo_clave");
            const campoCorreo = document.getElementById("campo_correo");
            if(datos.cargos == true ){
                // Animación suave:
                campoCorreo.classList.add('oculto');
                setTimeout(() => {
                    campoCorreo.style.display = "none";
                    campoClave.classList.add('visible');
                }, 400); // Espera la duración de la animación
                document.getElementById('clave').setAttribute('required', '');
                document.getElementById('correo').removeAttribute('required', '');
            }
            else if (datos.redirect){
                document.getElementById('correo').value = "";
                document.getElementById('clave').value = "";
                window.location.href = datos.redirect;
            }else if(datos.error){
                alert(datos.error)
                document.getElementById("clave").value="";
            }
        })
        .catch(error => {
            alert("Ocurrió un error durante la autenticación. Por favor intente nuevamente.");
        });
    });

    // --- Mostrar/ocultar contraseña ---
    const passwordInput = document.getElementById('clave');
    const togglePassword = document.getElementById('togglePassword');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Cambia el tipo de input
            const tipo = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', tipo);

            // Cambia el icono
            const icono = togglePassword.querySelector('i');
            if (icono) {
                icono.classList.toggle('fa-eye');
                icono.classList.toggle('fa-eye-slash');
            }
        });
    }
});