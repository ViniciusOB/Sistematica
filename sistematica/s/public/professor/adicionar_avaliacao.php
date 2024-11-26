<?php
// public/professor/adicionar_avaliacao.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Obter as turmas e disciplinas associadas ao professor
$professor_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("
    SELECT d.id AS disciplina_id, d.nome AS disciplina, t.id AS turma_id, t.nome AS turma
    FROM ProfessorDisciplinaTurma pdt
    JOIN Disciplina d ON pdt.disciplina_id = d.id
    JOIN Turma t ON pdt.turma_id = t.id
    WHERE pdt.professor_id = :professor_id
");
$stmt->execute(['professor_id' => $professor_id]);
$disciplinas_turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $disciplina_id = $_POST['disciplina_id'];
    $turma_id = $_POST['turma_id'];
    $tipo_avaliacao = $_POST['tipo_avaliacao'];
    $descricao = $_POST['descricao'];
    $data_entrega = $_POST['data_entrega'];  // Novo campo de data de entrega
    $bimestre = $_POST['bimestre'];  // Novo campo de bimestre
    $ano = $_POST['ano'];  // Novo campo de ano

    // Inserir a nova avaliação
    $stmt = $pdo->prepare("
        INSERT INTO Sistematica (professor_id, disciplina_id, turma_id, tipo_avaliacao, data_entrega, bimestre, ano, data_envio, descricao)
        VALUES (:professor_id, :disciplina_id, :turma_id, :tipo_avaliacao, :data_entrega, :bimestre, :ano, NOW(), :descricao)
    ");
    $stmt->execute([
        'professor_id' => $professor_id,
        'disciplina_id' => $disciplina_id,
        'turma_id' => $turma_id,
        'tipo_avaliacao' => $tipo_avaliacao,
        'data_entrega' => $data_entrega,
        'bimestre' => $bimestre,
        'ano' => $ano,
        'descricao' => $descricao
    ]);
    $sucesso = "Avaliação do tipo $tipo_avaliacao adicionada com sucesso!";
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Adicionar Nova Avaliação</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form action="adicionar_avaliacao.php" method="POST">
        <div class="form-group">
            <label for="disciplina_id">Disciplina</label>
            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                <option value="">Selecione uma Disciplina</option>
                <?php foreach ($disciplinas_turmas as $dt): ?>
                    <option value="<?php echo $dt['disciplina_id']; ?>"><?php echo htmlspecialchars($dt['disciplina']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="turma_id">Turma</label>
            <select name="turma_id" id="turma_id" class="form-control" required>
                <option value="">Selecione uma Turma</option>
                <?php foreach ($disciplinas_turmas as $dt): ?>
                    <option value="<?php echo $dt['turma_id']; ?>"><?php echo htmlspecialchars($dt['turma']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tipo_avaliacao">Tipo de Avaliação</label>
            <select name="tipo_avaliacao" id="tipo_avaliacao" class="form-control" required>
                <option value="">Selecione o Tipo de Avaliação</option>
                <option value="AV1">AV1</option>
                <option value="AV2">AV2</option>
                <option value="PD">Produtividade (PD)</option>
                <option value="Recuperacao">Recuperação</option>
            </select>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição da Avaliação</label>
            <textarea name="descricao" id="descricao" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="data_entrega">Data de Entrega</label>
            <input type="date" name="data_entrega" id="data_entrega" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="bimestre">Bimestre</label>
            <select name="bimestre" id="bimestre" class="form-control" required>
                <option value="">Selecione o Bimestre</option>
                <option value="1">1º Bimestre</option>
                <option value="2">2º Bimestre</option>
                <option value="3">3º Bimestre</option>
                <option value="4">4º Bimestre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="ano">Ano</label>
            <input type="number" name="ano" id="ano" class="form-control" value="<?php echo date('Y'); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Adicionar Avaliação</button>
        <a href="dashboard.php" class="btn btn-secondary">Voltar ao Painel</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
