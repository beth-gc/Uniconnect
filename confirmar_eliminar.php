<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

$id_actividad = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id_actividad) {
    header('Location: lista_actividades.php?status=error&msg=datos_invalidos');
    exit;
}

// Verificar que la actividad existe y que el usuario es el creador
$stmt = mysqli_prepare($conexionbd, 
    "SELECT a.titulo, a.tipo_actividad, a.fecha_evento, c.nombre_club, a.id_usuario 
     FROM actividades a 
     INNER JOIN clubes c ON a.id_club = c.id_club 
     WHERE a.id_actividad = ?");
mysqli_stmt_bind_param($stmt, "i", $id_actividad);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$actividad = mysqli_fetch_assoc($res);

if (!$actividad) {
    header('Location: lista_actividades.php?status=error&msg=actividad_no_existe');
    exit;
}

if ($actividad['id_usuario'] != $id_usuario) {
    header('Location: lista_actividades.php?status=error&msg=sin_permiso');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirmar eliminación de actividad en UniConnect.">
    <title>Confirmar eliminación - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_detalle_club.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="detalle_contenedor">
        <div class="detalle_card animacion-entrada" style="max-width: 550px;">
            <h2 class="detalle_titulo" style="color: #e74c3c;">⚠️ Confirmar eliminación</h2>

            <p class="detalle_descripcion" style="text-align: center;">
                ¿Estás seguro de que deseas eliminar esta actividad? Esta acción no se puede deshacer.
            </p>

            <div style="background: rgba(69, 60, 103, 0.08); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 8px;">
                <p style="font-family: var(--fuente-montserrat); color: var(--color-primario);">
                    <strong>Título:</strong> <?php echo htmlspecialchars($actividad['titulo']); ?>
                </p>
                <p style="font-family: var(--fuente-montserrat); color: var(--color-primario);">
                    <strong>Tipo:</strong> <?php echo htmlspecialchars($actividad['tipo_actividad']); ?>
                </p>
                <p style="font-family: var(--fuente-montserrat); color: var(--color-primario);">
                    <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($actividad['fecha_evento'])); ?>
                </p>
                <p style="font-family: var(--fuente-montserrat); color: var(--color-primario);">
                    <strong>Club:</strong> <?php echo htmlspecialchars($actividad['nombre_club']); ?>
                </p>
            </div>

            <div class="detalle_acciones">
                <a href="lista_actividades.php" class="boton_secundario" id="btn-cancelar">Cancelar</a>
                <a href="eliminar_actividad.php?id=<?php echo $id_actividad; ?>" class="boton_principal" id="btn-confirmar-eliminar" 
                   style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    Sí, eliminar
                </a>
            </div>
        </div>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
