<?php
// Proteger página: redirige a login si no hay sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?status=error&msg=sesion_requerida');
    exit;
}
