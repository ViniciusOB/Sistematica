<?php
// public/admin/excluir_professor.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM Usuario WHERE id = :id AND papel = 'Professor'");
$stmt->execute(['id' => $id]);

header('Location: gerenciar_professores.php');
exit;
?>
