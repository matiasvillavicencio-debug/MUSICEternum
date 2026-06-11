<?php
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    $sql = "UPDATE usuarios SET username = ?, email = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $email, $_SESSION['usuario_id']]);
    
    $_SESSION['username'] = $username;
    header("Location: ajustes.php?success=1");
    exit();
}
require_once '../includes/header.php';
?>
<section class="container-box mx-auto max-w-600 mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-md"><i class="fa-solid fa-gear text-danger"></i> Ajustes de Cuenta</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <?php if(isset($_GET['success'])): ?>
        <p class="text-success text-center mb-20">Tus datos personales han sido actualizados con éxito.</p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Nombre de Usuario Público</label>
            <input type="text" name="username" class="input-light" value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Correo Electrónico de Contacto</label>
            <input type="email" name="email" class="input-light" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Nivel de Autorización (Rol Inmodificable)</label>
            <input type="text" class="input-light text-muted" value="<?= strtoupper($user['role']) ?>" readonly>
        </div>
        <button type="submit" class="btn-confirm-bright-green w-100">Guardar Cambios Efectivos</button>
    </form>
</section>
<?php require_once '../includes/footer.php'; ?>