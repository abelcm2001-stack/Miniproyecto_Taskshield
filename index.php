
<?php

require_once 'conexion.php';

$sql = "SELECT incidencias.id, incidencias.titulo, incidencias.descripcion,
               incidencias.prioridad, incidencias.estado, incidencias.email_cliente,
               incidencias.tecnico_id, tecnicos.nombre AS tecnico_nombre
        FROM incidencias
        LEFT JOIN tecnicos ON incidencias.tecnico_id = tecnicos.id";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incidencias</title>

    <style>

        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
        }

    </style>

</head>

<body>

    <h1>Gestión de incidencias</h1>

    <a href="nueva_incidencia.php">Crear nueva incidencia</a>

    <br><br>

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

            <?php if (empty($incidencias)): ?>

                <tr>
                    <td colspan="8">No hay incidencias.</td>
                </tr>

            <?php else: ?>

                <?php foreach ($incidencias as $incidencia): ?>

                    <tr>

                        <td><?= htmlspecialchars($incidencia['id']) ?></td>

                        <td><?= htmlspecialchars($incidencia['titulo']) ?></td>

                        <td><?= htmlspecialchars($incidencia['descripcion']) ?></td>

                        <td><?= htmlspecialchars($incidencia['prioridad']) ?></td>

                        <td><?= htmlspecialchars($incidencia['estado']) ?></td>

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
                            <a href="editar_incidencia.php?id=<?= $incidencia['id'] ?>">
                                Editar
                            </a>

                            |

                            <a href="eliminar_incidencia.php?id=<?= $incidencia['id'] ?>">
                                Eliminar
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

        </tbody>

    </table>

</body>

</html>