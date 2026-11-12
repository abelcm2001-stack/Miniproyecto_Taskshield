
<?php

require_once 'conexion.php';

$sql = "SELECT id, titulo, descripcion, prioridad, estado, email_cliente
        FROM incidencias
        WHERE estado != :estado";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':estado' => 'Resuelta'
]);

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
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($incidencias)): ?>

                <tr>
                    <td colspan="7">No hay incidencias activas.</td>
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