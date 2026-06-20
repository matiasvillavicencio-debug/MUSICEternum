<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['aprobar_compra'])) {
    $id_compra_aprobar = $_GET['aprobar_compra'];
    $stmtAprobar = $pdo->prepare("UPDATE compras SET estado = 'aprobado' WHERE id = ?");
    $stmtAprobar->execute([$id_compra_aprobar]);
    header("Location: index.php");
    exit();
}

$eventos = $pdo->query("SELECT * FROM eventos")->fetchAll(PDO::FETCH_ASSOC);
$clases = $pdo->query("SELECT * FROM clases")->fetchAll(PDO::FETCH_ASSOC);

$stmtPendientes = $pdo->query("SELECT c.*, u.username, e.titulo as evento_titulo FROM compras c JOIN usuarios u ON c.id_usuario = u.id JOIN eventos e ON c.id_evento = e.id WHERE c.estado = 'pendiente' ORDER BY c.fecha_compra DESC");
$compras_pendientes = $stmtPendientes->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg">
    <div class="flex-between mt-30 mb-20">
        <h2 class="title-lg mb-0">Panel de control - Administrador</h2>
        <div class="flex-gap-15">
            <button class="btn-confirm-bright-green" onclick="window.location.href='crear_evento.php'">+ Añadir evento</button>
            <button class="btn-action-orange" onclick="window.location.href='crear_clase.php'">+ Añadir clase</button>
        </div>
    </div>

    <div class="container-box w-100 bg-transparent mb-40">
        <h3 class="title-md mb-20"><i class="fa-solid fa-clock-rotate-left text-danger"></i> Entradas pendientes de pago</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Código ticket</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Método</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras_pendientes as $pendiente): ?>
                <tr>
                    <td><strong><?= htmlspecialchars(substr($pendiente['codigo_qr'], 0, 15)) ?>...</strong></td>
                    <td><?= htmlspecialchars($pendiente['username']) ?></td>
                    <td><?= htmlspecialchars($pendiente['evento_titulo']) ?></td>
                    <td><span class="badge badge-pendiente"><?= strtoupper(htmlspecialchars($pendiente['metodo_pago'])) ?></span></td>
                    <td class="font-bold text-success-bold">$<?= number_format($pendiente['total'], 2) ?></td>
                    <td>
                        <a href="index.php?aprobar_compra=<?= $pendiente['id'] ?>" class="btn-confirm-bright-green text-decoration-none" onclick="return confirm('¿Confirmas que recibiste el pago de esta entrada y deseas aprobarla?')"><i class="fa-solid fa-check"></i> Aprobar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($compras_pendientes)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted p-15">Todas las compras han sido procesadas. No hay tickets pendientes.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="flex-container-50-50 bg-transparent">
        <div class="flex-col container-box w-100">
            <h3 class="title-md"><i class="fa-solid fa-ticket"></i> Todos los Eventos</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($eventos as $ev): ?>
                    <tr>
                        <td>#<?= $ev['id'] ?></td>
                        <td><?= htmlspecialchars($ev['titulo']) ?></td>
                        <td>$<?= number_format($ev['precio'], 2) ?></td>
                        <td class="action-links">
                            <a href="editar_evento.php?id=<?= $ev['id'] ?>"><i class="fa-solid fa-pen text-muted"></i></a>
                            <a href="../includes/eliminar.php?tipo=evento&id=<?= $ev['id'] ?>" class="text-danger"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex-col container-box w-100">
            <h3 class="title-md"><i class="fa-solid fa-book"></i> Todas las clases</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Modalidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clases as $cl): ?>
                    <tr>
                        <td>#<?= $cl['id'] ?></td>
                        <td><?= htmlspecialchars($cl['titulo']) ?></td>
                        <td><span class="badge badge-modalidad"><?= strtoupper(htmlspecialchars($cl['modalidad'])) ?></span></td>
                        <td class="action-links">
                            <a href="editar_clase.php?id=<?= $cl['id'] ?>"><i class="fa-solid fa-pen text-muted"></i></a>
                            <a href="../includes/eliminar.php?tipo=clase&id=<?= $cl['id'] ?>" class="text-danger"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>