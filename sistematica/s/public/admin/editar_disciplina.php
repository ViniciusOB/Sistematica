<?php
// public/admin/editar_disciplina.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Disciplina WHERE id = :id");
$stmt->execute(['id' => $id]);
$disciplina = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$disciplina) {
    header('Location: gerenciar_disciplinas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    $stmt = $pdo->prepare("UPDATE Disciplina SET nome = :nome WHERE id = :id");
    try {
        $stmt->execute(['nome' => $nome, 'id' => $id]);
        header('Location: gerenciar_disciplinas.php');
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar disciplina: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Editar Disciplina</h2>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<form action="editar_disciplina.php?id=<?php echo $id; ?>" method="POST">
    <div class="form-group">
        <label for="nome">Nome da Disciplina</label>
        <input type="text" name="nome" id="nome" class="form-control" value="<?php echo $disciplina['nome']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="gerenciar_disciplinas.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
