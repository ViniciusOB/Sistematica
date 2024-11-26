<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexao.php';

// Verificar se o professor está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

// Obter informações do professor
$id_professor = $_SESSION['id_professor'];
$stmt = $pdo->prepare('SELECT nome_professor FROM professores WHERE id = :id_professor');
$stmt->execute(['id_professor' => $id_professor]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

$nomeProfessor = $professor['nome_professor'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #001f3f; /* Azul marinho */
            padding-top: 20px;
            color: #f9fafb;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000;
        }

        .sidebar h4 {
            text-align: center;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #f9fafb;
        }

        .sidebar a {
            color: #e5e7eb;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            font-size: 1.1rem;
            transition: background-color 0.3s, color 0.3s;
        }

        .sidebar a:hover {
            background-color: #374151;
            color: #ffcc00;
        }

        .logout-footer {
            text-align: center;
            margin-top: auto;
            padding-bottom: 20px;
        }

        .logout-icon {
            font-size: 1.8rem;
            color: #f87171;
            cursor: pointer;
            transition: color 0.3s;
        }

        .logout-icon:hover {
            color: #ef4444;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4><?php echo htmlspecialchars($nomeProfessor); ?></h4>
    <a href="dashboard_professor.php">Painel</a>
    <a href="selecionar_sistematica.php">Sistemáticas</a>
    <a href="gerenciar_turmas.php">Turmas</a>
    <div class="logout-footer">
        <i class="fas fa-sign-out-alt logout-icon" onclick="confirmLogout()"></i>
    </div>
</div>

<script>
    function confirmLogout() {
        if (confirm('Tem certeza que deseja fazer logout?')) {
            window.location.href = 'logout.php';
        }
    }
</script>
</body>
</html>
