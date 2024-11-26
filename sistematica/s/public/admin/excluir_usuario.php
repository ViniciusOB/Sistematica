<?php
// public/admin/excluir_usuario.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    // Excluir o usuário
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);

    header('Location: gerenciar_usuarios.php');
    exit;
} else {
    header('Location: gerenciar_usuarios.php');
}
?>
