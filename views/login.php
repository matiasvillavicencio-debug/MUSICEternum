<?php

session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php");
        exit();
    } else {
        $error = "Credenciales incorrectas.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar - Eternum MUSIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-bg auth-bg-login"></div>
        <div class="auth-panel">
            <div class="glass-card">
                <h2 class="title-lg text-center">¡Hola!<br>Bienvenido de nuevo</h2>
                
                <?php if(isset($error)): ?>
                    <p class="text-danger text-center mb-20"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="email" name="email" class="glass-input" placeholder="Correo Electrónico" required>
                    
                    <div class="relative">
                        <input type="password" name="password" id="loginPass" class="glass-input" placeholder="Contraseña" required>
                        <i class="fa-solid fa-eye text-muted pass-icon" id="togglePass"></i>
                    </div>
                    
                    <div class="text-right mb-20">
                        <a href="recuperar_password.php" class="text-celeste-sm">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="glass-btn">Iniciar Sesión</button>
                    
                    <div class="auth-links">
                        <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Volver al inicio</a>
                        <a href="register.php">Crear una cuenta nueva</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePass');
        const password = document.querySelector('#loginPass');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>