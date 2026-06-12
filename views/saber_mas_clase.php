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

$aprenderas_list = array_filter(array_map('trim', explode("\n", $clase['que_aprenderas'] ?? '')));
$requisitos_list = array_filter(array_map('trim', explode("\n", $clase['requisitos'] ?? '')));
$modulos_list = array_filter(array_map('trim', explode("\n", $clase['modulos'] ?? '')));

require_once '../includes/header.php';
?>

<div class="course-hero">
    <div class="course-hero-content">
        <div>
            <div class="badges-container">
                <span class="badge badge-modalidad"><i class="fa-solid fa-laptop"></i> <?= strtoupper(htmlspecialchars($clase['modalidad'])) ?></span>
                <span class="badge badge-nivel"><i class="fa-solid fa-signal"></i> <?= strtoupper(htmlspecialchars($clase['nivel'])) ?></span>
            </div>
            <h1 class="course-title"><?= htmlspecialchars($clase['titulo']) ?></h1>
            <p class="course-subtitle"><?= htmlspecialchars($clase['descripcion']) ?></p>
            <div class="course-meta">
                <span><i class="fa-solid fa-chalkboard-user"></i> Creado por <strong><?= htmlspecialchars($clase['profesor_nombre']) ?></strong></span>
                <span><i class="fa-solid fa-language"></i> Español</span>
                <span><i class="fa-solid fa-certificate"></i> Certificado de finalización</span>
            </div>
        </div>
        
        <div class="floating-buy-card">
            <div class="price-tag-large">$<?= number_format($clase['precio'], 2) ?></div>
            
            <a href="comprar_curso.php?id=<?= $clase['id'] ?>" class="btn-primary-action w-100 mb-15">Inscribirse Ahora</a>
            
            <?php if(isset($_SESSION['usuario_id']) && $_SESSION['role'] === 'alumno'): ?>
                <a href="contacto_profesor.php?id_clase=<?= $clase['id'] ?>&id_profesor=<?= $clase['id_profesor'] ?>" class="btn-outline w-100"><i class="fa-solid fa-comments"></i> Chatear con el Profesor</a>
            <?php endif; ?>
            
            <p class="guarantee-text"><i class="fa-solid fa-shield-halved"></i> Garantía de reembolso de 30 días</p>
        </div>
    </div>
</div>

<section class="course-main-content">
    <div>
        <div class="learn-box">
            <h2 class="course-section-title">Lo que aprenderás</h2>
            <div class="learn-grid">
                <?php foreach ($aprenderas_list as $item): ?>
                    <div class="learn-item"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($item) ?></div>
                <?php endforeach; ?>
                
                <?php if(empty($aprenderas_list)): ?>
                    <div class="learn-item text-muted">La información de esta sección está siendo actualizada por el profesor.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="syllabus-container">
            <h2 class="course-section-title">Contenido del curso</h2>
            
            <?php foreach ($modulos_list as $index => $modulo): ?>
                <div class="syllabus-item">
                    <div class="syllabus-header">
                        <span><i class="fa-solid fa-book-open"></i> Módulo <?= $index + 1 ?></span>
                    </div>
                    <div class="syllabus-body">
                        <p><?= htmlspecialchars($modulo) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if(empty($modulos_list)): ?>
                <p class="text-muted p-15">El temario detallado estará disponible próximamente.</p>
            <?php endif; ?>
        </div>

        <div class="learn-box">
            <h2 class="course-section-title">Requisitos previos</h2>
            <ul class="bullet-list ml-15">
                <?php foreach ($requisitos_list as $req): ?>
                    <li><?= htmlspecialchars($req) ?></li>
                <?php endforeach; ?>
                
                <?php if(empty($requisitos_list)): ?>
                    <li class="text-muted" style="list-style: none;">No hay requisitos previos especificados.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div></div>
</section>

<?php require_once '../includes/footer.php'; ?>