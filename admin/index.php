<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$eventos = $pdo->query("SELECT * FROM eventos")->fetchAll(PDO::FETCH_ASSOC);
$clases = $pdo->query("SELECT * FROM clases")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg">
    <div class="flex-between mt-30 mb-20">
        <h2 class="title-lg" style="margin:0;">Control de Servidor</h2>
        <button class="btn-confirm-bright-green" onclick="window.location.href='/MUSICEternum/views/crear_evento.php'">+ Añadir Evento Manual</button>
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
            <h3 class="title-md"><i class="fa-solid fa-book"></i> Todas las Clases</h3>
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
                        <td><?= strtoupper($cl['modalidad']) ?></td>
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