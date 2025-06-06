<?php
// Configuración de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Requiere el archivo de conexión
require '../conexion.php';

// Establecer zona horaria
date_default_timezone_set('America/Bogota');

// Iniciar la sesión
session_start();
if (!isset($_SESSION['sesion']) || empty($_SESSION['sesion'])) {
    header('Location: ../index.html'); // Ajusta la ruta según corresponda
    exit;
}
$cargo = strtoupper($_SESSION['sesion']['cargo']);
$documento = $_SESSION['sesion']['documento'];
$nombre = $_SESSION['sesion']['nombre'];
$ticketId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar si es una solicitud AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getAnalistas':
            // Obtener analistas desde la tabla usuarios
            $sql = "SELECT id_usuario, documento, nombre FROM usuarios WHERE cargo = 'ANALISTA' ";
            $result = $conexion->query($sql);
            
            if ($result === false) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error en la consulta: ' . $conexion->error
                ]);
                exit;
            }
            
            $analistas = [];
            while ($row = $result->fetch_assoc()) {
                $analistas[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'analistas' => $analistas
            ]);
            break;
            
        case 'getTicketInfo':
            // Obtener ID del ticket
            $ticketId = $_POST['ticketId'] ?? 0;
            
            // Consultar información del ticket
            $sql = "SELECT id_ticket, codigo_ticket FROM tickets WHERE id_ticket = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param('i', $ticketId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result === false || $result->num_rows === 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Ticket no encontrado'
                ]);
                exit;
            }
            
            $ticket = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'ticket' => $ticket
            ]);
            break;
            
        case 'asignarTicket':
            // Obtener datos del formulario
            $ticketId = $_POST['ticketId'] ?? 0;
            $analistaId = $_POST['analistaId'] ?? '';
            
            // Validar datos
            if (empty($ticketId) || empty($analistaId)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ]);
                exit;
            }
            
            // Iniciar transacción
            $conexion->begin_transaction();
            
            try {
                // Actualizar el ticket con el analista asignado y cambiar estado a "En proceso"
                $sql = "UPDATE tickets SET 
                        doc_usuario_atencion = ?,
                        estado = 'En proceso',
                        fecha_actualizacion = NOW()
                        WHERE id_ticket = ?";
                
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param('si', $analistaId, $ticketId);
                $resultado = $stmt->execute();
                
                if (!$resultado) {
                    throw new Exception('Error al asignar ticket: ' . $stmt->error);
                }
                
                // Confirmar cambios
                $conexion->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Ticket asignado correctamente'
                ]);
            } catch (Exception $e) {
                // Revertir cambios en caso de error
                $conexion->rollback();
                
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
    
    exit;
}

// Si no es una solicitud AJAX, redireccionar a la página principal
header('Location: dinamizador.php');
exit;
?>