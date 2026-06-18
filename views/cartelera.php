<?php
session_start();
require_once '../includes/db.php';

$stmtEventos = $pdo->query("SELECT * FROM eventos ORDER BY fecha ASC");
$eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

$stmtClases = $pdo->query("SELECT c.*, u.username as profesor_nombre FROM clases c JOIN usuarios u ON c.id_profesor = u.id ORDER BY c.id DESC");
$clases = $stmtClases->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="flex-container-50-50 mt-30 mb-50">
    <div class="flex-col">
        <div class="section-header">
            <h2>Cartelera de conciertos</h2>
        </div>
        <div class="events-grid">
            <?php foreach ($eventos as $evento): ?>
                <a href="/MUSICEternum/views/comprar_entrada.php?id=<?= $evento['id'] ?>" class="card-link">
                    <div class="event-card">
                        <div class="img-thumb"><i class="fa-solid fa-music"></i></div>
                        <div class="event-info">
                            <h3><?= htmlspecialchars($evento['titulo']) ?></h3>
                            <p class="grid-card-price">$<?= number_format($evento['precio'], 2) ?></p>
                            <p class="event-desc"><?= htmlspecialchars($evento['descripcion']) ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="flex-col">
        <div class="section-header">
            <h2>Cursos</h2>
        </div>
        <div class="grid-academias">
            <?php foreach ($clases as $clase): ?>
                <div class="profesor-card">
                    <div class="profesor-header">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($clase['profesor_nombre']) ?>&background=69DDFF&color=282262&bold=true" class="profesor-avatar" alt="Avatar">
                        <div class="profesor-info">
                            <h3><?= htmlspecialchars($clase['titulo']) ?></h3>
                            <p>Prof. <?= htmlspecialchars($clase['profesor_nombre']) ?></p>
                        </div>
                    </div>
                    
                    <div class="profesor-body">
                        <div class="badges-container">
                            <span class="badge badge-modalidad"><i class="fa-solid fa-laptop"></i> <?= htmlspecialchars($clase['modalidad']) ?></span>
                            <span class="badge badge-nivel"><i class="fa-solid fa-signal"></i> <?= htmlspecialchars($clase['nivel']) ?></span>
                        </div>
                        
                        <p class="temario-title"><i class="fa-solid fa-list-check"></i> Resumen de la clase:</p>
                        <p class="temario-text event-desc"><?= htmlspecialchars($clase['descripcion']) ?></p>
                        
                        <div class="card-actions mt-15">
                            <button type="button" class="btn-outline w-100 btn-open-preview" 
                                data-id="<?= $clase['id'] ?>"
                                data-titulo="<?= htmlspecialchars($clase['titulo']) ?>"
                                data-profe="<?= htmlspecialchars($clase['profesor_nombre']) ?>"
                                data-precio="<?= number_format($clase['precio'], 2) ?>"
                                data-mod="<?= htmlspecialchars($clase['modalidad']) ?>"
                                data-niv="<?= htmlspecialchars($clase['nivel']) ?>"
                                data-desc="<?= htmlspecialchars($clase['descripcion']) ?>">
                                <i class="fa-solid fa-eye"></i> Vista Previa
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="modal-preview" id="modalPreview">
    <div class="preview-box">
        <button class="btn-close-modal" id="btnClosePreview"><i class="fa-solid fa-xmark"></i></button>
        <div class="preview-header">
            <h3 id="prevTitulo"></h3>
            <p id="prevProfe"></p>
        </div>
        <div class="preview-body">
            <div class="badges-container justify-center">
                <span class="badge badge-modalidad" id="prevMod"></span>
                <span class="badge badge-nivel" id="prevNiv"></span>
            </div>
            <p class="text-muted mt-15" id="prevDesc"></p>
            <div class="preview-price" id="prevPrecio"></div>
            
            <a href="#" id="prevLink" class="btn-primary-action w-100">Ver toda la información <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</div>

<script>
    const btnsPreview = document.querySelectorAll('.btn-open-preview');
    const modalPreview = document.getElementById('modalPreview');
    const btnClosePreview = document.getElementById('btnClosePreview');

    btnsPreview.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('prevTitulo').textContent = this.getAttribute('data-titulo');
            document.getElementById('prevProfe').textContent = "Prof. " + this.getAttribute('data-profe');
            document.getElementById('prevMod').innerHTML = "<i class='fa-solid fa-laptop'></i> " + this.getAttribute('data-mod');
            document.getElementById('prevNiv').innerHTML = "<i class='fa-solid fa-signal'></i> " + this.getAttribute('data-niv');
            document.getElementById('prevPrecio').textContent = "$" + this.getAttribute('data-precio');
            
            let desc = this.getAttribute('data-desc');
            document.getElementById('prevDesc').textContent = desc.length > 150 ? desc.substring(0, 150) + "..." : desc;
            
            document.getElementById('prevLink').href = "saber_mas_clase.php?id=" + this.getAttribute('data-id');
            
            modalPreview.classList.add('active');
        });
    });

    btnClosePreview.addEventListener('click', () => {
        modalPreview.classList.remove('active');
    });

    modalPreview.addEventListener('click', (e) => {
        if (e.target === modalPreview) {
            modalPreview.classList.remove('active');
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>