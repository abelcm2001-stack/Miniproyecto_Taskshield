CREATE DATABASE IF NOT EXISTS gestion_de_incidencias;
USE gestion_de_incidencias;

CREATE TABLE tecnicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    tareas_activas INT NOT NULL DEFAULT 0
);

CREATE TABLE incidencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad VARCHAR(20) NOT NULL,
    email_cliente VARCHAR(150) NOT NULL,
    estado VARCHAR(30) DEFAULT 'Pendiente',
    tecnico_id INT DEFAULT NULL,

    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
); 

INSERT INTO tecnicos (id, nombre, email, tareas_activas) VALUES
    (1, 'Ana Garcia', 'ana.garcia@example.com', 1),
    (2, 'Luis Perez', 'luis.perez@example.com', 1);

INSERT INTO incidencias (id, titulo, descripcion, prioridad, email_cliente, estado, tecnico_id) VALUES
    (1, 'Problema con el acceso', 'El cliente no puede iniciar sesion.', 'Alta', 'cliente1@example.com', 'En Proceso', 1),
    (2, 'Error al guardar', 'No se guardan los cambios del perfil.', 'Media', 'cliente2@example.com', 'En Proceso', 2),
    (3, 'Consulta de facturacion', 'El cliente solicita informacion de su factura.', 'Baja', 'cliente3@example.com', 'Pendiente', NULL);