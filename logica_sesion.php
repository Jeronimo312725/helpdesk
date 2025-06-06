<?php
// logica_sesion.php

// Función para enviar JSON y terminar el script
function enviarJson($array) {
    header('Content-Type: application/json');
    echo json_encode($array);
    exit;
}

// Iniciar sesión
session_start();

// Conexión a la base de datos
require_once 'conexion.php';

// Validar que se envió el formulario
if (!isset($_POST['correo'])) {
    enviarJson(['error' => 'Por favor, ingrese su correo electrónico.']);
}

$correo = trim($_POST['correo']);
$clave = isset($_POST['clave']) ? trim($_POST['clave']) : '';

// Buscar en usuarios
$sql_usuario = "SELECT id_usuario, documento, nombre, correo, telefono, clave, cargo FROM usuarios WHERE correo = ?";
$stmt_usuario = $conexion->prepare($sql_usuario);
$stmt_usuario->bind_param("s", $correo);
$stmt_usuario->execute();
$result_usuario = $stmt_usuario->get_result();
$usuario = $result_usuario->fetch_assoc();

// Buscar en funcionarios si no se encuentra en usuarios
$funcionario = null;
if (!$usuario) {
    $sql_funcionario = "SELECT id_funcionario, numero_documento AS documento, nombre, correo, telefono, cargo FROM funcionarios WHERE correo = ?";
    $stmt_funcionario = $conexion->prepare($sql_funcionario);
    $stmt_funcionario->bind_param("s", $correo);
    $stmt_funcionario->execute();
    $result_funcionario = $stmt_funcionario->get_result();
    $funcionario = $result_funcionario->fetch_assoc();
}

$sesion = null;
$cargo = null;

if ($usuario) {
    $sesion = [
        'tipo' => 'usuario',
        'id' => $usuario['id_usuario'],
        'documento' => $usuario['documento'],
        'nombre' => $usuario['nombre'],
        'correo' => $usuario['correo'],
        'telefono' => $usuario['telefono'],
        'cargo' => $usuario['cargo']
    ];
    $cargo = strtoupper(trim($usuario['cargo']));
} elseif ($funcionario) {
    $sesion = [
        'tipo' => 'funcionario',
        'id' => $funcionario['id_funcionario'],
        'documento' => $funcionario['documento'],
        'nombre' => $funcionario['nombre'],
        'correo' => $funcionario['correo'],
        'telefono' => $funcionario['telefono'],
        'cargo' => $funcionario['cargo']
    ];
    $cargo = strtoupper(trim($funcionario['cargo']));
} else {
    enviarJson(['error' => 'Correo no encontrado.']);
}

// Si aún no se ha enviado la clave, pedirla para los cargos apropiados
if (empty($clave)) {
    if ($cargo === 'ANALISTA' || $cargo === 'DINAMIZADOR') {
        enviarJson(['cargos' => true]);
    } else if ($cargo === 'FUNCIONARIO' || $cargo === 'VIGILANTE') {
        // Para funcionarios/vigilantes, no pedir clave, iniciar sesión directa
        $_SESSION['sesion'] = $sesion;
        enviarJson(['redirect' => 'funcionarios/registro_ticket.php']);
    } else if (empty($cargo)) {
        enviarJson(['error' => 'El usuario no tiene un cargo asignado en la base de datos. Contacte al administrador.']);
    } else {
        enviarJson(['error' => 'Tipo de cargo no reconocido. Por favor, revise el correo o contacte al administrador.']);
    }
}

// Si llegó aquí, se requiere clave (ANALISTA o DINAMIZADOR)
if ($cargo === 'ANALISTA' || $cargo === 'DINAMIZADOR') {
    $clave_bdd = $usuario ? $usuario['clave'] : null;

    if (!$clave_bdd) {
        enviarJson(['error' => 'No se encontró clave registrada para este usuario.']);
    }
    // Verifica la clave (puedes usar password_verify si está hasheada)
    if ($clave === $clave_bdd) {
        $_SESSION['sesion'] = $sesion;
        if ($cargo === 'ANALISTA') {
            enviarJson(['redirect' => 'dinamizador/dinamizador.php']);
        } else if ($cargo === 'DINAMIZADOR') {
            enviarJson(['redirect' => 'dinamizador/dinamizador.php']);
        }
    } else {
        enviarJson(['error' => 'Clave incorrecta.']);
    }
} else if ($cargo === 'FUNCIONARIO' || $cargo === 'VIGILANTE') {
    // Se permite acceso directo a funcionarios y vigilantes (sin clave)
    $_SESSION['sesion'] = $sesion;
    enviarJson(['redirect' => 'funcionarios/registro_ticket.php']);
} else if (empty($cargo)) {
    enviarJson(['error' => 'El usuario no tiene un cargo asignado en la base de datos. Contacte al administrador.']);
} else {
    enviarJson(['error' => 'Tipo de cargo no reconocido. Por favor, revise el correo o contacte al administrador.']);
}