document.addEventListener('DOMContentLoaded', function () {
    // Scroll al final del historial para mostrar el último evento
    var contenedor = document.getElementById('contenedor_de_historial');
    if(contenedor) {
        contenedor.scrollTop = contenedor.scrollHeight;
    }
});