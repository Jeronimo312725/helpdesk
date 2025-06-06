document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const menuHeaders = document.querySelectorAll('.menu-header');
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContainer = document.querySelector('.main-container');
    const sidebarOverlay = document.querySelector('.sidebar-poner');

    // Función para abrir/cerrar submenús
    menuHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const menuSection = this.parentElement;
            if (menuSection.classList.contains('active')) {
                menuSection.classList.remove('active');
                this.classList.remove('active');
            } else {
                document.querySelectorAll('.menu-section.active').forEach(section => {
                    section.classList.remove('active');
                    section.querySelector('.menu-header').classList.remove('active');
                });
                menuSection.classList.add('active');
                this.classList.add('active');
            }
        });
    });

    // Función para toggle del menú en móviles
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (mainContainer) {
                mainContainer.classList.toggle('main-container-full');
            }
            document.body.classList.toggle('menu-open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('active');
            }
        });
    }

    // Cerrar menú al hacer clic en el overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            if (mainContainer) {
                mainContainer.classList.add('main-container-full');
            }
            document.body.classList.remove('menu-open');
            this.classList.remove('active');
        });
    }

    // Cerrar menú al cambiar el tamaño de la ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('active');
            if (mainContainer) {
                mainContainer.classList.remove('main-container-full');
            }
            document.body.classList.remove('menu-open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
        }
    });

    // Añadir efecto "activo" al enlace actual basado en la URL
    const currentLocation = window.location.href;
    const menuLinks = document.querySelectorAll('.submenu a');
    menuLinks.forEach(link => {
        if (link.href === currentLocation) {
            link.classList.add('current-page');
            link.style.backgroundColor = 'rgba(255, 255, 255, 0.15)';
            const parentSection = link.closest('.menu-section');
            if (parentSection) {
                parentSection.classList.add('active');
                parentSection.querySelector('.menu-header').classList.add('active');
            }
        }
    });

    // Lógica para mostrar/ocultar menús según el cargo
    const cargo = (window.cargoUsuario || '').toUpperCase();

    if (cargo === 'DINAMIZADOR') {
        // No ocultes nada, ve todo
    } else if (cargo === 'ANALISTA') {
        // Oculta la sección de usuarios
        const menuUsuarios = document.querySelector('.menu-usuarios');
        if (menuUsuarios) menuUsuarios.style.display = 'none';
    } else if (cargo === 'FUNCIONARIO' || cargo === 'VIGILANTE') {
        // Oculta "Ver tickets"
        const itemVerTickets = document.querySelector('.item-ver-tickets');
        if (itemVerTickets) itemVerTickets.style.display = 'none';
        // Oculta la sección de usuarios
        const menuUsuarios = document.querySelector('.menu-usuarios');
        if (menuUsuarios) menuUsuarios.style.display = 'none';
    }

    // Cerrar sesión con SweetAlert
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: "¿Está seguro que desea salir?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = window.basePath + "logout.php";
                }
            });
        });
    }
});