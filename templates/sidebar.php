<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cargoUsuario = isset($_SESSION['sesion']['cargo']) ? strtoupper($_SESSION['sesion']['cargo']) : '';
?>
<!-- Botón toggle para responsive -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<button class="menu-toggle">
    <span></span>
    <span></span>
    <span></span>
</button>

<!-- Overlay para cerrar menú en responsive -->
<div class="sidebar-poner"></div>

<!-- Expone el cargo y basePath al JS global, y también las variables para la lógica de asignación de tickets -->
<script>
    window.cargoUsuario = "<?php echo $cargoUsuario; ?>";
    window.basePath = "<?php echo isset($basePath) ? $basePath : './'; ?>";
    window.usuarioCargo = "<?php echo isset($_SESSION['sesion']['cargo']) ? strtoupper($_SESSION['sesion']['cargo']) : ''; ?>";
    window.usuarioDocumento = "<?php echo isset($_SESSION['sesion']['documento']) ? $_SESSION['sesion']['documento'] : ''; ?>";
    window.usuarioNombre = "<?php echo isset($_SESSION['sesion']['nombre']) ? $_SESSION['sesion']['nombre'] : ''; ?>";
</script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Menú lateral -->
<aside class="sidebar">
    <div class="logo-container">
        <div id="imagen" src="<?php echo isset($basePath) ? $basePath : './'; ?>"></div>
    </div>
    
    <div class="menu-section menu-principal">
        <div class="menu-header">
        <ion-icon id="icono_menu" name="grid-outline"></ion-icon>
Principal
        </div>
        <ul class="submenu">
            <li class="item-ver-tickets"><a href="<?php echo isset($basePath) ? $basePath : './'; ?>dinamizador/dinamizador.php">Ver tickets</a></li>
            <li class="item-crear-ticket"><a href="<?php echo isset($basePath) ? $basePath : './'; ?>funcionarios/registro_ticket.php">Crear ticket</a></li>
        </ul>
    </div>
    
    <div class="menu-section menu-usuarios">
        <div class="menu-header">
        <ion-icon id="icono_usuarios" name="people-outline"></ion-icon>
Usuarios
        </div>
        <ul class="submenu">
            <li><a href="<?php echo isset($basePath) ? $basePath : './'; ?>dinamizador/dinamizador_ver_usuarios.php">Ver usuarios</a></li>
            <li><a href="<?php echo isset($basePath) ? $basePath : './'; ?>dinamizador/dinamizador_registro_usuarios.php">Registro usuarios</a></li>
        </ul>
    </div>
    
    <div class="logout-btn">
        <button type="button" id="logoutBtn" style="background:none; border:none; cursor:pointer;">
            <svg width="40" height="40" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg" transform="rotate(0 0 0)">
                <path d="M11.25 2.62451C10.0074 2.62451 9 3.63187 9 4.87451V6.60927C9.76128 6.98704 10.2493 7.76589 10.2493 8.62451V10.3745L11.75 10.3745C12.9926 10.3745 14 11.3819 14 12.6245C14 13.8672 12.9926 14.8745 11.75 14.8745H10.2493V16.6245C10.2493 17.4831 9.76128 18.262 9 18.6397V20.3745C9 21.6172 10.0074 22.6245 11.25 22.6245H17.25C18.4926 22.6245 19.5 21.6172 19.5 20.3745V4.87451C19.5 3.63187 18.4926 2.62451 17.25 2.62451H11.25Z" fill="#ffffff"/>
                <path d="M8.28618 7.93158C8.5665 8.04764 8.74928 8.32114 8.74928 8.62453L8.74928 11.8745L11.75 11.8745C12.1642 11.8745 12.5 12.2103 12.5 12.6245C12.5 13.0387 12.1642 13.3745 11.75 13.3745L8.74928 13.3745V16.6245C8.74928 16.9279 8.56649 17.2014 8.28617 17.3175C8.00585 17.4335 7.68322 17.3693 7.46877 17.1547L3.50385 13.187C3.34818 13.0496 3.25 12.8485 3.25 12.6245C3.25 12.4016 3.34723 12.2015 3.50159 12.0641L7.46878 8.09437C7.68324 7.87978 8.00587 7.81552 8.28618 7.93158Z" fill="#ffffff"/>
            </svg>
        </button>
    </div>
</aside>

<!-- Incluir el JavaScript para el menú -->
<script src="<?php echo isset($basePath) ? $basePath : './'; ?>templates/js/menu.js"></script>