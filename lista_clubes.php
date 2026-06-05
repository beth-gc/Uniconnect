<?php
include("includes/sesion.php");
include("includes/proteger.php");
include("includes/alertas.php");
include("baseDatos.php");

$conexionbd = conectar_bd();
$id_usuario = $_SESSION['id_usuario'];

// Obtener clubes con estado de membresía del usuario
$query = "SELECT c.*, 
          (SELECT COUNT(*) FROM miembros_club WHERE id_club = c.id_club) as total_miembros,
          (SELECT COUNT(*) FROM miembros_club WHERE id_club = c.id_club AND id_usuario = ?) as es_miembro
          FROM clubes c 
          ORDER BY c.nombre_club ASC";
$stmt = mysqli_prepare($conexionbd, $query);
mysqli_stmt_bind_param($stmt, "i", $id_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explora los clubes estudiantiles disponibles en UniConnect. Únete y participa.">
    <title>Clubes - UniConnect</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/style_clubes.css">
</head>
<body>
    <?php include("includes/header_privado.php"); ?>

    <main class="clubes_contenedor">
        <h2 class="clubes_titulo animacion-entrada">Clubes Disponibles</h2>

        <?php mostrar_alerta(); ?>

        <div class="clubes_grid">
            <?php while($club = mysqli_fetch_assoc($resultado)): ?>
            <div class="club_card animacion-entrada">
                <div class="club_card_header">
                    <span class="club_categoria"><?php echo htmlspecialchars($club['categoria'] ?? 'General'); ?></span>
                </div>
                <h3><?php echo htmlspecialchars($club['nombre_club']); ?></h3>
                <p><?php echo htmlspecialchars($club['descripcion'] ?? 'Sin descripción disponible.'); ?></p>
                <div class="club_card_footer">
                    <span class="club_miembros">👥 <?php echo $club['total_miembros']; ?> miembros</span>
                    <a class="boton_principal" href="detalle_club.php?id=<?php echo $club['id_club']; ?>" id="btn-detalle-<?php echo $club['id_club']; ?>">Ver detalle</a>
                </div>
                <?php if ($club['es_miembro'] > 0): ?>
                    <span class="club_badge_miembro">✓ Inscrito</span>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
<?php mysqli_close($conexionbd); ?>
