<?php
// public/admin/adicionar_usuario.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_usuario = $_POST['nome_usuario'];
    $senha = $_POST['senha'];
    $papel = $_POST['papel'];
    $nome_completo = $_POST['nome_completo'];
    $email = $_POST['email'];

    // Verificar se o nome de usuário já existe
    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE nome_usuario = :nome_usuario");
    $stmt->execute(['nome_usuario' => $nome_usuario]);
    $usuario_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_existente) {
        $erro = "Nome de usuário já está em uso.";
    } else {
        // Inserir o novo usuário
        $stmt = $pdo->prepare("INSERT INTO Usuario (nome_usuario, senha, papel, nome_completo, email) VALUES (:nome_usuario, :senha, :papel, :nome_completo, :email)");
        $stmt->execute([
            'nome_usuario' => $nome_usuario,
            'senha' => $senha,  // Lembre-se de usar hash de senha para produção
            'papel' => $papel,
            'nome_completo' => $nome_completo,
            'email' => $email
        ]);
        $sucesso = "Usuário adicionado com sucesso!";
    }
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Adicionar Novo Usuário</h2>

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

    <form action="adicionar_usuario.php" method="POST">
        <div class="form-group">
            <label for="nome_usuario">Nome de Usuário</label>
            <input type="text" name="nome_usuario" id="nome_usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="papel">Papel</label>
            <select name="papel" id="papel" class="form-control" required>
                <option value="Professor">Professor</option>
                <option value="Administrador">Administrador</option>
            </select>
        </div>
        <div class="form-group">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Adicionar Usuário</button>
        <a href="gerenciar_usuarios.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
