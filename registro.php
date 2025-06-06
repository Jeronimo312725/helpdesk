<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requiere el archivo de conexión
require 'conexion.php'; 

date_default_timezone_set('America/Bogota');

$response = "";
$error = false;
$registro_exitoso = false; // Variable para controlar si el registro fue exitoso

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre_completo']) ? mysqli_real_escape_string($conexion, trim($_POST['nombre_completo'])) : '';
    $num_documento = isset($_POST['numero_documento']) ? mysqli_real_escape_string($conexion, $_POST['numero_documento']) : '';
    $correo = isset($_POST['correo']) ? mysqli_real_escape_string($conexion, $_POST['correo']) : '';
    $telefono = isset($_POST['telefono']) ? mysqli_real_escape_string($conexion, $_POST['telefono']) : '';
    
    $fecha_ultima_sesion = date('Y-m-d H:i:s');

    // Eliminar espacios en blanco al inicio y al final del nombre
    $nombre = trim($nombre);
    $correo = strtolower($correo);

    // Normalizar caracteres especiales
    $replace_chars = [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'ñ' => 'n', 'Ñ' => 'N'
    ];

    $nombre = strtr($nombre, $replace_chars);
    $correo = strtr($correo, $replace_chars);
    $nombre = strtoupper($nombre);

    // Verificar si el número de documento ya existe
    $query = $conexion->prepare("SELECT * FROM `funcionarios` WHERE `numero_documento` = ?");
    $query->bind_param("s", $num_documento);
    $query->execute();
    $result = $query->get_result();
    $query->close();

    if ($result->num_rows > 0) {
        $response = 'Número de documento ya existe';
        $error = true;
    } else {
        // Registro del nuevo funcio
$registro = $conexion->prepare("INSERT INTO `funcionarios`(`numero_documento`, `nombre`, `correo`, `telefono`, `cargo`, `fecha_ultima_sesion`) 
VALUES (?, ?, ?, ?, 'FUNCIONARIO', ?)");
$registro->bind_param("sssss", $num_documento, $nombre, $correo, $telefono, $fecha_ultima_sesion);
        if ($registro->execute()) {
            $response = 'Registro exitoso';
            $registro_exitoso = true;
        } else {
            $response = 'Error al registrar el funcionario';
            $error = true;
        }
        $registro->close();
    }
}
?>

    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="templates/css/registro.css?v=1">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="cajita_cucho">
        <div class="caja_izquierda">
            <img id="imagen" src="iconosena.png" alt="Logo">
        </div>
        <div class="caja_derecha">
            <div class="logo">
                <div id="texto">REGISTRO</div>
            </div>
            <form action="registro.php" method="POST">
                <div class="campos">
                    <label for="numero_documento" class="titulos">Documento de Identidad</label>
                    <input type="text" name="numero_documento" id="numero_documento" placeholder="# Documento" pattern="^\d{6,16}$" 
                   minlength="6" maxlength="16" 
                  onkeypress='return validaNumericos(event)' required title="Debe contener entre 6 y 10 dígitos numéricos.">
                </div>
                <div class="campos">
                    <label for="nombre_completo" class="titulos">Nombre Completo</label>
                    <input type="text" name="nombre_completo" id="nombre_completo" placeholder="Escribe tu nombre completo" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$" required title="El nombre debe contener solo letras y espacios, y tener entre 3 y 50 caracteres.">
                </div>
                <div class="campos">
                    <label for="correo" class="titulos">Correo</label>
                    <input type="email" name="correo" id="correo" placeholder="ejemplo@sena.edu.co" required title="Ingrese un correo electrónico válido.">
                </div>
                <div class="campos">
                    <label for="telefono" class="titulos">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" placeholder="Número de contacto" pattern="^\+?\d{7,15}$" required title="El número de teléfono debe contener entre 7 y 15 dígitos y puede incluir un '+' al inicio.">
                </div>
                <button type="submit" class="btn_iniciar">REGISTRAR</button>
                <div class="registro">
                    Ya tengo cuenta <a href="index.html">Iniciar Sesión</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        const response = <?php echo json_encode($response); ?>;
        const error = <?php echo json_encode($error); ?>;
        const registro_exitoso = <?php echo json_encode($registro_exitoso); ?>;
    </script>
    <script src="templates/js/registro_funcionarios.js"></script>
</body>
</html>
<?php
// Cerrar la conexión al final del script
$conexion->close();
?>

