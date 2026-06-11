<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'artista') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/header.php';

?>
<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-chart-pie text-danger"></i> Analítica de Ventas</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    <div class="grid-3">
        <div class="stat-card">
            <i class="fa-solid fa-ticket stat-icon"></i>
            <div class="stat-number">1,240</div>
            <div class="stat-label">Tickets Vendidos (Mes)</div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-wallet stat-icon"></i>
            <div class="stat-number">$845k</div>
            <div class="stat-label">Ingresos Brutos Estimados</div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-eye stat-icon"></i>
            <div class="stat-number">15k</div>
            <div class="stat-label">Visitas a tu Perfil Artístico</div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>