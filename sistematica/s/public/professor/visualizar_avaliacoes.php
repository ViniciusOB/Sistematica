<?php
// public/professor/visualizar_avaliacoes.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Obter avaliações do professor
$stmt = $pdo->prepare("SELECT S.*, D.nome AS nome_disciplina, T.nome AS nome_turma FROM Sistematica S JOIN Disciplina D ON S.disciplina_id = D.id JOIN Turma T ON S.turma_id = T.id WHERE S.professor_id = :professor_id");
$stmt->execute(['professor_id' => $_SESSION['usuario_id']]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../templates/header.php'; ?>

<h2>Minhas Avaliações</h2>

<?php if (empty($avaliacoes)): ?>
    <p>Você não possui avaliações cadastradas.</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Disciplina</th>
                <th>Turma</th>
                <th>Tipo</th>
                <th>Bimestre</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($avaliacoes as $avaliacao): ?>
                <tr>
                    <td><?php echo htmlspecialchars($avaliacao['nome_disciplina']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['nome_turma']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['tipo_avaliacao']); ?></td>
                    <td><?php echo htmlspecialchars($avaliacao['bimestre']); ?>º</td>
                    <td><?php echo date('d/m/Y', strtotime($avaliacao['data_entrega'])); ?></td>
                    <td>
                        <?php 
                        // Se 'status' não existir, definir como 'Indefinido'
                        $status = isset($avaliacao['status']) ? $avaliacao['status'] : 'Indefinido'; 
                        echo htmlspecialchars($status); 
                        ?>
                    </td>
                    <td>
                        <?php if ($status == 'Pendente'): ?>
                            <a href="editar_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="excluir_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">Excluir</a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>Indisponível</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Painel</a>
<?php endif; ?>

<?php include '../../templates/footer.php'; ?>
