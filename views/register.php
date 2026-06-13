<?php

session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $password, $role])) {
        header("Location: login.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Eternum MUSIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-panel">
            <div class="glass-card">
                <h2 class="title-lg text-center">¡Unite!<br>Crea tu cuenta</h2>
                <form method="POST" action="">
                    <input type="text" name="username" class="glass-input" placeholder="Nombre de Usuario / Banda" required>
                    <input type="email" name="email" class="glass-input" placeholder="Correo Electrónico" required>
                    
                    <div class="relative">
                        <input type="password" name="password" id="regPass" class="glass-input" placeholder="Contraseña" required>
                        <i class="fa-solid fa-eye text-muted pass-icon" id="toggleRegPass"></i>
                    </div>
                    
                    <select name="role" class="glass-input text-gray" required>
                        <option value="" disabled selected>¿Qué buscas en Eternum?</option>
                        <option value="espectador">Soy Espectador (Quiero comprar entradas)</option>
                        <option value="alumno">Soy Alumno (Quiero tomar clases)</option>
                        <option value="artista">Soy Artista / Banda (Quiero crear eventos)</option>
                        <option value="profesor">Soy Profesor (Quiero dar clases)</option>
                    </select>

                    <button type="submit" class="glass-btn">Registrarse</button>
                    
                    <div class="auth-links">
                        <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> Volver</a>
                        <a href="login.php">¿Ya tienes una cuenta? Ingresar</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="auth-bg auth-bg-register"></div>
    </div>

    <script>
        document.querySelector('#toggleRegPass').addEventListener('click', function (e) {
            const password = document.querySelector('#regPass');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>