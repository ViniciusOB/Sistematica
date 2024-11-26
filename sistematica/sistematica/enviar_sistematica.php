<?php
include 'conexao.php';

// Verificar se os dados foram enviados corretamente
$id_disciplina = $_POST['id_disciplina'];
$id_professor = $_POST['id_professor'];
$bimestre = $_POST['bimestre'];
$turmas = $_POST['turmas']; // Array de turmas
$ano = date('Y'); // Definir o ano atual

// Buscar o ID do administrador associado ao professor
$stmt_admin = $pdo->prepare("SELECT id_admin FROM professores WHERE id = :id_professor");
$stmt_admin->execute(['id_professor' => $id_professor]);
$admin = $stmt_admin->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    echo "<script>alert('Erro: Nenhum administrador associado ao professor.'); window.history.back();</script>";
    exit();
}

$id_admin = $admin['id_admin'];

// Inserir a sistemática para cada turma
foreach ($turmas as $id_turma) {
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
        echo "<script>alert('Erro: Nenhuma relação professor-disciplina-turma encontrada.'); window.history.back();</script>";
        exit();
    }

    $id_pdt = $pdt['id_pdt'];

    // Inserir a sistemática diretamente na tabela Sistematica
    $query = "
        INSERT INTO Sistematica (id_admin, id_pdt, bimestre, ano, status)
        VALUES (:id_admin, :id_pdt, :bimestre, :ano, 'Pendente')
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':id_admin' => $id_admin,
        ':id_pdt' => $id_pdt,
        ':bimestre' => $bimestre,
        ':ano' => $ano
    ]);
}

// Redirecionar para a página de sucesso
header("Location: sucesso.php");
exit();
?>
