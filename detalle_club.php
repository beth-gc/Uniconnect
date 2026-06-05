<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

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

// Verificar si el usuario es miembro
$stmt_m = mysqli_prepare($conexionbd, "SELECT rol FROM miembros_club WHERE id_usuario = ? AND id_club = ?");
mysqli_stmt_bind_param($stmt_m, "ii", $id_usuario, $id_club);
mysqli_stmt_execute($stmt_m);
$res_m = mysqli_stmt_get_result($stmt_m);
$membresia = mysqli_fetch_assoc($res_m);
$es_miembro = $membresia !== null;

// Contar miembros
$stmt_count = mysqli_prepare($conexionbd, "SELECT COUNT(*) as total FROM miembros_club WHERE id_club = ?");
mysqli_stmt_bind_param($stmt_count, "i", $id_club);
mysqli_stmt_execute($stmt_count);
$res_count = mysqli_stmt_get_result($stmt_count);
$total_miembros = mysqli_fetch_assoc($res_count)['total'];

// Obtener próximas actividades del club
$stmt_act = mysqli_prepare($conexionbd, 
    "SELECT titulo, tipo_actividad, fecha_evento, descripcion_actividad 
     FROM actividades WHERE id_club = ? AND fecha_evento >= NOW() 
     ORDER BY fecha_evento ASC LIMIT 5");
mysqli_stmt_bind_param($stmt_act, "i", $id_club);
mysqli_stmt_execute($stmt_act);
$res_act = mysqli_stmt_get_result($stmt_act);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detalle del <?php echo htmlspecialchars($club['nombre_club']); ?> en UniConnect.">
    <title><?php echo htmlspecialchars($club['nombre_club']); ?> - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_detalle_club.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="detalle_contenedor">
        <div class="detalle_card animacion-entrada">
            <div class="detalle_header">
                <span class="club_categoria"><?php echo htmlspecialchars($club['categoria'] ?? 'General'); ?></span>
                <a href="lista_clubes.php" class="detalle_volver">← Volver a clubes</a>
            </div>

            <h2 class="detalle_titulo"><?php echo htmlspecialchars($club['nombre_club']); ?></h2>

            <?php mostrar_alerta(); ?>

            <p class="detalle_descripcion">
                <?php echo htmlspecialchars($club['descripcion'] ?? 'Sin descripción disponible.'); ?>
            </p>

            <div class="detalle_stats">
                <div class="detalle_stat">
                    <span class="stat_numero"><?php echo $total_miembros; ?></span>
                    <span class="stat_label">Miembros</span>
                </div>
                <div class="detalle_stat">
                    <span class="stat_numero"><?php echo mysqli_num_rows($res_act); ?></span>
                    <span class="stat_label">Eventos próximos</span>
                </div>
            </div>

            <?php 
            // Re-ejecutar la consulta ya que ya se consumió con num_rows
            mysqli_stmt_execute($stmt_act);
            $res_act = mysqli_stmt_get_result($stmt_act);
            if (mysqli_num_rows($res_act) > 0): 
            ?>
            <div class="detalle_actividades">
                <h3>Próximas actividades</h3>
                <ul>
                    <?php while($act = mysqli_fetch_assoc($res_act)): ?>
                    <li>
                        <span class="actividad_fecha">📅 <?php echo date('d M Y', strtotime($act['fecha_evento'])); ?></span>
                        <span class="actividad_nombre"><?php echo htmlspecialchars($act['titulo']); ?></span>
                        <span class="badge_tipo"><?php echo htmlspecialchars($act['tipo_actividad']); ?></span>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="detalle_acciones">
                <?php if ($es_miembro): ?>
                    <a href="procesar_membresia.php?accion=salir&id_club=<?php echo $id_club; ?>" class="boton_secundario" id="btn-salir-club">Salir del club</a>
                    <a href="miembros_club.php?id=<?php echo $id_club; ?>" class="boton_principal" id="btn-ver-miembros">Ver miembros</a>
                <?php else: ?>
                    <a href="procesar_membresia.php?accion=unirse&id_club=<?php echo $id_club; ?>" class="boton_principal" id="btn-unirse-club">Unirse al club</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
