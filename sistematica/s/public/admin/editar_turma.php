<?php
// public/admin/editar_turma.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM Turma WHERE id = :id");
$stmt->execute(['id' => $id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$turma) {
    header('Location: gerenciar_turmas.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $nivel_ensino = $_POST['nivel_ensino'];

    $stmt = $pdo->prepare("UPDATE Turma SET nome = :nome, nivel_ensino = :nivel_ensino WHERE id = :id");
    try {
        $stmt->execute(['nome' => $nome, 'nivel_ensino' => $nivel_ensino, 'id' => $id]);
        header('Location: gerenciar_turmas.php');
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao atualizar turma: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Editar Turma</h2>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<form action="editar_turma.php?id=<?php echo $id; ?>" method="POST">
    <div class="form-group">
        <label for="nome">Nome da Turma</label>
        <input type="text" name="nome" id="nome" class="form-control" value="<?php echo $turma['nome']; ?>" required>
    </div>
    <div class="form-group">
        <label for="nivel_ensino">Nível de Ensino</label>
        <select name="nivel_ensino" id="nivel_ensino" class="form-control" required>
            <option value="">Selecione</option>
            <option value="Ensino Médio" <?php if ($turma['nivel_ensino'] == 'Ensino Médio') echo 'selected'; ?>>Ensino Médio</option>
            <option value="Fundamental II" <?php if ($turma['nivel_ensino'] == 'Fundamental II') echo 'selected'; ?>>Fundamental II</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="gerenciar_turmas.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
