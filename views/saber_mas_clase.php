<?php
session_start();
require_once '../includes/db.php';

$id_clase = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT c.*, u.username as profesor_nombre FROM clases c JOIN usuarios u ON c.id_profesor = u.id WHERE c.id = ?");
$stmt->execute([$id_clase]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    header("Location: cartelera.php");
    exit();
}

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-lg mb-0">Información Detallada</h2>
        <a href="cartelera.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver a Cartelera</a>
    </div>

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
            
            <p class="temario-title"><i class="fa-solid fa-circle-info"></i> Descripción Completa y Temario:</p>
            <p class="temario-text"><?= nl2br(htmlspecialchars($clase['descripcion'])) ?></p>
            
            <div class="profesor-precio">
                Precio de Lista: $<?= number_format($clase['precio'], 2) ?>
            </div>
            
            <div class="card-actions">
                <a href="contacto_profesor.php?id_clase=<?= $clase['id'] ?>&id_profesor=<?= $clase['id_profesor'] ?>" class="btn-outline btn-action-full"><i class="fa-solid fa-comments"></i> Chatear para negociar precio</a>
                <a href="comprar_curso.php?id=<?= $clase['id'] ?>" class="btn-primary-action btn-action-full">Inscribirse Ahora <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>