<?php
// public/admin/editar_professor.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Usuario WHERE id = :id AND papel = 'Professor'");
$stmt->execute(['id' => $id]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$professor) {
    header('Location: gerenciar_professores.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_completo = $_POST['nome_completo'];
    $nome_usuario = $_POST['nome_usuario'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE Usuario SET nome_usuario = :nome_usuario, nome_completo = :nome_completo, email = :email WHERE id = :id");
    $stmt->execute([
        'nome_usuario' => $nome_usuario,
        'nome_completo' => $nome_completo,
        'email' => $email,
        'id' => $id
    ]);

    header('Location: gerenciar_professores.php');
    exit;
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Editar Professor</h2>

<form action="editar_professor.php?id=<?php echo $id; ?>" method="POST">
    <div class="form-group">
        <label for="nome_completo">Nome Completo</label>
        <input type="text" name="nome_completo" id="nome_completo" class="form-control" value="<?php echo $professor['nome_completo']; ?>" required>
    </div>
    <div class="form-group">
        <label for="nome_usuario">Nome de Usuário</label>
        <input type="text" name="nome_usuario" id="nome_usuario" class="form-control" value="<?php echo $professor['nome_usuario']; ?>" required>
    </div>
    <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" value="<?php echo $professor['email']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="gerenciar_professores.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
