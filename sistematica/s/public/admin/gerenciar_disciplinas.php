<?php
// public/admin/gerenciar_disciplinas.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Exibir lista de disciplinas
$stmt = $pdo->prepare("SELECT * FROM Disciplina");
$stmt->execute();
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../templates/header.php'; ?>

<h2>Gerenciar Disciplinas</h2>
<a href="adicionar_disciplina.php" class="btn btn-success mb-2">Adicionar Disciplina</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($disciplinas as $disciplina): ?>
            <tr>
                <td><?php echo $disciplina['nome']; ?></td>
                <td>
                    <a href="editar_disciplina.php?id=<?php echo $disciplina['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="excluir_disciplina.php?id=<?php echo $disciplina['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta disciplina?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<a href="dashboard.php" class="btn btn-secondary mt-4">Voltar para o Menu Principal</a>


<?php include '../../templates/footer.php'; ?>
