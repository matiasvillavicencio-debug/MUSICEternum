<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];
$rol = $_SESSION['role'];
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['actualizar_datos'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        
        $stmt = $pdo->prepare("UPDATE usuarios SET username = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$username, $email, $id_usuario])) {
            $_SESSION['username'] = $username;
            $mensaje = "Datos personales actualizados correctamente.";
        }
    } elseif (isset($_POST['subir_avatar']) && isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $directorio = '../uploads/avatars/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        $nombre_archivo = time() . '_' . basename($_FILES['avatar_file']['name']);
        $ruta_destino = $directorio . $nombre_archivo;
        $tipo_archivo = strtolower(pathinfo($ruta_destino, PATHINFO_EXTENSION));
        
        if (in_array($tipo_archivo, ['jpg', 'jpeg', 'png'])) {
            $stmtOld = $pdo->prepare("SELECT avatar FROM usuarios WHERE id = ?");
            $stmtOld->execute([$id_usuario]);
            $oldAvatar = $stmtOld->fetchColumn();
            
            if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], $ruta_destino)) {
                $stmt = $pdo->prepare("UPDATE usuarios SET avatar = ? WHERE id = ?");
                $stmt->execute([$nombre_archivo, $id_usuario]);
                $mensaje = "Foto de perfil actualizada con éxito.";
                
                if ($oldAvatar && file_exists($directorio . $oldAvatar)) {
                    unlink($directorio . $oldAvatar);
                }
            }
        } else {
            $mensaje = "Formato no válido. Utiliza únicamente JPG o PNG.";
        }
    } elseif (isset($_POST['eliminar_avatar'])) {
        $directorio = '../uploads/avatars/';
        $stmtOld = $pdo->prepare("SELECT avatar FROM usuarios WHERE id = ?");
        $stmtOld->execute([$id_usuario]);
        $oldAvatar = $stmtOld->fetchColumn();
        
        if ($oldAvatar) {
            $stmt = $pdo->prepare("UPDATE usuarios SET avatar = NULL WHERE id = ?");
            $stmt->execute([$id_usuario]);
            $mensaje = "Foto de perfil eliminada. Se ha restaurado tu avatar por defecto.";
            
            if (file_exists($directorio . $oldAvatar)) {
                unlink($directorio . $oldAvatar);
            }
        }
    }
}

$stmtUser = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmtUser->execute([$id_usuario]);
$usuarioData = $stmtUser->fetch(PDO::FETCH_ASSOC);

$avatar_actual = $usuarioData['avatar'] ? '/MUSICEternum/uploads/avatars/' . $usuarioData['avatar'] : "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['username']) . "&background=00B4D8&color=fff&bold=true";

require_once '../includes/header.php';
?>

<div class="dash-wrapper">
    <aside class="dash-sidebar">
        <div class="dash-sidebar-user text-center">
            <img src="<?= htmlspecialchars($avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username']) . '&background=00B4D8&color=fff&bold=true') ?>" alt="Avatar" class="avatar-md mb-10">
            <h3 class="title-md text-blanco"><?= htmlspecialchars($_SESSION['username']) ?></h3>
            <p class="text-celeste-sm"><?= strtoupper($rol) ?></p>
        </div>
        
        <nav class="dash-nav">
            <a href="dashboard.php" class="dash-nav-item active"><i class="fa-solid fa-table-columns"></i> Panel Principal</a>
            
            <?php if ($rol === 'profesor'): ?>
                <a href="dar_clase.php" class="dash-nav-item text-danger"><i class="fa-solid fa-video"></i> Dar clase en vivo</a>
                <a href="mis_alumnos.php" class="dash-nav-item"><i class="fa-solid fa-users"></i> Mis alumnos</a>
                <a href="subir_material.php" class="dash-nav-item"><i class="fa-solid fa-file-arrow-up"></i> Subir material</a>
                <a href="mensajes.php" class="dash-nav-item"><i class="fa-solid fa-envelope"></i> Mensajes <?php if($mensajes_sin_leer > 0): ?><span class="badge-noti"><?= $mensajes_sin_leer ?></span><?php endif; ?></a>
                <a href="estadisticas.php" class="dash-nav-item"><i class="fa-solid fa-chart-line"></i> Estadísticas e ingresos</a>
            <?php elseif ($rol === 'alumno'): ?>
                <a href="mis_cursos.php" class="dash-nav-item"><i class="fa-solid fa-book"></i> Mis cursos</a>
                <a href="calificaciones.php" class="dash-nav-item"><i class="fa-solid fa-star"></i> Calificaciones</a>
                <a href="material_estudio.php" class="dash-nav-item"><i class="fa-solid fa-folder-open"></i> Material de estudio</a>
            <?php elseif ($rol === 'espectador'): ?>
                <a href="mis_entradas.php" class="dash-nav-item"><i class="fa-solid fa-ticket"></i> Mis entradas</a>
            <?php endif; ?>
            
            <a href="ajustes.php" class="dash-nav-item"><i class="fa-solid fa-gear"></i> Configuración</a>
        </nav>
    </aside>

    <main class="dash-main">
        <div class="flex-between mb-20">
            <h2 class="title-lg mb-0"><i class="fa-solid fa-gear text-danger"></i> Ajustes</h2>
        </div>
        
        <?php if ($mensaje): ?>
            <div class="container-box text-center text-success-bold mb-20">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="flex-container-50-50 bg-transparent">
            <div class="flex-col container-box">
                <h3 class="title-md mb-20 text-center">Foto de perfil</h3>
                <div class="avatar-upload-container">
                    <img src="<?= htmlspecialchars($avatar_actual) ?>" alt="Tu Avatar" class="avatar-preview">
                    
                    <form method="POST" action="" enctype="multipart/form-data" class="w-100 text-center">
                        <input type="file" name="avatar_file" class="input-light mb-10" accept="image/png, image/jpeg" required>
                        <button type="submit" name="subir_avatar" class="btn-confirm-bright-green w-100"><i class="fa-solid fa-upload"></i> Subir nueva foto</button>
                    </form>

                    <?php if ($usuarioData['avatar']): ?>
                    <form method="POST" action="" class="w-100 mt-10">
                        <button type="submit" name="eliminar_avatar" class="btn-outline w-100 text-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar tu foto de perfil actual?');"><i class="fa-solid fa-trash"></i> Eliminar foto</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex-col container-box">
                <h3 class="title-md mb-20">Datos Personales</h3>
                <form method="POST" action="">
                    <div class="form-group mb-20">
                        <label>Nombre de usuario</label>
                        <input type="text" name="username" class="input-light" value="<?= htmlspecialchars($usuarioData['username']) ?>" required>
                    </div>
                    <div class="form-group mb-20">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" class="input-light" value="<?= htmlspecialchars($usuarioData['email']) ?>" required>
                    </div>
                    <div class="form-group mb-20">
                        <label>Rol (No modificable)</label>
                        <input type="text" class="input-light text-muted" value="<?= strtoupper($usuarioData['role']) ?>" readonly>
                    </div>
                    <button type="submit" name="actualizar_datos" class="btn-action-orange w-100 mt-15"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
                </form>
            </div>
        </div>
    </main>
</div>

<?php require_once '../includes/footer.php'; ?>