<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['role'] !== 'alumno') {
    header("Location: login.php");
    exit();
}
require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-check-double text-danger"></i> Mis Calificaciones</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <table class="admin-table container-box w-100">
        <thead>
            <tr>
                <th>Fecha de Evaluación</th>
                <th>Módulo / Examen</th>
                <th>Calificación Final</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= date('d/m/Y', strtotime('-5 days')) ?></td>
                <td>Producción musical básica - Proyecto Final</td>
                <td class="font-oswald-24 text-success-bold">95/100</td>
                <td><span class="status-success">Aprobado</span></td>
            </tr>
            <tr>
                <td><?= date('d/m/Y', strtotime('-15 days')) ?></td>
                <td>Teoría Musical Aplicada - Parcial Práctico</td>
                <td class="font-oswald-24 text-success-bold">75/100</td>
                <td><span class="status-success">Aprobado</span></td>
            </tr>
            <tr>
                <td><?= date('d/m/Y', strtotime('-30 days')) ?></td>
                <td>Mezcla y Mastering - Cuestionario Teórico</td>
                <td class="font-oswald-24 text-danger-bold">40/100</td>
                <td><span class="status-pending">Recuperatorio Pendiente</span></td>
            </tr>
        </tbody>
    </table>
</section>

<?php require_once '../includes/footer.php'; ?>