<?php
session_start();
include 'conexao.php';

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

$id_professor = $_SESSION['id_professor'];

// Buscar o nome do professor
$stmt = $pdo->prepare("SELECT nome_professor FROM professores WHERE id = :id");
$stmt->execute(['id' => $id_professor]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar estatísticas do professor
$stmt = $pdo->prepare("
    SELECT 
        COUNT(DISTINCT pdt.id_turma) as total_turmas,
        COUNT(DISTINCT pdt.id_disciplina) as total_disciplinas,
        COUNT(s.id) as total_sistematicas,
        SUM(CASE WHEN s.status = 'Concluída' THEN 1 ELSE 0 END) as sistematicas_concluidas
    FROM professordisciplinaturma pdt
    LEFT JOIN Sistematica s ON pdt.id_pdt = s.id_pdt
    WHERE pdt.id_professor = :id_professor
");
$stmt->execute(['id_professor' => $id_professor]);
$estatisticas = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar sistemáticas pendentes
$stmt = $pdo->prepare("
    SELECT s.id, d.nome AS disciplina, t.nome AS turma, s.bimestre, s.status
    FROM Sistematica s
    JOIN professordisciplinaturma pdt ON s.id_pdt = pdt.id_pdt
    JOIN disciplina d ON pdt.id_disciplina = d.id
    JOIN turma t ON pdt.id_turma = t.id
    WHERE pdt.id_professor = :id_professor AND s.status = 'Pendente'
    ORDER BY s.id DESC
    LIMIT 5
");
$stmt->execute(['id_professor' => $id_professor]);
$sistematicas_pendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar sistemáticas reprovadas
$stmt = $pdo->prepare("
    SELECT s.id, d.nome AS disciplina, t.nome AS turma, s.ano, s.bimestre, s.mensagem_reprovacao
    FROM Sistematica s
    JOIN professordisciplinaturma pdt ON s.id_pdt = pdt.id_pdt
    JOIN disciplina d ON pdt.id_disciplina = d.id
    JOIN turma t ON pdt.id_turma = t.id
    WHERE pdt.id_professor = :id_professor AND s.aprovada = 0
    ORDER BY s.id DESC
    LIMIT 5
");
$stmt->execute(['id_professor' => $id_professor]);
$sistematicas_reprovadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #001f3f;
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

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #001f3f;
            color: #ffffff;
            font-weight: bold;
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 250px;
            width: 250px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4><?php echo htmlspecialchars($professor['nome_professor']); ?></h4>
        <a href="dashboard_professor.php"><i class="fas fa-tachometer-alt mr-2"></i>Painel</a>
        <a href="sistematica.php"><i class="fas fa-clipboard-list mr-2"></i>Sistemática</a>
        <a href="turmas.php"><i class="fas fa-users mr-2"></i>Turmas</a>
        <div class="logout-footer">
            <i class="fas fa-sign-out-alt logout-icon" onclick="confirmLogout()"></i>
        </div>
    </div>

    <div class="main-content">
        <h1 class="mb-4">Dashboard do Professor</h1>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">Total de Turmas</div>
                    <div class="card-body">
                        <h2 class="card-title"><?php echo $estatisticas['total_turmas']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">Total de Disciplinas</div>
                    <div class="card-body">
                        <h2 class="card-title"><?php echo $estatisticas['total_disciplinas']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">Total de Sistemáticas</div>
                    <div class="card-body">
                        <h2 class="card-title"><?php echo $estatisticas['total_sistematicas']; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header">Sistemáticas Concluídas</div>
                    <div class="card-body">
                        <h2 class="card-title"><?php echo $estatisticas['sistematicas_concluidas']; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">Progresso das Sistemáticas</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="sistematicasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
    <div class="card">
        <div class="card-header">Sistemáticas Pendentes</div>
        <div class="card-body">
            <?php if (count($sistematicas_pendentes) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($sistematicas_pendentes as $sistematica): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="editar_sistematica.php?id=<?php echo htmlspecialchars($sistematica['id']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($sistematica['disciplina'] . ' - ' . $sistematica['turma'] . ' (' . $sistematica['bimestre'] . 'º Bimestre)'); ?>
                            </a>
                            <span class="badge badge-warning badge-pill"><?php echo htmlspecialchars($sistematica['status']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center">Não há sistemáticas pendentes.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="col-md-6 mb-4">
    <div class="card">
        <div class="card-header">Sistemáticas Reprovadas</div>
        <div class="card-body">
            <?php if (count($sistematicas_reprovadas) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($sistematicas_reprovadas as $sistematica): ?>
                        <li class="list-group-item">
                            <strong>Disciplina:</strong> <?php echo htmlspecialchars($sistematica['disciplina']); ?><br>
                            <strong>Turma:</strong> <?php echo htmlspecialchars($sistematica['turma']); ?><br>
                            <strong>Ano:</strong> <?php echo htmlspecialchars($sistematica['ano']); ?><br>
                            <strong>Bimestre:</strong> <?php echo htmlspecialchars($sistematica['bimestre']); ?>º<br>
                            <strong>Motivo da Reprovação:</strong> <?php echo htmlspecialchars($sistematica['mensagem_reprovacao']); ?><br>
                          
                            <a href="refazer_sistematica.php?id=<?php echo $sistematica['id']; ?>" class="btn btn-primary btn-sm mt-2"><i class="fas fa-edit"></i> Refazer</a>
                            <a href="excluir_sistematica.php?id=<?php echo $sistematica['id']; ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Tem certeza que deseja excluir esta sistemática?');"><i class="fas fa-trash-alt"></i> Excluir</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-center">Não há sistemáticas reprovadas.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


    <script>
        function confirmLogout() {
            if (confirm('Tem certeza que deseja fazer logout?')) {
                window.location.href = 'logout.php';
            }
        }

        // Configuração do gráfico
        var ctx = document.getElementById('sistematicasChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Concluídas', 'Pendentes'],
                datasets: [{
                    data: [<?php echo $estatisticas['sistematicas_concluidas']; ?>, <?php echo $estatisticas['total_sistematicas'] - $estatisticas['sistematicas_concluidas']; ?>],
                    backgroundColor: ['#28a745', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                }
            }
        });
    </script>
</body>
</html>