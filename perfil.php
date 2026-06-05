<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Obtener datos del usuario
$stmt = mysqli_prepare($conexionbd, "SELECT nombre, email, fecha_registro FROM usuarios WHERE id_usuario = ?");
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($res);

// Obtener clubes del usuario
$stmt_clubes = mysqli_prepare($conexionbd, 
    "SELECT c.nombre_club, mc.fecha_union, mc.rol 
     FROM miembros_club mc 
     INNER JOIN clubes c ON mc.id_club = c.id_club 
     WHERE mc.id_usuario = ? 
     ORDER BY mc.fecha_union DESC");
mysqli_stmt_bind_param($stmt_clubes, "i", $id_usuario);
mysqli_stmt_execute($stmt_clubes);
$res_clubes = mysqli_stmt_get_result($stmt_clubes);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tu perfil en UniConnect. Revisa tu información y clubes.">
    <title>Mi Perfil - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_perfil.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="perfil_contenedor">
        <div class="perfil_card animacion-entrada">
            <div class="perfil_avatar">
                <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
            </div>
            <h2 class="perfil_titulo">Mi Perfil</h2>

            <?php mostrar_alerta(); ?>

            <div class="perfil_info">
                <div class="perfil_campo">
                    <span class="perfil_label">👤 Nombre</span>
                    <span class="perfil_valor"><?php echo htmlspecialchars($usuario['nombre']); ?></span>
                </div>
                <div class="perfil_campo">
                    <span class="perfil_label">📧 Correo</span>
                    <span class="perfil_valor"><?php echo htmlspecialchars($usuario['email']); ?></span>
                </div>
                <div class="perfil_campo">
                    <span class="perfil_label">📅 Miembro desde</span>
                    <span class="perfil_valor"><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></span>
                </div>
            </div>

            <?php if (mysqli_num_rows($res_clubes) > 0): ?>
            <div class="perfil_clubes">
                <h3>Mis Clubes</h3>
                <div class="perfil_clubes_lista">
                    <?php while($club = mysqli_fetch_assoc($res_clubes)): ?>
                    <div class="perfil_club_tag">
                        <?php echo htmlspecialchars($club['nombre_club']); ?>
                        <span class="perfil_club_rol"><?php echo htmlspecialchars($club['rol']); ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="perfil_botones">
                <a href="cerrar_sesion.php" class="boton_secundario" id="btn-cerrar-sesion">Cerrar sesión</a>
            </div>
        </div>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
