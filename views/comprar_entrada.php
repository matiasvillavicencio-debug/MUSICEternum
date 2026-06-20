<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_evento = $_GET['id'] ?? null; 
$id_usuario = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['role'];

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    header("Location: cartelera.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_resena'])) {
    if ($rol_usuario !== 'artista') {
        $calificacion = (int)$_POST['calificacion'];
        $comentario = trim($_POST['comentario']);
        $imagen_nombre = null;

        if (isset($_FILES['imagen_resena']) && $_FILES['imagen_resena']['error'] === UPLOAD_ERR_OK) {
            $directorio = '../uploads/reviews/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['imagen_resena']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imagen_nombre = time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['imagen_resena']['tmp_name'], $directorio . $imagen_nombre);
            }
        }

        $stmtRev = $pdo->prepare("INSERT INTO resenas_eventos (id_evento, id_usuario, calificacion, comentario, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmtRev->execute([$id_evento, $id_usuario, $calificacion, $comentario, $imagen_nombre]);
        
        header("Location: comprar_entrada.php?id=" . $id_evento . "#seccion-resenas");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['metodo_pago'])) {
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

    $estado = ($metodo_pago === 'efectivo' || $metodo_pago === 'boleteria') ? 'pendiente' : 'aprobado';

    $sql = "INSERT INTO compras (id_usuario, id_evento, cantidad_asientos, total, metodo_pago, codigo_qr, estado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_usuario, $id_evento, $cantidad_asientos, $total, $metodo_pago, $codigo_qr, $estado]);

    if ($estado === 'pendiente') {
        header("Location: mis_entradas.php?msg=compra_pendiente");
    } else {
        header("Location: mis_entradas.php?msg=compra_exitosa");
    }
    exit();
}

$stmtResenas = $pdo->prepare("SELECT r.*, u.username, u.avatar FROM resenas_eventos r JOIN usuarios u ON r.id_usuario = u.id WHERE r.id_evento = ? ORDER BY r.fecha DESC");
$stmtResenas->execute([$id_evento]);
$resenas = $stmtResenas->fetchAll(PDO::FETCH_ASSOC);

$promedio = 0;
$total_resenas = count($resenas);
if ($total_resenas > 0) {
    $suma = array_sum(array_column($resenas, 'calificacion'));
    $promedio = round($suma / $total_resenas, 1);
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

<section id="seccion-resenas" class="reviews-section">
    <div class="reviews-header">
        <div>
            <h2 class="title-lg mb-0">Reseñas del Público</h2>
            <p class="text-muted">Descubre qué opinan los fans sobre este evento.</p>
        </div>
        <div class="review-stats">
            <div class="review-stats-score"><?= number_format($promedio, 1) ?></div>
            <div>
                <div class="review-stats-stars">
                    <?php for($i=1; $i<=5; $i++): ?>
                        <i class="fa-solid fa-star <?= $i <= round($promedio) ? 'text-gold' : 'text-gray' ?>"></i>
                    <?php endfor; ?>
                </div>
                <p class="text-muted text-sm m-0"><?= $total_resenas ?> valoraciones</p>
            </div>
        </div>
    </div>

    <?php if ($rol_usuario !== 'artista'): ?>
    <div class="review-form-box">
        <h3 class="title-md mb-15">Escribe tu reseña</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="submit_resena" value="1">
            
            <div class="star-rating-group">
                <input type="radio" id="star5" name="calificacion" value="5" required><label for="star5"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star4" name="calificacion" value="4"><label for="star4"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star3" name="calificacion" value="3"><label for="star3"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star2" name="calificacion" value="2"><label for="star2"><i class="fa-solid fa-star"></i></label>
                <input type="radio" id="star1" name="calificacion" value="1"><label for="star1"><i class="fa-solid fa-star"></i></label>
            </div>
            
            <textarea name="comentario" class="input-light w-100" rows="3" placeholder="¿Qué te pareció el evento? Cuéntanos tu experiencia..." required></textarea>
            
            <div class="file-upload-box">
                <label class="text-muted block mb-10"><i class="fa-solid fa-camera"></i> Subir foto del evento (Opcional)</label>
                <input type="file" name="imagen_resena" accept="image/png, image/jpeg, image/webp" class="w-100">
            </div>
            
            <button type="submit" class="btn-action-orange mt-15 px-30">Publicar Reseña</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="review-grid">
        <?php foreach ($resenas as $resena): ?>
        <div class="review-card">
            <div class="review-card-header">
                <img src="<?= $resena['avatar'] ? '/MUSICEternum/uploads/avatars/' . $resena['avatar'] : 'https://ui-avatars.com/api/?name=' . urlencode($resena['username']) . '&background=00B4D8&color=fff' ?>" class="review-avatar" alt="Avatar">
                <div>
                    <p class="review-author"><?= htmlspecialchars($resena['username']) ?></p>
                    <p class="review-date"><?= date('d/m/Y', strtotime($resena['fecha'])) ?></p>
                </div>
            </div>
            <div class="review-card-stars">
                <?php for($i=1; $i<=5; $i++): ?>
                    <i class="fa-solid fa-star <?= $i <= $resena['calificacion'] ? 'text-gold' : 'text-gray' ?>"></i>
                <?php endfor; ?>
            </div>
            <p class="review-text"><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
            
            <?php if ($resena['imagen']): ?>
                <img src="/MUSICEternum/uploads/reviews/<?= htmlspecialchars($resena['imagen']) ?>" class="review-uploaded-img" alt="Foto del evento">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($resenas)): ?>
            <div class="review-card" style="grid-column: 1 / -1; text-align: center; border: none; background: transparent;">
                <i class="fa-solid fa-comment-slash fa-2x mb-10 text-muted"></i><br>
                <span class="text-muted">Aún no hay reseñas para este evento. ¡Sé el primero en compartir tu experiencia!</span>
            </div>
        <?php endif; ?>
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
            document.getElementById('btnComprar').innerText = "Confirmar Pago Seguro";
        } else {
            uiTarjeta.classList.add('d-none');
            uiMensaje.classList.remove('d-none');
            document.querySelectorAll('#ui-tarjeta input').forEach(inp => inp.removeAttribute('required'));
            
            if (selectedValue === 'efectivo') {
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-barcode text-celeste-sm fa-2x mb-10'></i><br>Tu entrada quedará pendiente. Debes abonar en Rapipago y el administrador aprobará tu ticket.";
                document.getElementById('btnComprar').innerText = "Reservar Entrada";
            } else if (selectedValue === 'boleteria') {
                mensajeDinamico.innerHTML = "<i class='fa-solid fa-building text-celeste-sm fa-2x mb-10'></i><br>Tus asientos quedarán reservados como pendientes. Debes abonar en la puerta para recibir la aprobación.";
                document.getElementById('btnComprar').innerText = "Reservar Entrada";
            }
        }
    }
</script>

<?php require_once '../includes/footer.php'; ?>