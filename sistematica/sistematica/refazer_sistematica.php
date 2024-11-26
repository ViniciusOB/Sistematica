<?php
session_start();
include 'conexao.php';

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

$id_professor = $_SESSION['id_professor'];

// Verificar se o ID da sistemática foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID da sistemática inválido.');
}

$id_sistematica = $_GET['id'];

// Buscar a sistemática
$stmt = $pdo->prepare("
    SELECT s.*, pd.id_professor
    FROM Sistematica s
    JOIN professordisciplinaturma pd ON s.id_pdt = pd.id_pdt
    WHERE s.id = :id_sistematica
");
$stmt->execute(['id_sistematica' => $id_sistematica]);
$sistematica = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se a sistemática foi encontrada e pertence ao professor
if (!$sistematica || $sistematica['id_professor'] != $id_professor) {
    die('Sistematica não encontrada ou você não tem permissão para editar.');
}

// Atualizar a sistemática para um novo estado de "Pendente"
$stmt = $pdo->prepare("
    UPDATE Sistematica
    SET status = 'Pendente', aprovada = NULL, mensagem_reprovacao = NULL, data_envio = NOW()
    WHERE id = :id_sistematica
");
$stmt->execute(['id_sistematica' => $id_sistematica]);

// Redirecionar o professor de volta para o painel ou página desejada
header("Location: dashboard_professor.php?message=Sistematica refeita com sucesso.");
exit();
?>
