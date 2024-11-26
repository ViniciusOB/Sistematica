<?php
// public/professor/editar_avaliacao.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Professor') {
    header('Location: ../login.php');
    exit;
}

// Verifique se o ID da avaliação foi passado
if (!isset($_GET['id'])) {
    echo "ID de avaliação não fornecido.";
    exit;
}

// Obtenha o ID da avaliação
$id = $_GET['id'];

// Obtenha os dados da avaliação com base no ID
$stmt = $pdo->prepare("SELECT * FROM Sistematica WHERE id = :id AND professor_id = :professor_id");
$stmt->execute(['id' => $id, 'professor_id' => $_SESSION['usuario_id']]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avaliacao) {
    echo "Avaliação não encontrada ou você não tem permissão para editá-la.";
    exit;
}

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualizar os dados da avaliação
    $tipo_avaliacao = $_POST['tipo_avaliacao'];
    $bimestre = $_POST['bimestre'];
    $data_entrega = $_POST['data_entrega'];
    $descricao = $_POST['descricao'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE Sistematica SET tipo_avaliacao = :tipo_avaliacao, bimestre = :bimestre, data_entrega = :data_entrega, descricao = :descricao, status = :status WHERE id = :id AND professor_id = :professor_id");
    $stmt->execute([
        'tipo_avaliacao' => $tipo_avaliacao,
        'bimestre' => $bimestre,
        'data_entrega' => $data_entrega,
        'descricao' => $descricao,
        'status' => $status,
        'id' => $id,
        'professor_id' => $_SESSION['usuario_id']
    ]);

    $sucesso = "Avaliação atualizada com sucesso!";
}

// Preencher o formulário com os dados da avaliação
?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Editar Avaliação</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?php echo $sucesso; ?></div>
    <?php endif; ?>

    <form action="editar_avaliacao.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="tipo_avaliacao">Tipo de Avaliação</label>
            <select name="tipo_avaliacao" id="tipo_avaliacao" class="form-control" required>
                <option value="AV1" <?php if ($avaliacao['tipo_avaliacao'] == 'AV1') echo 'selected'; ?>>AV1</option>
                <option value="AV2" <?php if ($avaliacao['tipo_avaliacao'] == 'AV2') echo 'selected'; ?>>AV2</option>
                <option value="PD" <?php if ($avaliacao['tipo_avaliacao'] == 'PD') echo 'selected'; ?>>Produtividade</option>
                <option value="Recuperacao" <?php if ($avaliacao['tipo_avaliacao'] == 'Recuperacao') echo 'selected'; ?>>Recuperação</option>
            </select>
        </div>

        <div class="form-group">
            <label for="bimestre">Bimestre</label>
            <select name="bimestre" id="bimestre" class="form-control" required>
                <option value="1" <?php if ($avaliacao['bimestre'] == '1') echo 'selected'; ?>>1º Bimestre</option>
                <option value="2" <?php if ($avaliacao['bimestre'] == '2') echo 'selected'; ?>>2º Bimestre</option>
                <option value="3" <?php if ($avaliacao['bimestre'] == '3') echo 'selected'; ?>>3º Bimestre</option>
                <option value="4" <?php if ($avaliacao['bimestre'] == '4') echo 'selected'; ?>>4º Bimestre</option>
            </select>
        </div>

        <div class="form-group">
            <label for="data_entrega">Data de Entrega</label>
            <input type="date" name="data_entrega" id="data_entrega" class="form-control" value="<?php echo $avaliacao['data_entrega']; ?>" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea name="descricao" id="descricao" class="form-control" required><?php echo htmlspecialchars($avaliacao['descricao']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="Pendente" <?php if ($avaliacao['status'] == 'Pendente') echo 'selected'; ?>>Pendente</option>
                <option value="Concluída" <?php if ($avaliacao['status'] == 'Concluída') echo 'selected'; ?>>Concluída</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Avaliação</button>
        <a href="visualizar_avaliacoes.php" class="btn btn-secondary">Voltar</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>
