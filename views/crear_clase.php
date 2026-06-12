<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_profesor = $_SESSION['usuario_id'];
    $titulo = $_POST['titulo'];
    $nivel = $_POST['nivel'];
    $modalidad = $_POST['modalidad'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $que_aprenderas = $_POST['que_aprenderas'];
    $requisitos = $_POST['requisitos'];
    $modulos = $_POST['modulos'];

    $sql = "INSERT INTO clases (id_profesor, titulo, nivel, modalidad, precio, descripcion, que_aprenderas, requisitos, modulos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$id_profesor, $titulo, $nivel, $modalidad, $precio, $descripcion, $que_aprenderas, $requisitos, $modulos])) {
        header("Location: dashboard.php");
        exit();
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mt-30 mx-auto max-w-600 mb-50">
    <div class="flex-between mb-20">
        <h2 class="title-md mb-0"><i class="fa-solid fa-book-open text-danger"></i> Publicar Nuevo Curso</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
    </div>
    
    <form method="POST" action="">
        <div class="form-group mb-20">
            <label>Título del Curso</label>
            <input type="text" name="titulo" class="input-light" required>
        </div>
        <div class="flex-gap-15 mb-20">
            <div class="form-group w-100">
                <label>Nivel de Dificultad</label>
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
            <label>Costo del Curso (Precio Base)</label>
            <input type="number" step="0.01" name="precio" class="input-light" required>
        </div>
        <div class="form-group mb-20">
            <label>Descripción Corta (Aparecerá en la cartelera)</label>
            <textarea name="descripcion" class="input-light" rows="3" required></textarea>
        </div>
        
        <h3 class="title-md mt-30 mb-10 border-top pt-20">Estructura Detallada</h3>
        
        <div class="form-group mb-20">
            <label>¿Qué aprenderá el alumno? (Separa cada punto con Enter)</label>
            <textarea name="que_aprenderas" class="input-light" rows="4" required></textarea>
        </div>
        <div class="form-group mb-20">
            <label>Requisitos previos (Separa cada punto con Enter)</label>
            <textarea name="requisitos" class="input-light" rows="4" required></textarea>
        </div>
        <div class="form-group mb-20">
            <label>Temario de Módulos (Separa cada módulo con Enter)</label>
            <textarea name="modulos" class="input-light" rows="5" required></textarea>
        </div>
        
        <button type="submit" class="btn-confirm-bright-green w-100 mt-15">Publicar en Cartelera</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>