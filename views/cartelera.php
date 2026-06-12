<?php
session_start();
require_once '../includes/db.php';

$stmtEventos = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC");
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

$stmtClases = $pdo->query("SELECT c.*, u.username as profesor_nombre FROM clases c JOIN usuarios u ON c.id_profesor = u.id ORDER BY c.id DESC");
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
            <h2>Academias y Profesores</h2>
        </div>
        <div class="grid-academias">
            <?php foreach ($clases as $clase): ?>
                <div class="profesor-card">
                    <div class="profesor-header">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($clase['profesor_nombre']) ?>&background=69DDFF&color=282262&bold=true" class="profesor-avatar" alt="Avatar">
                        <div class="profesor-info">
                            <h3><?= htmlspecialchars($clase['titulo']) ?></h3>
                            <p>Prof. <?= htmlspecialchars($clase['profesor_nombre']) ?></p>
                        </div>
                    </div>
                    
                    <div class="profesor-body">
                        <div class="badges-container">
                            <span class="badge badge-modalidad"><i class="fa-solid fa-laptop"></i> <?= htmlspecialchars($clase['modalidad']) ?></span>
                            <span class="badge badge-nivel"><i class="fa-solid fa-signal"></i> <?= htmlspecialchars($clase['nivel']) ?></span>
                        </div>
                        
                        <p class="temario-title"><i class="fa-solid fa-list-check"></i> Temario Oficial:</p>
                        <p class="temario-text"><?= htmlspecialchars($clase['descripcion']) ?></p>
                        
                        <div class="profesor-precio">
                            $<?= number_format($clase['precio'], 2) ?>
                        </div>
                        
                        <div class="card-actions">
                            <a href="saber_mas_clase.php?id=<?= $clase['id'] ?>" class="btn-outline">Saber Más</a>
                            <a href="contacto_profesor.php?id_clase=<?= $clase['id'] ?>&id_profesor=<?= $clase['id_profesor'] ?>" class="btn-outline">Contacto</a>
                            <a href="comprar_curso.php?id=<?= $clase['id'] ?>" class="btn-primary-action btn-action-full">Inscribirse <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>