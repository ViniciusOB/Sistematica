<?php
// public/admin/gerenciar_professores.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Lógica para adicionar, editar e excluir professores

// Exibir lista de professores
$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE papel = 'Professor'");
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../templates/header.php'; ?>

<h2>Gerenciar Professores</h2>
<a href="adicionar_professor.php" class="btn btn-success mb-2">Adicionar Professor</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nome Completo</th>
            <th>Nome de Usuário</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($professores as $professor): ?>
            <tr>
                <td><?php echo $professor['nome_completo']; ?></td>
                <td><?php echo $professor['nome_usuario']; ?></td>
                <td><?php echo $professor['email']; ?></td>
                <td>
                    <a href="editar_professor.php?id=<?php echo $professor['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="excluir_professor.php?id=<?php echo $professor['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este professor?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include '../../templates/footer.php'; ?>
