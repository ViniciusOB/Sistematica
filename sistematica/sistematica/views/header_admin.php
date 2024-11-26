<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexao.php';

// Verificar se o admin está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Obter informações do admin
$id_admin = $_SESSION['id_admin'];
$stmt = $pdo->prepare('SELECT nome_admin FROM admin WHERE id_admin = :id_admin');
$stmt->execute(['id_admin' => $id_admin]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$nomeAdmin = $admin['nome_admin'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Link atualizado para Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Menu lateral */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgb(24,21,54); /* Cor moderna */
            padding-top: 20px;
            color: #f9fafb;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000; /* Garante que o menu fique acima do conteúdo */
            transition: transform 0.3s ease; /* Animação de deslizamento */
        }

        .sidebar h4 {
            text-align: center;
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 1px;
            color: #f9fafb;
        }

        .sidebar a {
            color: #e5e7eb;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            font-size: 1.1rem;
            transition: background-color 0.3s, color 0.3s;
            border-left: 4px solid transparent; /* Destaque sutil ao hover */
        }

        .sidebar a:hover {
            background-color: #374151;
            color: #ffcc00;
            border-left-color: #ffcc00; /* Realçar o link ativo */
        }

        .logout-footer {
            text-align: center;
            margin-top: auto; /* Mantém o ícone no final */
            padding-bottom: 20px;
        }

        .logout-icon {
            font-size: 1.8rem; /* Aumenta o tamanho do ícone */
            color: #f87171;
            margin-top: 10px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .logout-icon:hover {
            color: #ef4444; /* Efeito hover */
        }

        /* Ajuste do conteúdo principal */
        .main-content {
            margin-left: 250px; /* Espaço para o menu lateral */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .sidebar a {
                padding: 12px 15px;
                font-size: 1rem;
            }

            .main-content {
                margin-left: 200px; /* Ajuste para dispositivos menores */
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 180px;
            }

            .sidebar h4 {
                font-size: 1rem;
            }

            .sidebar a {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .main-content {
                margin-left: 180px; /* Ajuste para dispositivos menores */
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                transform: translateX(-250px); /* Oculta a barra lateral */
            }

            .sidebar.active {
                transform: translateX(0); /* Mostra a barra lateral quando ativo */
            }

            .toggle-btn {
                display: block; /* Exibe o botão de menu */
                position: fixed;
                top: 20px;
                left: 85%;
                font-size: 1.8rem;
                color: #f9fafb;
                background: none;
                border: none;
                cursor: pointer;
                z-index: 1001; /* Acima do menu lateral */
                background-color: black;
                border-radius: 10px;
            }

            .main-content {
                margin-left: 0; /* Ajuste do conteúdo para ocupar toda a tela */
            }
        }

        @media (min-width: 1000px) {
            .toggle-btn {
                opacity: 0;
                position: relative;
                top: 400px;
            }
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4><?php echo htmlspecialchars($nomeAdmin); ?></h4>
    <br><br>
    <br><br>
    <a href="dashboard_admin.php">Dashboard</a>
    <br>
    <a href="gerenciar_professores.php">Professores</a>
    <br><br>
    <a href="gerenciar_disciplinas.php">Disciplinas</a>
    <br><br>
    <a href="gerenciar_turmas.php">Turmas</a>
    <br><br>
    <a href="visualizar_sistematica.php">Sistemáticas</a>

    <div class="logout-footer">
        <i class="fas fa-sign-out-alt logout-icon" onclick="confirmLogout()"></i>
    </div>
</div>

<button class="toggle-btn">&#9776;</button>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    function confirmLogout() {
        if (confirm('Tem certeza que deseja fazer logout?')) {
            window.location.href = 'logout.php';
        }
    }

    document.querySelector('.toggle-btn').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
    });
</script>
</body>
</html>
