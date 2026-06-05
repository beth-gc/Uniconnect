<?php
function conectar_bd()
{
    $servidor = "localhost:3307";
    $nombrebd = "uniconnect_db";
    $usuario = "root";
    $contrasena = "";

    $conexion = mysqli_connect($servidor, $usuario, $contrasena, $nombrebd);
    
    if (!$conexion) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexion, "utf8mb4");
    
    return $conexion;
}
?>