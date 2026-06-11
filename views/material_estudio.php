<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT m.*, u.username AS profesor_nombre 
    FROM materiales m 
    JOIN usuarios u ON m.id_profesor = u.id 
    ORDER BY m.fecha_subida DESC
");
$materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-folder-open text-danger"></i> Material de Estudio</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <div class="w-100">
        <?php foreach ($materiales as $mat): ?>
            <div class="material-card">
                <div class="material-info">
                    <h3><?= htmlspecialchars($mat['titulo']) ?></h3>
                    <p>Curso: <strong><?= htmlspecialchars($mat['curso']) ?></strong> | Subido por: <?= htmlspecialchars($mat['profesor_nombre']) ?></p>
                    <p><i class="fa-regular fa-clock"></i> <?= date('d/m/Y', strtotime($mat['fecha_subida'])) ?></p>
                </div>
                <a href="../uploads/<?= htmlspecialchars($mat['archivo']) ?>" download class="btn-download">
                    <i class="fa-solid fa-download"></i> Descargar
                </a>
            </div>
        <?php endforeach; ?>

        <?php if (empty($materiales)): ?>
            <div class="container-box text-center">
                <p class="text-muted p-15">Aún no hay material de estudio disponible en la plataforma.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>