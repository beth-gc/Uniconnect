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

    // 1. Recolección y saneamiento básico
    $titulo = trim($_POST['titulo']);
    $tipo = $_POST['tipo_actividad'];
    $desc = trim($_POST['descripcion_actividad']);
    $fecha = $_POST['fecha_evento'];
    $id_club = filter_var($_POST['id_club'], FILTER_VALIDATE_INT);

    // 2. Validación lógica
    $fecha_actual = date('Y-m-d\TH:i');

    // Validación de campos vacíos
    if (empty($titulo) || empty($tipo) || empty($fecha) || !$id_club) {
        header('Location: crear_actividad.php?status=error&msg=datos_invalidos');
        exit;
    }

    // Validaciones de longitud
    if (strlen($titulo) > 100) {
        header('Location: crear_actividad.php?status=error&msg=titulo_largo');
        exit;
    }
    if (strlen($desc) > 500) {
        header('Location: crear_actividad.php?status=error&msg=desc_larga');
        exit;
    }

    // Validación de fecha futura
    if ($fecha < $fecha_actual) {
        header('Location: crear_actividad.php?status=error&msg=fecha_pasada');
        exit;
    }

    // Verificar que el club existe
    $checkClub = mysqli_prepare($conexionbd, "SELECT id_club FROM clubes WHERE id_club = ?");
    mysqli_stmt_bind_param($checkClub, "i", $id_club);
    mysqli_stmt_execute($checkClub);
    $resClub = mysqli_stmt_get_result($checkClub);
    if (mysqli_num_rows($resClub) == 0) {
        header('Location: crear_actividad.php?status=error&msg=club_no_existe');
        exit;
    }
    mysqli_stmt_close($checkClub);

    // Verificar que el usuario es miembro del club
    $checkMiembro = mysqli_prepare($conexionbd, "SELECT id_miembro FROM miembros_club WHERE id_usuario = ? AND id_club = ?");
    mysqli_stmt_bind_param($checkMiembro, "ii", $id_usuario, $id_club);
    mysqli_stmt_execute($checkMiembro);
    $resMiembro = mysqli_stmt_get_result($checkMiembro);
    if (mysqli_num_rows($resMiembro) == 0) {
        header('Location: crear_actividad.php?status=error&msg=no_es_miembro');
        exit;
    }
    mysqli_stmt_close($checkMiembro);

    // 3. Sentencia Preparada usando Stored Procedure
    $stmt = mysqli_prepare($conexionbd, "CALL sp_crear_actividad(?, ?, ?, ?, ?, ?)");
    
    mysqli_stmt_bind_param($stmt, "ssssii", $titulo, $tipo, $desc, $fecha, $id_club, $id_usuario);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: lista_actividades.php?status=success&msg=actividad_creada');
    } else {
        header('Location: crear_actividad.php?status=error&msg=db_error');
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexionbd);
} else {
    header('Location: crear_actividad.php');
}
?>