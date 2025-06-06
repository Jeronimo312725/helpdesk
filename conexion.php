<?php
$servername = "localhost"; // Hostname
$database = "help_desk"; // Base de datos
$username = "root"; // Nombre de usuario
$password = ""; 


$conexion = mysqli_connect($servername, $username, $password, $database);


if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>
