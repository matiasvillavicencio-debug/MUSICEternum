<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/header.php';
?>
<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-heart text-danger"></i> Tus Artistas Favoritos</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    <div class="grid-3">
        <div class="artist-card">
            <div class="artist-cover"><i class="fa-solid fa-fire"></i></div>
            <div class="artist-info">
                <h3 class="artist-name">Imagine Dragons</h3>
                <p class="text-muted mb-10">Banda de Rock / Pop Alternativo</p>
                <button class="btn-action-orange w-100">Ver Próximos Shows</button>
            </div>
        </div>
        <div class="artist-card">
            <div class="artist-cover"><i class="fa-solid fa-music"></i></div>
            <div class="artist-info">
                <h3 class="artist-name">Ed Sheeran</h3>
                <p class="text-muted mb-10">Solista Pop / Acústico</p>
                <button class="btn-action-orange w-100">Ver Próximos Shows</button>
            </div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>