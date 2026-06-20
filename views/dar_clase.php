<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/header.php';

?>

<section class="container-box-lg mt-30">
    <div class="camera-container">
        <h2 class="title-lg text-center"><i class="fa-solid fa-tower-broadcast text-danger"></i> Clase en vivo</h2>
        
        <video id="liveCamera" class="video-preview" autoplay playsinline muted></video>
        
        <a href="https://meet.google.com/new" target="_blank" class="btn-meet">
            <i class="fa-solid fa-video"></i> Iniciar Google Meet
        </a>
    </div>
</section>

<script>
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
    .then(function(stream) {
        let videoElement = document.getElementById('liveCamera');
        videoElement.srcObject = stream;
    })
    .catch(function(err) {
        console.error(err);
        alert("Por favor, permite el acceso a la cámara y el micrófono en tu navegador.");
    });
</script>

<?php require_once '../includes/footer.php'; ?>