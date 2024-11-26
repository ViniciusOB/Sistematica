<?php
session_start();

// Verificar se o admin está autenticado, redirecionar para a página de login se não estiver
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Obter o ID do admin da sessão
$id_admin = $_SESSION['id_admin'];

// Conectar ao banco de dados
include 'conexao.php';

// Consultar o nome do admin
$sql = "SELECT nome_admin FROM admin WHERE id_admin = :id_admin";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_admin' => $id_admin]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$nome_admin = $admin['nome_admin'];

// Consultar número total de sistemáticas, aprovadas e pendentes
$sql_sistematica = "SELECT 
                        COUNT(*) AS total_sistematica, 
                        SUM(CASE WHEN aprovada = 1 THEN 1 ELSE 0 END) AS sistematicas_aprovadas, 
                        SUM(CASE WHEN aprovada = 0 THEN 1 ELSE 0 END) AS sistematicas_pendentes 
                    FROM Sistematica WHERE id_admin = :id_admin";
$stmt_sistematica = $pdo->prepare($sql_sistematica);
$stmt_sistematica->execute(['id_admin' => $id_admin]);
$dados_sistematica = $stmt_sistematica->fetch(PDO::FETCH_ASSOC);

// Consultar número total de professores
$sql_professores = "SELECT COUNT(*) AS total_professores FROM professores";
$stmt_professores = $pdo->prepare($sql_professores);
$stmt_professores->execute();
$total_professores = $stmt_professores->fetchColumn();

// Incluir o header do admin
include 'views/header_admin.php';
?>

<div class="main-content">
    <h1>Dashboard Admin - <?php echo htmlspecialchars($nome_admin); ?></h1>

    <div class="cards">
        <!-- Card de Sistemáticas -->
        <div class="card">
            <i class="fas fa-clipboard-list"></i>
            <h3>Sistemáticas Totais</h3>
            <p><?php echo $dados_sistematica['total_sistematica']; ?></p>
            <p><span class="text-success">Aprovadas: <?php echo $dados_sistematica['sistematicas_aprovadas']; ?></span></p>
            <p><span class="text-danger">Pendentes: <?php echo $dados_sistematica['sistematicas_pendentes']; ?></span></p>
            <a href="visualizar_sistematica.php" class="btn btn-primary">Ver sistemáticas</a>
        </div>
        <br><br>
        <!-- Card de Professores -->
        <div class="card">
            <i class="fas fa-users"></i>
            <h3>Professores</h3>
            <p>Total: <?php echo $total_professores; ?></p>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
