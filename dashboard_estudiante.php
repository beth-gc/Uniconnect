<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Contar clubes inscritos
$stmt_clubes = mysqli_prepare($conexionbd, "SELECT COUNT(*) as total FROM miembros_club WHERE id_usuario = ?");
mysqli_stmt_bind_param($stmt_clubes, "i", $id_usuario);
mysqli_stmt_execute($stmt_clubes);
$res_clubes = mysqli_stmt_get_result($stmt_clubes);
$total_clubes = mysqli_fetch_assoc($res_clubes)['total'];

// Contar eventos próximos (de clubes en los que está inscrito)
$stmt_eventos = mysqli_prepare($conexionbd, 
    "SELECT COUNT(*) as total FROM actividades a 
     INNER JOIN miembros_club mc ON a.id_club = mc.id_club 
     WHERE mc.id_usuario = ? AND a.fecha_evento >= NOW()");
mysqli_stmt_bind_param($stmt_eventos, "i", $id_usuario);
mysqli_stmt_execute($stmt_eventos);
$res_eventos = mysqli_stmt_get_result($stmt_eventos);
$total_eventos = mysqli_fetch_assoc($res_eventos)['total'];

// Obtener próximas actividades de sus clubes
$stmt_prox = mysqli_prepare($conexionbd, 
    "SELECT a.titulo, a.tipo_actividad, a.fecha_evento, c.nombre_club 
     FROM actividades a 
     INNER JOIN clubes c ON a.id_club = c.id_club 
     INNER JOIN miembros_club mc ON a.id_club = mc.id_club 
     WHERE mc.id_usuario = ? AND a.fecha_evento >= NOW() 
     ORDER BY a.fecha_evento ASC LIMIT 5");
mysqli_stmt_bind_param($stmt_prox, "i", $id_usuario);
mysqli_stmt_execute($stmt_prox);
$res_prox = mysqli_stmt_get_result($stmt_prox);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tu dashboard en UniConnect. Revisa tus clubes, eventos y actividades.">
    <title>Dashboard - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_dashboard_estudiante.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="dashboard_contenedor">

        <section class="dashboard_bienvenida animacion-entrada">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>! 👋</h2>
            <p class="dashboard_subtexto">Aquí tienes un resumen de tu actividad</p>
            <div class="dashboard_botones">
                <a class="boton_principal" href="lista_clubes.php" id="btn-ver-clubes">Explorar clubes</a>
                <a class="boton_secundario" href="perfil.php" id="btn-ver-perfil">Mi perfil</a>
            </div>
        </section>

        <?php mostrar_alerta(); ?>

        <section class="dashboard_resumen">
            <div class="resumen_card animacion-entrada">
                <div class="resumen_icono">📚</div>
                <h3>Clubes inscritos</h3>
                <p class="resumen_numero"><?php echo $total_clubes; ?></p>
            </div>

            <div class="resumen_card animacion-entrada">
                <div class="resumen_icono">📅</div>
                <h3>Eventos próximos</h3>
                <p class="resumen_numero"><?php echo $total_eventos; ?></p>
            </div>

            <div class="resumen_card animacion-entrada">
                <div class="resumen_icono">⭐</div>
                <h3>Tu rol</h3>
                <p class="resumen_numero resumen_texto_sm">Estudiante</p>
            </div>
        </section>

        <?php if (mysqli_num_rows($res_prox) > 0): ?>
        <section class="dashboard_proximos animacion-entrada">
            <h3 class="seccion_titulo">Próximos eventos de tus clubes</h3>
            <div class="proximos_lista">
                <?php while($evento = mysqli_fetch_assoc($res_prox)): ?>
                <div class="proximo_card">
                    <div class="proximo_fecha">
                        <span class="proximo_dia"><?php echo date('d', strtotime($evento['fecha_evento'])); ?></span>
                        <span class="proximo_mes"><?php echo date('M', strtotime($evento['fecha_evento'])); ?></span>
                    </div>
                    <div class="proximo_info">
                        <h4><?php echo htmlspecialchars($evento['titulo']); ?></h4>
                        <p><?php echo htmlspecialchars($evento['nombre_club']); ?> · <?php echo htmlspecialchars($evento['tipo_actividad']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
