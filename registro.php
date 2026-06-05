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
    <meta name="description" content="Crea tu cuenta en UniConnect y únete a los clubes estudiantiles de tu universidad.">
    <title>Registro - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_formularios.css">
</head>
<body>
    <?php include("includes/header_publico.php"); ?>

    <main class="formulario_contenedor">
        <form class="formulario animacion-entrada" action="procesar_registro.php" method="POST" id="form-registro">
            <h2 class="formulario_titulo">Crear Cuenta</h2>

            <?php mostrar_alerta(); ?>

            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ingresa tu nombre" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required minlength="6">

            <button type="submit" class="boton_principal" id="btn-registro">Registrarse</button>

            <p class="formulario_texto">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
            </p>
        </form>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
