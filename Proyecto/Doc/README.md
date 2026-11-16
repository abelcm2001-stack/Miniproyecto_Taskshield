# Miniproyecto Taskshield

Este proyecto es una mini app en PHP para gestionar incidencias técnicas de clientes. La idea es simple: ver todas las incidencias, crear nuevas, asignarlas a un técnico, cambiar su estado y borrarlas cuando ya no hacen falta.

## Cómo está montado

La estructura es muy directa y está pensada para ser fácil de seguir:

- `index.php`: es la pantalla principal. Aquí se listan todas las incidencias y se muestran botones para editar o borrar.
- `nueva_incidencia.php`: formulario para registrar una incidencia nueva.
- `asignar_incidencia.php`: permite elegir el técnico responsable y cambia el estado de la incidencia.
- `editar_incidencia.php`: sirve para actualizar el estado de la incidencia (Pendiente, En Proceso, Resuelta).
- `eliminar_incidencia.php`: confirma y elimina la incidencia.
- `conexion.php`: conecta la app con MySQL usando PDO.
- `config.php`: guarda los datos de conexión a la base de datos.
- `Proyecto/Doc/squema.sql`: script SQL con la base de datos y algunos datos de ejemplo.

La base de datos tiene dos tablas principales:

- `tecnicos`: guarda los técnicos disponibles.
- `incidencias`: guarda cada incidencia con título, descripción, prioridad, email del cliente, estado y técnico asignado.

## Cómo probarlo

1. Enciende Apache y MySQL desde XAMPP.
2. Abre phpMyAdmin y crea la base de datos `Gestion_de_incidencias`.
3. Importa el archivo `Proyecto/Doc/squema.sql`.
4. Copia esta carpeta dentro de `C:\xampp\htdocs`.
5. Abre en el navegador esta URL:
   `http://localhost/Miniproyecto_Taskshield/index.php`

Si todo está bien, aparecerá la lista de incidencias y podrás probar crear, asignar, editar y eliminar registros.