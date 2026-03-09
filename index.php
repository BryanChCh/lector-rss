<?php
// index.php
require 'conexion.php';

// Consultamos todas las noticias de la base de datos, ordenadas por fecha descendente por defecto
$stmt = $pdo->query("SELECT n.*, f.url as feed_url FROM noticias n LEFT JOIN feeds f ON n.feed_id = f.id ORDER BY n.fecha DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Lector RSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <h1 class="text-center mb-4">Lector de Noticias RSS</h1>
        
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['mensaje']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="card p-4 mb-4 shadow-sm">
            <form action="agregar_feed.php" method="POST" class="d-flex">
                <input type="url" name="feed_url" class="form-control me-2" placeholder="Ingresa la URL del Feed RSS (Ej: https://blog.feedspot.com/world_news_rss_feeds/)" required>
                <button type="submit" class="btn btn-primary">Agregar Feed</button>
            </form>
        </div>

        <div class="text-end mb-4">
            <form action="actualizar_noticias.php" method="POST">
                <button type="submit" class="btn btn-success shadow-sm">Actualizar Noticias Ahora</button>
            </form>
        </div>

        <div class="card p-4 shadow-sm">
            <div class="table-responsive">
                <table id="tabla-noticias" class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Categorías</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias as $noticia): ?>
                            <tr>
                                <td><?= htmlspecialchars($noticia['fecha']) ?></td>
                                <td><?= htmlspecialchars($noticia['titulo']) ?></td>
                                <td><?= htmlspecialchars(substr(strip_tags($noticia['descripcion']), 0, 150)) ?>...</td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($noticia['categorias']) ?></span></td>
                                <td><a href="<?= htmlspecialchars($noticia['url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Leer más</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla-noticias').DataTable({
                "order": [[ 0, "desc" ]], // Ordenar por la primera columna (Fecha) de forma descendente por defecto
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json" // Traducir la interfaz al español
                }
            });
        });
    </script>
</body>
</html>