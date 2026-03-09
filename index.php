<?php
// index.php
require 'conexion.php';

// Consultamos todas las noticias, ordenadas por fecha descendente por defecto
$stmt = $pdo->query("SELECT n.*, f.url as feed_url FROM noticias n LEFT JOIN feeds f ON n.feed_id = f.id ORDER BY n.fecha DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Lector RSS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tarjeta-noticia .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .tarjeta-noticia .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="p-5 text-center bg-image rounded-3 mb-4 shadow-sm" style="
            background-image: url('https://images.unsplash.com/photo-1504711434969-e33886168f5c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center center;
            height: 250px;
            position: relative;
        ">
            <div class="mask rounded-3" style="background-color: rgba(0, 0, 0, 0.6); position: absolute; top: 0; bottom: 0; left: 0; right: 0;"></div>
            
            <div class="d-flex justify-content-center align-items-center h-100 position-relative">
                <div class="text-white">
                    <h1 class="mb-3 fw-bold display-4">Lector de Noticias RSS</h1>
                    <h5 class="mb-3 fw-light">Toda tu información centralizada en un solo lugar</h5>
                </div>
            </div>
        </div>
        
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['mensaje']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card p-3 shadow-sm border-0 h-100">
                    <form action="agregar_feed.php" method="POST" class="d-flex align-items-center h-100">
                        <input type="url" name="feed_url" class="form-control me-2" placeholder="Ingresa la URL del Feed RSS" required>
                        <button type="submit" class="btn btn-primary text-nowrap">Agregar Feed</button>
                    </form>
                </div>
            </div>
            <div class="col-md-4 mt-3 mt-md-0">
                <div class="card p-3 shadow-sm border-0 h-100 d-flex justify-content-center">
                    <form action="actualizar_noticias.php" method="POST" class="m-0 text-center text-md-end">
                        <button type="submit" class="btn btn-success w-100">↻ Actualizar Noticias</button>
                    </form>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row mb-4 align-items-center">
            <div class="col-md-8 mb-3 mb-md-0">
                <input type="text" id="buscador" class="form-control form-control-lg shadow-sm border-0" placeholder="🔍 Buscar noticias por palabra clave...">
            </div>
            <div class="col-md-4">
                <select id="ordenar" class="form-select form-select-lg shadow-sm border-0">
                    <option value="fecha_desc">Fecha (Más recientes)</option>
                    <option value="fecha_asc">Fecha (Más antiguas)</option>
                    <option value="titulo_asc">Título (A-Z)</option>
                    <option value="titulo_desc">Título (Z-A)</option>
                    <option value="categoria_asc">Categoría (A-Z)</option>
                    <option value="categoria_desc">Categoría (Z-A)</option>
                    <option value="descripcion_asc">Descripción (A-Z)</option>
                    <option value="descripcion_desc">Descripción (Z-A)</option>
                    <option value="url_asc">URL (A-Z)</option>
                    <option value="url_desc">URL (Z-A)</option>
                </select>
            </div>
        </div>

        <div class="row" id="contenedor-noticias">
            <?php if (empty($noticias)): ?>
                <div class="col-12 text-center text-muted my-5">
                    <h4>No hay noticias todavía.</h4>
                    <p>Agrega una URL de RSS y haz clic en "Actualizar Noticias".</p>
                </div>
            <?php else: ?>
                <?php foreach ($noticias as $noticia): 
                    // Preparamos los textos limpios para los atributos de ordenamiento
                    $cat_limpia = empty($noticia['categorias']) ? 'general' : strtolower($noticia['categorias']);
                    $desc_limpia = strtolower(substr(strip_tags($noticia['descripcion']), 0, 150));
                ?>
                    <div class="col-md-6 col-lg-4 mb-4 tarjeta-noticia" 
                         data-fecha="<?= htmlspecialchars($noticia['fecha']) ?>" 
                         data-titulo="<?= htmlspecialchars(strtolower($noticia['titulo'])) ?>"
                         data-categoria="<?= htmlspecialchars($cat_limpia) ?>"
                         data-descripcion="<?= htmlspecialchars($desc_limpia) ?>"
                         data-url="<?= htmlspecialchars(strtolower($noticia['url'])) ?>">
                        
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted text-uppercase" style="font-size: 0.75rem;">
                                        <?= date('d M Y, H:i', strtotime($noticia['fecha'])) ?>
                                    </small>
                                    <span class="badge bg-secondary">
                                        <?= empty($noticia['categorias']) ? 'General' : htmlspecialchars($noticia['categorias']) ?>
                                    </span>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-3"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                                
                                <p class="card-text text-secondary small flex-grow-1">
                                    <?= htmlspecialchars(substr(strip_tags($noticia['descripcion']), 0, 150)) ?>...
                                </p>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-end pb-3 pt-0">
                                <a href="<?= htmlspecialchars($noticia['url']) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">Leer artículo ↗</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center text-white mt-auto position-relative shadow-lg" style="
        background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center center;
    ">
        <div class="mask" style="background-color: rgba(0, 0, 0, 0.75); position: absolute; top: 0; bottom: 0; left: 0; right: 0;"></div>
        
        <div class="container p-4 position-relative">
            <section class="mb-2">
                <h5 class="text-uppercase fw-bold text-primary mb-3">Lector Web de RSS Feeds</h5>
                <p class="text-light opacity-75 small">
                    Permite la ingesta, almacenamiento y visualización dinámica de noticias mediante fuentes RSS externas.
                </p>
            </section>
        </div>
        
        <div class="text-center p-3 position-relative small" style="background-color: rgba(0, 0, 0, 0.3);">
            © <?= date('Y') ?> Desarrollado con PHP y SQLite
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscador = document.getElementById('buscador');
            const selectorOrden = document.getElementById('ordenar');
            const contenedor = document.getElementById('contenedor-noticias');
            
            if (!contenedor.querySelector('.tarjeta-noticia')) return;

            const tarjetas = Array.from(document.querySelectorAll('.tarjeta-noticia'));

            // 1. Motor de Búsqueda
            buscador.addEventListener('input', function(e) {
                const textoBuscado = e.target.value.toLowerCase().trim();
                tarjetas.forEach(tarjeta => {
                    const contenido = tarjeta.innerText.toLowerCase();
                    tarjeta.style.display = contenido.includes(textoBuscado) ? '' : 'none';
                });
            });

            // 2. Sistema de Ordenamiento Dinámico
            selectorOrden.addEventListener('change', function(e) {
                // Separamos el campo y la dirección (ej: "titulo_asc" -> campo: "titulo", orden: "asc")
                const partes = e.target.value.split('_');
                const campo = partes[0];
                const orden = partes[1];

                tarjetas.sort((a, b) => {
                    let valA = a.dataset[campo];
                    let valB = b.dataset[campo];

                    // Tratamiento especial para fechas, el resto se compara como texto
                    if (campo === 'fecha') {
                        valA = new Date(valA);
                        valB = new Date(valB);
                        return orden === 'asc' ? valA - valB : valB - valA;
                    } else {
                        return orden === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
                    }
                });

                // Reinyectamos las tarjetas ordenadas
                tarjetas.forEach(tarjeta => contenedor.appendChild(tarjeta));
            });
        });
    </script>
</body>
</html>