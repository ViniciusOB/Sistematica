<?php
// public/professor/dashboard.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Obtenção do nome do professor
$professor_id = $_SESSION['usuario_id'];
$nome_completo = $_SESSION['nome_completo'];

// Consultar todas as turmas e disciplinas associadas ao professor
$stmt = $pdo->prepare("
    SELECT d.nome AS disciplina, t.nome AS turma 
    FROM ProfessorDisciplinaTurma pdt
    JOIN Disciplina d ON pdt.disciplina_id = d.id
    JOIN Turma t ON pdt.turma_id = t.id
    WHERE pdt.professor_id = :professor_id
");
$stmt->execute(['professor_id' => $professor_id]);
$disciplinas_turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Painel do Professor</h2>
    <p>Bem-vindo, <?php echo htmlspecialchars($nome_completo); ?>!</p>

    <!-- Funções do professor -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Minhas Disciplinas e Turmas</div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($disciplinas_turmas as $dt): ?>
                            <li class="list-group-item">
                                Disciplina: <?php echo htmlspecialchars($dt['disciplina']); ?> | Turma: <?php echo htmlspecialchars($dt['turma']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Gerenciar Avaliações</div>
                <div class="card-body">
                    <a href="gerenciar_avaliacoes.php" class="btn btn-primary btn-block">Ver Avaliações Enviadas</a>
                    <a href="adicionar_avaliacao.php" class="btn btn-success btn-block">Adicionar Nova Avaliação</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão de logout -->
    <div class="row mt-4">
        <div class="col-md-12">
            <a href="../logout.php" class="btn btn-danger">Sair</a>
        </div>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>
