<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_clase = $_GET['id_clase'] ?? null;
$id_profesor = $_GET['id_profesor'] ?? null;
$id_alumno = $_GET['id_alumno'] ?? null;

$es_profesor = ($_SESSION['role'] === 'profesor');
$mi_id = $_SESSION['usuario_id'];
$id_otro = $es_profesor ? $id_alumno : $id_profesor;

if (!$id_clase || !$id_otro) {
    header("Location: cartelera.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mensaje']) && !empty(trim($_POST['mensaje']))) {
        $mensaje = trim($_POST['mensaje']);
        $sql = "INSERT INTO mensajes (id_clase, id_emisor, id_receptor, mensaje) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_clase, $mi_id, $id_otro, $mensaje]);
    }
    
    if (isset($_POST['precio_oferta']) && $es_profesor) {
        $precio_nuevo = $_POST['precio_oferta'];
        $sql = "INSERT INTO precios_personalizados (id_profesor, id_alumno, id_clase, precio_oferta) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mi_id, $id_otro, $id_clase, $precio_nuevo]);
    }
    
    header("Location: contacto_profesor.php?id_clase=$id_clase&id_profesor=$id_profesor&id_alumno=$id_alumno");
    exit();
}

$stmtMsgs = $pdo->prepare("SELECT * FROM mensajes WHERE id_clase = ? AND ((id_emisor = ? AND id_receptor = ?) OR (id_emisor = ? AND id_receptor = ?)) ORDER BY fecha_envio ASC");
$stmtMsgs->execute([$id_clase, $mi_id, $id_otro, $id_otro, $mi_id]);
$mensajes = $stmtMsgs->fetchAll(PDO::FETCH_ASSOC);

$stmtOtro = $pdo->prepare("SELECT username FROM usuarios WHERE id = ?");
$stmtOtro->execute([$id_otro]);
$otro_usuario = $stmtOtro->fetchColumn();

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-md mb-0"><i class="fa-solid fa-comments text-danger"></i> Chat con <?= htmlspecialchars($otro_usuario) ?></h2>
        <a href="<?= $es_profesor ? 'mis_alumnos.php' : 'cartelera.php' ?>" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="chat-container" id="cajaChat">
        <?php foreach ($mensajes as $msg): ?>
            <div class="chat-bubble <?= $msg['id_emisor'] == $mi_id ? 'chat-bubble-sent' : 'chat-bubble-received' ?>">
                <?= htmlspecialchars($msg['mensaje']) ?>
                <span class="chat-time"><?= date('H:i', strtotime($msg['fecha_envio'])) ?></span>
            </div>
        <?php endforeach; ?>
        <?php if(empty($mensajes)): ?>
            <p class="text-center text-muted">Inicia la conversación para consultar dudas o acordar un precio.</p>
        <?php endif; ?>
    </div>

    <form method="POST" action="" class="chat-input-area mb-20">
        <input type="text" name="mensaje" class="input-light" placeholder="Escribe un mensaje..." required autocomplete="off">
        <button type="submit" class="btn-confirm-bright-green"><i class="fa-solid fa-paper-plane"></i></button>
    </form>

    <?php if ($es_profesor): ?>
        <div class="offer-box">
            <h4><i class="fa-solid fa-tag"></i> Oferta Exclusiva para <?= htmlspecialchars($otro_usuario) ?></h4>
            <p class="text-muted mb-10">Genera un precio único. Solo este alumno verá la modificación al pagar.</p>
            <form method="POST" action="" class="chat-input-area">
                <input type="number" step="0.01" name="precio_oferta" class="input-light" placeholder="Ej: 3500.00" required>
                <button type="submit" class="btn-action-orange">Aplicar Descuento</button>
            </form>
        </div>
    <?php endif; ?>
</section>

<script>
    const cajaChat = document.getElementById('cajaChat');
    cajaChat.scrollTop = cajaChat.scrollHeight;
</script>

<?php require_once '../includes/footer.php'; ?>