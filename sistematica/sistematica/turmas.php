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

// Processar a seleção do bimestre
$bimestre_selecionado = isset($_POST['bimestre']) ? (int)$_POST['bimestre'] : null;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turmas e Sistemáticas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        .status-pendente {
            color: #ffc107;
        }

        .status-enviada {
            color: #28a745;
        }

        .status-nao-enviada {
            color: #dc3545;
        }

        .aprovacao-aprovada {
            color: #28a745;
        }

        .aprovacao-reprovada {
            color: #dc3545;
        }

        .aprovacao-aguardando {
            color: #ffc107;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h4><?php echo htmlspecialchars($professor['nome_professor']); ?></h4>
    <a href="dashboard_professor.php">Painel</a>
    <a href="sistematica.php">Sistemática</a>
    <a href="turmas.php">Turmas</a>
    <div class="logout-footer">
        <i class="fas fa-sign-out-alt logout-icon" onclick="confirmLogout()"></i>
    </div>
</div>

<div class="main-content">
    <h1>Turmas e Sistemáticas</h1>

    <form method="POST" action="">
        <label for="bimestre">Selecione o Bimestre:</label>
        <select name="bimestre" id="bimestre" onchange="this.form.submit()" required>
            <option value="">Selecione...</option>
            <option value="1" <?php if ($bimestre_selecionado == 1) echo 'selected'; ?>>1º Bimestre</option>
            <option value="2" <?php if ($bimestre_selecionado == 2) echo 'selected'; ?>>2º Bimestre</option>
            <option value="3" <?php if ($bimestre_selecionado == 3) echo 'selected'; ?>>3º Bimestre</option>
            <option value="4" <?php if ($bimestre_selecionado == 4) echo 'selected'; ?>>4º Bimestre</option>
        </select>
    </form>

    <?php if ($bimestre_selecionado): ?>
        <?php
        // Buscar turmas e disciplinas do professor para o bimestre selecionado
        $stmt_turmas = $pdo->prepare("
            SELECT t.nome AS turma_nome, d.nome AS disciplina_nome, s.status, s.aprovada
            FROM professordisciplinaturma pdt
            JOIN turma t ON pdt.id_turma = t.id
            JOIN disciplina d ON pdt.id_disciplina = d.id
            LEFT JOIN Sistematica s ON s.id_pdt = pdt.id_pdt AND s.bimestre = :bimestre
            WHERE pdt.id_professor = :id_professor
        ");
        $stmt_turmas->execute(['bimestre' => $bimestre_selecionado, 'id_professor' => $id_professor]);
        $turmas_disciplinas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Status da Sistemática</th>
                    <th>Aprovação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turmas_disciplinas as $turma_disciplina): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($turma_disciplina['turma_nome']); ?></td>
                        <td><?php echo htmlspecialchars($turma_disciplina['disciplina_nome']); ?></td>
                        <td class="<?php echo !$turma_disciplina['status'] ? 'status-nao-enviada' : ($turma_disciplina['status'] == 'Concluída' ? 'status-enviada' : 'status-pendente'); ?>">
                            <?php
                            if ($turma_disciplina['status']) {
                                echo $turma_disciplina['status'] == 'Concluída' ? 'Enviada' : 'Pendente';
                            } else {
                                echo 'Não enviada';
                            }
                            ?>
                        </td>
                        <td class="<?php echo $turma_disciplina['status'] == 'Concluída' ? (is_null($turma_disciplina['aprovada']) ? 'aprovacao-aguardando' : ($turma_disciplina['aprovada'] == 1 ? 'aprovacao-aprovada' : 'aprovacao-reprovada')) : ''; ?>">
                            <?php
                            if ($turma_disciplina['status'] == 'Concluída') {
                                echo is_null($turma_disciplina['aprovada']) ? 'Aguardando aprovação' :
                                     ($turma_disciplina['aprovada'] == 1 ? 'Aprovada' : 'Reprovada');
                            } else {
                                echo '---';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
