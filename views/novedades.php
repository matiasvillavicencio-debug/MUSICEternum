<?php
session_start();
require_once '../includes/header.php';
?>

<section class="container-box-lg mt-30 mb-50">
    <div class="section-header justify-center">
        <h2>Últimas Novedades</h2>
    </div>
    <div class="grid-3">
        <div class="material-card border-left-artista">
            <div class="material-info w-100">
                <h3>Nuevo Sistema de Butacas</h3>
                <p>Ahora puedes elegir tu asiento exacto en tiempo real al comprar entradas para tus artistas favoritos.</p>
                <p class="text-muted mt-10"><i class="fa-regular fa-clock"></i> Actualizado hoy</p>
            </div>
        </div>
        <div class="material-card border-left-profesor">
            <div class="material-info w-100">
                <h3>Nuevas Clases de Producción</h3>
                <p>Se han habilitado más cupos para el curso de Producción Musical Avanzada. ¡Inscríbete ahora!</p>
                <p class="text-muted mt-10"><i class="fa-regular fa-clock"></i> Hace 2 días</p>
            </div>
        </div>
        <div class="material-card border-left-alumno">
            <div class="material-info w-100">
                <h3>Integración con MercadoPago</h3>
                <p>Agregamos MercadoPago y pago en efectivo a través de sucursales para facilitar tus compras.</p>
                <p class="text-muted mt-10"><i class="fa-regular fa-clock"></i> Hace 1 semana</p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>