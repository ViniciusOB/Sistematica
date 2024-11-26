<?php
// public/admin/excluir_turma.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

// Verificar se a turma está associada a alguma avaliação ou professor
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Avaliacao WHERE turma_id = :id");
$stmt->execute(['id' => $id]);
$avaliacoes_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM ProfessorDisciplinaTurma WHERE turma_id = :id");
$stmt->execute(['id' => $id]);
$associacoes_count = $stmt->fetchColumn();

if ($avaliacoes_count > 0 || $associacoes_count > 0) {
    $erro = "Não é possível excluir a turma, pois está associada a avaliações ou professores.";
} else {
    $stmt = $pdo->prepare("DELETE FROM Turma WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header('Location: gerenciar_turmas.php');
exit;
?>
