<?php
// public/admin/dashboard.php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['papel'] != 'Administrador') {
    header('Location: ../login.php');
    exit;
}

// Variáveis para armazenar os filtros de busca
$ano = isset($_GET['ano']) ? $_GET['ano'] : '';
$bimestre = isset($_GET['bimestre']) ? $_GET['bimestre'] : '';
$nivel_ensino = isset($_GET['nivel_ensino']) ? $_GET['nivel_ensino'] : ''; // Novo filtro

// Consulta para obter as avaliações com base nos filtros de ano, bimestre e nível de ensino
$query = "
    SELECT S.*, D.nome AS nome_disciplina, T.nome AS nome_turma, T.nivel_ensino, U.nome_completo AS nome_professor
    FROM Sistematica S
    JOIN Disciplina D ON S.disciplina_id = D.id
    JOIN Turma T ON S.turma_id = T.id
    JOIN Usuario U ON S.professor_id = U.id
    WHERE 1 = 1
";

$params = [];

// Adiciona o filtro de ano se estiver preenchido
if (!empty($ano)) {
    $query .= " AND S.ano = :ano";
    $params['ano'] = $ano;
}

// Adiciona o filtro de bimestre se estiver preenchido
if (!empty($bimestre)) {
    $query .= " AND S.bimestre = :bimestre";
    $params['bimestre'] = $bimestre;
}

// Adiciona o filtro de nível de ensino se estiver preenchido
if (!empty($nivel_ensino)) {
    $query .= " AND T.nivel_ensino = :nivel_ensino";
    $params['nivel_ensino'] = $nivel_ensino;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para aprovar ou reprovar a avaliação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['aprovar'])) {
        // Aprovar a sistemática
        $id = $_POST['id'];
        $stmt = $pdo->prepare("UPDATE Sistematica SET aprovada = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header('Location: dashboard.php'); // Redireciona para evitar o reenvio do formulário
        exit;
    }

    if (isset($_POST['reprovar']) && !empty($_POST['mensagem_reprovacao'])) {
        // Reprovar a sistemática e enviar mensagem ao professor
        $id = $_POST['id'];
        $mensagem = $_POST['mensagem_reprovacao'];
        $stmt = $pdo->prepare("UPDATE Sistematica SET aprovada = 0, mensagem_reprovacao = :mensagem WHERE id = :id");
        $stmt->execute(['id' => $id, 'mensagem' => $mensagem]);
        header('Location: dashboard.php'); // Redireciona para evitar o reenvio do formulário
        exit;
    }
}

?>

<?php include '../../templates/header.php'; ?>

<div class="container mt-5">
    <h2>Painel do Administrador</h2>

    <!-- Menu de Navegação do Administrador -->
<!-- Menu de Navegação do Administrador -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <a class="navbar-brand" href="#">Menu Administrador</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Início</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gerenciar_usuarios.php">Gerenciar Usuários</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gerenciar_disciplinas.php">Gerenciar Disciplinas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="gerenciar_turmas.php">Gerenciar Turmas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="associar_professor.php">Associar Professor à Disciplina</a> <!-- Novo link -->
            </li>
        </ul>
    </div>
</nav>


    <!-- Filtros de Avaliações -->
    <h4>Filtrar Avaliações</h4>
    <form action="dashboard.php" method="GET" class="form-inline mb-4">
        <div class="form-group mr-3">
            <label for="ano" class="mr-2">Ano:</label>
            <input type="number" name="ano" id="ano" class="form-control" value="<?php echo htmlspecialchars($ano); ?>" placeholder="Digite o ano">
        </div>

        <div class="form-group mr-3">
            <label for="bimestre" class="mr-2">Bimestre:</label>
            <select name="bimestre" id="bimestre" class="form-control">
                <option value="">Todos os Bimestres</option>
                <option value="1" <?php if ($bimestre == '1') echo 'selected'; ?>>1º Bimestre</option>
                <option value="2" <?php if ($bimestre == '2') echo 'selected'; ?>>2º Bimestre</option>
                <option value="3" <?php if ($bimestre == '3') echo 'selected'; ?>>3º Bimestre</option>
                <option value="4" <?php if ($bimestre == '4') echo 'selected'; ?>>4º Bimestre</option>
            </select>
        </div>

        <div class="form-group mr-3">
            <label for="nivel_ensino" class="mr-2">Nível de Ensino:</label>
            <select name="nivel_ensino" id="nivel_ensino" class="form-control">
                <option value="">Todos os Níveis</option>
                <option value="Fundamental 2" <?php if ($nivel_ensino == 'Fundamental 2') echo 'selected'; ?>>Fundamental 2</option>
                <option value="Ensino Médio" <?php if ($nivel_ensino == 'Ensino Médio') echo 'selected'; ?>>Ensino Médio</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <h4>Resultados das Avaliações</h4>

    <?php if (empty($avaliacoes)): ?>
        <p>Nenhuma avaliação encontrada com os filtros aplicados.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Disciplina</th>
                    <th>Turma</th>
                    <th>Nível de Ensino</th>
                    <th>Professor</th>
                    <th>Tipo</th>
                    <th>Bimestre</th>
                    <th>Ano</th>
                    <th>Data de Entrega</th>
                    <th>Status</th>
                    <th>Aprovação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($avaliacoes as $avaliacao): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($avaliacao['nome_disciplina']); ?></td>
                        <td><?php echo htmlspecialchars($avaliacao['nome_turma']); ?></td>
                        <td><?php echo htmlspecialchars($avaliacao['nivel_ensino']); ?></td>
                        <td><?php echo htmlspecialchars($avaliacao['nome_professor']); ?></td>
                        <td><?php echo htmlspecialchars($avaliacao['tipo_avaliacao']); ?></td>
                        <td><?php echo htmlspecialchars($avaliacao['bimestre']); ?>º</td>
                        <td><?php echo htmlspecialchars($avaliacao['ano']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($avaliacao['data_entrega'])); ?></td>
                        <td><?php echo ($avaliacao['aprovada'] === NULL) ? 'Não Revisada' : (($avaliacao['aprovada'] == 1) ? 'Aprovada' : 'Reprovada'); ?></td>
                        <td>
                            <?php if ($avaliacao['aprovada'] === NULL): ?>
                                <form action="dashboard.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $avaliacao['id']; ?>">
                                    <button type="submit" name="aprovar" class="btn btn-success btn-sm">Aprovar</button>
                                </form>
                                <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reprovarModal<?php echo $avaliacao['id']; ?>">Reprovar</button>
                            <?php elseif ($avaliacao['aprovada'] == 0): ?>
                                <span class="badge badge-danger">Reprovada</span>
                            <?php elseif ($avaliacao['aprovada'] == 1): ?>
                                <span class="badge badge-success">Aprovada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="editar_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="excluir_avaliacao.php?id=<?php echo $avaliacao['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?');">Excluir</a>
                        </td>
                    </tr>

                    <!-- Modal para reprovar -->
                    <div class="modal fade" id="reprovarModal<?php echo $avaliacao['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="reprovarModalLabel<?php echo $avaliacao['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reprovarModalLabel<?php echo $avaliacao['id']; ?>">Reprovar Avaliação</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="dashboard.php" method="POST">
                                        <div class="form-group">
                                            <label for="mensagem_reprovacao">Motivo da Reprovação</label>
                                            <textarea name="mensagem_reprovacao" id="mensagem_reprovacao" class="form-control" required></textarea>
                                        </div>
                                        <input type="hidden" name="id" value="<?php echo $avaliacao['id']; ?>">
                                        <button type="submit" name="reprovar" class="btn btn-danger">Enviar Reprovação</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../../templates/footer.php'; ?>
