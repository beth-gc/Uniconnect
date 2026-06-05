<?php
include("includes/sesion.php");
include("includes/alertas.php");
?>
<!DOCTYPE html>
<html lang="es-mx">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UniConnect - Conecta con clubes y organizaciones estudiantiles de tu universidad. Descubre actividades, eventos y comunidades.">
    <title>UniConnect - Plataforma Universitaria</title>
    <link rel="icon" href="assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./styles/style.css">
</head>
<body>
    <?php 
    if ($sesion_activa) {
        include("includes/header_privado.php");
    } else {
        include("includes/header_publico.php");
    }
    ?>

    <main class="presentacion">
        <section class="presentacion_contenido animacion-entrada">
            <h1 class="presentacion_contenido_titulo">
                Bienvenido a nuestra <strong class="titulo-resaltar">plataforma universitaria!</strong> 
            </h1>
            <p class="presentacion_contenido_texto">
                Descubre y conéctate con la vibrante comunidad de clubes y organizaciones estudiantiles.
            </p> 
            <div class="presentacion_enlaces">
                <a href="lista_clubes.php" class="presentacion_enlaces_link" id="btn-ver-clubes">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Ver clubes
                </a>
                <a href="lista_actividades.php" class="presentacion_enlaces_link" id="btn-ver-actividades">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Ver actividades
                </a>
            </div>
        </section>
        <img class="presentacion_imagen animacion-entrada" src="assets/fcc.png" alt="Imagen de estudiantes universitarios">
    </main>

    <?php include("includes/footer.php"); ?>
</body>
</html>
