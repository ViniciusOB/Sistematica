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

// Verificar se o formulário foi enviado para cadastro de professor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $nome_professor = $_POST['nome_professor'];
    $email_professor = $_POST['email'];
    $senha_professor = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $id_admin = $_SESSION['id_admin']; // Associar o administrador logado

    // Verificar se o email já está cadastrado na tabela de professores
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM professores WHERE email_professor = :email");
    $stmt->execute(['email' => $email_professor]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists > 0) {
        $mensagem = "O email já está em uso. Por favor, use outro email.";
    } else {
        // Inserir o professor no banco de dados, incluindo a ligação ao administrador
        $sql = "INSERT INTO professores (nome_professor, email_professor, senha_professor, id_admin) 
                VALUES (:nome_professor, :email, :senha, :id_admin)";
        $stmt = $pdo->prepare($sql);
        $params = [
            'nome_professor' => $nome_professor,
            'email' => $email_professor,
            'senha' => $senha_professor,
            'id_admin' => $id_admin
        ];

        // Executa a query
        if ($stmt->execute($params)) {
            $mensagem = "Professor cadastrado com sucesso!";
        } else {
            $mensagem = "Erro ao cadastrar professor.";
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
    <title>Cadastrar Professor</title>
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

        .disabled-section {
            opacity: 0.5;
        }

        .warning {
            color: red;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="content">
    <h1>Cadastrar Novo Professor</h1>

    <?php if ($mensagem): ?>
        <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="nome_professor">Nome de Usuário:</label>
            <input type="text" class="form-control" id="nome_professor" name="nome_professor" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" class="form-control" id="senha" name="senha" required>
        </div>

        <!-- Seção de disciplinas e turmas (desativada inicialmente) -->
        <div class="form-group disabled-section">
            <label for="disciplinas">Disciplinas:</label>
            <select class="form-control" id="disciplinas" name="disciplinas[]" disabled>
                <option>Disciplinas serão listadas aqui após cadastro.</option>
            </select>
        </div>

        <div class="form-group disabled-section">
            <label for="turmas">Turmas:</label>
            <select class="form-control" id="turmas" name="turmas[]" disabled>
                <option>Turmas serão listadas aqui após cadastro.</option>
            </select>
        </div>

        <!-- Aviso -->
        <p class="warning">* Após cadastrar as disciplinas e as turmas, volte aqui e relacione-as ao professor.</p>

        <button type="submit" class="btn btn-primary" name="cadastrar">Cadastrar Professor</button>
    </form>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
