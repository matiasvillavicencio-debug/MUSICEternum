<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['tipo']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $tipo = $_GET['tipo'];

    if ($tipo === 'evento') {
        $sql = "DELETE FROM eventos WHERE id = ?";
    } elseif ($tipo === 'clase') {
        $sql = "DELETE FROM clases WHERE id = ?";
    } else {
        header("Location: ../admin/index.php");
        exit();
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: ../admin/index.php");
exit();
?>