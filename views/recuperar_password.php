<?php

session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $mensaje = "Se ha enviado un enlace de recuperación a tu correo electrónico.";
    } else {
        $error = "No existe ninguna cuenta asociada a este correo.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Eternum MUSIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-panel">
            <div class="glass-card">
                <h2 class="title-md text-center mb-20"><i class="fa-solid fa-lock"></i> Recuperar Acceso</h2>
                <p class="text-center mb-20 text-light-gray">Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>
                
                <?php if(isset($mensaje)): ?>
                    <p class="text-center mb-20 text-success-bold"><?= $mensaje ?></p>
                <?php endif; ?>

                <?php if(isset($error)): ?>
                    <p class="text-danger text-center mb-20"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="email" name="email" class="glass-input" placeholder="Tu correo electrónico" required>
                    <button type="submit" class="glass-btn mb-20">Enviar Enlace</button>
                    
                    <div class="auth-links justify-center">
                        <a href="login.php"><i class="fa-solid fa-arrow-left"></i> Volver a Iniciar Sesión</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="auth-bg auth-bg-login"></div>
    </div>
</body>
</html>