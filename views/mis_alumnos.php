<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/header.php';

?>

<section class="container-box-lg mt-30">
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
                    <th>Progreso</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="flex-center-gap">
                            <img src="https://ui-avatars.com/api/?name=Lucas+M&background=E2E8F0" alt="Avatar" class="avatar-sm">
                            <span>Lucas M.</span>
                        </div>
                    </td>
                    <td>Producción musical básica</td>
                    <td><span class="status-success">65% Completado</span></td>
                    <td><button class="btn-action-orange p-15"><i class="fa-solid fa-envelope"></i> Contactar</button></td>
                </tr>
                <tr>
                    <td>
                        <div class="flex-center-gap">
                            <img src="https://ui-avatars.com/api/?name=Sofia+P&background=E2E8F0" alt="Avatar" class="avatar-sm">
                            <span>Sofía P.</span>
                        </div>
                    </td>
                    <td>Teoría Musical Aplicada</td>
                    <td><span class="status-pending">10% Completado</span></td>
                    <td><button class="btn-action-orange p-15"><i class="fa-solid fa-envelope"></i> Contactar</button></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>