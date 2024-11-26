<?php
session_start();

// Verificar se o admin está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Inicializar a mensagem
$mensagem = '';
$sucesso = false; // Para exibir a mensagem de sucesso

// Obter o ID do professor via POST ou GET para que funcione após redirecionamento
$id_professor = isset($_POST['id_professor']) ? $_POST['id_professor'] : (isset($_GET['id_professor']) ? $_GET['id_professor'] : null);

if (!$id_professor) {
    header("Location: gerenciar_professores.php");
    exit();
}

// Buscar dados do professor
$stmt = $pdo->prepare("SELECT nome_professor FROM professores WHERE id = :id");
$stmt->execute(['id' => $id_professor]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar todas as disciplinas e turmas disponíveis
$disciplinas = $pdo->query("SELECT * FROM disciplina")->fetchAll(PDO::FETCH_ASSOC);
$turmas = $pdo->query("SELECT * FROM turma")->fetchAll(PDO::FETCH_ASSOC);

// Processar formulário para vincular disciplinas e turmas ao professor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vincular'])) {
    $id_disciplina = isset($_POST['id_disciplina']) ? $_POST['id_disciplina'] : null;
    $id_turma = isset($_POST['id_turma']) ? $_POST['id_turma'] : null;

    // Verificar se ambos os campos foram selecionados
    if (empty($id_disciplina) || empty($id_turma)) {
        $mensagem = "Por favor, selecione uma disciplina e uma turma.";
    } else {
        // Verificar se a relação já existe
        $query = "SELECT COUNT(*) FROM professordisciplinaturma WHERE id_professor = :id_professor AND id_disciplina = :id_disciplina AND id_turma = :id_turma";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id_professor' => $id_professor, 'id_disciplina' => $id_disciplina, 'id_turma' => $id_turma]);
        $existe_relacao = $stmt->fetchColumn();

        if ($existe_relacao > 0) {
            $mensagem = "Este professor já está vinculado à disciplina e turma selecionadas.";
        } else {
            // Inserir a relação na tabela professor-disciplina-turma
            $query_insert = "INSERT INTO professordisciplinaturma (id_professor, id_disciplina, id_turma) VALUES (:id_professor, :id_disciplina, :id_turma)";
            $stmt_insert = $pdo->prepare($query_insert);
            
            try {
                $stmt_insert->execute(['id_professor' => $id_professor, 'id_disciplina' => $id_disciplina, 'id_turma' => $id_turma]);
                $sucesso = true;
                $mensagem = "Vinculação realizada com sucesso!";
            } catch (PDOException $e) {
                $mensagem = "Erro ao vincular disciplina ou turma: " . $e->getMessage();
            }
        }
    }
}

// Processar exclusão de vinculação
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir'])) {
    $id_disciplina = $_POST['id_disciplina_excluir'];
    $id_turma = $_POST['id_turma_excluir'];

    // Excluir a relação da tabela professordisciplinaturma
    $query_delete = "DELETE FROM professordisciplinaturma WHERE id_professor = :id_professor AND id_disciplina = :id_disciplina AND id_turma = :id_turma";
    $stmt_delete = $pdo->prepare($query_delete);
    $stmt_delete->execute(['id_professor' => $id_professor, 'id_disciplina' => $id_disciplina, 'id_turma' => $id_turma]);

    $sucesso = true;
    $mensagem = "Vinculação excluída com sucesso!";

    // Redirecionar para evitar o reenvio do formulário ao atualizar a página
    header("Location: editar_professor.php?id_professor=$id_professor&mensagem=" . urlencode($mensagem));
    exit();
}

// Buscar disciplinas e turmas já vinculadas
$stmt = $pdo->prepare("SELECT pdt.id_disciplina, pdt.id_turma, d.nome as disciplina, t.nome as turma 
                       FROM professordisciplinaturma pdt
                       LEFT JOIN disciplina d ON pdt.id_disciplina = d.id
                       LEFT JOIN turma t ON pdt.id_turma = t.id
                       WHERE pdt.id_professor = :id_professor");
$stmt->execute(['id_professor' => $id_professor]);
$vinculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Professor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<style>
    .container{
        margin-left:20%;
    }
</style>
<div class="container mt-5">
    <h1>Vincular Disciplinas e Turmas para <?php echo htmlspecialchars($professor['nome_professor']); ?></h1>

    <?php if ($mensagem): ?>
        <div class="alert <?php echo $sucesso ? 'alert-success' : 'alert-info'; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id_professor" value="<?php echo htmlspecialchars($id_professor); ?>">

        <div class="form-group">
            <label for="disciplina">Selecione a Disciplina:</label>
            <select name="id_disciplina" class="form-control">
                <option value="">Selecione...</option>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <option value="<?php echo $disciplina['id']; ?>"><?php echo htmlspecialchars($disciplina['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="turma">Selecione a Turma:</label>
            <select name="id_turma" class="form-control">
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?php echo $turma['id']; ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="vincular" class="btn btn-primary">Vincular</button>
    </form>

    <h2 class="mt-5">Disciplinas e Turmas vinculadas</h2>
    <ul>
        <?php foreach ($vinculos as $vinculo): ?>
            <li>
                <?php echo htmlspecialchars($vinculo['disciplina']) . " - " . htmlspecialchars($vinculo['turma']); ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id_professor" value="<?php echo htmlspecialchars($id_professor); ?>">
                    <input type="hidden" name="id_disciplina_excluir" value="<?php echo $vinculo['id_disciplina']; ?>">
                    <input type="hidden" name="id_turma_excluir" value="<?php echo $vinculo['id_turma']; ?>">
                    <button type="submit" name="excluir" class="btn btn-danger btn-sm">Excluir</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
