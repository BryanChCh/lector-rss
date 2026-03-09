<?php
// conexion.php

try {
    // Esto crea un archivo llamado 'rss_database.sqlite' en tu carpeta
    $pdo = new PDO('sqlite:rss_database.sqlite');
    
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear la tabla para guardar las URLs de los feeds que el usuario ingrese
    $pdo->exec("CREATE TABLE IF NOT EXISTS feeds (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        url TEXT UNIQUE NOT NULL
    )");

    // Crear la tabla para almacenar las noticias extraídas
    $pdo->exec("CREATE TABLE IF NOT EXISTS noticias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titulo TEXT NOT NULL,
        url TEXT UNIQUE NOT NULL,
        descripcion TEXT,
        fecha DATETIME,
        categorias TEXT,
        feed_id INTEGER,
        FOREIGN KEY (feed_id) REFERENCES feeds(id)
    )");

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>