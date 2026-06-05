<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Obtener ID de la actividad
$id_actividad = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_actividad) {
    header('Location: lista_actividades.php?status=error&msg=datos_invalidos');
    exit;
}

// Obtener datos actuales de la actividad
$stmt = mysqli_prepare($conexionbd, 
    "SELECT a.*, c.nombre_club 
     FROM actividades a 
     INNER JOIN clubes c ON a.id_club = c.id_club 
     WHERE a.id_actividad = ?");
mysqli_stmt_bind_param($stmt, "i", $id_actividad);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$actividad = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

// Verificar que la actividad existe
if (!$actividad) {
    header('Location: lista_actividades.php?status=error&msg=actividad_no_existe');
    exit;
}

// Solo el creador puede editar
if ($actividad['id_usuario'] != $id_usuario) {
    header('Location: lista_actividades.php?status=error&msg=sin_permiso');
    exit;
}

// Obtener clubes del usuario para el selector
$query_clubes = "SELECT c.id_club, c.nombre_club 
                 FROM clubes c 
                 INNER JOIN miembros_club mc ON c.id_club = mc.id_club 
                 WHERE mc.id_usuario = ? 
                 ORDER BY c.nombre_club ASC";
$stmt_clubes = mysqli_prepare($conexionbd, $query_clubes);
mysqli_stmt_bind_param($stmt_clubes, "i", $id_usuario);
mysqli_stmt_execute($stmt_clubes);
$res_clubes = mysqli_stmt_get_result($stmt_clubes);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Edita los datos de tu actividad en UniConnect.">
    <title>Editar Actividad - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_formularios.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="formulario_contenedor">
        <form class="formulario animacion-entrada" action="procesar_editar_actividad.php" method="POST" id="form-editar-actividad">
            <h2 class="formulario_titulo">Editar Actividad</h2>

            <?php mostrar_alerta(); ?>

            <input type="hidden" name="id_actividad" value="<?php echo $id_actividad; ?>">

            <label for="titulo">Título del Evento</label>
            <input type="text" id="titulo" name="titulo" 
                   value="<?php echo htmlspecialchars($actividad['titulo']); ?>" 
                   required maxlength="100">

            <label for="tipo">Tipo de Actividad</label>
            <select id="tipo" name="tipo_actividad" required>
                <option value="">Selecciona un tipo...</option>
                <?php 
                $tipos = ['Taller', 'Conferencia', 'Torneo', 'Deportivo', 'Social'];
                foreach ($tipos as $tipo): 
                ?>
                <option value="<?php echo $tipo; ?>" <?php echo ($actividad['tipo_actividad'] == $tipo) ? 'selected' : ''; ?>>
                    <?php echo $tipo; ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion_actividad" 
                      rows="3" maxlength="500"><?php echo htmlspecialchars($actividad['descripcion_actividad']); ?></textarea>

            <label for="fecha">Fecha y Hora</label>
            <input type="datetime-local" id="fecha" name="fecha_evento" 
                   value="<?php echo date('Y-m-d\TH:i', strtotime($actividad['fecha_evento'])); ?>" 
                   required>

            <label for="club">Club Organizador</label>
            <select id="club" name="id_club" required>
                <option value="">Selecciona uno de tus clubes...</option>
                <?php while($club = mysqli_fetch_assoc($res_clubes)): ?>
                <option value="<?php echo $club['id_club']; ?>" <?php echo ($actividad['id_club'] == $club['id_club']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($club['nombre_club']); ?>
                </option>
                <?php endwhile; ?>
            </select>
            <p class="formulario_texto" style="font-size: 0.8rem; opacity: 0.7;">Solo aparecen los clubes a los que perteneces.</p>

            <button type="submit" class="boton_principal" id="btn-guardar-edicion">Guardar Cambios</button>

            <p class="formulario_texto">
                ¿Deseas cancelar? <a href="lista_actividades.php">Volver a la lista</a>
            </p>
        </form>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
