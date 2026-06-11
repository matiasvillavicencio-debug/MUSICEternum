<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'artista') {
    header("Location: ../index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id_artista = ? ORDER BY fecha ASC");
$stmt->execute([$_SESSION['usuario_id']]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>
<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-microphone-lines text-danger"></i> Mis Presentaciones</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    <table class="admin-table container-box w-100">
        <thead>
            <tr>
                <th>Fecha de Show</th>
                <th>Nombre del Concierto</th>
                <th>Escenario</th>
                <th>Estado de Cartelera</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $ev): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($ev['fecha'])) ?></td>
                <td><strong><?= htmlspecialchars($ev['titulo']) ?></strong></td>
                <td><?= htmlspecialchars($ev['lugar']) ?></td>
                <td><span class="status-success">Confirmado</span></td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($eventos)): ?>
            <tr>
                <td colspan="4" class="text-center text-muted p-15">Aún no has creado ningún evento. <a href="crear_evento.php" class="text-danger">Añadir uno ahora</a>.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
<?php require_once '../includes/footer.php'; ?>