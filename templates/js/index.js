window.onload = function() {
  if (window.history && window.history.pushState) {
    window.history.pushState('nohistory', null, '');
    window.onpopstate = function() {
      // Si el usuario intenta ir hacia atrás, lo redirigimos al index
      window.location.href = '/index.html'; // Reemplaza '/index.html' con la ruta correcta a tu página principal
    };
  }
}