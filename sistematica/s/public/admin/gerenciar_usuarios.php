<?php
// public/admin/gerenciar_usuarios.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Consulta para buscar todos os usuários
$stmt = $pdo->prepare("SELECT * FROM Usuario");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Gerenciar Usuários</h2>

    <!-- Tabela de usuários -->
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Nome Completo</th>
                <th>Nome de Usuário</th>
                <th>Email</th>
                <th>Papel</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nome_completo']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome_usuario']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['papel']); ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="excluir_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="adicionar_usuario.php" class="btn btn-success mt-4">Adicionar Novo Usuário</a>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Voltar para o Menu Principal</a>

</div>

<?php include '../../templates/footer.php'; ?>
