<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'profesor') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
    $titulo = $_POST["titulo"];
    $curso = $_POST["curso"];
    $id_profesor = $_SESSION["usuario_id"];
    
    $directorio = "../uploads/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    
    $nombre_archivo = time() . "_" . basename($_FILES["archivo"]["name"]);
    $ruta_destino = $directorio . $nombre_archivo;
    
    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta_destino)) {
        $sql = "INSERT INTO materiales (id_profesor, titulo, curso, archivo) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$id_profesor, $titulo, $curso, $nombre_archivo])) {
            header("Location: dashboard.php?msg=material_subido");
            exit();
        }
    }
}

require_once '../includes/header.php';
?>

<section class="container-box mx-auto max-w-600 mt-30">
    <div class="flex-between mb-20">
        <h2 class="title-md"><i class="fa-solid fa-cloud-arrow-up text-danger"></i> Subir Material</h2>
        <a href="dashboard.php" class="text-muted"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    </div>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group mb-20">
            <label>Título del archivo</label>
            <input type="text" name="titulo" class="input-light" placeholder="Ej: Partituras Clase 1" required>
        </div>
        <div class="form-group mb-20">
            <label>Módulo / Curso correspondiente</label>
            <select name="curso" class="input-light" required>
                <option value="" disabled selected>Selecciona un curso...</option>
                <option value="Producción musical básica">Producción musical básica</option>
                <option value="Teoría Musical Aplicada">Teoría Musical Aplicada</option>
                <option value="Mezcla y Mastering">Mezcla y Mastering</option>
            </select>
        </div>
        <div class="form-group mb-20">
            <label>Archivo de Estudio (PDF, ZIP, MP3)</label>
            <input type="file" name="archivo" class="input-light" required>
        </div>
        <button type="submit" class="btn-confirm-bright-green w-100">Subir a la nube de Eternum</button>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>