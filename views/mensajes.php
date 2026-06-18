<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

$id_profesor = $_SESSION['usuario_id'];

$sql = "
    SELECT u.id as id_alumno, u.username as alumno_nombre, c.id as id_clase, c.titulo as clase_titulo,
           MAX(m.fecha_envio) as ultimo_mensaje_fecha,
           SUM(CASE WHEN m.id_receptor = ? AND m.leido = 0 THEN 1 ELSE 0 END) as sin_leer
    FROM mensajes m
    JOIN usuarios u ON (m.id_emisor = u.id OR m.id_receptor = u.id)
    JOIN clases c ON m.id_clase = c.id
    WHERE c.id_profesor = ? AND u.id != ?
    GROUP BY u.id, c.id, u.username, c.titulo
    ORDER BY ultimo_mensaje_fecha DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_profesor, $id_profesor, $id_profesor]);
$conversaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="dash-wrapper">
    <aside class="dash-sidebar">
        <div class="dash-sidebar-user text-center">
            <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Avatar" class="avatar-md mb-10">
            <h3 class="title-md text-blanco"><?= htmlspecialchars($_SESSION['username']) ?></h3>
            <p class="text-celeste-sm">PROFESOR</p>
        </div>
        <nav class="dash-nav">
            <a href="dashboard.php" class="dash-nav-item"><i class="fa-solid fa-table-columns"></i> Panel Principal</a>
            <a href="crear_clase.php" class="dash-nav-item text-success-bold"><i class="fa-solid fa-plus"></i> Crear Nueva Clase</a>
            <a href="mis_alumnos.php" class="dash-nav-item"><i class="fa-solid fa-users"></i> Mis Alumnos</a>
            <a href="subir_material.php" class="dash-nav-item"><i class="fa-solid fa-file-arrow-up"></i> Subir Material</a>
            <a href="mensajes.php" class="dash-nav-item active"><i class="fa-solid fa-envelope"></i> Bandeja de Entrada</a>
            <a href="estadisticas.php" class="dash-nav-item"><i class="fa-solid fa-chart-line"></i> Estadísticas</a>
            <a href="ajustes.php" class="dash-nav-item"><i class="fa-solid fa-gear"></i> Configuración</a>
        </nav>
    </aside>
    <main class="dash-main">
        <div class="flex-between mb-20">
            <h2 class="title-lg mb-0"><i class="fa-solid fa-envelope text-danger"></i> Mensajes Recientes</h2>
        </div>
        
        <div class="container-box w-100 bg-transparent">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Curso Impartido</th>
                        <th>Último Mensaje</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conversaciones as $conv): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($conv['alumno_nombre']) ?></strong></td>
                        <td><?= htmlspecialchars($conv['clase_titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($conv['ultimo_mensaje_fecha'])) ?></td>
                        <td>
                            <?php if ($conv['sin_leer'] > 0): ?>
                                <span class="badge badge-modalidad" style="background: var(--rojo-pasion); color: white;"><?= $conv['sin_leer'] ?> nuevos</span>
                            <?php else: ?>
                                <span class="text-muted">Al día</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="contacto_profesor.php?id_clase=<?= $conv['id_clase'] ?>&id_alumno=<?= $conv['id_alumno'] ?>" class="btn-outline">Abrir Chat</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($conversaciones)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted p-15">Tu bandeja de entrada está vacía.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php require_once '../includes/footer.php'; ?>