<?php
session_start();
include("baseDatos.php");

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

$id_actividad = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_actividad) {
    header('Location: lista_actividades.php?status=error&msg=datos_invalidos');
    exit;
}

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Verificar que la actividad existe y obtener quién la creó
$stmt = mysqli_prepare($conexionbd, "SELECT id_usuario FROM actividades WHERE id_actividad = ?");
mysqli_stmt_bind_param($stmt, "i", $id_actividad);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$actividad = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$actividad) {
    header('Location: lista_actividades.php?status=error&msg=actividad_no_existe');
    exit;
}

// Solo el creador puede eliminar la actividad
if ($actividad['id_usuario'] != $id_usuario) {
    header('Location: lista_actividades.php?status=error&msg=sin_permiso');
    exit;
}

// Eliminar la actividad usando Stored Procedure
$stmt_del = mysqli_prepare($conexionbd, "CALL sp_eliminar_actividad(?, ?)");
mysqli_stmt_bind_param($stmt_del, "ii", $id_actividad, $id_usuario);

if (mysqli_stmt_execute($stmt_del)) {
    header('Location: lista_actividades.php?status=success&msg=actividad_eliminada');
} else {
    header('Location: lista_actividades.php?status=error&msg=db_error');
}

mysqli_stmt_close($stmt_del);
mysqli_close($conexionbd);
?>
