<?php
session_start();

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Obter os dados enviados pelo formulário
$id_sistematica = $_POST['id_sistematica'] ?? null;
$descricao_av1 = $_POST['descricao_av1'] ?? '';
$data_limite_av1 = $_POST['data_limite_av1'] ?? '';
$descricao_av2 = $_POST['descricao_av2'] ?? '';
$data_limite_av2 = $_POST['data_limite_av2'] ?? '';
$descricao_rec = $_POST['descricao_rec'] ?? '';
$data_limite_rec = $_POST['data_limite_rec'] ?? '';
$descricao_pd = $_POST['descricao_pd'] ?? [];
$data_limite_pd = $_POST['data_limite_pd'] ?? [];

// Validar se a sistemática existe
if (!$id_sistematica) {
    echo "<script>alert('Erro: ID da sistemática não foi fornecido.'); window.history.back();</script>";
    exit();
}

try {
    // Iniciar uma transação para garantir a integridade dos dados
    $pdo->beginTransaction();

    // Atualizar AV1
    $stmt_av1 = $pdo->prepare("
        UPDATE AV1 SET descricao = :descricao, data_limite = :data_limite
        WHERE id_sistematica = :id_sistematica
    ");
    $stmt_av1->execute([
        ':descricao' => $descricao_av1,
        ':data_limite' => $data_limite_av1,
        ':id_sistematica' => $id_sistematica
    ]);

    // Atualizar AV2
    $stmt_av2 = $pdo->prepare("
        UPDATE AV2 SET descricao = :descricao, data_limite = :data_limite
        WHERE id_sistematica = :id_sistematica
    ");
    $stmt_av2->execute([
        ':descricao' => $descricao_av2,
        ':data_limite' => $data_limite_av2,
        ':id_sistematica' => $id_sistematica
    ]);

    // Atualizar Recuperação
    $stmt_rec = $pdo->prepare("
        UPDATE Recuperacao SET descricao = :descricao, data_limite = :data_limite
        WHERE id_sistematica = :id_sistematica
    ");
    $stmt_rec->execute([
        ':descricao' => $descricao_rec,
        ':data_limite' => $data_limite_rec,
        ':id_sistematica' => $id_sistematica
    ]);

    // Atualizar PD (remover antigos e adicionar novos)
    // Excluir todos os registros de PD associados à sistemática
    $stmt_delete_pd = $pdo->prepare("DELETE FROM PD WHERE id_sistematica = :id_sistematica");
    $stmt_delete_pd->execute([':id_sistematica' => $id_sistematica]);

    // Inserir novos registros de PD
    $stmt_insert_pd = $pdo->prepare("
        INSERT INTO PD (id_sistematica, descricao, data_limite)
        VALUES (:id_sistematica, :descricao, :data_limite)
    ");
    foreach ($descricao_pd as $index => $descricao) {
        $stmt_insert_pd->execute([
            ':id_sistematica' => $id_sistematica,
            ':descricao' => $descricao,
            ':data_limite' => $data_limite_pd[$index]
        ]);
    }

    // Confirmar a transação
    $pdo->commit();

    // Redirecionar para o dashboard do professor com uma mensagem de sucesso
    echo "<script>alert('Sistematica atualizada com sucesso!'); window.location.href = 'dashboard_professor.php';</script>";
} catch (Exception $e) {
    // Reverter a transação em caso de erro
    $pdo->rollBack();
    echo "<script>alert('Erro ao salvar as alterações: " . $e->getMessage() . "'); window.history.back();</script>";
}
?>
