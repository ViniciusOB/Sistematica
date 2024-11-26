<?php
// public/admin/adicionar_disciplina.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    $stmt = $pdo->prepare("INSERT INTO Disciplina (nome) VALUES (:nome)");
    try {
        $stmt->execute(['nome' => $nome]);
        header('Location: gerenciar_disciplinas.php');
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar disciplina: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Adicionar Disciplina</h2>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<form action="adicionar_disciplina.php" method="POST">
    <div class="form-group">
        <label for="nome">Nome da Disciplina</label>
        <input type="text" name="nome" id="nome" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="gerenciar_disciplinas.php" class="btn btn-secondary">Cancelar</a>
    <a href="dashboard.php" class="btn btn-secondary mt-4">Voltar para o Menu Principal</a>

</form>

<?php include '../../templates/footer.php'; ?>
