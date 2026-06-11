<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/header.php';

$role = $_SESSION['role'];
$username = $_SESSION['username'];
?>

<div class="dash-wrapper">
    <aside class="dash-sidebar">
        <div class="dash-sidebar-user">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($username) ?>&background=00B4D8&color=fff" alt="Avatar">
            <h3 class="title-md mb-5"><?= htmlspecialchars($username) ?></h3>
            <p class="text-muted font-bold"><?= strtoupper($role) ?></p>
        </div>

        <a href="dashboard.php" class="dash-nav-item active"><i class="fa-solid fa-house"></i> Resumen General</a>
        <a href="notificaciones.php" class="dash-nav-item"><i class="fa-solid fa-bell"></i> Notificaciones</a>

        <?php if ($role === 'profesor'): ?>
            <a href="dar_clase.php" class="dash-nav-item"><i class="fa-solid fa-video"></i> Transmitir en Vivo</a>
            <a href="subir_material.php" class="dash-nav-item"><i class="fa-solid fa-upload"></i> Subir Material</a>
            <a href="mis_alumnos.php" class="dash-nav-item"><i class="fa-solid fa-users"></i> Ver mis Alumnos</a>

        <?php elseif ($role === 'artista'): ?>
            <a href="crear_evento.php" class="dash-nav-item"><i class="fa-regular fa-calendar-plus"></i> Crear Nuevo Evento</a>
            <a href="mis_presentaciones.php" class="dash-nav-item"><i class="fa-solid fa-microphone-lines"></i> Mis Presentaciones</a>
            <a href="estadisticas.php" class="dash-nav-item"><i class="fa-solid fa-chart-pie"></i> Estadísticas de Venta</a>

        <?php elseif ($role === 'alumno'): ?>
            <a href="mis_cursos.php" class="dash-nav-item"><i class="fa-solid fa-graduation-cap"></i> Mis Cursos Activos</a>
            <a href="material_estudio.php" class="dash-nav-item"><i class="fa-solid fa-folder-open"></i> Material de Estudio</a>
            <a href="calificaciones.php" class="dash-nav-item"><i class="fa-solid fa-check-double"></i> Mis Calificaciones</a>

        <?php elseif ($role === 'espectador'): ?>
            <a href="mis_entradas.php" class="dash-nav-item"><i class="fa-solid fa-ticket"></i> Mis E-Tickets</a>
            <a href="artistas_favoritos.php" class="dash-nav-item"><i class="fa-solid fa-heart"></i> Artistas Favoritos</a>
            <a href="historial_compras.php" class="dash-nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Historial de Compras</a>
        <?php endif; ?>

        <a href="ajustes.php" class="dash-nav-item"><i class="fa-solid fa-gear"></i> Ajustes de Cuenta</a>
        <a href="../includes/logout.php" class="dash-nav-item text-danger mt-30"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </aside>

    <main class="dash-main">
        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'compra_exitosa'): ?>
            <div class="stage" style="background: var(--verde-confirmar); margin-bottom: 20px; border-radius: 10px;">¡PAGO PROCESADO CON ÉXITO! TUS ENTRADAS ESTÁN LISTAS.</div>
        <?php endif; ?>

        <h2 class="title-lg">¡Bienvenido de nuevo, <?= htmlspecialchars($username) ?>!</h2>
        
        <div class="dash-grid">
            <?php if ($role === 'profesor'): ?>
                <div class="dash-card w-100 border-left-profesor">
                    <div class="dash-card-header">
                        <h3 class="title-md"><i class="fa-solid fa-chalkboard-user"></i> Tu Próxima Clase</h3>
                        <button class="btn-confirm-bright-green btn-sm" onclick="window.location.href='dar_clase.php'">Iniciar Ahora</button>
                    </div>
                    <p class="text-muted">Tienes 45 alumnos inscritos activos este mes.</p>
                </div>
            <?php endif; ?>

            <?php if ($role === 'artista'): ?>
                <div class="dash-card w-100 border-left-artista">
                    <div class="dash-card-header">
                        <h3 class="title-md"><i class="fa-solid fa-fire"></i> Rendimiento de Eventos</h3>
                        <a href="crear_evento.php" class="text-celeste-sm">+ Añadir Evento</a>
                    </div>
                    <p class="text-muted">Has vendido 120 entradas en los últimos 7 días.</p>
                </div>
            <?php endif; ?>

            <?php if ($role === 'alumno'): ?>
                <div class="dash-card w-100 border-left-alumno">
                    <div class="dash-card-header">
                        <h3 class="title-md"><i class="fa-solid fa-spinner"></i> Progreso de Aprendizaje</h3>
                        <a href="mis_cursos.php" class="text-muted">Ir al aula virtual</a>
                    </div>
                    <p class="text-muted">Estás al 65% del curso "Producción Musical Básica".</p>
                </div>
            <?php endif; ?>

            <?php if ($role === 'espectador'): ?>
                <div class="dash-card w-100 border-left-espectador">
                    <div class="dash-card-header">
                        <h3 class="title-md"><i class="fa-solid fa-qrcode"></i> Entradas Próximas</h3>
                        <a href="mis_entradas.php" class="text-muted">Ver todas</a>
                    </div>
                    <p class="text-muted">Explora la cartelera para adquirir asientos exclusivos.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>