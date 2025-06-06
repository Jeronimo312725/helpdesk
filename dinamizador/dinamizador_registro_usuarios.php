<?php
session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html');
    exit;
}
require '../conexion.php';

$esEdicion = false;
$usuarioEditar = null;
$tituloFormulario = "Registro de Usuarios";
$botonTexto = "Registrar Usuario";

// --- Decodifica parámetros y busca usuario si es edición ---
if (
    isset($_GET['modo']) && $_GET['modo'] === 'editar' &&
    isset($_GET['id']) && isset($_GET['tipo'])
) {
    $esEdicion = true;
    $id = intval(base64_decode($_GET['id']));
    $tipo = base64_decode($_GET['tipo']);
    if ($tipo === 'Funcionario') {
        $sql = "SELECT id_funcionario AS id, numero_documento, nombre, correo, telefono, cargo FROM funcionarios WHERE id_funcionario = $id";
    } else {
        $sql = "SELECT id_usuario AS id, documento AS numero_documento, nombre, correo, telefono, cargo FROM usuarios WHERE id_usuario = $id";
    }
    $result = $conexion->query($sql);
    if ($result && $result->num_rows > 0) {
        $usuarioEditar = $result->fetch_assoc();
        $tituloFormulario = "Editar Usuario";
        $botonTexto = "Actualizar Usuario";
    } else {
        header("Location: dinamizador_registro_usuarios.php");
        exit;
    }
}

$response = "";
$error = false;
$registro_exitoso = false;
$redirigir_ver_usuarios = false;

// PROCESAMIENTO POST (registro o edición)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['esEdicion']) && $_POST['esEdicion'] == "1") {
        // --- EDICIÓN ---
        $id = intval($_POST['id']);
        $tipo = $_POST['tipo'];
        $documento = isset($_POST['documento']) ? mysqli_real_escape_string($conexion, $_POST['documento']) : '';
        $nombre = isset($_POST['nombre']) ? mysqli_real_escape_string($conexion, trim($_POST['nombre'])) : '';
        $correo = isset($_POST['correo']) ? mysqli_real_escape_string($conexion, $_POST['correo']) : '';
        $telefono = isset($_POST['telefono']) ? mysqli_real_escape_string($conexion, $_POST['telefono']) : '';
        $cargo = isset($_POST['cargo']) ? mysqli_real_escape_string($conexion, $_POST['cargo']) : '';
        $clave = isset($_POST['clave']) ? $_POST['clave'] : '';

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

        if ($tipo == 'Funcionario') {
            $check_sql = "SELECT id_funcionario FROM funcionarios WHERE numero_documento = '$documento' AND id_funcionario != $id";
            $tabla = 'funcionarios';
            $id_campo = 'id_funcionario';
            $doc_campo = 'numero_documento';
        } else {
            $check_sql = "SELECT id_usuario FROM usuarios WHERE documento = '$documento' AND id_usuario != $id";
            $tabla = 'usuarios';
            $id_campo = 'id_usuario';
            $doc_campo = 'documento';
        }
        $check_result = $conexion->query($check_sql);
        if ($check_result && $check_result->num_rows > 0) {
            $response = 'El documento ya está registrado en otro usuario';
            $error = true;
        } else {
            // Verificar correo duplicado
            if ($tipo == 'Funcionario') {
                $check_email_sql = "SELECT id_funcionario FROM funcionarios WHERE correo = '$correo' AND id_funcionario != $id";
            } else {
                $check_email_sql = "SELECT id_usuario FROM usuarios WHERE correo = '$correo' AND id_usuario != $id";
            }
            $check_email_result = $conexion->query($check_email_sql);
            if ($check_email_result && $check_email_result->num_rows > 0) {
                $response = 'El correo electrónico ya está en uso por otro usuario';
                $error = true;
            } else {
                // Solo actualizar clave si es ANALISTA/DINAMIZADOR y fue digitada
                if (!empty($clave) && in_array($cargo, ['DINAMIZADOR', 'ANALISTA'])) {
                    $clave_encriptada = md5($clave);
                    $sql = "UPDATE $tabla SET 
                        $doc_campo = '$documento',
                        nombre = '$nombre',
                        correo = '$correo',
                        telefono = '$telefono',
                        cargo = '$cargo',
                        clave = '$clave_encriptada'
                        WHERE $id_campo = $id";
                } else {
                    // No actualizar clave, solo otros campos
                    $sql = "UPDATE $tabla SET 
                        $doc_campo = '$documento',
                        nombre = '$nombre',
                        correo = '$correo',
                        telefono = '$telefono',
                        cargo = '$cargo'
                        WHERE $id_campo = $id";
                }
                $result = $conexion->query($sql);
                if ($result) {
                    $response = 'Usuario actualizado correctamente';
                    $registro_exitoso = true;
                    $redirigir_ver_usuarios = true;
                } else {
                    $response = 'Error al actualizar: ' . $conexion->error;
                    $error = true;
                }
            }
        }
    } else {
        // --- REGISTRO NUEVO ---
        $documento = isset($_POST['documento']) ? mysqli_real_escape_string($conexion, $_POST['documento']) : '';
        $nombre = isset($_POST['nombre']) ? mysqli_real_escape_string($conexion, trim($_POST['nombre'])) : '';
        $correo = isset($_POST['correo']) ? mysqli_real_escape_string($conexion, $_POST['correo']) : '';
        $telefono = isset($_POST['telefono']) ? mysqli_real_escape_string($conexion, $_POST['telefono']) : '';
        $cargo = isset($_POST['cargo']) ? mysqli_real_escape_string($conexion, $_POST['cargo']) : '';
        $clave = isset($_POST['clave']) ? mysqli_real_escape_string($conexion, $_POST['clave']) : '';

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

        $es_usuario = in_array($cargo, ['ANALISTA', 'DINAMIZADOR']);
        $es_funcionario = in_array($cargo, ['FUNCIONARIO', 'VIGILANTE']);

        if ($es_usuario) {
            // Validar duplicado en usuarios
            $query = $conexion->prepare("SELECT * FROM `usuarios` WHERE `documento` = ?");
            $query->bind_param("s", $documento);
            $query->execute();
            $result = $query->get_result();
            $query->close();

            if ($result->num_rows > 0) {
                $response = 'El documento ya está registrado en el sistema';
                $error = true;
            } else {
                // Validar correo duplicado en usuarios
                $query = $conexion->prepare("SELECT * FROM `usuarios` WHERE `correo` = ?");
                $query->bind_param("s", $correo);
                $query->execute();
                $result = $query->get_result();
                $query->close();

                if ($result->num_rows > 0) {
                    $response = 'El correo electrónico ya está en uso';
                    $error = true;
                } else {
                    // Registro usuario (requiere clave)
                    if (empty($clave)) {
                        $response = 'Debe ingresar una contraseña para este tipo de usuario.';
                        $error = true;
                    } else {
                        $clave_hash = md5($clave);
                        $registro = $conexion->prepare("INSERT INTO `usuarios`(`documento`, `nombre`, `correo`, `telefono`, `clave`, `cargo`) VALUES (?, ?, ?, ?, ?, ?)");
                        $registro->bind_param("ssssss", $documento, $nombre, $correo, $telefono, $clave_hash, $cargo);

                        if ($registro->execute()) {
                            $response = 'Usuario registrado exitosamente';
                            $registro_exitoso = true;
                        } else {
                            $response = 'Error al registrar el usuario: ' . $conexion->error;
                            $error = true;
                        }
                        $registro->close();
                    }
                }
            }
        } elseif ($es_funcionario) {
            // Validar duplicado en funcionarios
            $query = $conexion->prepare("SELECT * FROM `funcionarios` WHERE `numero_documento` = ?");
            $query->bind_param("s", $documento);
            $query->execute();
            $result = $query->get_result();
            $query->close();

            if ($result->num_rows > 0) {
                $response = 'El documento ya está registrado en el sistema';
                $error = true;
            } else {
                // Validar correo duplicado en funcionarios
                $query = $conexion->prepare("SELECT * FROM `funcionarios` WHERE `correo` = ?");
                $query->bind_param("s", $correo);
                $query->execute();
                $result = $query->get_result();
                $query->close();

                if ($result->num_rows > 0) {
                    $response = 'El correo electrónico ya está en uso';
                    $error = true;
                } else {
                    // Registro funcionario (NO requiere clave)
                    $registro = $conexion->prepare("INSERT INTO `funcionarios`(`numero_documento`, `nombre`, `correo`, `telefono`, `cargo`) VALUES (?, ?, ?, ?, ?)");
                    $registro->bind_param("sssss", $documento, $nombre, $correo, $telefono, $cargo);

                    if ($registro->execute()) {
                        $response = 'Funcionario registrado exitosamente';
                        $registro_exitoso = true;
                    } else {
                        $response = 'Error al registrar el funcionario: ' . $conexion->error;
                        $error = true;
                    }
                    $registro->close();
                }
            }
        } else {
            $response = 'Cargo no válido para registro';
            $error = true;
        }

        $_SESSION['response'] = $response;
        $_SESSION['error'] = $error;
        $_SESSION['registro_exitoso'] = $registro_exitoso;
        header("Location: dinamizador_registro_usuarios.php");
        exit();
    }
}

// MENSAJES DESPUÉS DEL REGISTRO (después del redirect, solo en registro)
if (isset($_SESSION['response'])) {
    $response = $_SESSION['response'];
    $error = $_SESSION['error'];
    $registro_exitoso = $_SESSION['registro_exitoso'];
    unset($_SESSION['response'], $_SESSION['error'], $_SESSION['registro_exitoso']);
}

$pageTitle = $tituloFormulario;
$basePath = '../';
$extraCSS = '<link rel="stylesheet" href="../templates/css/dinamizador_registro_usuarios.css">';

include_once('../templates/header.php');
include_once('../templates/sidebar.php');
include_once('../templates/main-container-begin.php');

// --- Lógica para mostrar el campo contraseña solo para ANALISTA o DINAMIZADOR ---
$mostrarClave = false;
$valorCargo = '';
$cargos_usuario = ['ANALISTA', 'DINAMIZADOR'];
if ($esEdicion && $usuarioEditar) {
    $valorCargo = $usuarioEditar['cargo'];
    if (in_array($usuarioEditar['cargo'], $cargos_usuario)) {
        $mostrarClave = true;
    }
} elseif (!$esEdicion) {
    // Mostrar campo clave solo para registro de usuario DINAMIZADOR/ANALISTA (se controla en el frontend por JS, y en el backend por validación)
    $mostrarClave = true;
}

// Para saber si el cargo es solo de usuario o funcionario/vigilante
$cargos_funcion = ['FUNCIONARIO','VIGILANTE'];
$soloUsuario = $esEdicion && in_array($valorCargo, $cargos_usuario);
$soloFuncion = $esEdicion && in_array($valorCargo, $cargos_funcion);
?>
   
<h1><?php echo $tituloFormulario; ?></h1>
<div class="registro-contenedor">
    <div class="carta-cuerpito">
        <form id="formulario_registro" action="dinamizador_registro_usuarios.php<?php echo $esEdicion ? '?modo=editar&id=' . urlencode(base64_encode($usuarioEditar['id'])) . '&tipo=' . urlencode(base64_encode($tipo)) : ''; ?>" method="POST">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="esEdicion" value="1">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuarioEditar['id']); ?>">
                <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo); ?>">
            <?php endif; ?>
            <div class="filas">
                <div class="form-group">
                    <label for="documento">Documento de Identidad</label>
                    <input type="tel" id="documento" name="documento"
                        value="<?php echo $esEdicion ? htmlspecialchars($usuarioEditar['numero_documento']) : ''; ?>"
                        placeholder="Ingrese número de documento" pattern="[0-9]{6,11}" maxlength="11" minlength="6"
                        title="Ingrese número de documento entre 6 y 11 caracteres (solo numeros)" required <?php echo $esEdicion ? 'readonly' : ''; ?>>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre"
                        value="<?php echo $esEdicion ? htmlspecialchars($usuarioEditar['nombre']) : ''; ?>"
                        placeholder="Ingrese nombre completo" style="text-transform:uppercase" required>
                </div>
            </div>
            <div class="filas">
                <div class="form-group">
                    <label for="correo">Correo Electrxxónico</label>
                    <input type="email" id="correo" name="correo"
                        value="<?php echo $esEdicion ? htmlspecialchars($usuarioEditar['correo']) : ''; ?>"
                        placeholder="ejemplo@correo.com" required>
                    <div id="correo-error" class="correo-error"></div>
                </div>
                <div class="form-group">
                    <label for="telefono">Número Telefónico</label>
                    <input type="text" id="telefono" name="telefono"
                        value="<?php echo $esEdicion ? htmlspecialchars($usuarioEditar['telefono']) : ''; ?>"
                        placeholder="Ingrese número telefónico" required>
                </div>
            </div>
            <div class="filas">
                <div class="form-group">
                    <label for="cargo">Cargo</label>
                    <select id="cargo" name="cargo" required <?php echo ($soloFuncion) ? '' : ''; ?>>
                        <option value="" disabled <?php echo !$esEdicion ? 'selected' : ''; ?>>Seleccione un cargo</option>
                        <?php if ($esEdicion && $soloUsuario): ?>
                            <option value="ANALISTA" <?php if($valorCargo == 'ANALISTA') echo 'selected'; ?>>ANALISTA</option>
                            <option value="DINAMIZADOR" <?php if($valorCargo == 'DINAMIZADOR') echo 'selected'; ?>>DINAMIZADOR</option>
                        <?php elseif ($esEdicion && $soloFuncion): ?>
                            <option value="<?php echo htmlspecialchars($valorCargo); ?>" selected><?php echo htmlspecialchars($valorCargo); ?></option>
                        <?php else: // Registro ?>
                            <option value="ANALISTA">ANALISTA</option>
                            <option value="DINAMIZADOR">DINAMIZADOR</option>
                            <option value="VIGILANTE">VIGILANTE</option>
                            <option value="FUNCIONARIO">FUNCIONARIO</option>
                        <?php endif; ?>
                    </select>
                </div>
        <?php if ($mostrarClave): ?>
<div class="form-group" for-clave>
    <label for="clave"><?php echo $esEdicion ? 'Nueva contraseña (dejar vacío para mantener actual)' : 'Contraseña'; ?></label>
    <div class="password-container">
        <input 
            type="password" 
            id="clave" 
            name="clave"
            placeholder="<?php echo $esEdicion ? 'Nueva contraseña (opcional)' : 'Ingrese contraseña'; ?>"
        >
        <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
    </div>
    <div id="password-requirements" class="password-hint">
        La contraseña debe tener entre 8 y 15 caracteres, incluir al menos una letra mayúscula, 
        una minúscula, un número y un carácter especial (@$!%?#&.).
    </div>
</div>
<?php endif; ?>
            </div>
            <div id="contenedor-boton" class="form-group">
                <button type="submit" class="btn-registrar"><?php echo $botonTexto; ?></button>
            </div>
        </form>
    </div>
</div>
<script>
    const response = <?php echo json_encode($response); ?>;
    const error = <?php echo json_encode($error); ?>;
    const registro_exitoso = <?php echo json_encode($registro_exitoso); ?>;
    const esEdicion = <?php echo $esEdicion ? 'true' : 'false'; ?>;
    const cargoActual = <?php echo json_encode($valorCargo); ?>;
    const redirigir_ver_usuarios = <?php echo json_encode($redirigir_ver_usuarios); ?>;
</script>
<script src="../templates/js/dinamizador_registro_usuarios.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (<?php echo json_encode($soloFuncion); ?>) {
        var cargoSel = document.getElementById('cargo');
        if (cargoSel) {
            cargoSel.addEventListener('mousedown', function(e){ e.preventDefault(); });
            cargoSel.addEventListener('keydown', function(e){ e.preventDefault(); });
            cargoSel.addEventListener('change', function(e){ this.value = '<?php echo htmlspecialchars($valorCargo); ?>'; });
        }
    }
});
</script>
<?php
include_once('../templates/main-container-end.php');
include_once('../templates/footer.php');
$conexion->close();
?>