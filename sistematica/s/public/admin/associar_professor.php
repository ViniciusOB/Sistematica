<?php
// public/admin/associar_professor.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Obter lista de professores
$stmt = $pdo->prepare("SELECT id, nome_completo FROM Usuario WHERE papel = 'Professor'");
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter lista de disciplinas
$stmt = $pdo->prepare("SELECT id, nome FROM Disciplina");
$stmt->execute();
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter lista de turmas
$stmt = $pdo->prepare("SELECT id, nome FROM Turma");
$stmt->execute();
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $professor_id = $_POST['professor_id'];
    $disciplina_id = $_POST['disciplina_id'];
    $turma_id = $_POST['turma_id'];

    // Verificar se a associação já existe
    $stmt = $pdo->prepare("SELECT * FROM ProfessorDisciplinaTurma WHERE professor_id = :professor_id AND disciplina_id = :disciplina_id AND turma_id = :turma_id");
    $stmt->execute(['professor_id' => $professor_id, 'disciplina_id' => $disciplina_id, 'turma_id' => $turma_id]);
    $associacao_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($associacao_existente) {
        $erro = "Esta associação já existe.";
    } else {
        // Inserir a nova associação
        $stmt = $pdo->prepare("INSERT INTO ProfessorDisciplinaTurma (professor_id, disciplina_id, turma_id) VALUES (:professor_id, :disciplina_id, :turma_id)");
        $stmt->execute([
            'professor_id' => $professor_id,
            'disciplina_id' => $disciplina_id,
            'turma_id' => $turma_id
        ]);
        $sucesso = "Associação criada com sucesso!";
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Associar Professor a Disciplina e Turma</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($sucesso): ?>
        <div class="alert alert-success">
            <?php echo $sucesso; ?>
        </div>
    <?php endif; ?>

    <form action="associar_professor.php" method="POST">
        <div class="form-group">
            <label for="professor_id">Professor</label>
            <select name="professor_id" id="professor_id" class="form-control" required>
                <option value="">Selecione um Professor</option>
                <?php foreach ($professores as $professor): ?>
                    <option value="<?php echo $professor['id']; ?>"><?php echo htmlspecialchars($professor['nome_completo']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="disciplina_id">Disciplina</label>
            <select name="disciplina_id" id="disciplina_id" class="form-control" required>
                <option value="">Selecione uma Disciplina</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?php echo $disciplina['id']; ?>"><?php echo htmlspecialchars($disciplina['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="turma_id">Turma</label>
            <select name="turma_id" id="turma_id" class="form-control" required>
                <option value="">Selecione uma Turma</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Criar Associação</button>
        <a href="dashboard.php" class="btn btn-secondary">Voltar para o Menu Principal</a> <!-- Botão de voltar -->
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
