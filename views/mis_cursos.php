<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['role'] !== 'alumno') {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT c.*, u.username as profesor_nombre 
    FROM clases c 
    JOIN usuarios u ON c.id_profesor = u.id
");
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-graduation-cap text-danger"></i> Mis Cursos Activos</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="w-100">
        <?php foreach ($cursos as $curso): ?>
            <div class="material-card border-left-alumno">
                <div class="material-info">
                    <h3><?= htmlspecialchars($curso['titulo']) ?></h3>
                    <p>Profesor: <strong><?= htmlspecialchars($curso['profesor_nombre']) ?></strong> | Modalidad: <?= strtoupper(htmlspecialchars($curso['modalidad'])) ?></p>
                    <p class="text-success mt-10"><i class="fa-solid fa-play"></i> En curso actualmente</p>
                </div>
                <a href="material_estudio.php" class="btn-action-orange text-decoration-none">
                    <i class="fa-solid fa-folder-open"></i> Ver Aula
                </a>
            </div>
        <?php endforeach; ?>

        <?php if (empty($cursos)): ?>
            <div class="container-box text-center">
                <p class="text-muted p-15">Aún no estás inscrito en ningún curso o academia de Eternum.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>