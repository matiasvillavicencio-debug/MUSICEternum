<?php
session_start();
require_once 'includes/db.php';

$stmtEventos = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC LIMIT 6");
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

$stmtClases = $pdo->query("SELECT * FROM clases ORDER BY id DESC LIMIT 6");
$clases = $stmtClases->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<section id="hero">
    <div class="hero-content">
        <h1>Eternum <span>MUSIC</span></h1>
        <p class="tagline">Donde la música trasciende, nosotros también.</p>
        <p class="hero-desc">El punto de encuentro definitivo para la cultura musical en Argentina. Un espacio diseñado para derribar barreras y conectar todas las piezas del ecosistema en un solo lugar.</p>
    </div>
</section>

<section id="cartelera" class="flex-container-50-50">
    <div class="flex-col">
        <div class="section-header">
            <h2>Cartelera de Conciertos</h2>
        </div>
        <div class="events-grid">
            <?php foreach ($eventos as $evento): ?>
                <a href="/MUSICEternum/views/comprar_entrada.php?id=<?= $evento['id'] ?>" class="card-link">
                    <div class="event-card">
                        <div class="img-thumb"><i class="fa-solid fa-music"></i></div>
                        <div class="event-info">
                            <h4><?= htmlspecialchars($evento['titulo']) ?></h4>
                            <p class="grid-card-price">$<?= number_format($evento['precio'], 2) ?></p>
                            <p class="event-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-col">
        <div class="section-header">
            <h2>Academias y Clases</h2>
        </div>
        <div class="events-grid">
            <?php foreach ($clases as $clase): ?>
                <a href="/MUSICEternum/views/mis_cursos.php" class="card-link">
                    <div class="event-card">
                        <div class="img-thumb"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div class="event-info">
                            <h4><?= htmlspecialchars($clase['titulo']) ?></h4>
                            <p class="grid-card-price">$<?= number_format($clase['precio'], 2) ?></p>
                            <p class="event-desc"><?= htmlspecialchars($clase['descripcion']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>