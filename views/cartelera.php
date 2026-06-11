<?php
session_start();
require_once '../includes/db.php';

$stmtEventos = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC");
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

$stmtClases = $pdo->query("SELECT * FROM clases ORDER BY id DESC");
$clases = $stmtClases->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="flex-container-50-50 mt-30 mb-50">
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
                            <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
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
                            <h3><?= htmlspecialchars($clase['titulo']) ?></h3>
                            <p class="grid-card-price">$<?= number_format($clase['precio'], 2) ?></p>
                            <p class="event-desc"><?= htmlspecialchars($clase['descripcion']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>