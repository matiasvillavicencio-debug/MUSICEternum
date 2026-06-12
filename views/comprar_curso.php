<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_clase = $_GET['id'] ?? null;
$id_alumno = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT c.*, u.username as profesor_nombre FROM clases c JOIN usuarios u ON c.id_profesor = u.id WHERE c.id = ?");
$stmt->execute([$id_clase]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    header("Location: cartelera.php");
    exit();
}

$stmtOferta = $pdo->prepare("SELECT precio_oferta FROM precios_personalizados WHERE id_clase = ? AND id_alumno = ? ORDER BY id DESC LIMIT 1");
$stmtOferta->execute([$id_clase, $id_alumno]);
$oferta = $stmtOferta->fetch(PDO::FETCH_ASSOC);

$precio_final = $oferta ? $oferta['precio_oferta'] : $clase['precio'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $metodo_pago = $_POST['metodo_pago'];
    $sql = "INSERT INTO inscripciones (id_usuario, id_clase, total, metodo_pago) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_alumno, $id_clase, $precio_final, $metodo_pago]);

    if ($metodo_pago === 'mercadopago') {
        header("Location: https://www.mercadopago.com.ar/");
        exit();
    } else {
        header("Location: mis_cursos.php?msg=inscripcion_exitosa");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-lg mb-0"><i class="fa-solid fa-graduation-cap text-danger"></i> Inscripción a Curso</h2>
        <a href="cartelera.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>

    <div class="flex-container-50-50 bg-transparent">
        <div class="flex-col">
            <div class="profesor-card">
                <div class="profesor-header">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($clase['profesor_nombre']) ?>&background=00B4D8&color=fff" class="profesor-avatar" alt="Avatar">
                    <div class="profesor-info">
                        <h3><?= htmlspecialchars($clase['titulo']) ?></h3>
                        <p>Impartido por: <strong><?= htmlspecialchars($clase['profesor_nombre']) ?></strong></p>
                    </div>
                </div>
                <div class="temario-list">
                    <p><?= htmlspecialchars($clase['descripcion']) ?></p>
                    <p class="text-success-bold mt-10">Modalidad: <?= strtoupper(htmlspecialchars($clase['modalidad'])) ?></p>
                    <p class="font-bold mt-10">Nivel: <?= htmlspecialchars($clase['nivel']) ?></p>
                </div>
            </div>
        </div>

        <div class="flex-col">
            <div class="payment-box">
                <?php if($oferta): ?>
                    <h3 class="title-md text-center">
                        <i class="fa-solid fa-wallet"></i> Total: 
                        <span class="text-strike">$<?= number_format($clase['precio'], 2) ?></span> 
                        <span class="text-success-bold">$<?= number_format($precio_final, 2) ?></span>
                    </h3>
                    <p class="text-center text-success-bold mb-20">¡Precio especial acordado con el profesor aplicado!</p>
                <?php else: ?>
                    <h3 class="title-md text-center"><i class="fa-solid fa-wallet"></i> Total a Pagar: $<span id="totalPrice"><?= number_format($precio_final, 2) ?></span></h3>
                <?php endif; ?>

                <form method="POST" action="" id="paymentForm">
                    <h4 class="mb-10 text-muted">Selecciona tu Método de Pago</h4>
                    
                    <label class="payment-method-box">
                        <input type="radio" name="metodo_pago" value="tarjeta" onchange="togglePaymentUI()" checked>
                        <i class="fa-solid fa-credit-card"></i> Tarjeta de Débito o Crédito
                    </label>
                    
                    <label class="payment-method-box">
                        <input type="radio" name="metodo_pago" value="mercadopago" onchange="togglePaymentUI()">
                        <i class="fa-solid fa-handshake"></i> MercadoPago
                    </label>

                    <div id="ui-tarjeta" class="credit-card-ui mt-15">
                        <div class="form-group mb-20">
                            <input type="text" class="input-light" placeholder="Número de Tarjeta" maxlength="19" required>
                        </div>
                        <div class="flex-gap-15">
                            <div class="form-group w-100">
                                <input type="text" class="input-light" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group w-100">
                                <input type="text" class="input-light" placeholder="CVC" maxlength="3" required>
                            </div>
                        </div>
                    </div>

                    <div id="ui-mensaje" class="container-box mt-15 text-center d-none">
                        <p class="text-muted" id="mensajeDinamico"></p>
                    </div>

                    <button type="submit" class="btn-confirm-bright-green w-100 mt-15">Inscribirse al Curso</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function togglePaymentUI() {
        const radios = document.getElementsByName('metodo_pago');
        const uiTarjeta = document.getElementById('ui-tarjeta');
        const uiMensaje = document.getElementById('ui-mensaje');
        const mensajeDinamico = document.getElementById('mensajeDinamico');
        
        let selectedValue = 'tarjeta';
        for (const radio of radios) {
            if (radio.checked) selectedValue = radio.value;
        }

        if (selectedValue === 'tarjeta') {
            uiTarjeta.classList.remove('d-none');
            uiMensaje.classList.add('d-none');
            document.querySelectorAll('#ui-tarjeta input').forEach(inp => inp.setAttribute('required', 'true'));
        } else {
            uiTarjeta.classList.add('d-none');
            uiMensaje.classList.remove('d-none');
            document.querySelectorAll('#ui-tarjeta input').forEach(inp => inp.removeAttribute('required'));
            
            if (selectedValue === 'mercadopago') {
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-mobile-screen text-celeste-sm fa-2x mb-10'></i><br>Serás redirigido a la aplicación oficial de MercadoPago al confirmar.";
            }
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>