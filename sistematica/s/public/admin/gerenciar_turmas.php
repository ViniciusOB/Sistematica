<?php
// public/admin/gerenciar_turmas.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Consulta para buscar todas as turmas
$stmt = $pdo->prepare("SELECT * FROM Turma");
$stmt->execute();
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciar Turmas</h2>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Nível de Ensino</th> <!-- Coluna para Nível de Ensino -->
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($turmas as $turma): ?>
                <tr>
                    <td><?php echo htmlspecialchars($turma['nome']); ?></td>

                    <!-- Verifica se o campo 'nivel_ensino' está presente -->
                    <td><?php echo isset($turma['nivel_ensino']) ? htmlspecialchars($turma['nivel_ensino']) : 'Não Definido'; ?></td>

                    <td>
                        <a href="editar_turma.php?id=<?php echo $turma['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="excluir_turma.php?id=<?php echo $turma['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta turma?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="adicionar_turma.php" class="btn btn-success mt-4">Adicionar Nova Turma</a>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Voltar para o Menu Principal</a>

</div>

<?php include '../../templates/footer.php'; ?>
