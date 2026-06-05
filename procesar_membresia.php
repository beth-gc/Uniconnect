<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Obtener acción e ID del club
$accion = $_GET['accion'] ?? '';
$id_club = filter_input(INPUT_GET, 'id_club', FILTER_VALIDATE_INT);

if (!$id_club || !in_array($accion, ['unirse', 'salir'])) {
    header('Location: lista_clubes.php');
    exit;
}

// Verificar que el club existe
$stmt_check = mysqli_prepare($conexionbd, "SELECT id_club FROM clubes WHERE id_club = ?");
mysqli_stmt_bind_param($stmt_check, "i", $id_club);
mysqli_stmt_execute($stmt_check);
$res_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($res_check) == 0) {
    header('Location: lista_clubes.php?status=error&msg=club_no_existe');
    exit;
}

if ($accion === 'unirse') {
    // Verificar si ya es miembro
    $stmt_m = mysqli_prepare($conexionbd, "SELECT id_miembro FROM miembros_club WHERE id_usuario = ? AND id_club = ?");
    mysqli_stmt_bind_param($stmt_m, "ii", $id_usuario, $id_club);
    mysqli_stmt_execute($stmt_m);
    $res_m = mysqli_stmt_get_result($stmt_m);
    
    if (mysqli_num_rows($res_m) == 0) {
        $stmt_insert = mysqli_prepare($conexionbd, "INSERT INTO miembros_club (id_usuario, id_club) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ii", $id_usuario, $id_club);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }
    
    header("Location: detalle_club.php?id=$id_club&status=success&msg=membresia_unido");
    exit;
    
} elseif ($accion === 'salir') {
    $stmt_delete = mysqli_prepare($conexionbd, "DELETE FROM miembros_club WHERE id_usuario = ? AND id_club = ?");
    mysqli_stmt_bind_param($stmt_delete, "ii", $id_usuario, $id_club);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    
    header("Location: detalle_club.php?id=$id_club&status=success&msg=membresia_salido");
    exit;
}

mysqli_close($conexionbd);
?>
