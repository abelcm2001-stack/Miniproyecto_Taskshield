<?php

require_once 'conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, titulo, descripcion, estado FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($estado === 'Pendiente' || $estado === 'En Proceso' || $estado === 'Resuelta') {

        $stmt = $pdo->prepare("UPDATE incidencias SET estado = :estado WHERE id = :id");
        $stmt->execute([
            ':estado' => $estado,
            ':id'     => $id
        ]);

        header("Location: index.php");
        exit;
    }
}

$sel_pendiente  = $incidencia['estado'] === 'Pendiente'  ? 'selected' : '';
$sel_en_proceso = $incidencia['estado'] === 'En Proceso' ? 'selected' : '';
$sel_resuelta   = $incidencia['estado'] === 'Resuelta'   ? 'selected' : '';

$id_incidencia = htmlspecialchars($incidencia['id']);
$titulo        = htmlspecialchars($incidencia['titulo']);
$descripcion   = htmlspecialchars($incidencia['descripcion']);
$estado_actual = htmlspecialchars($incidencia['estado']);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar incidencia</title>

</head>

<body>

    <h1>Editar incidencia nº <?= $id_incidencia ?></h1>

    <p><strong>Título:</strong> <?= $titulo ?></p>
    <p><strong>Descripción:</strong> <?= $descripcion ?></p>
    <p><strong>Estado actual:</strong> <?= $estado_actual ?></p>

    <form method="post" action="editar_incidencia.php?id=<?= $id_incidencia ?>">

        <p>
            <label>Cambiar estado a:</label><br>
            <select name="estado">
                <option value="Pendiente" <?= $sel_pendiente ?>>Pendiente</option>
                <option value="En Proceso" <?= $sel_en_proceso ?>>En Proceso</option>
                <option value="Resuelta" <?= $sel_resuelta ?>>Resuelta</option>
            </select>
        </p>

        <button type="submit">Guardar cambios</button>

    </form> 
        <br><br>
        <a href="index.php">Volver al listado</a>
        
</body>

</html>