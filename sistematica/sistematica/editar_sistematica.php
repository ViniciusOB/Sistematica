<?php
session_start();

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

$id_sistematica = $_GET['id'] ?? null;

if ($id_sistematica) {
    // Buscar os dados da sistemática, incluindo detalhes das avaliações e recuperação
    $stmt = $pdo->prepare("
        SELECT s.*, d.nome as disciplina_nome, t.nome as turma_nome
        FROM Sistematica s
        JOIN professordisciplinaturma pdt ON s.id_pdt = pdt.id_pdt
        JOIN disciplina d ON pdt.id_disciplina = d.id
        JOIN turma t ON pdt.id_turma = t.id
        WHERE s.id = :id_sistematica
    ");
    $stmt->execute(['id_sistematica' => $id_sistematica]);
    $sistematica = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sistematica) {
        echo "<script>alert('Erro: Sistematica não encontrada.'); window.history.back();</script>";
        exit();
    }

    // Buscar dados das avaliações AV1, AV2, PDs e Recuperação
    $stmt_av1 = $pdo->prepare("SELECT * FROM AV1 WHERE id_sistematica = :id_sistematica");
    $stmt_av1->execute(['id_sistematica' => $id_sistematica]);
    $av1 = $stmt_av1->fetch(PDO::FETCH_ASSOC);

    $stmt_av2 = $pdo->prepare("SELECT * FROM AV2 WHERE id_sistematica = :id_sistematica");
    $stmt_av2->execute(['id_sistematica' => $id_sistematica]);
    $av2 = $stmt_av2->fetch(PDO::FETCH_ASSOC);

    $stmt_pd = $pdo->prepare("SELECT * FROM PD WHERE id_sistematica = :id_sistematica");
    $stmt_pd->execute(['id_sistematica' => $id_sistematica]);
    $pd_list = $stmt_pd->fetchAll(PDO::FETCH_ASSOC);

    $stmt_rec = $pdo->prepare("SELECT * FROM Recuperacao WHERE id_sistematica = :id_sistematica");
    $stmt_rec->execute(['id_sistematica' => $id_sistematica]);
    $recuperacao = $stmt_rec->fetch(PDO::FETCH_ASSOC);
} else {
    echo "<script>alert('Erro: ID da Sistematica não fornecido.'); window.history.back();</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sistematica</title>
    <link rel="stylesheet" href="CSS/sistematica.css">
</head>
<body>
<div class="form-container">
    <h2>Editar Sistemática</h2>
    <p><strong>Disciplina:</strong> <?php echo htmlspecialchars($sistematica['disciplina_nome']); ?></p>
    <p><strong>Turma:</strong> <?php echo htmlspecialchars($sistematica['turma_nome']); ?></p>

    <form method="POST" action="salvar_edicao_sistematica.php">
        <input type="hidden" name="id_sistematica" value="<?php echo htmlspecialchars($id_sistematica); ?>">

        <div class="form-row">
            <div class="form-column">
                <h3>AV1</h3>
                <label for="descricao_av1">Descrição:</label>
                <textarea name="descricao_av1" id="descricao_av1" rows="3"><?php echo htmlspecialchars($av1['descricao'] ?? ''); ?></textarea>

                <label for="data_limite_av1">Data Limite:</label>
                <input type="date" name="data_limite_av1" id="data_limite_av1" value="<?php echo htmlspecialchars($av1['data_limite'] ?? ''); ?>">
            </div>

            <div class="form-column">
                <h3>AV2</h3>
                <label for="descricao_av2">Descrição:</label>
                <textarea name="descricao_av2" id="descricao_av2" rows="3"><?php echo htmlspecialchars($av2['descricao'] ?? ''); ?></textarea>

                <label for="data_limite_av2">Data Limite:</label>
                <input type="date" name="data_limite_av2" id="data_limite_av2" value="<?php echo htmlspecialchars($av2['data_limite'] ?? ''); ?>">
            </div>

            <div class="form-column">
                <h3>Recuperação</h3>
                <label for="descricao_rec">Descrição:</label>
                <textarea name="descricao_rec" id="descricao_rec" rows="3"><?php echo htmlspecialchars($recuperacao['descricao'] ?? ''); ?></textarea>

                <label for="data_limite_rec">Data Limite:</label>
                <input type="date" name="data_limite_rec" id="data_limite_rec" value="<?php echo htmlspecialchars($recuperacao['data_limite'] ?? ''); ?>">
            </div>
        </div>

        <!-- Exibir todos os PDs associados -->
        <div class="form-column">
            <h3>Produtividade</h3>
            <div id="pd-sections">
                <?php foreach ($pd_list as $index => $pd): ?>
                    <div class="pd-instance">
                        <label for="descricao_pd[]">Descrição do PD:</label>
                        <textarea name="descricao_pd[]" rows="3"><?php echo htmlspecialchars($pd['descricao']); ?></textarea>

                        <label for="data_limite_pd[]">Data Limite PD:</label>
                        <input type="date" name="data_limite_pd[]" value="<?php echo htmlspecialchars($pd['data_limite']); ?>">
                        <?php if ($index > 0): ?>
                            <button type="button" class="remove-pd" onclick="removePD(this)">Remover</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addPD()">Adicionar Outro PD</button>
        </div>

        <div class="form-footer">
    <button type="submit">Salvar Alterações</button>
    <a href="dashboard_professor.php" class="back-button">Voltar</a>
</div>

    </form>
</div>

<script>
    function addPD() {
        const pdSection = document.createElement('div');
        pdSection.classList.add('pd-instance');
        pdSection.innerHTML = `
            <label for="descricao_pd[]">Descrição do PD:</label>
            <textarea name="descricao_pd[]" rows="3" required></textarea>
            <label for="data_limite_pd[]">Data Limite PD:</label>
            <input type="date" name="data_limite_pd[]" required>
            <button type="button" class="remove-pd" onclick="removePD(this)">Remover</button>
        `;
        document.getElementById('pd-sections').appendChild(pdSection);
    }

    function removePD(button) {
        const pdInstance = button.parentElement;
        pdInstance.remove();
    }
</script>
</body>
</html>