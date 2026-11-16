<?php

require_once 'config.php';

// Con estos datos abrimos la conexión con MySQL.
try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {

    // Si la conexión falla, mostramos el error para saber qué ha pasado.
    die("Error de conexión: " . $e->getMessage());

}

?>