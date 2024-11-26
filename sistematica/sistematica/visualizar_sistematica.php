<?php
session_start();

// Verificar se o admin está autenticado
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Filtros de nível de ensino
$nivel_ensino = isset($_GET['nivel_ensino']) ? $_GET['nivel_ensino'] : 'Fundamental 2';

// Buscar turmas baseadas no nível de ensino
$stmt_turmas = $pdo->prepare("
    SELECT id, nome 
    FROM turma 
    WHERE nivel_ensino = :nivel_ensino
");
$stmt_turmas->execute(['nivel_ensino' => $nivel_ensino]);
$turmas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);

include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Sistemática</title>
    <link rel="stylesheet" href="CSS/visualizar_sistematica.css"> <!-- CSS externo para o layout -->
</head>
<body>
    <!-- Conteúdo principal -->
    <div class="main-content">

        <!-- Filtros para Fundamental 2 e Ensino Médio -->
        <div class="filter-buttons">
            <a href="?nivel_ensino=Ensino Médio" class="filter-button <?php echo ($nivel_ensino == 'Ensino Médio') ? 'active' : ''; ?>">Ensino Médio</a>
            <a href="?nivel_ensino=Fundamental 2" class="filter-button <?php echo ($nivel_ensino == 'Fundamental 2') ? 'active' : ''; ?>">Fundamental 2</a>
        </div>

        <h3 class="turmas-title">Turmas Inscritas</h3>
        <div class="turmas-container">
            <?php if (!empty($turmas)): ?>
                <?php foreach ($turmas as $turma): ?>
                    <form action="visualizar_bimestres.php" method="POST">
                        <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($turma['id']); ?>">
                        <button class="turma-card">
                            <?php echo htmlspecialchars($turma['nome']); ?>
                        </button>
                    </form>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-turmas">Nenhuma turma inscrita neste nível de ensino.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
