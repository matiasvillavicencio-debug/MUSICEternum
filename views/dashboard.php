<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$rol = $_SESSION['role'];

$mis_clases = [];
$total_alumnos = 0;
$total_ingresos = 0;
$mensajes_sin_leer = 0;

if (isset($_GET['eliminar_clase']) && $rol === 'profesor') {
    $id_clase = $_GET['eliminar_clase'];
    $stmt = $pdo->prepare("DELETE FROM clases WHERE id = ? AND id_profesor = ?");
    $stmt->execute([$id_clase, $id_usuario]);
    header("Location: dashboard.php");
    exit();
}

if ($rol === 'profesor') {
    $stmt = $pdo->prepare("SELECT * FROM clases WHERE id_profesor = ? ORDER BY id DESC");
    $stmt->execute([$id_usuario]);
    $mis_clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtAlumnos = $pdo->prepare("SELECT COUNT(DISTINCT i.id_usuario) FROM inscripciones i JOIN clases c ON i.id_clase = c.id WHERE c.id_profesor = ?");
    $stmtAlumnos->execute([$id_usuario]);
    $total_alumnos = $stmtAlumnos->fetchColumn();

    $stmtIngresos = $pdo->prepare("SELECT SUM(i.total) FROM inscripciones i JOIN clases c ON i.id_clase = c.id WHERE c.id_profesor = ?");
    $stmtIngresos->execute([$id_usuario]);
    $total_ingresos = $stmtIngresos->fetchColumn() ?: 0;

    $stmtMsgs = $pdo->prepare("SELECT COUNT(*) FROM mensajes WHERE id_receptor = ? AND leido = 0");
    $stmtMsgs->execute([$id_usuario]);
    $mensajes_sin_leer = $stmtMsgs->fetchColumn();
}

require_once '../includes/header.php';
?>

<div class="dash-wrapper">
    <aside class="dash-sidebar">
        <div class="dash-sidebar-user text-center">
            <img src="<?= htmlspecialchars($avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=00B4D8&color=fff&bold=true') ?>" alt="Avatar" class="avatar-md mb-10">
            <h3 class="title-md text-blanco"><?= htmlspecialchars($_SESSION['username']) ?></h3>
            <p class="text-celeste-sm"><?= strtoupper($rol) ?></p>
        </div>
        
        <nav class="dash-nav">
            <a href="dashboard.php" class="dash-nav-item active"><i class="fa-solid fa-table-columns"></i> Panel Principal</a>
            
            <?php if ($rol === 'profesor'): ?>
                <a href="dar_clase.php" class="dash-nav-item text-danger"><i class="fa-solid fa-video"></i> Dar clase en vivo</a>
                <a href="mis_alumnos.php" class="dash-nav-item"><i class="fa-solid fa-users"></i> Mis alumnos</a>
                <a href="subir_material.php" class="dash-nav-item"><i class="fa-solid fa-file-arrow-up"></i> Subir material</a>
                <a href="mensajes.php" class="dash-nav-item"><i class="fa-solid fa-envelope"></i> Mensajes <?php if($mensajes_sin_leer > 0): ?><span class="badge-noti"><?= $mensajes_sin_leer ?></span><?php endif; ?></a>
                <a href="estadisticas.php" class="dash-nav-item"><i class="fa-solid fa-chart-line"></i> Estadísticas e ingresos</a>
            <?php elseif ($rol === 'alumno'): ?>
                <a href="mis_cursos.php" class="dash-nav-item"><i class="fa-solid fa-book"></i> Mis cursos</a>
                <a href="calificaciones.php" class="dash-nav-item"><i class="fa-solid fa-star"></i> Calificaciones</a>
                <a href="material_estudio.php" class="dash-nav-item"><i class="fa-solid fa-folder-open"></i> Material de estudio</a>
            <?php elseif ($rol === 'espectador'): ?>
                <a href="mis_entradas.php" class="dash-nav-item"><i class="fa-solid fa-ticket"></i> Mis entradas</a>
            <?php endif; ?>
            
            <a href="ajustes.php" class="dash-nav-item"><i class="fa-solid fa-gear"></i> Configuración</a>
        </nav>
    </aside>

    <main class="dash-main">
        <?php if ($rol === 'profesor'): ?>
            <div class="flex-between mb-20">
                <h2 class="title-lg mb-0"><i class="fa-solid fa-chalkboard-user text-danger"></i> Resumen</h2>
                <div class="flex-gap-15">
                    <a href="crear_clase.php" class="btn-confirm-bright-green"><i class="fa-solid fa-plus"></i> Publicar curso</a>
                </div>
            </div>

            <div class="grid-3 mb-30">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
                    <div class="stat-info">
                        <h3><?= count($mis_clases) ?></h3>
                        <p>Clases publicadas</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_alumnos ?></h3>
                        <p>Alumnos inscritos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-success-bold"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="stat-info">
                        <h3>$<?= number_format($total_ingresos, 2) ?></h3>
                        <p>Ingresos Brutos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-danger"><i class="fa-solid fa-envelope"></i></div>
                    <div class="stat-info">
                        <h3><?= $mensajes_sin_leer ?></h3>
                        <p>Mensajes sin leer</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                    <div class="stat-info">
                        <h3>5.0</h3>
                        <p>Calificación media</p>
                    </div>
                </div>
            </div>

            <div class="container-box w-100 bg-transparent">
                <h3 class="title-md mb-20">Mis Clases</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título del curso</th>
                            <th>Modalidad</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mis_clases as $clase): ?>
                        <tr>
                            <td>#<?= $clase['id'] ?></td>
                            <td><strong><?= htmlspecialchars($clase['titulo']) ?></strong></td>
                            <td><span class="badge badge-modalidad"><?= strtoupper(htmlspecialchars($clase['modalidad'])) ?></span></td>
                            <td class="font-bold text-success-bold">$<?= number_format($clase['precio'], 2) ?></td>
                            <td class="action-links">
                                <a href="editar_clase.php?id=<?= $clase['id'] ?>" class="text-muted" title="Modificar"><i class="fa-solid fa-pen"></i></a>
                                <a href="dashboard.php?eliminar_clase=<?= $clase['id'] ?>" class="text-danger ml-15" onclick="return confirm('¿Estás seguro de que deseas retirar este curso de la cartelera de Eternum?')" title="Eliminar"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($mis_clases)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted p-15">Aún no has publicado ninguna clase en la cartelera. Selecciona "Publicar curso" para empezar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="flex-between mb-20">
                <h2 class="title-lg mb-0">Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?></h2>
            </div>
            <div class="container-box text-center">
                <p class="text-muted p-15 mb-20">Navega utilizando el menú lateral izquierdo para acceder a todas las funciones de tu cuenta.</p>
                <?php if ($rol === 'alumno'): ?>
                    <a href="mis_cursos.php" class="btn-action-orange">Ir a Mis cursos</a>
                <?php elseif ($rol === 'espectador'): ?>
                    <a href="mis_entradas.php" class="btn-action-orange">Ver Mis tickets</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>