<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'artista') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];
    $lugar = $_POST['lugar'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $id_artista = $_SESSION['usuario_id'];

    $sql = "INSERT INTO eventos (id_artista, titulo, lugar, fecha, precio, descripcion) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$id_artista, $titulo, $lugar, $fecha, $precio, $descripcion])) {
        header("Location: dashboard.php");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600">
    <h2 class="title-md text-center"><i class="fa-regular fa-calendar-plus"></i> Añadir Nuevo Evento</h2>
    
    <form class="tool-form active mt-15" method="POST" action="">
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
            <select name="lugar" class="input-light" required>
                <option value="Movistar Arena">Movistar Arena</option>
                <option value="Luna Park">Luna Park</option>
                <option value="Teatro Vorterix">Teatro Vorterix</option>
            </select>
        </div>
        <div class="form-group mb-20">
            <label>Precio del ticket</label>
            <input type="number" step="0.01" name="precio" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Descripción</label>
            <textarea name="descripcion" class="input-light" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn-confirm-bright-green w-100">Publicar Evento</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>