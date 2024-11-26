<?php
session_start();

// Verificar se o admin está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Obter o ID do admin logado
$id_admin = $_SESSION['id_admin'];

// Variável para mensagem de erro ou sucesso
$mensagem = '';

// Verificar se uma ação de exclusão foi realizada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    $id_professor = $_POST['excluir'];
    
    // Deletar o professor do banco de dados, apenas se ele pertencer ao admin logado
    $sql = "DELETE FROM professores WHERE id = :id AND id_admin = :id_admin";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['id' => $id_professor, 'id_admin' => $id_admin])) {
        $mensagem = "Professor excluído com sucesso!";
    } else {
        $mensagem = "Erro ao excluir professor.";
    }
}

// Verificar se uma pesquisa foi realizada
$busca = isset($_POST['busca']) ? $_POST['busca'] : '';

// Buscar todos os professores cadastrados pelo admin logado
$sql = "SELECT * FROM professores WHERE id_admin = :id_admin";
if (!empty($busca)) {
    $sql .= " AND (nome_professor LIKE :busca OR nome_professor LIKE :busca OR email_professor LIKE :busca)";
}
$stmt = $pdo->prepare($sql);
if (!empty($busca)) {
    $stmt->execute(['id_admin' => $id_admin, 'busca' => '%' . $busca . '%']);
} else {
    $stmt->execute(['id_admin' => $id_admin]);
}
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar se existem registros em disciplina ou turma
$sql_disciplina = "SELECT COUNT(*) FROM disciplina";
$sql_turma = "SELECT COUNT(*) FROM turma";

$stmt_disciplina = $pdo->prepare($sql_disciplina);
$stmt_disciplina->execute();
$disciplinas_count = $stmt_disciplina->fetchColumn();

$stmt_turma = $pdo->prepare($sql_turma);
$stmt_turma->execute();
$turmas_count = $stmt_turma->fetchColumn();

// Variável de controle para exibir o botão de editar
$mostrarEditar = ($disciplinas_count > 0 || $turmas_count > 0);

// Incluindo o header
include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Professores</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #1f2937;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f3f4f6;
        }

        .btn-primary {
            background-color: #1d4ed8;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .clear-search {
            background-color: red;
            color: white;
            padding: 5px 8px;
            border-radius: 50%;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">

    <div class="content">
        <div class="container mx-auto px-4 py-6 md:py-8">
            <div class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-800 mb-4">Gerenciar Professores</h1>

                <?php if ($mensagem): ?>
                    <div class="alert alert-info bg-green-100 border border-green-300 text-green-700 rounded-lg p-4 mb-4">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <a href="cadastrar_professores.php" class="block sm:inline-block bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded mb-4">Cadastrar Novo Professor</a>

                <!-- Barra de busca -->
                <form method="post" action="" class="flex flex-col sm:flex-row sm:items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" class="form-control bg-gray-100 border border-gray-300 rounded py-2 px-3 text-gray-700 w-full sm:flex-1 sm:max-w-sm" name="busca" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Buscar professor">
                    <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded w-full sm:w-auto">Buscar</button>
                    <?php if (!empty($busca)): ?>
                        <a href="gerenciar_professores.php" class="clear-search">X</a>
                    <?php endif; ?>
                </form>

                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-blue-800 mb-4">Lista de Professores</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead class="hidden sm:table-header-group bg-blue-800 text-white">
                            <tr>
                                <th class="p-3 text-left">Nome</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professores as $professor): ?>
                                <!-- Tabela responsiva, estilo "stacked" para mobile -->
                                <tr class="block sm:table-row border-b">
                                    <td data-label="Nome" class="p-3 flex items-center sm:table-cell sm:text-left">
                                        <div class="block">
                                            <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($professor['nome_professor']); ?></span>
                                        </div>
                                    </td>
                                    
                                    <!-- Dados de email, exibidos em telas pequenas com rótulos -->
                                    <td data-label="Email" class="block p-3 sm:table-cell sm:text-left">
                                        <span class="block sm:hidden font-semibold text-gray-600">Email:</span>
                                        <?php echo htmlspecialchars($professor['email_professor']); ?>
                                    </td>

                                    <td data-label="Ações" class="block p-3 sm:table-cell sm:text-left">
                                        <?php if ($mostrarEditar): ?>
                                            <form method="post" action="editar_professores.php">
                                                <input type="hidden" name="id_professor" value="<?php echo $professor['id']; ?>">
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded inline-block">Editar</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="post" action="">
                                            <input type="hidden" name="excluir" value="<?php echo $professor['id']; ?>">
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded inline-block"
                                                    onclick="return confirm('Tem certeza que deseja excluir este professor?')">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
