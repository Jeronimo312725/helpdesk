

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
                    console.log("Texto recibido del servidor:", text); // para depurar
                    try{
                    
                        return JSON.parse(text);
                    
                    } catch (e){
                        console.error("Respuesta no es JSON válido:", text);
                        throw new Error("La respuesta del servidor no es JSON válido. Respuesta recibida: " + text);
                    }
                
                });
            })
            .then(datos=>{
                console.log("respuesta", datos);

   if(datos.cargos == true ){
    const campoClave = document.getElementById("campo_clave");
    const campoCorreo = document.getElementById("campo_correo");
    campoCorreo.style.display = "none";
    campoClave.classList.add('visible');
    document.getElementById('clave').setAttribute('required', '');
    document.getElementById('correo').removeAttribute('required', '');
}
                else if (datos.redirect){
                    console.log("redireccionando")
                    document.getElementById('correo').value = "";
                    document.getElementById('clave').value = "";
                    window.location.href = datos.redirect;
                }else if(datos.error){
                    alert(datos.error) //se deja el campo vacio para cuando redireccione - se accede al campo clave y no al div para poder limpiarlo 
                    document.getElementById("clave").value="";

                }else{
                    console.log("sin accion valida", datos )
                }
            })
    
            .catch(error => {
                console.error('Error:', error);
                alert("Ocurrió un error durante la autenticación. Por favor intente nuevamente.");
            });

        })

        
})
