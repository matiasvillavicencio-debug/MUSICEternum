<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$id_evento = $_GET['id'] ?? null;
if (!$id_evento) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $lugar = $_POST['lugar'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $sql = "UPDATE eventos SET titulo = ?, lugar = ?, precio = ?, descripcion = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$titulo, $lugar, $precio, $descripcion, $id_evento]);
    
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->execute([$id_evento]);
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="container-box mx-auto max-w-600 mt-30">
    <h2 class="title-md text-center"><i class="fa-solid fa-pen-to-square"></i> Modificar Evento #<?= $evento['id'] ?></h2>
    
    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Título del Evento</label>
            <input type="text" name="titulo" class="input-light" value="<?= htmlspecialchars($evento['titulo']) ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Lugar / Estadio</label>
            <input type="text" name="lugar" class="input-light" value="<?= htmlspecialchars($evento['lugar']) ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Precio del Ticket ($)</label>
            <input type="number" step="0.01" name="precio" class="input-light" value="<?= $evento['precio'] ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Descripción Oficial</label>
            <textarea name="descripcion" class="input-light" rows="4" required><?= htmlspecialchars($evento['descripcion']) ?></textarea>
        </div>
        
        <button type="submit" class="btn-action-orange w-100">Guardar Cambios en Base de Datos</button>
        <div class="text-center mt-15">
            <a href="index.php" class="text-muted">Cancelar y volver</a>
        </div>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>