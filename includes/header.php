<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

$avatar_url = "";
$mensajes_sin_leer = 0;

if (isset($_SESSION['usuario_id'])) {
    $id_usuario_nav = $_SESSION['usuario_id'];
    
    $stmtUserNav = $pdo->prepare("SELECT avatar, username FROM usuarios WHERE id = ?");
    $stmtUserNav->execute([$id_usuario_nav]);
    $userNavDB = $stmtUserNav->fetch(PDO::FETCH_ASSOC);
    
    if ($userNavDB && !empty($userNavDB['avatar'])) {
        $avatar_url = '/MUSICEternum/uploads/avatars/' . $userNavDB['avatar'];
    } else {
        $avatar_url = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['username']) . "&background=00B4D8&color=fff&bold=true";
    }
    
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'profesor') {
        $stmtMsgNav = $pdo->prepare("SELECT COUNT(*) FROM mensajes WHERE id_receptor = ? AND leido = 0");
        $stmtMsgNav->execute([$id_usuario_nav]);
        $mensajes_sin_leer = $stmtMsgNav->fetchColumn();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eternum MUSIC</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/MUSICEternum/assets/css/styles.css">
</head>
<body class="body-padding">
    <nav class="navbar">
        <div class="nav-brand">
            <a href="/MUSICEternum/index.php">
                <img src="/MUSICEternum/assets/image/Logo%20reducido.png" alt="Eternum MUSIC" class="nav-logo">
            </a>
        </div>
        <div class="nav-links">
            <a href="/MUSICEternum/index.php">Inicio</a>
            <a href="/MUSICEternum/views/novedades.php">Novedades</a>
            <a href="/MUSICEternum/views/cartelera.php">Cartelera</a>
            <a href="/MUSICEternum/views/nosotros.php">Nosotros</a>
            
            <?php if (isset($_SESSION['usuario_id'])): ?>
                
                <?php if ($_SESSION['role'] === 'profesor'): ?>
                    <a href="/MUSICEternum/views/estadisticas.php">Estadísticas</a>
                    <a href="/MUSICEternum/views/mensajes.php">Mensajes <?php if($mensajes_sin_leer > 0): ?><span class="badge-noti"><?= $mensajes_sin_leer ?></span><?php endif; ?></a>
                <?php elseif ($_SESSION['role'] === 'artista'): ?>
                    <a href="/MUSICEternum/views/crear_evento.php">Crear Evento</a>
                <?php elseif ($_SESSION['role'] === 'alumno'): ?>
                    <a href="/MUSICEternum/views/dashboard.php">Mis Cursos</a>
                <?php elseif ($_SESSION['role'] === 'espectador'): ?>
                    <a href="/MUSICEternum/views/dashboard.php">Mis Entradas</a>
                <?php endif; ?>

                <div class="nav-profile">
                    <img src="<?= htmlspecialchars($avatar_url) ?>" alt="Avatar" class="nav-avatar">
                    <div class="nav-dropdown">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="/MUSICEternum/admin/index.php"><i class="fa-solid fa-shield"></i> Panel Administrador</a>
                        <?php else: ?>
                            <a href="/MUSICEternum/views/dashboard.php"><i class="fa-solid fa-table-columns"></i> Mi Panel de Control</a>
                        <?php endif; ?>
                        
                        <a href="/MUSICEternum/views/ajustes.php"><i class="fa-solid fa-gear"></i> Configuración</a>
                        <a href="/MUSICEternum/includes/logout.php" class="text-danger-dropdown"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
                    </div>
                </div>

            <?php else: ?>
                <button class="btn-nav-login" onclick="window.location.href='/MUSICEternum/views/login.php'"><i class="fa-solid fa-user"></i> Ingresar</button>
            <?php endif; ?>
        </div>
    </nav>