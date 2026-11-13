<?php

require_once 'conexion.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
    $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
    $prioridad = filter_input(INPUT_POST, 'prioridad', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email_cliente', FILTER_VALIDATE_EMAIL);

    if ($email === false || $email === null) {

        $error = "El correo electrónico no es válido.";

    } elseif (empty($titulo) || empty($descripcion) || empty($prioridad)) {

        $error = "Todos los campos son obligatorios.";

    } else {

        $sql = "INSERT INTO incidencias 
                (titulo, descripcion, prioridad, email_cliente, estado)
                VALUES (:titulo, :descripcion, :prioridad, :email, 'Pendiente')";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':prioridad' => $prioridad,
            ':email' => $email
        ]);

        $id = $pdo->lastInsertId();

        header("Location: index.php?id=" . $id);
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva incidencia</title>
</head>

<body>

    <h1>Registrar nueva incidencia</h1>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Título:</label><br>
        <input type="text" name="titulo" required>
        <br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" required></textarea>
        <br><br>

        <label>Prioridad:</label><br>
        <select name="prioridad" required>
            <option value="">Selecciona una prioridad</option>
            <option value="Baja">Baja</option>
            <option value="Media">Media</option>
            <option value="Alta">Alta</option>
        </select>
        <br><br>

        <label>Email del cliente:</label><br>
        <input type="email" name="email_cliente" required>
        <br><br>

        <button type="submit">Guardar incidencia</button>

    </form>

    <br>

    <a href="index.php">Volver al listado</a>

</body>

</html>