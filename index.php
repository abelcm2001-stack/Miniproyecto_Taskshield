
<?php

require_once 'conexion.php';

// Buscamos las incidencias junto al nombre del técnico que tiene asignado cada una.
$sql = "SELECT incidencias.id, incidencias.titulo, incidencias.descripcion,
               incidencias.prioridad, incidencias.estado, incidencias.email_cliente,
               incidencias.tecnico_id, tecnicos.nombre AS tecnico_nombre
        FROM incidencias
        LEFT JOIN tecnicos ON incidencias.tecnico_id = tecnicos.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Guardamos los resultados para mostrarlos más abajo.
$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estas clases sirven para dar un color distinto a cada estado.
$clases_estado = [
    'Pendiente' => 'pendiente',
    'En Proceso' => 'en-proceso',
    'Resuelta' => 'resuelta',
    'Resuelto' => 'resuelta'
];

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencias</title>
    <link rel="stylesheet" href="estilos.css">

</head>

<body>

    <h1>Gestión de incidencias</h1>

    <a href="nueva_incidencia.php">Crear nueva incidencia</a>

    <table>

        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Email cliente</th>
                <th>Técnico responsable</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <!-- Si no hay incidencias, mostramos un aviso; si las hay, enseñamos sus datos. -->
            <?php if (empty($incidencias)): ?>

                <tr>
                    <td colspan="8">No hay incidencias.</td>
                </tr>

            <?php else: ?>

                <?php foreach ($incidencias as $incidencia): ?>

                    <tr>

                        <!-- Escapamos los datos antes de mostrarlos en la página. -->
                        <td><?= htmlspecialchars($incidencia['id']) ?></td>

                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>

                        <td><?= htmlspecialchars($incidencia['descripcion']) ?></td>

                        <td><?= htmlspecialchars($incidencia['prioridad']) ?></td>

                        <td>
                            <span class="estado estado-<?= htmlspecialchars($clases_estado[$incidencia['estado']] ?? 'desconocido') ?>">
                                <?= htmlspecialchars($incidencia['estado']) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($incidencia['email_cliente']) ?></td>

                        <td>
                            <?= htmlspecialchars($incidencia['tecnico_nombre'] ?? 'Sin asignar') ?>
                            <form method="get" action="asignar_incidencia.php">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($incidencia['id']) ?>">
                                <button type="submit">
                                    <?= $incidencia['tecnico_id'] === null ? 'Asignar técnico' : 'Cambiar técnico' ?>
                                </button>
                            </form>
                        </td>

                        <td>
                            <div class="acciones">
                                <a class="boton accion-editar" href="editar_incidencia.php?id=<?= $incidencia['id'] ?>">Editar</a>
                                <a class="boton accion-eliminar" href="eliminar_incidencia.php?id=<?= $incidencia['id'] ?>">Eliminar</a>
                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</body>

</html>