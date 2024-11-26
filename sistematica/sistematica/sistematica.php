<?php
session_start();

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';
$id_professor = $_SESSION['id_professor'];

// Buscar o nome do professor
$stmt = $pdo->prepare("SELECT nome_professor FROM professores WHERE id = :id");
$stmt->execute(['id' => $id_professor]);
$professor = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar todas as disciplinas vinculadas ao professor
$stmt_disciplinas = $conn->prepare("
    SELECT d.id, d.nome 
    FROM professordisciplinaturma pdt
    JOIN disciplina d ON pdt.id_disciplina = d.id
    WHERE pdt.id_professor = :id_professor
");
$stmt_disciplinas->execute(['id_professor' => $id_professor]);
$disciplinas = $stmt_disciplinas->fetchAll(PDO::FETCH_ASSOC);

// Inicialize as variáveis
$turmas = [];
$id_disciplina = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_disciplina'])) {
    $id_disciplina = $_POST['id_disciplina'];

    // Buscar turmas vinculadas ao professor e disciplina selecionada
    $stmt_turma = $conn->prepare("
        SELECT t.id, t.nome 
        FROM professordisciplinaturma pdt
        JOIN turma t ON pdt.id_turma = t.id
        WHERE pdt.id_professor = :id_professor AND pdt.id_disciplina = :id_disciplina
    ");
    $stmt_turma->execute(['id_professor' => $id_professor, 'id_disciplina' => $id_disciplina]);
    $turmas = $stmt_turma->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_SESSION['erro'])): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($_SESSION['erro']); ?>
    </div>
    <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['sucesso'])): ?>
    <div class="success-message">
        <?php echo htmlspecialchars($_SESSION['sucesso']); ?>
    </div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Sistemática</title>
    <link rel="stylesheet" href="CSS/sistematica.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        .error-message {
            color: #e3342f;
            background-color: #f8d7da;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
        }

        .success-message {
            color: #155724;
            background-color: #d4edda;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
        }

    </style>
</head>
<body>

<div class="form-container">
    <h2>Selecione a Matéria para Criar a Sistemática</h2>

    <form id="disciplinaForm" method="POST" action="">
        <label for="id_disciplina">Disciplina:</label>
        <select name="id_disciplina" id="id_disciplina" required onchange="this.form.submit()">
            <option value="">Selecione uma disciplina...</option>
            <?php foreach ($disciplinas as $disciplina): ?>
                <option value="<?php echo htmlspecialchars($disciplina['id']); ?>" <?php echo ($disciplina['id'] == $id_disciplina) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($disciplina['nome']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($id_disciplina && !empty($turmas)): ?>
    <form id="sistematicaForm" method="POST" action="salvar_sistematica.php">
        <input type="hidden" name="id_professor" value="<?php echo htmlspecialchars($id_professor); ?>">
        <input type="hidden" name="id_disciplina" value="<?php echo htmlspecialchars($id_disciplina); ?>">

        <!-- Seções de AV1, AV2, PD e Recuperação -->
        <div class="form-row">
            <!-- AV1 -->
            <div class="form-column">
                <h3>AV1</h3>
                <label for="descricao_av1">Descrição:</label>
                <textarea name="descricao_av1" rows="3" required></textarea>
                <label for="data_limite_av1">Data Limite:</label>
                <input type="date" name="data_limite_av1" required>
            </div>
            <!-- AV2 -->
            <div class="form-column">
                <h3>AV2</h3>
                <label for="descricao_av2">Descrição:</label>
                <textarea name="descricao_av2" rows="3" required></textarea>
                <label for="data_limite_av2">Data Limite:</label>
                <input type="date" name="data_limite_av2" required>
            </div>
            <!-- PD -->
            <div class="form-column">
                <h3>Produtividade</h3>
                <div id="pd-sections">
                    <div class="pd-instance">
                        <label for="descricao_pd[]">Descrição:</label>
                        <textarea name="descricao_pd[]" rows="3" required></textarea>
                        <label for="data_limite_pd[]">Data Limite:</label>
                        <input type="date" name="data_limite_pd[]" required>
                    </div>
                </div>
                <button type="button" onclick="addPD()">Adicionar Outro PD</button>
            </div>
            <!-- Recuperação -->
            <div class="form-column">
                <h3>Recuperação</h3>
                <label for="descricao_rec">Descrição:</label>
                <textarea name="descricao_rec" rows="3" required></textarea>
                <label for="data_limite_rec">Data Limite:</label>
                <input type="date" name="data_limite_rec" required>
            </div>
        </div>

        <!-- Seleção de Turma e Bimestre -->
        <div class="form-footer select">
            <h3>Selecione a Turma e Bimestre</h3>
            <label for="id_turma">Turma:</label>
            <select name="id_turma" required>
                <option value="">Selecione...</option>
                <?php foreach ($turmas as $turma): ?>
                    <option value="<?php echo htmlspecialchars($turma['id']); ?>"><?php echo htmlspecialchars($turma['nome']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="bimestre">Bimestre:</label>
            <select name="bimestre" required>
                <option value="">Selecione...</option>
                <option value="1">1º Bimestre</option>
                <option value="2">2º Bimestre</option>
                <option value="3">3º Bimestre</option>
                <option value="4">4º Bimestre</option>
            </select>

            <button type="submit">Confirmar Envio</button>
        </div>
        <a href="dashboard_professor.php" class="back-button">Voltar</a>
    </form>
    <?php elseif ($id_disciplina): ?>
        <p>Não foram encontradas turmas vinculadas a esta disciplina para o professor. Verifique o banco de dados.</p>
    <?php endif; ?>
</div>

<div class="sidebar">
        <h4><?php echo htmlspecialchars($professor['nome_professor']); ?></h4>
        <a href="dashboard_professor.php">Painel</a>
        <a href="sistematica.php">Sistemática</a>
        <a href="turmas.php">Turmas</a>
        <div class="logout-footer">
            <i class="fas fa-sign-out-alt logout-icon" onclick="confirmLogout()"></i>
        </div>
    </div>

<script>
    function addPD() {
        const pdSection = document.createElement('div');
        pdSection.classList.add('pd-instance');
        pdSection.innerHTML = `
            <label for="descricao_pd[]">Descrição do PD:</label>
            <textarea name="descricao_pd[]" rows="3" required></textarea>
            <label for="data_limite_pd[]">Data Limite PD:</label>
            <input type="date" name="data_limite_pd[]" required>
            <button type="button" class="remove-pd" onclick="removePD(this)"><i class="fas fa-trash"></i></button>
        `;
        document.getElementById('pd-sections').appendChild(pdSection);
    }

    function removePD(button) {
        button.parentElement.remove();
    }

    function confirmLogout() {
            if (confirm('Tem certeza que deseja fazer logout?')) {
                window.location.href = 'logout.php';
            }
        }
</script>

</body>
</html>
