<?php
// public/admin/aprovar_avaliacoes.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $avaliacao_id = $_POST['avaliacao_id'];
    $acao = $_POST['acao'];
    $comentario = $_POST['comentario'];

    if ($acao == 'aprovar') {
        $stmt = $pdo->prepare("UPDATE Avaliacao SET status = 'Aprovado', aprovado_por = :admin_id, aprovado_em = NOW(), comentario_admin = :comentario WHERE id = :id");
    } else {
        $stmt = $pdo->prepare("UPDATE Avaliacao SET status = 'Rejeitado', aprovado_por = :admin_id, aprovado_em = NOW(), comentario_admin = :comentario WHERE id = :id");
    }

    $stmt->execute([
        'admin_id' => $_SESSION['usuario_id'],
        'comentario' => $comentario,
        'id' => $avaliacao_id
    ]);
}

// Obter avaliações pendentes
$stmt = $pdo->prepare("SELECT A.*, U.nome_completo AS nome_professor, D.nome AS nome_disciplina, T.nome AS nome_turma FROM Avaliacao A JOIN Usuario U ON A.professor_id = U.id JOIN Disciplina D ON A.disciplina_id = D.id JOIN Turma T ON A.turma_id = T.id WHERE status = 'Pendente'");
$stmt->execute();
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../templates/header.php'; ?>

<h2>Aprovar Avaliações</h2>

<?php if (empty($avaliacoes)): ?>
    <p>Não há avaliações pendentes.</p>
<?php else: ?>
    <?php foreach ($avaliacoes as $avaliacao): ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong><?php echo $avaliacao['nome_disciplina']; ?></strong> - <?php echo $avaliacao['nome_turma']; ?>
            </div>
            <div class="card-body">
                <p><strong>Professor:</strong> <?php echo $avaliacao['nome_professor']; ?></p>
                <p><strong>Tipo:</strong> <?php echo $avaliacao['tipo']; ?></p>
                <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($avaliacao['data_avaliacao'])); ?></p>
                <p><strong>Descrição:</strong> <?php echo nl2br($avaliacao['descricao']); ?></p>
                <p><strong>Critérios de Avaliação:</strong> <?php echo nl2br($avaliacao['criterios_avaliacao']); ?></p>
                <form action="aprovar_avaliacoes.php" method="POST">
                    <input type="hidden" name="avaliacao_id" value="<?php echo $avaliacao['id']; ?>">
                    <div class="form-group">
                        <label for="comentario">Comentário:</label>
                        <textarea name="comentario" id="comentario" class="form-control"></textarea>
                    </div>
                    <button type="submit" name="acao" value="aprovar" class="btn btn-success">Aprovar</button>
                    <button type="submit" name="acao" value="rejeitar" class="btn btn-danger">Rejeitar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include '../../templates/footer.php'; ?>
