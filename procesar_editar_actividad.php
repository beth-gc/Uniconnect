<?php
session_start();
include("baseDatos.php");

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexionbd = conectar_bd();
    $id_usuario = $_SESSION['id_usuario'];

    // 1. Recolección y saneamiento
    $id_actividad = filter_var($_POST['id_actividad'], FILTER_VALIDATE_INT);
    $titulo = trim($_POST['titulo']);
    $tipo = $_POST['tipo_actividad'];
    $desc = trim($_POST['descripcion_actividad']);
    $fecha = $_POST['fecha_evento'];
    $id_club = filter_var($_POST['id_club'], FILTER_VALIDATE_INT);

    // 2. Validaciones
    if (!$id_actividad || empty($titulo) || empty($tipo) || empty($fecha) || !$id_club) {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=datos_invalidos");
        exit;
    }

    if (strlen($titulo) > 100) {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=titulo_largo");
        exit;
    }
    if (strlen($desc) > 500) {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=desc_larga");
        exit;
    }

    // Verificar que la actividad existe y pertenece al usuario
    $stmt_check = mysqli_prepare($conexionbd, "SELECT id_usuario FROM actividades WHERE id_actividad = ?");
    mysqli_stmt_bind_param($stmt_check, "i", $id_actividad);
    mysqli_stmt_execute($stmt_check);
    $res_check = mysqli_stmt_get_result($stmt_check);
    $actividad = mysqli_fetch_assoc($res_check);
    mysqli_stmt_close($stmt_check);

    if (!$actividad) {
        header('Location: lista_actividades.php?status=error&msg=actividad_no_existe');
        exit;
    }

    if ($actividad['id_usuario'] != $id_usuario) {
        header('Location: lista_actividades.php?status=error&msg=sin_permiso');
        exit;
    }

    // Verificar que el club existe
    $checkClub = mysqli_prepare($conexionbd, "SELECT id_club FROM clubes WHERE id_club = ?");
    mysqli_stmt_bind_param($checkClub, "i", $id_club);
    mysqli_stmt_execute($checkClub);
    $resClub = mysqli_stmt_get_result($checkClub);
    if (mysqli_num_rows($resClub) == 0) {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=club_no_existe");
        exit;
    }
    mysqli_stmt_close($checkClub);

    // Verificar que el usuario es miembro del club
    $checkMiembro = mysqli_prepare($conexionbd, "SELECT id_miembro FROM miembros_club WHERE id_usuario = ? AND id_club = ?");
    mysqli_stmt_bind_param($checkMiembro, "ii", $id_usuario, $id_club);
    mysqli_stmt_execute($checkMiembro);
    $resMiembro = mysqli_stmt_get_result($checkMiembro);
    if (mysqli_num_rows($resMiembro) == 0) {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=no_es_miembro");
        exit;
    }
    mysqli_stmt_close($checkMiembro);

    // 3. Ejecutar UPDATE usando Stored Procedure
    $stmt = mysqli_prepare($conexionbd, "CALL sp_actualizar_actividad(?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssii", $id_actividad, $titulo, $tipo, $desc, $fecha, $id_club, $id_usuario);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: lista_actividades.php?status=success&msg=actividad_actualizada');
    } else {
        header("Location: editar_actividad.php?id=$id_actividad&status=error&msg=db_error");
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexionbd);
} else {
    header('Location: lista_actividades.php');
}
?>
