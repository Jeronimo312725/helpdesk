<?php
// Configuración de la página
$pageTitle = 'Creación de Tickets';
$basePath = '../';
$extraCSS = '<link rel="stylesheet" href="../templates/css/registro_ticket.css">';
$extraJS = '<script src="../templates/js/registro_ticket.js"></script>';

// Configuración de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requiere el archivo de conexión
require '../conexion.php'; 

// Establecer zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar la sesión para poder usar mensajes flash y datos del usuario
session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html'); // Ajusta la ruta según corresponda
    exit;
}

// Variable para controlar mensajes de éxito o error
$mensaje = '';
$tipo_mensaje = '';
$ticket_creado = false;
$codigo_generado = '';

// Verificar si hay mensajes flash de sesión anterior
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    $tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
    $codigo_generado = $_SESSION['codigo_generado'] ?? '';
    $ticket_creado = ($tipo_mensaje == 'exito');
    unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje'], $_SESSION['codigo_generado']);
}

// Función para obtener la lista de casos
function coordinaciones_lista($conexion) {
    $sql = "SELECT DISTINCT `tipo_caso`, `caso`, `prioridad_caso` FROM `casos`";
    $result = $conexion->query($sql);
    $casos = [];
    while ($row = $result->fetch_assoc()) {
        $casos[] = $row['tipo_caso'] . ' - ' . $row['caso'];
    }
    return $casos;
}

// Procesar el formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_caso_completo = $_POST['tipo_caso'] ?? '';
    $ubicacion = $_POST['ubicacion'] ?? '';
    $observaciones = trim($_POST['observaciones'] ?? '');

    // Validación especial: Si ubicación es Ambientes, observaciones es obligatorio
    if ($ubicacion === 'Ambientes' && $observaciones === '') {
        $_SESSION['mensaje'] = 'El campo observaciones es obligatorio si la ubicación es Ambientes.';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($observaciones === '') {
        $observaciones = 'Sin observaciones';
    }
    $doc_usuario_creador = $_SESSION['sesion']['documento'] ?? null;
    $nombre_usuario = $_SESSION['sesion']['nombre'] ?? 'Sistema';

    if (empty($doc_usuario_creador)) {
        $_SESSION['mensaje'] = 'No se encontró información del usuario en la sesión. Por favor vuelva a iniciar sesión.';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: ../index.php');
        exit;
    }

    if (empty($tipo_caso_completo) || empty($ubicacion)) {
        $_SESSION['mensaje'] = 'Todos los campos obligatorios deben estar completos';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $partes = explode(' - ', $tipo_caso_completo);
        if (count($partes) < 2) {
            $_SESSION['mensaje'] = 'Formato de tipo de caso incorrecto';
            $_SESSION['tipo_mensaje'] = 'error';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $tipo_caso = $partes[0];
            $caso = $partes[1];

            $sql_prioridad = "SELECT prioridad_caso FROM casos WHERE tipo_caso = ? AND caso = ? LIMIT 1";
            $stmt_prioridad = $conexion->prepare($sql_prioridad);
            $stmt_prioridad->bind_param("ss", $tipo_caso, $caso);
            $stmt_prioridad->execute();
            $resultado_prioridad = $stmt_prioridad->get_result();

            $prioridad = ($resultado_prioridad->num_rows > 0)
                ? $resultado_prioridad->fetch_assoc()['prioridad_caso']
                : 'Media';

            // Generar código del ticket
            $prefijo = strtoupper(substr($tipo_caso, 0, 3));
            $anio = date('Y');
            $sql_consecutivo = "SELECT MAX(SUBSTRING(codigo_ticket, 8)) as ultimo_consecutivo 
                                FROM tickets 
                                WHERE codigo_ticket LIKE '%$anio%'";
            $resultado_consecutivo = $conexion->query($sql_consecutivo);
            $fila_consecutivo = $resultado_consecutivo->fetch_assoc();
            $ultimo_consecutivo = intval($fila_consecutivo['ultimo_consecutivo'] ?? 0);
            $nuevo_consecutivo = $ultimo_consecutivo + 1;
            $consecutivo_formateado = str_pad($nuevo_consecutivo, 4, '0', STR_PAD_LEFT);
            $codigo_ticket = $prefijo . $anio . $consecutivo_formateado;
            $estado = 'Abierto';

            $conexion->begin_transaction();
            try {
                // 1. Insertar ticket
                $sql_ticket = "INSERT INTO tickets (codigo_ticket, tipo_caso, prioridad, ubicacion, doc_usuario_creador, estado) 
                               VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_ticket = $conexion->prepare($sql_ticket);
                $stmt_ticket->bind_param("ssssss", $codigo_ticket, $tipo_caso_completo, $prioridad, $ubicacion, $doc_usuario_creador, $estado);

                if ($stmt_ticket->execute()) {
                    $id_ticket = $conexion->insert_id;

                    // 2. Insertar comentario inicial en historial_ticket SIEMPRE
                    $tipo_evento = 'comentario_inicial';
                    $sql_historial = "INSERT INTO historial_ticket 
                        (id_ticket, tipo_evento, descripcion, usuario) 
                        VALUES (?, ?, ?, ?)";
                    $stmt_historial = $conexion->prepare($sql_historial);
                    $stmt_historial->bind_param("isss", $id_ticket, $tipo_evento, $observaciones, $nombre_usuario);
                    if ($stmt_historial->execute()) {
                        $id_historial = $conexion->insert_id;

                        // 3. Guardar archivos adjuntos si existen
                        if (isset($_FILES['adjuntos']) && !empty($_FILES['adjuntos']['name'][0])) {
                            $total = count($_FILES['adjuntos']['name']);
                            for ($i = 0; $i < $total; $i++) {
                                if ($_FILES['adjuntos']['error'][$i] === UPLOAD_ERR_OK) {
                                    $tmp_name = $_FILES['adjuntos']['tmp_name'][$i];
                                    $name = basename($_FILES['adjuntos']['name'][$i]);
                                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                                    $safe_name = uniqid('adj_') . '.' . $ext;
                                    $ruta_destino = "../uploads/" . $safe_name;

                                    // Asegura la existencia de la carpeta uploads
                                    if (!is_dir("../uploads")) {
                                        mkdir("../uploads", 0777, true);
                                    }

                                    if (move_uploaded_file($tmp_name, $ruta_destino)) {
                                        $tipo_mime = mime_content_type($ruta_destino);
                                        $sql_adjunto = "INSERT INTO archivos_adjuntos 
                                            (id_historial, nombre_archivo, ruta_archivo, tipo_mime) 
                                            VALUES (?, ?, ?, ?)";
                                        $stmt_adjunto = $conexion->prepare($sql_adjunto);
                                        $ruta_relativa = "uploads/" . $safe_name;
                                        $stmt_adjunto->bind_param("isss", $id_historial, $name, $ruta_relativa, $tipo_mime);
                                        $stmt_adjunto->execute();
                                    } else {
                                        error_log("No se pudo mover el archivo $tmp_name a $ruta_destino");
                                    }
                                } else {
                                    error_log("Error al subir archivo: " . $_FILES['adjuntos']['error'][$i]);
                                }
                            }
                        }
                    }

                    $conexion->commit();
                    $_SESSION['mensaje'] = "¡Ticket creado exitosamente! Código: $codigo_ticket";
                    $_SESSION['tipo_mensaje'] = 'exito';
                    $_SESSION['codigo_generado'] = $codigo_ticket;
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $conexion->rollback();
                    $_SESSION['mensaje'] = "Error al crear el ticket: " . $stmt_ticket->error;
                    $_SESSION['tipo_mensaje'] = 'error';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            } catch (Exception $e) {
                $conexion->rollback();
                $_SESSION['mensaje'] = "Error en la transacción: " . $e->getMessage();
                $_SESSION['tipo_mensaje'] = 'error';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }
}

$casos = coordinaciones_lista($conexion);

// Incluir el encabezado
include_once('../templates/header.php');
include_once('../templates/sidebar.php');
include_once('../templates/main-container-begin.php');

// Imprimir variables globales JS para mensajes (solo si hay mensaje)
if ($mensaje) {
    echo '<script>';
    echo 'window.ticketMessage = ' . json_encode($mensaje) . ';';
    echo 'window.ticketMessageType = ' . json_encode($tipo_mensaje) . ';';
    echo 'window.ticketCode = ' . json_encode($codigo_generado) . ';';
    echo '</script>';
}
?>

<div class="ticket-container">

<div id="primera_linea">   
 <div><h1>CREACIÓN DE TICKETS     -  <?php echo htmlspecialchars($_SESSION['sesion']['nombre'] ?? 'Usuario'); ?> </h1></div> 
 <div id="saludo_content"><h2></h2></div>     
</div>

    <div id="contenedor-formulario" data-cargo="<?php echo htmlspecialchars(strtoupper($_SESSION['sesion']['cargo'] ?? '')); ?>">
        <form id="ticketForm" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_caso">TIPO DE CASO <span class="required">*</span></label>
                    <div class="selectores">
                        <select id="tipo_caso" name="tipo_caso">
                            <option value="" selected disabled>Selecciona</option>
                            <?php
                                foreach ($casos as $caso) {
                                    echo "<option value=\"$caso\">$caso</option>";
                                }
                                if (empty($casos)) {
                                    echo "<option value=\"\" disabled>No hay opciones disponibles</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <span id="tipo_caso_error" class="error-message">Por favor seleccione un tipo de caso</span>
                </div>
                
                <div class="form-group">
                    <label for="ubicacion">UBICACIÓN <span class="required">*</span></label>
                    <div class="selectores">
                        <select id="ubicacion" name="ubicacion">
                            <option value="" selected disabled>Selecciona</option>
                            <option value="Administracion Educativa">Administración Educativa</option>
                            <option value="Almacen">Almacén</option>
                            <option value="Ambientes">Ambientes</option>
                            <option value="Archivo Gestion Documental">Archivo Gestión Documental</option>
                            <option value="Aula electricidad">Aula electricidad</option>
                            <option value="Auditorio">Auditorio</option>
                            <option value="Audiovisuales">Audiovisuales</option>
                            <option value="Biblioteca">Biblioteca</option>
                            <option value="Bienestar para el aprendiz">Bienestar para el aprendiz</option>
                            <option value="Bizerta">Bizerta</option>
                            <option value="Campesena">Campesena</option>
                            <option value="Certificacion Academica">Certificación Académica</option>
                            <option value="Complejo Agroindustrial">Complejo Agroindustrial</option>
                            <option value="Comunicaciones">Comunicaciones</option>
                            <option value="Contratacion de aprendicez">Contratación de aprendices</option>
                            <option value="Coordinacion de Comercio">Coordinación de Comercio</option>
                            <option value="Cuarto Vigilancia">Cuarto Vigilancia</option>
                            <option value="Diseño curricular">Diseño curricular</option>
                            <option value="Edificio nuevo">Edificio nuevo</option>
                            <option value="Edificio nuevo (coordinacion administrativa)">Edificio nuevo (coordinación administrativa)</option>
                            <option value="Edificio nuevo (coordinacion de virtualidad)">Edificio nuevo (coordinación de virtualidad)</option>
                            <option value="Edificio nuevo (coordinacion formacion profesional)">Edificio nuevo (coordinación formación profesional)</option>
                            <option value="Edificio nuevo (oficina contadora)">Edificio nuevo (oficina contadora)</option>
                            <option value="Edificio nuevo (oficina de calidad)">Edificio nuevo (oficina de calidad)</option>
                            <option value="Edificio nuevo (oficina de compras)">Edificio nuevo (oficina de compras)</option>
                            <option value="Edificio nuevo (oficina de contratacion)">Edificio nuevo (oficina de contratación)</option>
                            <option value="Edificio nuevo (oficina de yamilet bocanegra)">Edificio nuevo (oficina de Yamilet Bocanegra)</option>
                            <option value="Edificio nuevo (oficina diego chavarriaga)">Edificio nuevo (oficina Diego Chavarriaga)</option>
                            <option value="Edificio nuevo (oficina juridica)">Edificio nuevo (oficina jurídica)</option>
                            <option value="Edificio nuevo (oficina ludwig mauricio)">Edificio nuevo (oficina Ludwig Mauricio)</option>
                            <option value="Edificio nuevo (oficina olga ladino)">Edificio nuevo (oficina Olga Ladino)</option>
                            <option value="Edificio nuevo (subdireccion)">Edificio nuevo (subdirección)</option>
                            <option value="Emprendimiento">Emprendimiento</option>
                            <option value="Enfermeria General">Enfermería General</option>
                            <option value="Enfermeria para el aprendiz">Enfermería para el aprendiz</option>
                            <option value="Ganaderia">Ganadería</option>
                            <option value="Hangar">Hangar</option>
                            <option value="LAMFA">LAMFA</option>
                            <option value="Oficina Lider">Oficina Líder</option>
                            <option value="Porteria">Portería</option>
                            <option value="Sala de Instructores">Sala de Instructores</option>
                            <option value="Sennova">Sennova</option>
                            <option value="Talento Humano">Talento Humano</option>
                            <option value="Taller de Oficiales">Taller de Oficiales</option>
                            <option value="Taller metalmecanica">Taller metalmecánica</option>
                            <option value="Vivero">Vivero</option>
                        </select>
                    </div>
                    <span id="ubicacion_error" class="error-message">Por favor seleccione una ubicación</span>
                </div>
            </div>
            
            <div class="form-group observaciones-container" id="observaciones-con">
                <label for="observaciones">OBSERVACIONES</label>
                <textarea id="observaciones" name="observaciones" rows="4" placeholder="Describe el motivo del ticket (opcional)"></textarea>
                <span id="observaciones_error" class="error-message" style="display:none;">Especifica el Ambiente.</span>
            </div>

            <div class="form-group" >
                <label for="adjuntos">ARCHIVOS ADJUNTOS</label>
                <input type="file" id="adjuntos" name="adjuntos[]" multiple accept="image/*">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-publicar">PUBLICAR</button>
            </div>
        </form>
    </div>
</div>

<script src="../templates/js/registro_ticket.js"></script>

<?php
include_once('../templates/main-container-end.php');
include_once('../templates/footer.php');
?>