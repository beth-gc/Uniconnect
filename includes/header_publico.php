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
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'link_activo' : ''; ?>" href="index.php">Inicio</a>
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'link_activo' : ''; ?>" href="login.php">Iniciar sesión</a>
        <a class="encabezado_menu_link <?php echo basename($_SERVER['PHP_SELF']) == 'registro.php' ? 'link_activo' : ''; ?>" href="registro.php">Registrarse</a>
    </nav>
</header>
