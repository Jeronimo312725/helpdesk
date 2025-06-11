<?php
// Configuración de la página
$pageTitle = 'Historial del Ticket';
$basePath = '../';
$extraCSS = '<link rel="stylesheet" href="../templates/css/historial_ticket.css">';
$extraJS = '<script src="../templates/js/historial_ticket.js"></script>';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html');
    exit;
}

require '../conexion.php'; 
date_default_timezone_set('America/Bogota');

// Obtener el id del ticket
$ticket_id = 0;
if (isset($_GET['id'])) {
    $decoded = base64_decode($_GET['id']);
    if (is_numeric($decoded)) {
        $ticket_id = intval($decoded);
    }
}

// Consultar info del ticket (incluyendo tipo_caso y ubicacion)
$ticket_data = [];
$codigo_ticket = '';
if ($ticket_id > 0) {
    $stmt = $conexion->prepare("SELECT codigo_ticket, tipo_caso, prioridad, ubicacion, doc_usuario_creador, estado, doc_usuario_atencion FROM tickets WHERE id_ticket = ?");
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->bind_result($codigo_ticket_db, $tipo_caso, $prioridad, $ubicacion, $doc_creador, $estado, $doc_asignado);
    if ($stmt->fetch()) {
        $ticket_data = [
            'codigo_ticket' => $codigo_ticket_db,
            'tipo_caso'     => $tipo_caso,
            'prioridad'     => $prioridad,
            'ubicacion'     => $ubicacion,
            'doc_creador'   => $doc_creador,
            'estado'        => $estado,
            'doc_asignado'  => $doc_asignado
        ];
        $codigo_ticket = $codigo_ticket_db;
    } else {
        $codigo_ticket = 'No encontrado';
    }
    $stmt->close();
} else {
    $codigo_ticket = 'ID inválida';
}

// Función para obtener datos de usuario o funcionario por documento
function obtenerDatosUsuario($conexion, $documento) {
    $stmt = $conexion->prepare("SELECT nombre, correo, telefono FROM usuarios WHERE documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $stmt->bind_result($nombre, $correo, $telefono);
    if ($stmt->fetch()) {
        $stmt->close();
        return ['nombre' => $nombre, 'correo' => $correo, 'telefono' => $telefono];
    }
    $stmt->close();
    $stmt = $conexion->prepare("SELECT nombre, correo, telefono FROM funcionarios WHERE numero_documento = ?");
    $stmt->bind_param("s", $documento);
    $stmt->execute();
    $stmt->bind_result($nombre, $correo, $telefono);
    if ($stmt->fetch()) {
        $stmt->close();
        return ['nombre' => $nombre, 'correo' => $correo, 'telefono' => $telefono];
    }
    $stmt->close();
    return ['nombre' => 'No encontrado', 'correo' => 'No encontrado', 'telefono' => 'No encontrado'];
}

$datos_creador = isset($ticket_data['doc_creador']) ? obtenerDatosUsuario($conexion, $ticket_data['doc_creador']) : ['nombre'=>'', 'correo'=>'', 'telefono'=>''];
$datos_asignado = isset($ticket_data['doc_asignado']) ? obtenerDatosUsuario($conexion, $ticket_data['doc_asignado']) : ['nombre'=>'', 'correo'=>'', 'telefono'=>''];

// OBTENER DATOS DE SESIÓN DEL USUARIO
$usuario_doc = $_SESSION['sesion']['documento'] ?? '';
$usuario_cargo = strtoupper($_SESSION['sesion']['cargo'] ?? '');

// Determinar si el usuario puede comentar o cambiar estado
$puede_modificar = true;
if (
    $usuario_cargo === 'ANALISTA' && 
    $usuario_doc !== ($ticket_data['doc_asignado'] ?? '')
) {
    $puede_modificar = false;
}

// Bloquear intentos de publicar comentario si no tiene permiso
if (!$puede_modificar && isset($_POST['enviar_comentario'])) {
    header("Location: " . $_SERVER["REQUEST_URI"]);
    exit;
}

// Publicar comentario/cambiar estado
$comentario_exito = $comentario_errores = "";
if ($puede_modificar && isset($_POST['enviar_comentario'])) {
    $comentario = trim($_POST['comentario'] ?? '');
    $nuevo_estado = trim($_POST['estado'] ?? '');
    $usuario = $_SESSION['sesion']['nombre'] ?? 'Usuario';
    $usuario_doc = $_SESSION['sesion']['documento'] ?? '';
    $fecha = date('Y-m-d H:i:s');
    $errores = [];
    $imagenes_guardadas = [];

    $conexion->begin_transaction();
    try {
        // 1. Si el estado cambió, registrar evento en historial y actualizar ticket
        $estado_actual = $ticket_data['estado'];
        if (!empty($nuevo_estado) && strtoupper($nuevo_estado) !== strtoupper($estado_actual)) {
            $tipo_evento = 'cambio_estado';
            $descripcion = "Cambio de estado: " . strtoupper($estado_actual) . " ➔ " . strtoupper($nuevo_estado);
            $sql = "INSERT INTO historial_ticket (id_ticket, tipo_evento, descripcion, usuario, fecha_evento) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("issss", $ticket_id, $tipo_evento, $descripcion, $usuario, $fecha);
            $stmt->execute();
            $stmt->close();
            // Actualizar estado en ticket
            $stmt2 = $conexion->prepare("UPDATE tickets SET estado = ? WHERE id_ticket = ?");
            $stmt2->bind_param("si", $nuevo_estado, $ticket_id);
            $stmt2->execute();
            $stmt2->close();
        }

        // 2. Si hay comentario, registrar en historial_ticket
        $id_historial = null;
        if (!empty($comentario)) {
            $tipo_evento = 'comentario';
            $sql = "INSERT INTO historial_ticket (id_ticket, tipo_evento, descripcion, usuario, fecha_evento) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("issss", $ticket_id, $tipo_evento, $comentario, $usuario, $fecha);
            $stmt->execute();
            $id_historial = $conexion->insert_id;
            $stmt->close();

            // 3. Guardar archivos adjuntos si existen
            if ($id_historial && isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
                $total = count($_FILES['imagenes']['name']);
                $carpeta = "../uploads/";
                if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['imagenes']['error'][$i] == 0) {
                        $tmp_name = $_FILES['imagenes']['tmp_name'][$i];
                        $nombre = basename($_FILES['imagenes']['name'][$i]);
                        $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
                        $nombre_final = uniqid('img_') . "." . $extension;
                        $ruta_destino = $carpeta . $nombre_final;
                        if (move_uploaded_file($tmp_name, $ruta_destino)) {
                            $tipo_mime = mime_content_type($ruta_destino);
                            $sql_adj = "INSERT INTO archivos_adjuntos (id_historial, nombre_archivo, ruta_archivo, tipo_mime) VALUES (?, ?, ?, ?)";
                            $stmt2 = $conexion->prepare($sql_adj);
                            $ruta_relativa = "uploads/" . $nombre_final;
                            $stmt2->bind_param("isss", $id_historial, $nombre, $ruta_relativa, $tipo_mime);
                            $stmt2->execute();
                            $stmt2->close();
                        } else {
                            $errores[] = "Error al guardar $nombre";
                        }
                    }
                }
            }
        }
        $conexion->commit();
        $comentario_exito = "Comentario publicado correctamente.";
        // --- REDIRECT PARA EVITAR DUPLICADO AL RECARGAR ---
        header("Location: " . $_SERVER["REQUEST_URI"]);
        exit;
    } catch (Exception $e) {
        $conexion->rollback();
        $errores[] = "Error en la transacción: " . $e->getMessage();
    }
    if (!empty($errores)) {
        $comentario_errores = implode("<br>", $errores);
    }
}

$estado_actual = isset($ticket_data['estado']) ? trim(strtoupper($ticket_data['estado'])) : '';

// Incluir el encabezado
include_once('../templates/header.php');
include_once('../templates/sidebar.php');
include_once('../templates/main-container-begin.php');
?>

<div class="ticket-container">
    <div id="atras-codigo">
         <a id="icono-atras" href="../dinamizador/dinamizador.php"><ion-icon name="arrow-back-outline"></ion-icon></a>
         <h1><?php echo htmlspecialchars($codigo_ticket); ?></h1>
    </div>
    
    <div id="contenedor-info-ticket">
        <div class="cont-fila" id="fila_1">
            <div class="info-ticket">
                <label>Estado Ticket : </label>
                <p><?php echo htmlspecialchars($ticket_data['estado'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Tipo de caso : </label>
                <p><?php echo htmlspecialchars($ticket_data['tipo_caso'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Prioridad : </label>
                <p><?php echo htmlspecialchars($ticket_data['prioridad'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Ubicación : </label>
                <p><?php echo htmlspecialchars($ticket_data['ubicacion'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Asignado a : </label>
                <p><?php echo htmlspecialchars($datos_asignado['nombre'] ?? ''); ?></p>
            </div>
        </div>

        <div class="cont-fila" id="fila_2">
            <div class="info-ticket">
                <label>Creado por : </label>
                <p><?php echo htmlspecialchars($datos_creador['nombre'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Correo : </label>
                <p><?php echo htmlspecialchars($datos_creador['correo'] ?? ''); ?></p>
            </div>
            <div class="info-ticket">
                <label>Celular : </label>
                <p><?php echo htmlspecialchars($datos_creador['telefono'] ?? ''); ?></p>
            </div>
        </div>      
    </div>
       
    <div class="divisor"></div>
    <div id="contenedor_de_historial">
    <?php
    // Mostrar historial del ticket
    $sql = "SELECT ht.id_historial, ht.tipo_evento, ht.descripcion, ht.usuario, ht.fecha_evento 
            FROM historial_ticket ht 
            WHERE ht.id_ticket = ? 
            ORDER BY ht.fecha_evento ASC, ht.id_historial ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventos = [];
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
    $stmt->close();

    foreach ($eventos as $evento) {
        $tipo = strtolower($evento['tipo_evento']);
        $usuario = htmlspecialchars($evento['usuario']);
        $fecha = date('d/m/Y H:i', strtotime($evento['fecha_evento']));
        $descripcion = trim($evento['descripcion']);
        $adjuntos = [];

        // Adjuntos solo para comentarios/comentario_inicial
        if ($tipo === "comentario" || $tipo === "comentario_inicial") {
            $stmt_adj = $conexion->prepare("SELECT nombre_archivo, ruta_archivo, tipo_mime FROM archivos_adjuntos WHERE id_historial = ?");
            $stmt_adj->bind_param("i", $evento['id_historial']);
            $stmt_adj->execute();
            $res_adj = $stmt_adj->get_result();
            while ($adj = $res_adj->fetch_assoc()) {
                $adjuntos[] = $adj;
            }
            $stmt_adj->close();
        }

        // CLASE EXTRA PARA EL PRIMER COMENTARIO (DERECHA Y DIF COLOR)
        $clase_extra = ($tipo === "comentario_inicial") ? ' evento-inicial-derecha' : '';
        if ($tipo === "comentario" || $tipo === "comentario_inicial") {
            echo "<div class='evento evento-publicacion$clase_extra'>";
            echo "<div class='evento-header'>";
            echo $tipo === "comentario_inicial" ? "<span class='evento-tipo'>Observacion del caso</span>" : "<span class='evento-tipo'> Publicado por </span>";
            echo "<span class='evento-usuario'>$usuario</span>";
            echo "<span class='evento-fecha'>$fecha</span>";
            echo "</div>";
            if ($descripcion) {
                echo "<div class='evento-descripcion'>" . nl2br(htmlspecialchars($descripcion)) . "</div>";
            }
            if (!empty($adjuntos)) {
                echo "<div class='evento-adjuntos'>";
                foreach ($adjuntos as $adj) {
                    $ruta_web = '../' . htmlspecialchars($adj['ruta_archivo']);
                    $tipo_mime = $adj['tipo_mime'];
                    $nombre_archivo = htmlspecialchars($adj['nombre_archivo']);
                    if (strpos($tipo_mime, 'image/') === 0) {
                        echo "<a href='$ruta_web' target='_blank'><img class='miniatura-adjunto' src='$ruta_web' alt='$nombre_archivo'></a> ";
                    } else {
                        echo "<a href='$ruta_web' target='_blank' class='link-adjunto'>$nombre_archivo</a> ";
                    }
                }
                echo "</div>";
            }
            echo "</div>";
        } else {
            // Línea simple
            $color = ($tipo === "cambio_estado") ? "#e89827" : "#888";
            echo "<div class='evento-linea-simple' style='color:$color'>";
            echo htmlspecialchars($descripcion) . " - $usuario - $fecha";
            echo "</div>";
        }
    }
    ?>
    </div>
    <div class="divisor"></div>

    <div id="contenedor_comentario_analista">
        <h3>Agregar Comentario</h3>
        <?php if($comentario_exito): ?>
            <div class="mensaje-exito"><?php echo $comentario_exito; ?></div>
        <?php endif; ?>
        <?php if($comentario_errores): ?>
            <div class="mensaje-error"><?php echo $comentario_errores; ?></div>
        <?php endif; ?>
        <?php if ($puede_modificar): ?>
        <form id="form_comentario" method="POST" enctype="multipart/form-data">
            <div id="campo-texto" class="campo-form">
                <textarea id="comentario" name="comentario" rows="4" required ></textarea>
            </div>
            <div class="campo-form">
                <input type="file" id="imagenes" name="imagenes[]" accept="image/*" multiple>
            </div>
            <div id="ultimo_cont">
                <div id="lista_estado">
                    <select name="estado" id="selector_estados">
                        <option value="ABIERTO" <?php echo ($estado_actual == 'ABIERTO') ? 'selected' : ''; ?>>ABIERTO</option>
                        <option value="EN PROCESO" <?php echo ($estado_actual == 'EN PROCESO') ? 'selected' : ''; ?>>EN PROCESO</option>
                        <option value="SOLUCIONADO" <?php echo ($estado_actual == 'SOLUCIONADO') ? 'selected' : ''; ?>>SOLUCIONADO</option>
                        <option value="EN PAUSA" <?php echo ($estado_actual == 'EN PAUSA') ? 'selected' : ''; ?>>EN PAUSA</option>
                    </select>
                </div>
                <div>
                    <button id="btn-publicar" name="enviar_comentario" type="submit">PUBLICAR</button>
                </div>
                <div id="cont_reasignacion">
                    <span class="action-btn" title="Reasignar ticket" onclick="reasignarTicket2(<?php echo $ticket_id; ?>);">
                        <ion-icon id="manito" name="hand-left-outline"></ion-icon>
                    </span>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="mensaje-info" style="margin-top: 10px;">
                Solo puedes visualizar el historial de este ticket porque no está asignado a ti.<br>
                Si necesitas comentar o modificar el estado, solicita la reasignación del ticket.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal y JS para reasignación -->
<script src="../templates/js/historial_ticket_reasignacion.js"></script>

<?php
include_once('../templates/main-container-end.php');
include_once('../templates/footer.php');
?>