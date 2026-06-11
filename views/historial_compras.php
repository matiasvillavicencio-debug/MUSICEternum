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
        <h2 class="title-lg"><i class="fa-solid fa-clock-rotate-left text-danger"></i> Historial de Transacciones</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    <table class="admin-table container-box w-100">
        <thead>
            <tr>
                <th>Fecha de Compra</th>
                <th>Concepto / Detalle</th>
                <th>Total Facturado</th>
                <th>Estado del Pago</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= date('d/m/Y') ?></td>
                <td>3x Butacas - Concierto Principal</td>
                <td>$3,600.00</td>
                <td><span class="status-success">Aprobado</span></td>
            </tr>
        </tbody>
    </table>
</section>
<?php require_once '../includes/footer.php'; ?>