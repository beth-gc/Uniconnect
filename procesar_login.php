<?php
session_start();
include("baseDatos.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexionbd = conectar_bd();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validación básica
    if (empty($email) || empty($password)) {
        header('Location: login.php?status=error&msg=campos_vacios');
        exit;
    }

    // Buscar usuario por email
    $stmt = mysqli_prepare($conexionbd, "SELECT id_usuario, nombre, email, password_hash FROM usuarios WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($usuario = mysqli_fetch_assoc($resultado)) {
        // Verificar contraseña
        if (password_verify($password, $usuario['password_hash'])) {
            // Iniciar sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];

            header('Location: dashboard_estudiante.php');
            exit;
        } else {
            header('Location: login.php?status=error&msg=credenciales');
            exit;
        }
    } else {
        header('Location: login.php?status=error&msg=credenciales');
        exit;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexionbd);
} else {
    header('Location: login.php');
    exit;
}
?>
