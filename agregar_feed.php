<?php
// agregar_feed.php

// 1. Incluimos la conexión a la base de datos
require 'conexion.php';

// 2. Verificamos que el formulario realmente haya enviado algo por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['feed_url'])) {
    
    // 3. Limpiamos y validamos la URL por seguridad básica
    $url = filter_var($_POST['feed_url'], FILTER_SANITIZE_URL);
    
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        try {
            // 4. Preparamos la consulta SQL para evitar inyecciones
            $stmt = $pdo->prepare("INSERT INTO feeds (url) VALUES (:url)");
            $stmt->bindParam(':url', $url);
            
            // 5. Ejecutamos la inserción
            $stmt->execute();
            
            // Redirigimos de vuelta al index con un mensaje de éxito (opcional)
            header("Location: index.php?mensaje=Feed agregado correctamente");
            exit;
            
        } catch (PDOException $e) {
            // Si la URL ya existe, SQLite arrojará un error porque definimos la columna como UNIQUE
            if ($e->getCode() == 23000) {
                header("Location: index.php?error=La URL ya está registrada");
                exit;
            } else {
                echo "Error al guardar el feed: " . $e->getMessage();
            }
        }
    } else {
        header("Location: index.php?error=URL inválida");
        exit;
    }
} else {
    // Si alguien entra directamente a este archivo sin pasar por el formulario, lo regresamos al inicio
    header("Location: index.php");
    exit;
}
?>