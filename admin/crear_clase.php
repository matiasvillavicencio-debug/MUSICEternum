<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$stmtProfesores = $pdo->query("SELECT id, username FROM usuarios WHERE role = 'profesor'");
$profesores = $stmtProfesores->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_profesor = $_POST['id_profesor'];
    $titulo = $_POST['titulo'];
    $nivel = $_POST['nivel'];
    $modalidad = $_POST['modalidad'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];

    $sql = "INSERT INTO clases (id_profesor, titulo, nivel, modalidad, precio, descripcion) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$id_profesor, $titulo, $nivel, $modalidad, $precio, $descripcion])) {
        header("Location: index.php");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-md mb-0"><i class="fa-solid fa-book-open text-danger"></i> Alta de Clase / Academia</h2>
        <a href="index.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Asignar Profesor</label>
            <select name="id_profesor" class="input-light" required>
                <option value="" disabled selected>Selecciona un docente registrado...</option>
                <?php foreach($profesores as $prof): ?>
                    <option value="<?= $prof['id'] ?>"><?= htmlspecialchars($prof['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group mb-20">
            <label>Nombre del Curso</label>
            <input type="text" name="titulo" class="input-light" required>
        </div>
        <div class="flex-gap-15 mb-20">
            <div class="form-group w-100">
                <label>Nivel</label>
                <select name="nivel" class="input-light" required>
                    <option value="Principiante">Principiante</option>
                    <option value="Intermedio">Intermedio</option>
                    <option value="Avanzado">Avanzado</option>
                </select>
            </div>
            <div class="form-group w-100">
                <label>Modalidad</label>
                <select name="modalidad" class="input-light" required>
                    <option value="online">Online</option>
                    <option value="presencial">Presencial</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-20">
            <label>Costo del Curso</label>
            <input type="number" step="0.01" name="precio" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Temario y Descripción</label>
            <textarea name="descripcion" class="input-light" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn-action-orange w-100">Añadir al Sistema</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>