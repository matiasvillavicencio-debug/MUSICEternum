<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$stmtArtistas = $pdo->query("SELECT id, username FROM usuarios WHERE role = 'artista'");
$artistas = $stmtArtistas->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_artista = $_POST['id_artista'];
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO eventos (id_artista, titulo, lugar, fecha, precio, descripcion) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$id_artista, $titulo, $lugar, $fecha, $precio, $descripcion])) {
        header("Location: index.php");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-md mb-0"><i class="fa-regular fa-calendar-plus text-danger"></i> Alta de Evento</h2>
        <a href="index.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Asignar Artista / Banda</label>
            <select name="id_artista" class="input-light" required>
                <option value="" disabled selected>Selecciona un artista de la base de datos...</option>
                <?php foreach($artistas as $art): ?>
                    <option value="<?= $art['id'] ?>"><?= htmlspecialchars($art['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-20">
            <label>Nombre del evento</label>
            <input type="text" name="titulo" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Fecha del show</label>
            <input type="datetime-local" name="fecha" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Lugar</label>
            <input type="text" name="lugar" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Precio del ticket</label>
            <input type="number" step="0.01" name="precio" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Descripción</label>
            <textarea name="descripcion" class="input-light" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn-confirm-bright-green w-100">Forzar Publicación</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>