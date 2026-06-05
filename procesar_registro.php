<?php
session_start();
include("baseDatos.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexionbd = conectar_bd();

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validación de campos vacíos
    if (empty($nombre) || empty($email) || empty($password)) {
        header('Location: registro.php?status=error&msg=campos_vacios');
        exit;
    }

    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: registro.php?status=error&msg=email_invalido');
        exit;
    }

    // Validar longitud de contraseña
    if (strlen($password) < 6) {
        header('Location: registro.php?status=error&msg=password_corta');
        exit;
    }

    // Validar longitud de nombre
    if (strlen($nombre) > 100) {
        header('Location: registro.php?status=error&msg=nombre_largo');
        exit;
    }

    // Verificar si el email ya existe
    $stmt_check = mysqli_prepare($conexionbd, "SELECT id_usuario FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    $resultado = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($resultado) > 0) {
        header('Location: registro.php?status=error&msg=email_existe');
        mysqli_stmt_close($stmt_check);
        exit;
    }
    mysqli_stmt_close($stmt_check);

    // Hash de contraseña e inserción usando Stored Procedure
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conexionbd, "CALL sp_registrar_usuario(?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $password_hash);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: login.php?status=success&msg=registro_exitoso');
    } else {
        header('Location: registro.php?status=error&msg=db_error');
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexionbd);
} else {
    header('Location: registro.php');
    exit;
}
?>
