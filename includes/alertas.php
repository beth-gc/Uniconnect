<?php
/**
 * Muestra una alerta si hay un mensaje en la URL
 * Uso: incluir después de abrir <main>
 */
function mostrar_alerta() {
    if (isset($_GET['status'])) {
        $tipo = $_GET['status']; // 'success' o 'error'
        $msg = $_GET['msg'] ?? '';
        
        $mensajes = [
            // Éxito
            'registro_exitoso' => '¡Cuenta creada exitosamente! Ahora puedes iniciar sesión.',
            'actividad_creada' => '¡Actividad publicada correctamente!',
            'membresia_unido' => '¡Te has unido al club exitosamente!',
            'membresia_salido' => 'Has salido del club.',
            'perfil_actualizado' => 'Perfil actualizado correctamente.',
            'actividad_actualizada' => '¡Actividad actualizada correctamente!',
            
            // Errores
            'campos_vacios' => 'Por favor, completa todos los campos.',
            'credenciales' => 'Email o contraseña incorrectos.',
            'email_invalido' => 'El formato del email no es válido.',
            'email_existe' => 'Este email ya está registrado.',
            'password_corta' => 'La contraseña debe tener al menos 6 caracteres.',
            'nombre_largo' => 'El nombre es demasiado largo (máximo 100 caracteres).',
            'datos_invalidos' => 'Por favor, rellena todos los campos correctamente.',
            'db_error' => 'Hubo un problema con la base de datos. Intenta de nuevo.',
            'fecha_pasada' => 'La fecha del evento no puede ser anterior a la actual.',
            'titulo_largo' => 'El título es demasiado largo (máximo 100 caracteres).',
            'desc_larga' => 'La descripción es demasiado larga (máximo 500 caracteres).',
            'club_no_existe' => 'El club seleccionado no existe.',
            'sesion_requerida' => 'Debes iniciar sesión para acceder a esta página.',
            'no_es_miembro' => 'Debes ser miembro del club para crear actividades en él.',
            'actividad_eliminada' => '¡Actividad eliminada correctamente!',
            'actividad_no_existe' => 'La actividad que intentas eliminar no existe.',
            'sin_permiso' => 'No tienes permiso para realizar esta acción. Solo el creador puede eliminar la actividad.',
        ];
        
        $texto = $mensajes[$msg] ?? 'Operación completada.';
        $clase = $tipo === 'success' ? 'alerta_exito' : 'alerta_error';
        $icono = $tipo === 'success' ? '✅' : '⚠️';
        
        echo "<div class=\"alerta $clase\" id=\"alerta-msg\">";
        echo "<span>$icono $texto</span>";
        echo "<button class=\"alerta_cerrar\" onclick=\"this.parentElement.remove()\">✕</button>";
        echo "</div>";
    }
}
