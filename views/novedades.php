<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<div class="max-w-1200 mx-auto w-100 mt-20 mb-50">
    <div class="novedades-header">
        <h1>Últimas <span>Novedades</span></h1>
        <p class="text-muted mt-10">Mantente al tanto de los próximos lanzamientos, expansiones académicas y noticias de la comunidad.</p>
    </div>

    <div class="novedades-grid">

        <article class="novedades-card">
            <img src="https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Novedad" class="novedades-img">
            <div class="novedades-body">
                <div class="novedades-meta">
                    <span><i class="fa-solid fa-book"></i> Academia</span>
                    <span>02 de Noviembre</span>
                </div>
                <h2 class="novedades-title">Nuevos Cursos de Producción con Ableton</h2>
                <p class="novedades-text">Hemos cerrado acuerdos con tres de los productores más importantes del género electrónico nacional para traerte módulos intensivos de mezcla y masterización desde cero.</p>
                <a href="cartelera.php" class="btn-outline text-center text-decoration-none">Ver Cursos</a>
            </div>
        </article>

        <article class="novedades-card">
            <img src="https://images.unsplash.com/photo-1526478806334-5fd488fcaabc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Novedad" class="novedades-img">
            <div class="novedades-body">
                <div class="novedades-meta">
                    <span><i class="fa-solid fa-star"></i> Plataforma</span>
                    <span>28 de Octubre</span>
                </div>
                <h2 class="novedades-title">Sistema de Reseñas Oficial Activado</h2>
                <p class="novedades-text">¡Tu voz importa más que nunca! Ahora todos los espectadores pueden dejar reseñas, calificaciones y fotografías luego de asistir a un evento para apoyar a sus artistas favoritos directamente.</p>
                <a href="login.php" class="btn-outline text-center text-decoration-none">Ingresa a tu cuenta</a>
            </div>
        </article>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>