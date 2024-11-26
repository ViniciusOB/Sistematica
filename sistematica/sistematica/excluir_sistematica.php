<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard_professor.php");
    exit();
}

$id_sistematica = $_GET['id'];

// Excluir a sistemática
$stmt = $pdo->prepare("DELETE FROM Sistematica WHERE id = :id");
$stmt->execute(['id' => $id_sistematica]);

$_SESSION['sucesso'] = "Sistematica excluída com sucesso!";
header("Location: dashboard_professor.php");
exit();
?>
