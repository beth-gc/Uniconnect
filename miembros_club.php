<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("baseDatos.php");

$conexionbd = conectar_bd();

// Obtener ID del club
$id_club = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_club) {
    header('Location: lista_clubes.php');
    exit;
}

// Obtener datos del club
$stmt = mysqli_prepare($conexionbd, "SELECT * FROM clubes WHERE id_club = ?");
mysqli_stmt_bind_param($stmt, "i", $id_club);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$club = mysqli_fetch_assoc($res);

if (!$club) {
    header('Location: lista_clubes.php');
    exit;
}

// Obtener miembros
$stmt_m = mysqli_prepare($conexionbd, 
    "SELECT u.nombre, u.email, mc.fecha_union, mc.rol 
     FROM miembros_club mc 
     INNER JOIN usuarios u ON mc.id_usuario = u.id_usuario 
     WHERE mc.id_club = ? 
     ORDER BY mc.fecha_union ASC");
mysqli_stmt_bind_param($stmt_m, "i", $id_club);
mysqli_stmt_execute($stmt_m);
$res_m = mysqli_stmt_get_result($stmt_m);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Miembros del <?php echo htmlspecialchars($club['nombre_club']); ?> en UniConnect.">
    <title>Miembros - <?php echo htmlspecialchars($club['nombre_club']); ?> - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_lista.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="lista_contenedor">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <h2 class="presentacion_contenido_titulo" style="font-size: 1.5rem;">
                Miembros de <span class="titulo-resaltar"><?php echo htmlspecialchars($club['nombre_club']); ?></span>
            </h2>
            <a href="detalle_club.php?id=<?php echo $id_club; ?>" class="boton_secundario" id="btn-volver-club">← Volver al club</a>
        </div>

        <table class="tabla_actividades">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Miembro desde</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($res_m) > 0): ?>
                    <?php while($miembro = mysqli_fetch_assoc($res_m)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($miembro['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($miembro['email']); ?></td>
                        <td><span class="badge_tipo"><?php echo htmlspecialchars($miembro['rol']); ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($miembro['fecha_union'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px;">Este club aún no tiene miembros. ¡Sé el primero!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
