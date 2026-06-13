<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_evento = $_GET['id'] ?? null; 

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    header("Location: cartelera.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['usuario_id'];
    $metodo_pago = $_POST['metodo_pago'];
    $cantidad_asientos = (int)$_POST['cantidad_asientos'];
    $cupon_aplicado = strtoupper(trim($_POST['cupon_aplicado']));
    
    $total = $cantidad_asientos * $evento['precio'];

    if ($cupon_aplicado === 'ETERNUMOFICIAL' || $cupon_aplicado === 'FESTEJACONETERNUM') {
        $stmtVerificar = $pdo->prepare("SELECT COUNT(*) FROM cupones_usados WHERE id_usuario = ? AND codigo_cupon = ?");
        $stmtVerificar->execute([$id_usuario, $cupon_aplicado]);
        
        if ($stmtVerificar->fetchColumn() == 0) {
            if ($cupon_aplicado === 'ETERNUMOFICIAL') {
                $total = ceil($cantidad_asientos / 2) * $evento['precio'];
            } else {
                $total = $total * 0.8;
            }
            
            $stmtCupon = $pdo->prepare("INSERT INTO cupones_usados (id_usuario, codigo_cupon) VALUES (?, ?)");
            $stmtCupon->execute([$id_usuario, $cupon_aplicado]);
        }
    }
    
    $codigo_qr = "ETNM-" . strtoupper(uniqid()) . "-U" . $id_usuario . "-E" . $id_evento;

    $sql = "INSERT INTO compras (id_usuario, id_evento, cantidad_asientos, total, metodo_pago, codigo_qr) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario, $id_evento, $cantidad_asientos, $total, $metodo_pago, $codigo_qr]);

    if ($metodo_pago === 'mercadopago') {
        header("Location: https://www.mercadopago.com.ar/");
        exit();
    } else {
        header("Location: mis_entradas.php?msg=compra_exitosa");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="flex-container-50-50 mt-30 mb-50">
    <div class="flex-col">
        <h2 class="title-lg mb-20"><?= htmlspecialchars($evento['titulo']) ?></h2>
        <p class="text-muted mb-20"><i class="fa-solid fa-location-dot text-danger"></i> <?= htmlspecialchars($evento['lugar']) ?></p>
        
        <div class="seat-container">
            <div class="stage">ESCENARIO PRINCIPAL</div>
            
            <div class="seat-legend">
                <div class="legend-item"><div class="legend-box box-available"></div> Disponible</div>
                <div class="legend-item"><div class="legend-box box-selected"></div> Tu Elección</div>
                <div class="legend-item"><div class="legend-box box-occupied"></div> Ocupado</div>
            </div>

            <div class="seat-map" id="seatMap"></div>
        </div>
    </div>

    <div class="flex-col">
        <div class="payment-box">
            <h3 class="title-md text-center"><i class="fa-solid fa-cart-shopping"></i> Resumen de Compra</h3>
            
            <div class="total-price-display">
                Total: $<span id="totalPrice">0.00</span>
            </div>

            <div class="coupon-box">
                <h4 class="mb-0 text-muted"><i class="fa-solid fa-ticket"></i> ¿Tienes un código promocional?</h4>
                <div class="coupon-input-group">
                    <input type="text" id="cuponInput" class="input-light">
                    <button type="button" class="btn-outline" onclick="aplicarCupon()">Canjear</button>
                </div>
                <p id="cuponMsg" class="coupon-msg"></p>
            </div>

            <form method="POST" action="" id="paymentForm" class="mt-20">
                <input type="hidden" name="cantidad_asientos" id="cantidadAsientosInput" value="0">
                <input type="hidden" name="cupon_aplicado" id="cuponAplicadoInput" value="">
                <input type="hidden" id="precioUnitario" value="<?= $evento['precio'] ?>">

                <h4 class="mb-10 text-muted">Selecciona tu Método de Pago</h4>
                
                <label class="payment-method-box">
                    <input type="radio" name="metodo_pago" value="tarjeta" onchange="togglePaymentUI()" checked>
                    <i class="fa-solid fa-credit-card"></i> Tarjeta de Débito o Crédito
                </label>
                
                <label class="payment-method-box">
                    <input type="radio" name="metodo_pago" value="mercadopago" onchange="togglePaymentUI()">
                    <i class="fa-solid fa-handshake"></i> MercadoPago
                </label>
                
                <label class="payment-method-box">
                    <input type="radio" name="metodo_pago" value="efectivo" onchange="togglePaymentUI()">
                    <i class="fa-solid fa-money-bill"></i> Pago en Efectivo (Rapipago)
                </label>
                
                <label class="payment-method-box">
                    <input type="radio" name="metodo_pago" value="boleteria" onchange="togglePaymentUI()">
                    <i class="fa-solid fa-ticket"></i> Pagar en Boletería
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

                <button type="submit" class="btn-confirm-bright-green w-100 mt-15" id="btnComprar" disabled>Confirmar Pago Seguro</button>
            </form>
        </div>
    </div>
</section>

<script>
    let cuponActivo = '';

    document.addEventListener('DOMContentLoaded', () => {
        const seatMap = document.getElementById('seatMap');
        
        for (let i = 0; i < 48; i++) {
            const seat = document.createElement('div');
            seat.classList.add('seat');
            if (Math.random() < 0.3) seat.classList.add('occupied');
            seat.addEventListener('click', () => {
                if (!seat.classList.contains('occupied')) {
                    seat.classList.toggle('selected');
                    updateTotal();
                }
            });
            seatMap.appendChild(seat);
        }
    });

    function aplicarCupon() {
        const input = document.getElementById('cuponInput').value.trim().toUpperCase();
        const msg = document.getElementById('cuponMsg');
        const inputOculto = document.getElementById('cuponAplicadoInput');

        if (input === 'ETERNUMOFICIAL' || input === 'FESTEJACONETERNUM') {
            cuponActivo = input;
            inputOculto.value = input;
            msg.textContent = "¡Cupón válido! Descuento aplicado al total.";
            msg.className = "coupon-msg text-success active";
        } else {
            cuponActivo = '';
            inputOculto.value = '';
            msg.textContent = "Cupón inválido o ya utilizado.";
            msg.className = "coupon-msg text-danger active";
        }
        updateTotal();
    }

    function updateTotal() {
        const selected = document.querySelectorAll('.seat.selected').length;
        const precioUnitario = parseFloat(document.getElementById('precioUnitario').value);
        const cantidadAsientosInput = document.getElementById('cantidadAsientosInput');
        const totalPriceEl = document.getElementById('totalPrice');
        const btnComprar = document.getElementById('btnComprar');
        
        cantidadAsientosInput.value = selected;
        let total = selected * precioUnitario;

        if (cuponActivo === 'ETERNUMOFICIAL') {
            total = Math.ceil(selected / 2) * precioUnitario;
        } else if (cuponActivo === 'FESTEJACONETERNUM') {
            total = total * 0.8;
        }

        totalPriceEl.innerText = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        if (selected > 0) {
            btnComprar.removeAttribute('disabled');
        } else {
            btnComprar.setAttribute('disabled', 'true');
        }
    }

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
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-mobile-screen text-celeste-sm fa-2x mb-10'></i><br>Serás redirigido a la aplicación oficial de MercadoPago.";
            } else if (selectedValue === 'efectivo') {
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-barcode text-celeste-sm fa-2x mb-10'></i><br>Se generará un código de barras para que abones en Rapipago.";
            } else if (selectedValue === 'boleteria') {
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-building text-celeste-sm fa-2x mb-10'></i><br>Tus asientos quedarán reservados. Debes abonar en la puerta.";
            }
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>