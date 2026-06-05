<header class="encabezado" id="header-principal">
    <div class="encabezado_logo">
        <img class="logo_imagen" src="assets/logo.svg" alt="Logo UniConnect">
        <a class="encabezado_menu_link logo_texto" href="index.php">UniConnect</a>
    </div>
    <button class="hamburguesa" id="btn-hamburguesa" aria-label="Abrir menú">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <nav class="encabezado_menu" id="nav-menu">
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'lista_clubes.php' ? 'link_activo' : ''; ?>" href="lista_clubes.php">Clubes</a>
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'lista_actividades.php' ? 'link_activo' : ''; ?>" href="lista_actividades.php">Actividades</a>
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard_estudiante.php' ? 'link_activo' : ''; ?>" href="dashboard_estudiante.php">Mi Dashboard</a>
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'perfil.php' ? 'link_activo' : ''; ?>" href="perfil.php">
            <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Perfil'); ?>
        </a>
        <a class="encabezado_menu_link link_salir" href="cerrar_sesion.php">Salir</a>
    </nav>
</header>
