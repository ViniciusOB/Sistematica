<?php
// public/admin/adicionar_turma.php
require_once '../../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $nivel_ensino = $_POST['nivel_ensino'];

    $stmt = $pdo->prepare("INSERT INTO Turma (nome, nivel_ensino) VALUES (:nome, :nivel_ensino)");
    try {
        $stmt->execute(['nome' => $nome, 'nivel_ensino' => $nivel_ensino]);
        header('Location: gerenciar_turmas.php');
        exit;
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar turma: " . $e->getMessage();
    }
}
?>

<?php include '../../templates/header.php'; ?>

<h2>Adicionar Turma</h2>

<?php if (isset($erro)): ?>
    <div class="alert alert-danger"><?php echo $erro; ?></div>
<?php endif; ?>

<form action="adicionar_turma.php" method="POST">
    <div class="form-group">
        <label for="nome">Nome da Turma</label>
        <input type="text" name="nome" id="nome" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="nivel_ensino">Nível de Ensino</label>
        <select name="nivel_ensino" id="nivel_ensino" class="form-control" required>
            <option value="">Selecione</option>
            <option value="Ensino Médio">Ensino Médio</option>
            <option value="Fundamental II">Fundamental II</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Salvar</button>
    <a href="gerenciar_turmas.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include '../../templates/footer.php'; ?>
