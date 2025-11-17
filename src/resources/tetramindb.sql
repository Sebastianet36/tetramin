SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE usuarios (
  id_usuario INT(6) NOT NULL AUTO_INCREMENT,
  nombre_usuario VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  fecha_registro DATETIME NOT NULL,
  es_admin TINYINT(1) NOT NULL,
  usuario_activo TINYINT(1) NOT NULL,
  ubicacion VARCHAR(255) NOT NULL,
  PRIMARY KEY (id_usuario)
) ENGINE=InnoDB;

CREATE TABLE idioma (
  id_idioma INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_idioma)
) ENGINE=InnoDB;

CREATE TABLE configuracion (
  id_config INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  id_idioma INT(11) NOT NULL,
  PRIMARY KEY (id_config),
  CONSTRAINT fk_config_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_config_idioma FOREIGN KEY (id_idioma)
    REFERENCES idioma(id_idioma)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE modojuego (
  id_modo INT(11) NOT NULL AUTO_INCREMENT,
  nombre_modo VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_modo)
) ENGINE=InnoDB;

CREATE TABLE usuariomodojuego (
  id_usuario_modo INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  id_modo INT(11) NOT NULL,
  PRIMARY KEY (id_usuario_modo),
  CONSTRAINT fk_usuariomodo_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_usuariomodo_modo FOREIGN KEY (id_modo)
    REFERENCES modojuego(id_modo)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE record (
  id_record INT(11) NOT NULL AUTO_INCREMENT,
  id_usuario INT(11) NOT NULL,
  fecha_jugada DATETIME NOT NULL,
  puntaje INT(11) NOT NULL,
  duracion INT(11) NOT NULL,
  nivel INT(11) NOT NULL,
  lineas INT(11) NOT NULL,
  id_modo INT(11) NOT NULL,
  PRIMARY KEY (id_record),
  CONSTRAINT fk_record_usuario FOREIGN KEY (id_usuario)
    REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_record_modo FOREIGN KEY (id_modo)
    REFERENCES modojuego(id_modo)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertar modos de juego básicos
INSERT INTO modojuego (nombre_modo) VALUES 
('Clasico'),
('Carrera'),
('Cheese'),
('Supervivencia');

DELIMITER //
CREATE PROCEDURE GuardarRecord(
    IN p_id_usuario INT,
    IN p_puntaje INT,
    IN p_duracion INT,
    IN p_nivel INT,
    IN p_lineas INT,
    IN p_id_modo INT,
    OUT p_es_nuevo_record BOOLEAN
)
BEGIN
    DECLARE v_id_record INT;
    DECLARE v_puntaje_actual INT;

    SELECT id_record, puntaje INTO v_id_record, v_puntaje_actual
    FROM record
    WHERE id_usuario = p_id_usuario AND id_modo = p_id_modo
    LIMIT 1;

    IF v_id_record IS NOT NULL THEN
        IF p_puntaje > v_puntaje_actual THEN
            UPDATE record
            SET puntaje = p_puntaje, duracion = p_duracion, nivel = p_nivel, lineas = p_lineas, fecha_jugada = NOW()
            WHERE id_record = v_id_record;
            SET p_es_nuevo_record = TRUE;
        ELSE
            SET p_es_nuevo_record = FALSE;
        END IF;
    ELSE
        INSERT INTO record (id_usuario, fecha_jugada, puntaje, duracion, nivel, lineas, id_modo)
        VALUES (p_id_usuario, NOW(), p_puntaje, p_duracion, p_nivel, p_lineas, p_id_modo);
        SET p_es_nuevo_record = TRUE;
    END IF;
END //
DELIMITER ;