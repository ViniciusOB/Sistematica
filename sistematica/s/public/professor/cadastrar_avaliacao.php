<?php
// public/professor/cadastrar_avaliacao.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Obter disciplinas e turmas associadas ao professor
$stmt = $pdo->prepare("SELECT PD.disciplina_id, D.nome FROM ProfessorDisciplinaTurma PD JOIN Disciplina D ON PD.disciplina_id = D.id WHERE PD.professor_id = :professor_id GROUP BY PD.disciplina_id");
$stmt->execute(['professor_id' => $_SESSION['usuario_id']]);
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT PD.turma_id, T.nome FROM ProfessorDisciplinaTurma PD JOIN Turma T ON PD.turma_id = T.id WHERE PD.professor_id = :professor_id GROUP BY PD.turma_id");
$stmt->execute(['professor_id' => $_SESSION['usuario_id']]);
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $disciplina_id = $_POST['disciplina_id'];
    $turma_id = $_POST['turma_id'];
    $ano_letivo = $_POST['ano_letivo'];
    $bimestre = $_POST['bimestre'];
    $tipo = $_POST['tipo'];
    $data_avaliacao = $_POST['data_avaliacao'];
    $descricao = $_POST['descricao'];
    $criterios_avaliacao = $_POST['criterios_avaliacao'];

    // Verificar se a associação professor-disciplina-turma existe
    $stmt = $pdo->prepare("SELECT * FROM ProfessorDisciplinaTurma WHERE professor_id = :professor_id AND disciplina_id = :disciplina_id AND turma_id = :turma_id");
    $stmt->execute([
        'professor_id' => $_SESSION['usuario_id'],
        'disciplina_id' => $disciplina_id,
        'turma_id' => $turma_id
    ]);

    if ($stmt->rowCount() == 0) {
        $erro = "Associação inválida entre professor, disciplina e turma.";
    } else {
        // Inserir avaliação
        $stmt = $pdo->prepare("INSERT INTO Avaliacao (professor_id, disciplina_id, turma_id, ano_letivo, bimestre, tipo, data_avaliacao, descricao, criterios_avaliacao) VALUES (:professor_id, :disciplina_id, :turma_id, :ano_letivo, :bimestre, :tipo, :data_avaliacao, :descricao, :criterios_avaliacao)");

        try {
            $stmt->execute([
                'professor_id' => $_SESSION['usuario_id'],
                'disciplina_id' => $disciplina_id,
                'turma_id' => $turma_id,
                'ano_letivo' => $ano_letivo,
                'bimestre' => $bimestre,
                'tipo' => $tipo,
                'data_avaliacao' => $data_avaliacao,
                'descricao' => $descricao,
                'criterios_avaliacao' => $criterios_avaliacao
            ]);

            $sucesso = "Avaliação cadastrada com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar avaliação: " . $e->getMessage();
        }
    }
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Cadastrar Avaliação</h2>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>
<?php if (isset($sucesso)): ?>
    <div class="alert alert-success"><?php echo $sucesso; ?></div>
<?php endif; ?>

<form action="cadastrar_avaliacao.php" method="POST">
    <div class="form-group">
        <label for="disciplina_id">Disciplina</label>
        <select name="disciplina_id" id="disciplina_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($disciplinas as $disciplina): ?>
                <option value="<?php echo $disciplina['disciplina_id']; ?>"><?php echo $disciplina['nome']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="turma_id">Turma</label>
        <select name="turma_id" id="turma_id" class="form-control" required>
            <option value="">Selecione</option>
            <?php foreach ($turmas as $turma): ?>
                <option value="<?php echo $turma['turma_id']; ?>"><?php echo $turma['nome']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="ano_letivo">Ano Letivo</label>
        <input type="number" name="ano_letivo" id="ano_letivo" class="form-control" value="<?php echo date('Y'); ?>" required>
    </div>
    <div class="form-group">
        <label for="bimestre">Bimestre</label>
        <select name="bimestre" id="bimestre" class="form-control" required>
            <option value="">Selecione</option>
            <option value="1">1º Bimestre</option>
            <option value="2">2º Bimestre</option>
            <option value="3">3º Bimestre</option>
            <option value="4">4º Bimestre</option>
        </select>
    </div>
    <div class="form-group">
        <label for="tipo">Tipo de Avaliação</label>
        <select name="tipo" id="tipo" class="form-control" required>
            <option value="">Selecione</option>
            <option value="AV1">AV1</option>
            <option value="AV2">AV2</option>
            <option value="PD">PD</option>
        </select>
    </div>
    <div class="form-group">
        <label for="data_avaliacao">Data da Avaliação</label>
        <input type="date" name="data_avaliacao" id="data_avaliacao" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="descricao">Descrição</label>
        <textarea name="descricao" id="descricao" class="form-control" required></textarea>
    </div>
    <div class="form-group">
        <label for="criterios_avaliacao">Critérios de Avaliação</label>
        <textarea name="criterios_avaliacao" id="criterios_avaliacao" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Cadastrar</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
