-- =============================================
-- UniConnect - Procedimientos Almacenados y Triggers
-- Ejecutar en phpMyAdmin sobre uniconnect_db
-- =============================================

-- -----------------------------------------------
-- 1. TABLA DE AUDITORÍA
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS log_auditoria (
    id_log INT(11) AUTO_INCREMENT PRIMARY KEY,
    tabla_afectada VARCHAR(50) NOT NULL,
    accion VARCHAR(20) NOT NULL,
    detalle TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- 2. PROCEDIMIENTOS ALMACENADOS
-- -----------------------------------------------

-- Eliminar procedimientos existentes si los hay
DROP PROCEDURE IF EXISTS sp_registrar_usuario;
DROP PROCEDURE IF EXISTS sp_crear_actividad;
DROP PROCEDURE IF EXISTS sp_actualizar_actividad;
DROP PROCEDURE IF EXISTS sp_eliminar_actividad;
DROP PROCEDURE IF EXISTS sp_buscar_actividades;

DELIMITER //

-- ---- SP: Registrar usuario ----
CREATE PROCEDURE sp_registrar_usuario(
    IN p_nombre VARCHAR(100),
    IN p_email VARCHAR(150),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    INSERT INTO usuarios (nombre, email, password_hash)
    VALUES (p_nombre, p_email, p_password_hash);
END //

-- ---- SP: Crear actividad ----
CREATE PROCEDURE sp_crear_actividad(
    IN p_titulo VARCHAR(100),
    IN p_tipo VARCHAR(50),
    IN p_descripcion TEXT,
    IN p_fecha VARCHAR(30),
    IN p_id_club INT,
    IN p_id_usuario INT
)
BEGIN
    INSERT INTO actividades (titulo, tipo_actividad, descripcion_actividad, fecha_evento, id_club, id_usuario)
    VALUES (p_titulo, p_tipo, p_descripcion, p_fecha, p_id_club, p_id_usuario);
END //

-- ---- SP: Actualizar actividad ----
CREATE PROCEDURE sp_actualizar_actividad(
    IN p_id_actividad INT,
    IN p_titulo VARCHAR(100),
    IN p_tipo VARCHAR(50),
    IN p_descripcion TEXT,
    IN p_fecha VARCHAR(30),
    IN p_id_club INT,
    IN p_id_usuario INT
)
BEGIN
    UPDATE actividades 
    SET titulo = p_titulo,
        tipo_actividad = p_tipo,
        descripcion_actividad = p_descripcion,
        fecha_evento = p_fecha,
        id_club = p_id_club
    WHERE id_actividad = p_id_actividad 
      AND id_usuario = p_id_usuario;
END //

-- ---- SP: Eliminar actividad ----
CREATE PROCEDURE sp_eliminar_actividad(
    IN p_id_actividad INT,
    IN p_id_usuario INT
)
BEGIN
    DELETE FROM actividades 
    WHERE id_actividad = p_id_actividad 
      AND id_usuario = p_id_usuario;
END //

-- ---- SP: Buscar actividades ----
CREATE PROCEDURE sp_buscar_actividades(
    IN p_termino VARCHAR(100)
)
BEGIN
    SELECT a.*, c.nombre_club, u.nombre AS nombre_creador
    FROM actividades a
    INNER JOIN clubes c ON a.id_club = c.id_club
    LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
    WHERE a.titulo LIKE CONCAT('%', p_termino, '%')
       OR a.descripcion_actividad LIKE CONCAT('%', p_termino, '%')
       OR a.tipo_actividad LIKE CONCAT('%', p_termino, '%')
       OR c.nombre_club LIKE CONCAT('%', p_termino, '%')
    ORDER BY a.fecha_evento ASC;
END //

DELIMITER ;

-- -----------------------------------------------
-- 3. TRIGGERS
-- -----------------------------------------------

DROP TRIGGER IF EXISTS tr_log_nuevo_usuario;
DROP TRIGGER IF EXISTS tr_log_actividad_eliminada;

DELIMITER //

-- ---- Trigger: Log cuando se registra un usuario nuevo ----
CREATE TRIGGER tr_log_nuevo_usuario
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO log_auditoria (tabla_afectada, accion, detalle)
    VALUES ('usuarios', 'INSERT', 
            CONCAT('Nuevo usuario registrado: ', NEW.nombre, ' (', NEW.email, ')'));
END //

-- ---- Trigger: Log cuando se elimina una actividad ----
CREATE TRIGGER tr_log_actividad_eliminada
BEFORE DELETE ON actividades
FOR EACH ROW
BEGIN
    INSERT INTO log_auditoria (tabla_afectada, accion, detalle)
    VALUES ('actividades', 'DELETE', 
            CONCAT('Actividad eliminada: "', OLD.titulo, '" (ID: ', OLD.id_actividad, ', Club ID: ', OLD.id_club, ')'));
END //

DELIMITER ;
