<?php
// public/professor/gerenciar_avaliacoes.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Obter as avaliações enviadas pelo professor
$professor_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("
    SELECT s.id, d.nome AS disciplina, t.nome AS turma, s.tipo_avaliacao, s.data_envio, s.data_entrega, s.bimestre, s.ano, s.descricao 
    FROM Sistematica s
    JOIN Disciplina d ON s.disciplina_id = d.id
    JOIN Turma t ON s.turma_id = t.id
    WHERE s.professor_id = :professor_id
");
$stmt->execute(['professor_id' => $professor_id]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Minhas Avaliações</h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Disciplina</th>
                <th>Turma</th>
                <th>Tipo de Avaliação</th> <!-- Coluna para mostrar o tipo de avaliação -->
                <th>Data de Envio</th>
                <th>Data de Entrega</th>
                <th>Bimestre</th>
                <th>Ano</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avaliacoes as $avaliacao): ?>
                <tr>
                    <td><?php echo htmlspecialchars($avaliacao['disciplina']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['turma']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['tipo_avaliacao']); ?></td> <!-- Exibe o tipo de avaliação -->
                    <td><?php echo htmlspecialchars($avaliacao['data_envio']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['data_entrega']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['bimestre']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['ano']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['descricao']); ?></td>
                    <td>
                        <a href="editar_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="excluir_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Painel</a>
</div>

<?php include '../../templates/footer.php'; ?>
