<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/header.php';
?>
<section class="container-box mx-auto max-w-600 mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-md"><i class="fa-solid fa-bell text-danger"></i> Buzón de Notificaciones</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    <div class="notif-item">
        <div class="notif-icon bg-blue"><i class="fa-solid fa-shield-check"></i></div>
        <div class="notif-content">
            <h4>Alerta de Seguridad</h4>
            <p>Se ha registrado un nuevo inicio de sesión en tu cuenta de Eternum MUSIC desde un dispositivo desconocido.</p>
            <div class="notif-time">Hace 15 minutos</div>
        </div>
    </div>
    <div class="notif-item">
        <div class="notif-icon bg-orange"><i class="fa-solid fa-bolt"></i></div>
        <div class="notif-content">
            <h4>Actualización de Plataforma</h4>
            <p>Hemos renovado la interfaz gráfica e incluido el nuevo sistema de butacas interactivas.</p>
            <div class="notif-time">Hace 2 días</div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>