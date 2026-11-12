CREATE TABLE tecnicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    tareas_activas INT DEFAULT 0
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