<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['artista', 'profesor'])) {
    header("Location: ../index.php");
    exit();
}

$rol = $_SESSION['role'];
$id_usuario = $_SESSION['usuario_id'];

if ($rol === 'profesor') {
    $stmtIngresos = $pdo->prepare("SELECT SUM(i.total) FROM inscripciones i JOIN clases c ON i.id_clase = c.id WHERE c.id_profesor = ?");
    $stmtIngresos->execute([$id_usuario]);
    $total_ingresos = $stmtIngresos->fetchColumn() ?: 0;

    $stmtAlumnos = $pdo->prepare("SELECT COUNT(DISTINCT i.id_usuario) FROM inscripciones i JOIN clases c ON i.id_clase = c.id WHERE c.id_profesor = ?");
    $stmtAlumnos->execute([$id_usuario]);
    $total_alumnos_unicos = $stmtAlumnos->fetchColumn();

    $stmtCursos = $pdo->prepare("SELECT COUNT(*) FROM clases WHERE id_profesor = ?");
    $stmtCursos->execute([$id_usuario]);
    $total_cursos = $stmtCursos->fetchColumn();

    $stmtPopulares = $pdo->prepare("SELECT c.titulo, COUNT(i.id) as cantidad_inscritos, SUM(i.total) as ingresos_clase FROM clases c LEFT JOIN inscripciones i ON c.id = i.id_clase WHERE c.id_profesor = ? GROUP BY c.id ORDER BY cantidad_inscritos DESC");
    $stmtPopulares->execute([$id_usuario]);
    $clases_populares = $stmtPopulares->fetchAll(PDO::FETCH_ASSOC);
}

require_once '../includes/header.php';
?>

<?php if ($rol === 'profesor'): ?>
    <div class="dash-wrapper">
        <aside class="dash-sidebar">
            <div class="dash-sidebar-user text-center">
                <img src="<?= htmlspecialchars($avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=00B4D8&color=fff&bold=true') ?>" alt="Avatar" class="avatar-md mb-10">
                <h3 class="title-md text-blanco"><?= htmlspecialchars($_SESSION['username']) ?></h3>
                <p class="text-celeste-sm">PROFESOR</p>
            </div>
            <nav class="dash-nav">
                <a href="dashboard.php" class="dash-nav-item"><i class="fa-solid fa-table-columns"></i> Panel principal</a>
                <a href="crear_clase.php" class="dash-nav-item text-success-bold"><i class="fa-solid fa-plus"></i> Crear nueva clase</a>
                <a href="mis_alumnos.php" class="dash-nav-item"><i class="fa-solid fa-users"></i> Mis alumnos</a>
                <a href="subir_material.php" class="dash-nav-item"><i class="fa-solid fa-file-arrow-up"></i> Subir material</a>
                <a href="mensajes.php" class="dash-nav-item"><i class="fa-solid fa-envelope"></i> Bandeja de entrada</a>
                <a href="estadisticas.php" class="dash-nav-item active"><i class="fa-solid fa-chart-line"></i> Estadísticas</a>
                <a href="ajustes.php" class="dash-nav-item"><i class="fa-solid fa-gear"></i> Configuración</a>
            </nav>
        </aside>
        <main class="dash-main">
            <div class="flex-between mb-20">
                <h2 class="title-lg mb-0"><i class="fa-solid fa-chart-pie text-danger"></i> Rendimiento financiero y académico</h2>
            </div>
            
            <div class="grid-3 mb-30">
                <div class="stat-card">
                    <div class="stat-icon text-success-bold"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div class="stat-info">
                        <h3>$<?= number_format($total_ingresos, 2) ?></h3>
                        <p>Ingresos brutos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-celeste-sm"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_alumnos_unicos ?></h3>
                        <p>Alumnos únicos activos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon text-danger"><i class="fa-solid fa-book"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_cursos ?></h3>
                        <p>Cursos publicados</p>
                    </div>
                </div>
            </div>

            <div class="container-box w-100 bg-transparent">
                <h3 class="title-md mb-20">Rendimiento detallado por curso</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Título del curso</th>
                            <th>Total de inscritos</th>
                            <th>Ingresos generados</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clases_populares as $clase): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($clase['titulo']) ?></strong></td>
                            <td><?= $clase['cantidad_inscritos'] ?> alumnos</td>
                            <td class="text-success-bold font-bold">$<?= number_format($clase['ingresos_clase'] ?: 0, 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($clases_populares)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted p-15">No tienes estadísticas disponibles todavía.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

<?php elseif ($rol === 'artista'): ?>
    <section class="container-box-lg mt-30">
        <div class="flex-between mb-20">
            <h2 class="title-lg"><i class="fa-solid fa-chart-pie text-danger"></i> Analítica de Ventas</h2>
            <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
        </div>
        <div class="grid-3">
            <div class="stat-card">
                <i class="fa-solid fa-ticket stat-icon"></i>
                <div class="stat-number">1,240</div>
                <div class="stat-label">Tickets vendidos (Mes)</div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-wallet stat-icon"></i>
                <div class="stat-number">$845k</div>
                <div class="stat-label">Ingresos brutos estimados</div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-eye stat-icon"></i>
                <div class="stat-number">15k</div>
                <div class="stat-label">Visitas a tu perfil artístico</div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>