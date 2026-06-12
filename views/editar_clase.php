<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

$id_clase = $_GET['id'] ?? null;
$id_profesor = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM clases WHERE id = ? AND id_profesor = ?");
$stmt->execute([$id_clase, $id_profesor]);
$clase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clase) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $nivel = $_POST['nivel'];
    $modalidad = $_POST['modalidad'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $que_aprenderas = $_POST['que_aprenderas'];
    $requisitos = $_POST['requisitos'];
    $modulos = $_POST['modulos'];

    $sql = "UPDATE clases SET titulo = ?, nivel = ?, modalidad = ?, precio = ?, descripcion = ?, que_aprenderas = ?, requisitos = ?, modulos = ? WHERE id = ? AND id_profesor = ?";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$titulo, $nivel, $modalidad, $precio, $descripcion, $que_aprenderas, $requisitos, $modulos, $id_clase, $id_profesor])) {
        header("Location: dashboard.php");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-md mb-0"><i class="fa-solid fa-pen-to-square text-danger"></i> Editar Curso</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
    </div>
    
    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Título del Curso</label>
            <input type="text" name="titulo" class="input-light" value="<?= htmlspecialchars($clase['titulo']) ?>" required>
        </div>
        <div class="flex-gap-15 mb-20">
            <div class="form-group w-100">
                <label>Nivel de Dificultad</label>
                <select name="nivel" class="input-light" required>
                    <option value="Principiante" <?= $clase['nivel'] == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                    <option value="Intermedio" <?= $clase['nivel'] == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                    <option value="Avanzado" <?= $clase['nivel'] == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                </select>
            </div>
            <div class="form-group w-100">
                <label>Modalidad</label>
                <select name="modalidad" class="input-light" required>
                    <option value="online" <?= $clase['modalidad'] == 'online' ? 'selected' : '' ?>>Online</option>
                    <option value="presencial" <?= $clase['modalidad'] == 'presencial' ? 'selected' : '' ?>>Presencial</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-20">
            <label>Costo del Curso (Precio Base)</label>
            <input type="number" step="0.01" name="precio" class="input-light" value="<?= htmlspecialchars($clase['precio']) ?>" required>
        </div>
        <div class="form-group mb-20">
            <label>Descripción Corta (Aparecerá en la cartelera)</label>
            <textarea name="descripcion" class="input-light" rows="3" required><?= htmlspecialchars($clase['descripcion']) ?></textarea>
        </div>
        
        <h3 class="title-md mt-30 mb-10 border-top pt-20">Estructura Detallada</h3>
        
        <div class="form-group mb-20">
            <label>¿Qué aprenderá el alumno? (Separa cada punto con Enter)</label>
            <textarea name="que_aprenderas" class="input-light" rows="4" required><?= htmlspecialchars($clase['que_aprenderas']) ?></textarea>
        </div>
        <div class="form-group mb-20">
            <label>Requisitos previos (Separa cada punto con Enter)</label>
            <textarea name="requisitos" class="input-light" rows="4" required><?= htmlspecialchars($clase['requisitos']) ?></textarea>
        </div>
        <div class="form-group mb-20">
            <label>Temario de Módulos (Separa cada módulo con Enter)</label>
            <textarea name="modulos" class="input-light" rows="5" required><?= htmlspecialchars($clase['modulos']) ?></textarea>
        </div>
        
        <button type="submit" class="btn-action-orange w-100 mt-15">Guardar Cambios Efectivos</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>