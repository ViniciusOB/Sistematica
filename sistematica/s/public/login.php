<?php
// public/login.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_usuario = $_POST['nome_usuario'];
    $senha = $_POST['senha'];

    // Verifique se o nome de usuário existe no banco de dados
    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE nome_usuario = :nome_usuario");
    $stmt->execute(['nome_usuario' => $nome_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifique se o usuário foi encontrado e se a senha coincide
    if ($usuario && $senha == $usuario['senha']) {  // Comparação direta de senhas
        // Login bem-sucedido
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['papel'] = $usuario['papel'];
        $_SESSION['nome_completo'] = $usuario['nome_completo'];

        // Redirecionar para o painel apropriado
        if ($usuario['papel'] == 'Administrador') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: professor/dashboard.php');
        }
        exit;
    } else {
        $erro = "Nome de usuário ou senha incorretos.";
    }
}
?>

<?php include '../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Login</h2>
    <?php if (isset($erro)): ?>
        <div class="alert alert-danger"><?php echo $erro; ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="nome_usuario">Nome de Usuário</label>
            <input type="text" name="nome_usuario" id="nome_usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
