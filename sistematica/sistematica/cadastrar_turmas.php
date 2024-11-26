<?php
session_start();

// Verificar se o admin está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Variável para mensagem de erro ou sucesso
$mensagem = '';

// Verificar se o formulário foi enviado para cadastro de turma
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome_turma = $_POST['nome_turma'];
    $nivel_ensino = $_POST['nivel_ensino'];

    // Inserir a turma no banco de dados
    $sql = "INSERT INTO turma (nome, nivel_ensino) VALUES (:nome, :nivel_ensino)";
    $stmt = $pdo->prepare($sql);
    $params = [
        'nome' => $nome_turma,
        'nivel_ensino' => $nivel_ensino
    ];

    if ($stmt->execute($params)) {
        $mensagem = "Turma cadastrada com sucesso!";
    } else {
        $mensagem = "Erro ao cadastrar turma.";
    }
}
include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Turma</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }

        .form-group label {
            font-weight: bold;
        }

        .alert {
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="content">
    <h1>Cadastrar Nova Turma</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="nome_turma">Nome da Turma:</label>
            <input type="text" class="form-control" id="nome_turma" name="nome_turma" required>
        </div>
        <div class="form-group">
            <label for="nivel_ensino">Nível de Ensino:</label>
            <select class="form-control" id="nivel_ensino" name="nivel_ensino" required>
                <option value="Fundamental 2">Fundamental 2</option>
                <option value="Ensino Médio">Ensino Médio</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="cadastrar">Cadastrar Turma</button>
    </form>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
