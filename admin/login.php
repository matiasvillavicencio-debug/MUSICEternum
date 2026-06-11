<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND password = ? AND role = 'admin'");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Acceso denegado o no posees nivel 5.";
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-panel bg-darker w-full-flex-none">
            <div class="glass-card mx-auto border-danger">
                <h2 class="title-md text-center text-danger"><i class="fa-solid fa-server"></i> Sistema Central Eternum</h2>
                
                <?php if(isset($error)): ?>
                    <p class="text-danger text-center mb-20"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="email" name="email" class="glass-input" placeholder="Correo Autorizado" required>
                    <input type="password" name="password" class="glass-input" placeholder="Contraseña de Administrador" required>
                    
                    <button type="submit" class="glass-btn btn-danger">Acceder al Servidor</button>
                    
                    <div class="auth-links text-center w-100 justify-center mt-30">
                        <a href="../index.php">Volver al sitio público</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>