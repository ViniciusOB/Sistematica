<?php
// public/admin/adicionar_professor.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_completo = $_POST['nome_completo'];
    $nome_usuario = $_POST['nome_usuario'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO Usuario (nome_usuario, senha, papel, nome_completo, email) VALUES (:nome_usuario, :senha, 'Professor', :nome_completo, :email)");
    $stmt->execute([
        'nome_usuario' => $nome_usuario,
        'senha' => $senha,
        'nome_completo' => $nome_completo,
        'email' => $email
    ]);

    header('Location: gerenciar_professores.php');
    exit;
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Adicionar Professor</h2>

<form action="adicionar_professor.php" method="POST">
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" name="nome_completo" id="nome_completo" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nome_usuario">Nome de Usuário</label>
        <input type="text" name="nome_usuario" id="nome_usuario" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="gerenciar_professores.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
