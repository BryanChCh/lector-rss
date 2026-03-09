<?php
// actualizar_noticias.php

require 'conexion.php';

// 1. Obtenemos todos los feeds que el usuario ha registrado en la base de datos
$stmt = $pdo->query("SELECT id, url FROM feeds");
$feeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Iteramos sobre cada URL guardada
foreach ($feeds as $feed) {
    $feed_id = $feed['id'];
    $url = $feed['url'];

    // 3. Cargamos el XML nativamente. 
    // Usamos '@' para suprimir advertencias en pantalla si una URL de feed está caída o el XML es inválido.
    $rss = @simplexml_load_file($url);

    // Si el archivo se cargó correctamente, procedemos
    if ($rss) {
        
        // La estructura estándar de un RSS 2.0 guarda los artículos dentro de <channel><item>
        if (isset($rss->channel->item)) {
            $items = $rss->channel->item;
            
            foreach ($items as $item) {
                // 4. Extraemos los campos solicitados en tus instrucciones
                $titulo = (string) $item->title;
                $enlace = (string) $item->link;
                $descripcion = (string) $item->description;
                
                // Formateamos la fecha para que SQLite la entienda bien (Año-Mes-Día Hora:Minuto:Segundo)
                $fecha_str = (string) $item->pubDate;
                $fecha = date('Y-m-d H:i:s', strtotime($fecha_str));

                // Un artículo puede tener múltiples categorías, las extraemos y unimos por comas
                $categorias_arr = [];
                if (isset($item->category)) {
                    foreach ($item->category as $cat) {
                        $categorias_arr[] = (string) $cat;
                    }
                }
                $categorias = implode(', ', $categorias_arr);

                // 5. Insertamos la noticia en la base de datos
                // Usamos "INSERT OR IGNORE" (propio de SQLite). 
                // Como le dijimos a la tabla que la URL es UNIQUE, si la noticia ya existe, 
                // SQLite simplemente la ignora y no arroja error, evitando duplicados.
                $insert_stmt = $pdo->prepare("
                    INSERT OR IGNORE INTO noticias (titulo, url, descripcion, fecha, categorias, feed_id) 
                    VALUES (:titulo, :url, :descripcion, :fecha, :categorias, :feed_id)
                ");
                
                $insert_stmt->execute([
                    ':titulo' => $titulo,
                    ':url' => $enlace,
                    ':descripcion' => $descripcion,
                    ':fecha' => $fecha,
                    ':categorias' => $categorias,
                    ':feed_id' => $feed_id
                ]);
            }
        }
    }
}

// 6. Una vez que termina de procesar todos los feeds, redirigimos a la página principal
header("Location: index.php?mensaje=Noticias actualizadas correctamente");
exit;
?>