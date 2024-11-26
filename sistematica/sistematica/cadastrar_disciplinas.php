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

// Verificar se o formulário foi enviado para cadastro de disciplina
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome_disciplina = $_POST['nome_disciplina'];

    // Verificar se a disciplina já está cadastrada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM disciplina WHERE nome = :nome");
    $stmt->execute(['nome' => $nome_disciplina]);
    $disciplinaExists = $stmt->fetchColumn();

    if ($disciplinaExists > 0) {
        $mensagem = "A disciplina já está cadastrada.";
    } else {
        // Inserir a disciplina no banco de dados
        $sql = "INSERT INTO disciplina (nome) VALUES (:nome)";
        $stmt = $pdo->prepare($sql);
        $params = ['nome' => $nome_disciplina];

        if ($stmt->execute($params)) {
            $mensagem = "Disciplina cadastrada com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar disciplina.";
        }
    }
}
include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Disciplina</title>
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
    <h1>Cadastrar Nova Disciplina</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="nome_disciplina">Nome da Disciplina:</label>
            <input type="text" class="form-control" id="nome_disciplina" name="nome_disciplina" required>
        </div>
        <button type="submit" class="btn btn-primary" name="cadastrar">Cadastrar Disciplina</button>
    </form>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
