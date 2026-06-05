<?php
include("includes/sesion.php");
include("includes/alertas.php");

// Si ya está logueado, redirigir al dashboard
if ($sesion_activa) {
    header('Location: dashboard_estudiante.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en UniConnect para acceder a clubes y actividades universitarias.">
    <title>Iniciar Sesión - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_formularios.css">
</head>
<body>
    <?php include("includes/header_publico.php"); ?>

    <main class="formulario_contenedor">
        <form class="formulario animacion-entrada" action="procesar_login.php" method="POST" id="form-login">
            <h2 class="formulario_titulo">Iniciar Sesión</h2>

            <?php mostrar_alerta(); ?>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>

            <button type="submit" class="boton_principal" id="btn-login">Entrar</button>

            <p class="formulario_texto">
                ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
            </p>
        </form>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
