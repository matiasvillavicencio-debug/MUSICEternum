<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<section class="nosotros-hero">
    <div class="max-w-1200 mx-auto">
        <h1>El Escenario de Todos</h1>
        <p>Eternum MUSIC nació con un propósito desafiante: unificar la industria musical argentina en un ecosistema digital único, donde bandas, profesores y fanáticos convergen para hacer historia.</p>
    </div>
</section>

<div class="max-w-1200 mx-auto w-100 mb-50">
    <section class="nosotros-grid">
        <div class="nosotros-card">
            <i class="fa-solid fa-ticket nosotros-icon"></i>
            <h3>Eventos en vivo</h3>
            <p>Conectamos a los artistas emergentes y consagrados directamente con su audiencia. Una cartelera ininterrumpida de pasión y sonido en las mejores salas del país.</p>
        </div>
        <div class="nosotros-card">
            <i class="fa-solid fa-graduation-cap nosotros-icon"></i>
            <h3>Academia musical</h3>
            <p>La educación es la base del futuro artístico. Maestros y profesionales dictan masterclasses en producción, teoría y técnica vocal al alcance de un clic.</p>
        </div>
        <div class="nosotros-card">
            <i class="fa-solid fa-handshake-angle nosotros-icon"></i>
            <h3>Comunidad y respaldo</h3>
            <p>No somos solo una ticketea, somos tu soporte. Fomentamos la crítica constructiva, la conexión directa con creadores y la compra segura sin intermediarios abusivos.</p>
        </div>
    </section>
</div>

<?php require_once '../includes/footer.php'; ?>