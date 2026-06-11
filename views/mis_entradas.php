<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT c.*, e.titulo, e.lugar, e.fecha 
    FROM compras c 
    JOIN eventos e ON c.id_evento = e.id 
    WHERE c.id_usuario = ? 
    ORDER BY c.fecha_compra DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-lg"><i class="fa-solid fa-ticket text-danger"></i> Mis E-Tickets</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver al panel</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'compra_exitosa'): ?>
        <div class="stage bg-blue mb-20 border-radius-10">¡TRANSACCIÓN APROBADA! TUS ENTRADAS ESTÁN LISTAS PARA USAR.</div>
    <?php endif; ?>

    <div class="w-100">
        <?php foreach ($entradas as $entrada): ?>
            <div class="ticket-card" onclick="mostrarQR('<?= htmlspecialchars($entrada['codigo_qr']) ?>', '<?= htmlspecialchars($entrada['titulo']) ?>')">
                <div class="ticket-info">
                    <h3><?= htmlspecialchars($entrada['titulo']) ?></h3>
                    <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($entrada['lugar']) ?> | <i class="fa-regular fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($entrada['fecha'])) ?></p>
                    <p class="text-success mt-10"><strong><?= $entrada['cantidad_asientos'] ?> Asiento(s) Reservado(s)</strong></p>
                </div>
                <div class="text-center">
                    <i class="fa-solid fa-qrcode fa-3x text-muted"></i>
                    <p class="text-muted mt-10 text-sm">Tocar para escanear</p>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($entradas)): ?>
            <div class="container-box text-center">
                <p class="text-muted p-15">No tienes tickets adquiridos. ¡Explora la cartelera para no perderte nada!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="qr-modal" id="qrModal">
    <div class="qr-content">
        <h3 class="title-md text-center-danger" id="qrEventTitle"></h3>
        <p class="text-muted">Presenta este código en la puerta del recinto.</p>
        <img src="" alt="Código QR" class="qr-img" id="qrImage">
        <p class="text-muted font-bold" id="qrCodeText"></p>
        <button class="btn-close-qr" onclick="cerrarQR()">Cerrar Ticket</button>
    </div>
</div>

<script>
    function mostrarQR(codigo, titulo) {
        document.getElementById('qrEventTitle').innerText = titulo;
        document.getElementById('qrImage').src = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(codigo);
        document.getElementById('qrCodeText').innerText = "ID: " + codigo;
        document.getElementById('qrModal').classList.add('active');
    }

    function cerrarQR() {
        document.getElementById('qrModal').classList.remove('active');
    }

    document.getElementById('qrModal').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarQR();
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>