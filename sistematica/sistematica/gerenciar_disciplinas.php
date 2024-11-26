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
    $id_disciplina = $_POST['excluir'];
    
    // Deletar a disciplina do banco de dados
    $sql = "DELETE FROM disciplina WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute(['id' => $id_disciplina])) {
        $mensagem = "Disciplina excluída com sucesso!";
    } else {
        $mensagem = "Erro ao excluir disciplina.";
    }
}

// Buscar todas as disciplinas cadastradas
$sql = "SELECT * FROM disciplina";
if (isset($_POST['busca'])) {
    $busca = $_POST['busca'];
    $sql .= " WHERE nome LIKE :busca";
}
$stmt = $pdo->prepare($sql);
if (isset($busca)) {
    $stmt->execute(['busca' => '%' . $busca . '%']);
} else {
    $stmt->execute();
}
$disciplinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Disciplinas</title>
    <!-- Incluindo o Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .content {
            margin-left: 250px; /* Espaço para o menu lateral */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        /* Estilo adicional para a tabela ocupar a largura correta */
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
            background-color: #1f2937; /* Cor de fundo mais escura para o cabeçalho */
            color: white; /* Texto branco */
        }

        tr:nth-child(even) {
            background-color: #f3f4f6; /* Alternar as cores das linhas */
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

        /* Responsividade */
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
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-blue-800 mb-4">Gerenciar Disciplinas</h1>

                <?php if ($mensagem): ?>
                    <div class="alert alert-info bg-green-100 border border-green-300 text-green-700 rounded-lg p-4 mb-4">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <a href="cadastrar_disciplinas.php" class="block sm:inline-block bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded mb-4">Cadastrar Nova Disciplina</a>

                <!-- Barra de busca mais flexível e amigável -->
                <form method="post" action="" class="flex flex-col sm:flex-row sm:items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
                    <input type="text" class="form-control bg-gray-100 border border-gray-300 rounded py-2 px-3 text-gray-700 w-full sm:flex-1 sm:max-w-sm" name="busca" placeholder="Buscar disciplina">
                    <button type="submit" class="bg-blue-800 hover:bg-blue-900 text-white font-bold py-2 px-4 rounded w-full sm:w-auto">Buscar</button>
                </form>

                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-blue-800 mb-4">Lista de Disciplinas</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead class="hidden sm:table-header-group bg-blue-800 text-white">
                            <tr>
                                <th class="p-3 text-left">Nome</th>
                                <th class="p-3 text-left">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disciplinas as $disciplina): ?>
                                <!-- Tabela responsiva, estilo "stacked" para mobile -->
                                <tr class="block sm:table-row border-b">
                                    <td data-label="Nome" class="p-3 sm:table-cell sm:text-left">
                                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($disciplina['nome']); ?></span>
                                    </td>

                                    <td data-label="Ações" class="block p-3 sm:table-cell sm:text-left">
                                        <form method="post" action="">
                                            <input type="hidden" name="excluir" value="<?php echo $disciplina['id']; ?>">
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded inline-block"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta disciplina?')">Excluir</button>
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

    <!-- Tailwind CDN - already included above -->
    <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
