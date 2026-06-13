<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="hero-content">
        <img src="/MUSICEternum/assets/image/Logo%20reducido.png" alt="Eternum MUSIC Oficial" class="hero-logo">
        <p class="tagline">Donde la música trasciende, nosotros también.</p>
        <p class="hero-desc">El punto de encuentro definitivo para la cultura musical en Argentina. Un espacio diseñado para derribar barreras y conectar todas las piezas del ecosistema en un solo lugar.</p>
        <button class="btn-confirm-bright-green mt-15" onclick="window.location.href='/MUSICEternum/views/cartelera.php'">Ver Cartelera Oficial</button>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>