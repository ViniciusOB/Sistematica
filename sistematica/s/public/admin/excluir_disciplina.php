<?php
// public/admin/excluir_disciplina.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

// Verificar se a disciplina está associada a alguma avaliação ou professor
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Avaliacao WHERE disciplina_id = :id");
$stmt->execute(['id' => $id]);
$avaliacoes_count = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM ProfessorDisciplinaTurma WHERE disciplina_id = :id");
$stmt->execute(['id' => $id]);
$associacoes_count = $stmt->fetchColumn();

if ($avaliacoes_count > 0 || $associacoes_count > 0) {
    $erro = "Não é possível excluir a disciplina, pois está associada a avaliações ou professores.";
} else {
    $stmt = $pdo->prepare("DELETE FROM Disciplina WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header('Location: gerenciar_disciplinas.php');
exit;
?>
