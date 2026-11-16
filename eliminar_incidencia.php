<?php

require_once 'conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Comprueba que la incidencia existe
$stmt = $pdo->prepare("SELECT id, titulo, descripcion, estado FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    header("Location: index.php");
    exit;
}

$mensaje = "";

// Solo borramos la incidencia cuando se confirma desde el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['confirmar'])) {

        // Elimina la incidencia
        $stmt = $pdo->prepare("DELETE FROM incidencias WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Comprueba si se ha eliminado correctamente
        if ($stmt->rowCount() > 0) {
            header("Location: index.php");
            exit;

    } else {
        header("Location: index.php");
        exit;
    }
}
}

$id_incidencia = htmlspecialchars($incidencia['id']);
$titulo = htmlspecialchars($incidencia['titulo']);
$descripcion = htmlspecialchars($incidencia['descripcion']);
$estado = htmlspecialchars($incidencia['estado']);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar incidencia</title>

</head>

<body>

    <h1>Eliminar incidencia nº <?= $id_incidencia ?></h1>

    <p><strong>Título:</strong> <?= $titulo ?></p>
    <p><strong>Descripción:</strong> <?= $descripcion ?></p>
    <p><strong>Estado:</strong> <?= $estado ?></p>

    <h3>¿Estás seguro de que quieres eliminar esta incidencia?</h3>

    <?php if (!empty($mensaje)): ?>
        <p style="color: red;"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form method="post" action="eliminar_incidencia.php?id=<?= $id_incidencia ?>">

        <button type="submit" name="confirmar" value="1">
            Eliminar incidencia
        </button>

        <a href="index.php">Cancelar</a>

    </form>

</body>

</html>
