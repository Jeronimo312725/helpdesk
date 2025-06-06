<?php
// Activar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1); // ¡Ponlo en 0 en producción!

$pageTitle = 'Ver usuarios';
$basePath = '../';
$extraCSS = '<link rel="stylesheet" href="../templates/css/ver_usuarios.css">';

// Iniciar la sesión
session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html'); // Ajusta la ruta según corresponda
    exit;
}

// --- PROCESAMIENTO AJAX ---
if (isset($_POST['action'])) {
    $conexionFile = $basePath . 'conexion.php';
    if (file_exists($conexionFile)) {
        include_once($conexionFile);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Archivo de conexión no encontrado: ' . $conexionFile
        ]);
        exit;
    }

    if (!isset($conexion) || !$conexion) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error de conexión a la base de datos'
        ]);
        exit;
    }

    $action = $_POST['action'];

    switch ($action) {
        case 'getUsuarios':
            $search = $_POST['search'] ?? '';
            $todos_usuarios = [];
            try {
                // FUNCIONARIOS
                $sql_funcionarios = "SELECT id_funcionario, numero_documento, nombre, correo, telefono, cargo, fecha_ultima_sesion, 'Funcionario' as tipo, 'N/A' as clave FROM funcionarios WHERE estado = 'ACTIVO'";
                if (!empty($search)) {
                    $sql_funcionarios .= " AND (
                        numero_documento LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        nombre LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        correo LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        cargo LIKE '%" . $conexion->real_escape_string($search) . "%'
                    )";
                }
                $result_funcionarios = $conexion->query($sql_funcionarios);
                if ($result_funcionarios) {
                    while ($row = $result_funcionarios->fetch_assoc()) {
                        $todos_usuarios[] = $row;
                    }
                }
                // USUARIOS
                $sql_usuarios = "SELECT id_usuario, documento as numero_documento, nombre, correo, telefono, cargo, fecha_ultima_sesion, 'Usuario' as tipo, clave FROM usuarios WHERE estado = 'ACTIVO'";
                if (!empty($search)) {
                    $sql_usuarios .= " AND (
                        documento LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        nombre LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        correo LIKE '%" . $conexion->real_escape_string($search) . "%' OR 
                        cargo LIKE '%" . $conexion->real_escape_string($search) . "%'
                    )";
                }
                $result_usuarios = $conexion->query($sql_usuarios);
                if ($result_usuarios) {
                    while ($row = $result_usuarios->fetch_assoc()) {
                        $todos_usuarios[] = $row;
                    }
                }
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'usuarios' => $todos_usuarios
                ]);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'getUsuario':
            $id = $_POST['id'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';
            if (empty($id) || empty($tipo)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID o tipo de usuario no especificado'
                ]);
                exit;
            }
            try {
                if ($tipo == 'Funcionario') {
                    $sql = "SELECT id_funcionario, numero_documento, nombre, correo, telefono, cargo, 'Funcionario' as tipo FROM funcionarios WHERE id_funcionario = " . intval($id);
                } else {
                    $sql = "SELECT id_usuario, documento as numero_documento, nombre, correo, telefono, cargo, 'Usuario' as tipo FROM usuarios WHERE id_usuario = " . intval($id);
                }
                $result = $conexion->query($sql);
                if ($result && $result->num_rows > 0) {
                    $usuario = $result->fetch_assoc();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'usuario' => $usuario
                    ]);
                } else {
                    throw new Exception('Usuario no encontrado');
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'actualizarUsuario':
            $id = $_POST['id'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';
            $documento = $_POST['documento'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $cargo = $_POST['cargo'] ?? '';
            $clave = $_POST['clave'] ?? '';

            if (empty($id) || empty($tipo)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID o tipo de usuario no especificado'
                ]);
                exit;
            }

            try {
                // Limpiar y normalizar datos
                $documento = mysqli_real_escape_string($conexion, $documento);
                $nombre = mysqli_real_escape_string($conexion, trim($nombre));
                $correo = mysqli_real_escape_string($conexion, strtolower($correo));
                $telefono = mysqli_real_escape_string($conexion, $telefono);
                $cargo = mysqli_real_escape_string($conexion, $cargo);

                // Normalizar caracteres especiales en nombre y correo
                $replace_chars = [
                    'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
                    'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
                    'ñ' => 'n', 'Ñ' => 'N'
                ];
                $nombre = strtr($nombre, $replace_chars);
                $correo = strtr($correo, $replace_chars);
                $nombre = strtoupper($nombre);

                // Verificar si el documento ya existe en otro usuario
                if ($tipo == 'Funcionario') {
                    $check_sql = "SELECT id_funcionario FROM funcionarios WHERE numero_documento = '$documento' AND id_funcionario != " . intval($id);
                    $tabla = 'funcionarios';
                    $id_campo = 'id_funcionario';
                    $doc_campo = 'numero_documento';
                } else {
                    $check_sql = "SELECT id_usuario FROM usuarios WHERE documento = '$documento' AND id_usuario != " . intval($id);
                    $tabla = 'usuarios';
                    $id_campo = 'id_usuario';
                    $doc_campo = 'documento';
                }
                $check_result = $conexion->query($check_sql);
                if ($check_result && $check_result->num_rows > 0) {
                    throw new Exception('El documento ya está registrado en otro usuario');
                }

                // Verificar si el correo ya existe en otro usuario
                if ($tipo == 'Funcionario') {
                    $check_email_sql = "SELECT id_funcionario FROM funcionarios WHERE correo = '$correo' AND id_funcionario != " . intval($id);
                } else {
                    $check_email_sql = "SELECT id_usuario FROM usuarios WHERE correo = '$correo' AND id_usuario != " . intval($id);
                }
                $check_email_result = $conexion->query($check_email_sql);
                if ($check_email_result && $check_email_result->num_rows > 0) {
                    throw new Exception('El correo electrónico ya está en uso por otro usuario');
                }

                // Construir consulta de actualización
                if (!empty($clave)) {
                    $clave_encriptada = md5($clave);
                    $sql = "UPDATE $tabla SET 
                        $doc_campo = '$documento',
                        nombre = '$nombre',
                        correo = '$correo',
                        telefono = '$telefono',
                        cargo = '$cargo',
                        clave = '$clave_encriptada'
                        WHERE $id_campo = " . intval($id);
                } else {
                    $sql = "UPDATE $tabla SET 
                        $doc_campo = '$documento',
                        nombre = '$nombre',
                        correo = '$correo',
                        telefono = '$telefono',
                        cargo = '$cargo'
                        WHERE $id_campo = " . intval($id);
                }
                $result = $conexion->query($sql);
                if ($result) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true
                    ]);
                } else {
                    throw new Exception('Error al actualizar: ' . $conexion->error);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;

        case 'inhabilitarUsuario':
            $id = $_POST['id'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';
            if (empty($id) || empty($tipo)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID o tipo de usuario no especificado'
                ]);
                exit;
            }
            try {
                if ($tipo == 'Funcionario') {
                    $tabla = 'funcionarios';
                    $id_campo = 'id_funcionario';
                } else {
                    $tabla = 'usuarios';
                    $id_campo = 'id_usuario';
                }
                $sql = "UPDATE $tabla SET estado = 'INACTIVO' WHERE $id_campo = " . intval($id);
                $result = $conexion->query($sql);
                if ($result) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Usuario inhabilitado correctamente'
                    ]);
                } else {
                    throw new Exception('Error al inhabilitar usuario: ' . $conexion->error);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
    }
    exit; // FIN del procesamiento AJAX
}

// ----------- RENDER HTML NORMAL -----------
try {
    include_once($basePath . 'conexion.php');
    include_once($basePath . 'templates/header.php');
    include_once($basePath . 'templates/sidebar.php');
    include_once($basePath . 'templates/main-container-begin.php');
} catch (Exception $e) {
    echo "<div class='error'>Error al cargar las plantillas: " . $e->getMessage() . "</div>";
}
?>
<!-- Cargar la versión mejorada del script de JavaScript -->
<script src="../templates/js/ver_usuarios.js"></script>

<h1>Listado de Usuarios</h1>
<div class="filters-container">
    <div id="primer_contenedor">
        <div id="contenedor_buscar" class="search-filter">
            <input type="text" id="searchInput" placeholder="Buscar por documento, nombre, correo o cargo">
            <div id="contenedor_logo"></div>
        </div>
    </div>
</div>
<div class="users-container">
    <div id="id_tabla_usuarios" class="usuarios-table-container">
        <table id="usuariosTable" class="usuarios-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Cargo</th>
                   
                    <th>Clave</th>
                    <th>Última Sesión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los usuarios se cargarán dinámicamente aquí -->
                <tr>
                    <td colspan="9" style="text-align: center;">Cargando usuarios...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
try {
    include_once($basePath . 'templates/main-container-end.php');
    include_once($basePath . 'templates/footer.php');
} catch (Exception $e) {
    echo "<div class='error'>Error al cargar los elementos finales: " . $e->getMessage() . "</div>";
}
?>