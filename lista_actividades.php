<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Verificar si hay término de búsqueda
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

if (!empty($busqueda)) {
    // Usar Stored Procedure para buscar
    $stmt = mysqli_prepare($conexionbd, "CALL sp_buscar_actividades(?)");
    mysqli_stmt_bind_param($stmt, "s", $busqueda);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
} else {
    // Consulta normal con JOIN
    $query = "SELECT a.*, c.nombre_club, u.nombre as nombre_creador 
              FROM actividades a 
              INNER JOIN clubes c ON a.id_club = c.id_club 
              LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
              ORDER BY a.fecha_evento ASC";
    $resultado = mysqli_query($conexionbd, $query);
}
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lista de actividades y eventos de los clubes estudiantiles en UniConnect.">
    <title>Actividades - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_lista.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="lista_contenedor">

        <?php mostrar_alerta(); ?>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <h2 class="presentacion_contenido_titulo" style="font-size: 1.5rem;">
                Próximas <span class="titulo-resaltar">Actividades</span>
            </h2>
            <a href="crear_actividad.php" class="boton_principal boton_nueva" id="btn-nueva-actividad">+ Nueva Actividad</a>
        </div>

        <!-- Barra de búsqueda -->
        <form class="buscador_form animacion-entrada" action="lista_actividades.php" method="GET" id="form-buscar">
            <div class="buscador_campo">
                <input type="text" name="buscar" id="input-buscar" 
                       placeholder="Buscar por título, tipo, descripción o club..." 
                       value="<?php echo htmlspecialchars($busqueda); ?>"
                       autocomplete="off">
                <button type="submit" class="buscador_btn" id="btn-buscar">🔍 Buscar</button>
            </div>
            <?php if (!empty($busqueda)): ?>
                <div class="buscador_resultados">
                    <span>Resultados para: <strong>"<?php echo htmlspecialchars($busqueda); ?>"</strong></span>
                    <a href="lista_actividades.php" class="buscador_limpiar" id="btn-limpiar-busqueda">✕ Limpiar búsqueda</a>
                </div>
            <?php endif; ?>
        </form>

        <table class="tabla_actividades animacion-entrada">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha y Hora</th>
                    <th>Club</th>
                    <th>Creado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultado) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['titulo']); ?></strong></td>
                        <td><span class="badge_tipo"><?php echo htmlspecialchars($row['tipo_actividad']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['descripcion_actividad']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['fecha_evento'])); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_club']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_creador'] ?? '—'); ?></td>
                        <td>
                            <?php if ($row['id_usuario'] == $id_usuario): ?>
                                <div class="acciones_grupo">
                                    <a href="editar_actividad.php?id=<?php echo $row['id_actividad']; ?>" 
                                       class="boton_editar" 
                                       id="btn-editar-<?php echo $row['id_actividad']; ?>">
                                        ✏️ Editar
                                    </a>
                                    <a href="confirmar_eliminar.php?id=<?php echo $row['id_actividad']; ?>" 
                                       class="boton_eliminar" 
                                       id="btn-eliminar-<?php echo $row['id_actividad']; ?>">
                                        🗑️ Eliminar
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="texto_gris">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <?php if (!empty($busqueda)): ?>
                                No se encontraron actividades que coincidan con "<strong><?php echo htmlspecialchars($busqueda); ?></strong>".
                            <?php else: ?>
                                No hay actividades registradas actualmente.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>