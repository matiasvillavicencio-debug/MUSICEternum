<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

$id_profesor = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT i.*, u.username as alumno_nombre, u.id as id_alumno, c.titulo as clase_titulo, c.id as id_clase FROM inscripciones i JOIN usuarios u ON i.id_usuario = u.id JOIN clases c ON i.id_clase = c.id WHERE c.id_profesor = ? ORDER BY i.fecha_inscripcion DESC");
$stmt->execute([$id_profesor]);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-users text-danger"></i> Mis Alumnos Activos</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <div class="container-box w-100 bg-transparent">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Curso Inscrito</th>
                    <th>Fecha de Ingreso</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                <tr>
                    <td>
                        <div class="flex-center-gap">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($alumno['alumno_nombre']) ?>&background=E2E8F0" alt="Avatar" class="avatar-sm">
                            <span><?= htmlspecialchars($alumno['alumno_nombre']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($alumno['clase_titulo']) ?></td>
                    <td><?= date('d/m/Y', strtotime($alumno['fecha_inscripcion'])) ?></td>
                    <td>
                        <a href="contacto_profesor.php?id_clase=<?= $alumno['id_clase'] ?>&id_alumno=<?= $alumno['id_alumno'] ?>" class="btn-action-orange p-15 text-decoration-none" style="display: inline-block;">
                            <i class="fa-solid fa-comments"></i> Chat / Ofertar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($alumnos)): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted p-15">Aún no tienes alumnos inscritos o el administrador no ha vinculado alumnos manualmente en la base de datos para pruebas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>