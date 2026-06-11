<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $secret_key = $_POST['secret_key'];

    if ($secret_key === "54321") {
        $role = 'admin';
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $role])) {
            header("Location: login.php");
            exit();
        }
    } else {
        $error = "Clave de seguridad del sistema denegada.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-panel bg-darker">
            <div class="glass-card border-danger">
                <h2 class="title-md text-center text-danger"><i class="fa-solid fa-shield"></i> Alta de Administrador</h2>
                
                <?php if(isset($error)): ?>
                    <p class="text-danger text-center mb-20"><?= $error ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="text" name="username" class="glass-input" placeholder="Nombre Oficial" required>
                    <input type="email" name="email" class="glass-input" placeholder="Correo Corporativo" required>
                    <input type="password" name="password" class="glass-input" placeholder="Contraseña Segura" required>
                    <input type="password" name="secret_key" class="glass-input" placeholder="PIN de Seguridad del Servidor" required>

                    <button type="submit" class="glass-btn btn-danger">Crear Acceso Root</button>
                    
                    <div class="auth-links">
                        <a href="../index.php">Abortar</a>
                        <a href="login.php" class="text-danger">Ingreso Admin</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>