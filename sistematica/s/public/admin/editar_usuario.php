<?php
// public/admin/editar_usuario.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $usuario_id = $_GET['id'];

    // Buscar dados do usuário
    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nome_completo = $_POST['nome_completo'];
        $email = $_POST['email'];
        $papel = $_POST['papel'];

        // Atualizar os dados do usuário
        $stmt = $pdo->prepare("UPDATE Usuario SET nome_completo = :nome_completo, email = :email, papel = :papel WHERE id = :id");
        $stmt->execute([
            'nome_completo' => $nome_completo,
            'email' => $email,
            'papel' => $papel,
            'id' => $usuario_id
        ]);

        header('Location: gerenciar_usuarios.php');
        exit;
    }
} else {
    header('Location: gerenciar_usuarios.php');
    exit;
}
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Editar Usuário</h2>

    <form action="editar_usuario.php?id=<?php echo $usuario_id; ?>" method="POST">
        <div class="form-group">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" name="nome_completo" id="nome_completo" class="form-control" value="<?php echo htmlspecialchars($usuario['nome_completo']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="papel">Papel</label>
            <select name="papel" id="papel" class="form-control" required>
                <option value="Professor" <?php if ($usuario['papel'] == 'Professor') echo 'selected'; ?>>Professor</option>
                <option value="Administrador" <?php if ($usuario['papel'] == 'Administrador') echo 'selected'; ?>>Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="gerenciar_usuarios.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
