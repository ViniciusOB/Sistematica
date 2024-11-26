<?php
session_start();
include 'conexao.php';

// Verificar se o admin está autenticado
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

$id_turma = $_POST['id_turma'] ?? $_GET['id_turma'];
$bimestre = $_POST['bimestre'] ?? $_GET['bimestre'];
$ano = date("Y"); // Define o ano atual ou utilize um campo POST para ano se disponível

// Consulta para buscar todas as sistemáticas da turma e bimestre
$stmt = $pdo->prepare("
    SELECT s.*, d.nome AS disciplina_nome, p.nome_professor, t.nome AS turma_nome, 
           av1.descricao AS av1_desc, av1.data_limite AS av1_data, 
           av2.descricao AS av2_desc, av2.data_limite AS av2_data,
           rec.descricao AS rec_desc, rec.data_limite AS rec_data,
           GROUP_CONCAT(pd.descricao ORDER BY pd.data_limite ASC SEPARATOR '||') AS pd_descs,
           GROUP_CONCAT(pd.data_limite ORDER BY pd.data_limite ASC SEPARATOR '||') AS pd_datas
    FROM Sistematica s
    JOIN professordisciplinaturma pdt ON s.id_pdt = pdt.id_pdt
    JOIN disciplina d ON pdt.id_disciplina = d.id
    JOIN professores p ON pdt.id_professor = p.id
    JOIN turma t ON pdt.id_turma = t.id
    LEFT JOIN AV1 av1 ON av1.id_sistematica = s.id
    LEFT JOIN AV2 av2 ON av2.id_sistematica = s.id
    LEFT JOIN Recuperacao rec ON rec.id_sistematica = s.id
    LEFT JOIN PD pd ON pd.id_sistematica = s.id
    WHERE s.bimestre = :bimestre AND s.ano = :ano AND pdt.id_turma = :id_turma
    GROUP BY s.id
");

$stmt->execute(['bimestre' => $bimestre, 'ano' => $ano, 'id_turma' => $id_turma]);
$sistematicas = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Processar a aprovação ou reprovação da sistemática
if (isset($_POST['aprovar']) || isset($_POST['reprovar'])) {
    $id_sistematica = $_POST['id_sistematica'];
    $aprovada = isset($_POST['aprovar']) ? 1 : 0;
    $mensagem_reprovacao = isset($_POST['mensagem_reprovacao']) ? $_POST['mensagem_reprovacao'] : null;
    
    $update_stmt = $pdo->prepare("
        UPDATE Sistematica
        SET aprovada = :aprovada, status = 'Concluída', mensagem_reprovacao = :mensagem_reprovacao
        WHERE id = :id_sistematica
    ");
    $update_stmt->execute([
        'aprovada' => $aprovada,
        'mensagem_reprovacao' => $mensagem_reprovacao,
        'id_sistematica' => $id_sistematica
    ]);
    
    header("Location: visualizar_sistematica_por_bimestre.php?id_turma=$id_turma&bimestre=$bimestre");
    exit();
}

include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Sistemáticas</title>
    <link rel="stylesheet" href="CSS/visualizar_sistematica_por_bimestre.css">
    <style>
        .sistematicas-container {
            display: flex;
            justify-content: space-between;
            margin-left:30%
        }
        .sistematicas-section {
            width: 48%;
        }
        .sistematica-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .button-approve {
            background-color: green;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .back-button{
            margin-left:40%
            
        }
        .button-reprove {
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
        }
        .status-aprovado {
            color: green;
            font-weight: bold;
        }
        .status-reprovado {
            color: red;
            font-weight: bold;
        }
        .status-pendente {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sistematicas-container">
    <div class="sistematicas-section">
        <h2>Sistemáticas Aprovadas</h2>
        <?php
        foreach ($sistematicas as $sistematica) {
            if ($sistematica['aprovada'] == 1) {
                exibirSistematica($sistematica);
            }
        }
        ?>
        <button onclick="window.location.href='imprimir_sistematica_pdf.php?id_turma=<?php echo $id_turma; ?>&bimestre=<?php echo $bimestre; ?>'">Gerar PDF</button>

    </div>
    <div class="sistematicas-section">
        <h2>Sistemáticas Não Aprovadas</h2>
        <?php
        foreach ($sistematicas as $sistematica) {
            if ($sistematica['aprovada'] != 1) {
                exibirSistematica($sistematica);
            }
        }
        ?>
    </div>
</div>

<div class="button-container">
    <a href="visualizar_bimestres.php" class="back-button">Voltar</a>
</div>

</body>
</html>

<?php
function exibirSistematica($sistematica) {
    global $id_turma, $bimestre;
    ?>
    <div class="sistematica-item">
        <h3><?php echo htmlspecialchars($sistematica['disciplina_nome']); ?></h3>
        <p><strong>Professor:</strong> <?php echo htmlspecialchars($sistematica['nome_professor']); ?></p>
        <p><strong>Turma:</strong> <?php echo htmlspecialchars($sistematica['turma_nome']); ?></p>
        <p><strong>Bimestre:</strong> <?php echo htmlspecialchars($sistematica['bimestre']); ?></p>
        <p><strong>Ano:</strong> <?php echo htmlspecialchars($sistematica['ano']); ?></p>

        <div class="avaliacao-section">
            <h4>AV1</h4>
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($sistematica['av1_desc']); ?></p>
            <p><strong>Data Limite:</strong> <?php echo htmlspecialchars($sistematica['av1_data']); ?></p>
        </div>

        <div class="avaliacao-section">
            <h4>AV2</h4>
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($sistematica['av2_desc']); ?></p>
            <p><strong>Data Limite:</strong> <?php echo htmlspecialchars($sistematica['av2_data']); ?></p>
        </div>

        <div class="avaliacao-section">
            <h4>PD</h4>
            <?php
            $pd_descs = explode('||', $sistematica['pd_descs']);
            $pd_datas = explode('||', $sistematica['pd_datas']);
            foreach ($pd_descs as $index => $pd_desc) {
                echo "<p><strong>Descrição:</strong> " . htmlspecialchars($pd_desc) . "</p>";
                echo "<p><strong>Data Limite:</strong> " . htmlspecialchars($pd_datas[$index]) . "</p>";
            }
            ?>
        </div>

        <div class="avaliacao-section">
            <h4>Recuperação</h4>
            <p><strong>Descrição:</strong> <?php echo htmlspecialchars($sistematica['rec_desc']); ?></p>
            <p><strong>Data Limite:</strong> <?php echo htmlspecialchars($sistematica['rec_data']); ?></p>
        </div>

        <?php
        if ($sistematica['aprovada'] == 1) {
            echo "<p class='status-aprovado'>Status: Aprovada</p>";
        } elseif ($sistematica['status'] == 'Concluída') {
            echo "<p class='status-reprovado'>Status: Reprovada</p>";
            if (!empty($sistematica['mensagem_reprovacao'])) {
                echo "<p><strong>Motivo da reprovação:</strong> " . htmlspecialchars($sistematica['mensagem_reprovacao']) . "</p>";
            }
        } else {
            echo "<p class='status-pendente'>Status: Pendente</p>";
            ?>
            <form action="visualizar_sistematica_por_bimestre.php" method="POST">
                <input type="hidden" name="id_sistematica" value="<?php echo $sistematica['id']; ?>">
                <input type="hidden" name="id_turma" value="<?php echo $id_turma; ?>">
                <input type="hidden" name="bimestre" value="<?php echo $bimestre; ?>">
                <button type="submit" name="aprovar" class="button-approve">Aprovar</button>
                <button type="submit" name="reprovar" class="button-reprove">Reprovar</button>
                <div class="mensagem-reprovacao">
                    <label for="mensagem_reprovacao"><strong>Mensagem de Reprovação (opcional):<strong></label>
                    <textarea name="mensagem_reprovacao" id="mensagem_reprovacao" rows="3"></textarea>
                </div>
            </form>
            <?php
        }
        ?>
    </div>
    <?php
}
?>