# Casos de prueba - Gestión de incidencias

En este documento recojo tres situaciones importantes para comprobar cómo responde la aplicación. En cada caso explico qué se prueba, qué debería pasar y dejo el trozo de código relacionado para que sea fácil encontrarlo.

## 1) Email inválido en el formulario

### Qué se prueba
Se intenta crear una incidencia poniendo un correo mal escrito o que no termine en `@gmail.com` o `@hotmail.com`. La aplicación comprueba el correo antes de guardar nada.

### Resultado esperado
- Si el formato del correo no es válido, aparece un mensaje indicando el problema.
- Si el correo tiene otro dominio, se avisa de que solo se aceptan Gmail o Hotmail.
- En ambos casos, la incidencia no se guarda.

### Código que lo controla
Archivo: `nueva_incidencia.php`

```php
$email = filter_input(INPUT_POST, 'email_cliente', FILTER_VALIDATE_EMAIL);

if ($email === false || $email === null) {

    $error = "El correo electrónico no es válido. Usa un formato como nombre@gmail.com o nombre@hotmail.com.";

} elseif (preg_match('/@(gmail\.com|hotmail\.com)$/i', $email) !== 1) {

    $error = "El correo debe terminar en @gmail.com o @hotmail.com. Ejemplo: nombre@gmail.com.";

} elseif (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) {

    $error = "Selecciona una prioridad válida.";

} elseif (empty($titulo) || empty($descripcion) || empty($prioridad)) {

    $error = "Todos los campos son obligatorios.";

} else {

    $sql = "INSERT INTO incidencias 
            (titulo, descripcion, prioridad, email_cliente, estado)
            VALUES (:titulo, :descripcion, :prioridad, :email, 'Pendiente')";
```

### Resumen
La validación se hace antes del `INSERT`. Si el correo no pasa el filtro, se muestra el error y no se llega a guardar la incidencia.

---

## 2) Editar una incidencia con un ID que no existe en la URL

### Qué se prueba
Se abre la página de edición con un ID que no corresponde a ninguna incidencia, por ejemplo `editar_incidencia.php?id=9999`.

### Resultado esperado
- La página controla el ID y no muestra un error de PHP.
- Si la incidencia no existe, se vuelve al listado principal (`index.php`).
- No se modifica ningún registro.

### Código que lo controla
Archivo: `editar_incidencia.php`

```php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM incidencias WHERE id = :id");
$stmt->execute([':id' => $id]);
$incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$incidencia) {
    header("Location: index.php");
    exit;
}
```

### Resumen
Primero se comprueba que el ID sea un número válido y después se busca en la base de datos. Si no se encuentra la incidencia, se redirige al listado y se detiene el resto del código.

---

## 3) Falla la segunda parte de la transacción

### Qué se prueba
Al cambiar el técnico responsable, se hacen varios cambios relacionados dentro de una transacción:
1. Se resta una tarea activa al técnico anterior, si lo había.
2. Se suma una tarea activa al técnico nuevo, si se ha elegido uno.
3. Se actualiza la incidencia con el nuevo responsable.

Se comprueba qué pasa si falla una de esas operaciones, por ejemplo la actualización del técnico nuevo. En ese caso no deberían quedarse guardados solo algunos cambios.

### Resultado esperado
- Si falla una consulta de la transacción, se ejecuta `ROLLBACK` y se deshacen también los cambios anteriores.
- Si la actualización final de la incidencia no afecta a ninguna fila, también se hace `ROLLBACK`.
- La base de datos no debería quedarse con los datos actualizados a medias. Si ocurre un error en una consulta, se muestra un mensaje indicando que no se guardaron los cambios.

### Código que lo controla
Archivo: `asignar_incidencia.php`

```php
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
```

### Resumen
`beginTransaction()` inicia la operación como un bloque. Los cambios solo quedan confirmados con `commit()`; si algo falla, `rollBack()` los deshace para evitar que la base de datos quede a medias.