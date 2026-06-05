<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Solo mostrar clubes donde el usuario es miembro
$query_clubes = "SELECT c.id_club, c.nombre_club 
                 FROM clubes c 
                 INNER JOIN miembros_club mc ON c.id_club = mc.id_club 
                 WHERE mc.id_usuario = ? 
                 ORDER BY c.nombre_club ASC";
$stmt_clubes = mysqli_prepare($conexionbd, $query_clubes);
mysqli_stmt_bind_param($stmt_clubes, "i", $id_usuario);
mysqli_stmt_execute($stmt_clubes);
$res_clubes = mysqli_stmt_get_result($stmt_clubes);
$tiene_clubes = mysqli_num_rows($res_clubes) > 0;
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Crea una nueva actividad o evento para tu club en UniConnect.">
    <title>Nueva Actividad - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_formularios.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="formulario_contenedor">
        <?php if (!$tiene_clubes): ?>
        <div class="formulario animacion-entrada" style="text-align: center;">
            <h2 class="formulario_titulo">No puedes crear actividades</h2>
            <p style="font-family: var(--fuente-montserrat); color: var(--color-primario); line-height: 1.6;">
                Debes ser miembro de al menos un club para poder crear actividades. 
                Únete a un club primero.
            </p>
            <a href="lista_clubes.php" class="boton_principal" id="btn-explorar-clubes">Explorar clubes</a>
        </div>
        <?php else: ?>
        <form class="formulario animacion-entrada" action="procesar_actividad.php" method="POST" id="form-actividad">
            <h2 class="formulario_titulo">Crear Actividad</h2>

            <?php mostrar_alerta(); ?>

            <label for="titulo">Título del Evento</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ej: Torneo de Ajedrez" required maxlength="100">

            <label for="tipo">Tipo de Actividad</label>
            <select id="tipo" name="tipo_actividad" required>
                <option value="">Selecciona un tipo...</option>
                <option value="Taller">Taller</option>
                <option value="Conferencia">Conferencia</option>
                <option value="Torneo">Torneo</option>
                <option value="Deportivo">Deportivo</option>
                <option value="Social">Social</option>
            </select>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion_actividad" placeholder="Detalles de la actividad..." rows="3" maxlength="500"></textarea>

            <label for="fecha">Fecha y Hora</label>
            <input type="datetime-local" id="fecha" name="fecha_evento" required>

            <label for="club">Club Organizador</label>
            <select id="club" name="id_club" required>
                <option value="">Selecciona uno de tus clubes...</option>
                <?php while($club = mysqli_fetch_assoc($res_clubes)): ?>
                <option value="<?php echo $club['id_club']; ?>">
                    <?php echo htmlspecialchars($club['nombre_club']); ?>
                </option>
                <?php endwhile; ?>
            </select>
            <p class="formulario_texto" style="font-size: 0.8rem; opacity: 0.7;">Solo aparecen los clubes a los que perteneces.</p>

            <button type="submit" class="boton_principal" id="btn-publicar">Publicar Actividad</button>

            <p class="formulario_texto">
                ¿Deseas volver? <a href="dashboard_estudiante.php">Cancelar</a>
            </p>
        </form>
        <?php endif; ?>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>