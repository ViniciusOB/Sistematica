<?php
session_start();

// Verificar se o professor está autenticado
if (!isset($_SESSION['id_professor'])) {
    header("Location: login.php");
    exit();
}

// Conectar ao banco de dados
include 'conexao.php';

// Obter os dados enviados pelo formulário
$id_professor = $_POST['id_professor'] ?? null;
$id_disciplina = $_POST['id_disciplina'] ?? null;
$id_turma = $_POST['id_turma'] ?? null;
$bimestre = $_POST['bimestre'] ?? null;

// Validar campos obrigatórios
$errors = [];
if (empty($id_turma)) $errors[] = 'Turma';
if (empty($id_disciplina)) $errors[] = 'Disciplina';
if (empty($id_professor)) $errors[] = 'Professor';
if (empty($bimestre)) $errors[] = 'Bimestre';

$descricao_av1 = $_POST['descricao_av1'] ?? '';
$data_limite_av1 = $_POST['data_limite_av1'] ?? '';
$descricao_av2 = $_POST['descricao_av2'] ?? '';
$data_limite_av2 = $_POST['data_limite_av2'] ?? '';
$descricao_pd = $_POST['descricao_pd'] ?? [];
$data_limite_pd = $_POST['data_limite_pd'] ?? [];
$descricao_rec = $_POST['descricao_rec'] ?? '';
$data_limite_rec = $_POST['data_limite_rec'] ?? '';

// Verificar campos de avaliações
if (empty($descricao_av1)) $errors[] = 'Descrição da AV1';
if (empty($data_limite_av1)) $errors[] = 'Data Limite AV1';
if (empty($descricao_av2)) $errors[] = 'Descrição da AV2';
if (empty($data_limite_av2)) $errors[] = 'Data Limite AV2';
if (empty($descricao_pd)) $errors[] = 'Descrição dos PDs';
if (empty($data_limite_pd)) $errors[] = 'Data Limite dos PDs';
if (empty($descricao_rec)) $errors[] = 'Descrição da Recuperação';
if (empty($data_limite_rec)) $errors[] = 'Data Limite da Recuperação';

if (!empty($errors)) {
    $error_message = "Erro: Preencha os seguintes campos: " . implode(', ', $errors);
    echo "<script>alert('$error_message'); window.history.back();</script>";
    exit();
}

// Buscar o ID do administrador associado ao professor
$stmt = $pdo->prepare("SELECT id_admin FROM professores WHERE id = :id_professor");
$stmt->execute(['id_professor' => $id_professor]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "<script>alert('Erro: Nenhum administrador associado ao professor.'); window.history.back();</script>";
    exit();
}

$id_admin = $admin['id_admin'];

try {
    // Iniciar a transação
    $pdo->beginTransaction();

    // Buscar o ID da relação professor-disciplina-turma
    $stmt_pdt = $pdo->prepare("
        SELECT id_pdt FROM professordisciplinaturma
        WHERE id_professor = :id_professor AND id_disciplina = :id_disciplina AND id_turma = :id_turma
    ");
    $stmt_pdt->execute([
        ':id_professor' => $id_professor,
        ':id_disciplina' => $id_disciplina,
        ':id_turma' => $id_turma
    ]);
    $pdt = $stmt_pdt->fetch(PDO::FETCH_ASSOC);

    if (!$pdt) {
        echo "<script>alert('Erro: Nenhuma relação professor-disciplina-turma encontrada para a turma selecionada.'); window.history.back();</script>";
        $pdo->rollBack();
        exit();
    }

    $id_pdt = $pdt['id_pdt'];

    // Verificar se já existe uma sistemática para essa turma, disciplina, bimestre e ano
    $stmt_verificar = $pdo->prepare("
        SELECT COUNT(*) FROM Sistematica
        WHERE id_pdt = :id_pdt AND bimestre = :bimestre AND ano = YEAR(CURRENT_DATE)
    ");
    $stmt_verificar->execute([
        ':id_pdt' => $id_pdt,
        ':bimestre' => $bimestre
    ]);
    $sistematica_existe = $stmt_verificar->fetchColumn();

    if ($sistematica_existe > 0) {
        echo "<script>alert('Erro: Já existe uma sistemática para essa turma, disciplina e bimestre neste ano.'); window.history.back();</script>";
        $pdo->rollBack();
        exit();
    }

    // Inserir a sistemática
    $stmt_sistematica = $pdo->prepare("
        INSERT INTO Sistematica (id_admin, id_pdt, bimestre, ano, status)
        VALUES (:id_admin, :id_pdt, :bimestre, YEAR(CURRENT_DATE), 'Pendente')
    ");
    $stmt_sistematica->execute([
        ':id_admin' => $id_admin,
        ':id_pdt' => $id_pdt,
        ':bimestre' => $bimestre
    ]);

    $id_sistematica = $pdo->lastInsertId(); // Obter o ID da sistemática recém-inserida

    // Inserir AV1
    $stmt_av1 = $pdo->prepare("INSERT INTO AV1 (id_sistematica, descricao, data_limite) VALUES (:id_sistematica, :descricao, :data_limite)");
    $stmt_av1->execute([
        ':id_sistematica' => $id_sistematica,
        ':descricao' => $descricao_av1,
        ':data_limite' => $data_limite_av1
    ]);

    // Inserir AV2
    $stmt_av2 = $pdo->prepare("INSERT INTO AV2 (id_sistematica, descricao, data_limite) VALUES (:id_sistematica, :descricao, :data_limite)");
    $stmt_av2->execute([
        ':id_sistematica' => $id_sistematica,
        ':descricao' => $descricao_av2,
        ':data_limite' => $data_limite_av2
    ]);

    // Inserir PDs
    foreach ($descricao_pd as $index => $pd) {
        $stmt_pd = $pdo->prepare("INSERT INTO PD (id_sistematica, descricao, data_limite) VALUES (:id_sistematica, :descricao, :data_limite)");
        $stmt_pd->execute([
            ':id_sistematica' => $id_sistematica,
            ':descricao' => $pd,
            ':data_limite' => $data_limite_pd[$index]
        ]);
    }

    // Inserir Recuperação
    $stmt_rec = $pdo->prepare("INSERT INTO Recuperacao (id_sistematica, descricao, data_limite) VALUES (:id_sistematica, :descricao, :data_limite)");
    $stmt_rec->execute([
        ':id_sistematica' => $id_sistematica,
        ':descricao' => $descricao_rec,
        ':data_limite' => $data_limite_rec
    ]);

    // Confirmar a transação
    $pdo->commit();

    // Redirecionar para o dashboard do professor
    header("Location: dashboard_professor.php");
    exit();
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<script>alert('Erro ao salvar a sistemática: " . $e->getMessage() . "'); window.history.back();</script>";
}
?>
