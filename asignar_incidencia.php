
<?php

require_once 'conexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

// Busca la incidencia
$stmt = $pdo->prepare("SELECT * FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    header("Location: index.php");
    exit;
}

// Coge los técnicos que están disponibles
$stmt = $pdo->query("SELECT id, nombre FROM tecnicos");
$tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = "";
$tecnico_seleccionado = $incidencia['tecnico_id'] === null ? '' : (string) $incidencia['tecnico_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tecnico_enviado = filter_input(INPUT_POST, 'tecnico_id', FILTER_UNSAFE_RAW);
    $tecnico_id = null;
    if ($tecnico_enviado !== null) {
        $tecnico_seleccionado = $tecnico_enviado;
    }

    if ($tecnico_enviado === null) {

        $error = "Selecciona un técnico o deja la incidencia sin asignar.";

    } elseif ($tecnico_enviado === '') {

        $tecnico_id = null;

    } else {

        $tecnico_id = filter_var($tecnico_enviado, FILTER_VALIDATE_INT);

        if ($tecnico_id === false || $tecnico_id <= 0) {
            $error = "Selecciona un técnico válido.";
        }
    }

    if ($error === "" && $tecnico_id !== null) {
        $stmt = $pdo->prepare("SELECT id FROM tecnicos WHERE id = :id");
        $stmt->execute([':id' => $tecnico_id]);

        if (!$stmt->fetch()) {
            $error = "El técnico seleccionado no existe.";
        }
    }

    if ($error === "") {
        $tecnico_anterior = $incidencia['tecnico_id'] === null ? null : (int) $incidencia['tecnico_id'];
        $tecnico_id = $tecnico_id === null ? null : (int) $tecnico_id;

        if ($tecnico_anterior === $tecnico_id) {
            header("Location: index.php");
            exit;
        }

        try {
            $pdo->beginTransaction();

            if ($tecnico_anterior !== null) {
                $stmt = $pdo->prepare("
                    UPDATE tecnicos
                    SET tareas_activas = GREATEST(tareas_activas - 1, 0)
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $tecnico_anterior]);
            }

            if ($tecnico_id !== null) {
                $stmt = $pdo->prepare("
                    UPDATE tecnicos
                    SET tareas_activas = tareas_activas + 1
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $tecnico_id]);
            }

            $estado = $tecnico_id !== null ? 'En Proceso' : $incidencia['estado'];

            $stmt = $pdo->prepare("
                UPDATE incidencias
                SET estado = :estado, tecnico_id = :tecnico_id
                WHERE id = :id AND tecnico_id <=> :tecnico_anterior
            ");
            $stmt->execute([
                ':estado' => $estado,
                ':tecnico_id' => $tecnico_id,
                ':id' => $id,
                ':tecnico_anterior' => $tecnico_anterior
            ]);

            if ($stmt->rowCount() !== 1) {
                $pdo->rollBack();
                $error = "No se pudo cambiar el responsable. Recarga la página e inténtalo de nuevo.";
            } else {
                $pdo->commit();
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("Error al cambiar el responsable de la incidencia: " . $e->getMessage());
            $error = "Error al cambiar el responsable. No se han guardado los cambios.";
        }
    }
}

$titulo = htmlspecialchars($incidencia['titulo']);
$es_reasignacion = $incidencia['tecnico_id'] !== null;

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $es_reasignacion ? 'Cambiar técnico' : 'Asignar incidencia' ?></title>

</head>

<body>

    <h1><?= $es_reasignacion ? 'Cambiar técnico' : 'Asignar incidencia' ?></h1>

    <p><strong>Incidencia:</strong> <?= $titulo ?></p>

    <?php if (!empty($error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="post">

        <label>Seleccionar técnico responsable:</label><br>

        <select name="tecnico_id">

            <option value="" <?= $tecnico_seleccionado === '' ? 'selected' : '' ?>>Sin asignar</option>

            <?php foreach ($tecnicos as $tecnico): ?>

                <option value="<?= htmlspecialchars($tecnico['id']) ?>" <?= $tecnico_seleccionado === (string) $tecnico['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tecnico['nombre']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <br><br>

        <button type="submit">Guardar responsable</button>

    </form>
    
    <br><br>
        
    <a href="index.php">Volver al listado</a>
</body>

</html>
