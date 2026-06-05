<?php
// Verificar si hay sesión activa
session_start();
$sesion_activa = isset($_SESSION['id_usuario']);
