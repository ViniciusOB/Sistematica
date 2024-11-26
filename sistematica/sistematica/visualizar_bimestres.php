<?php
session_start();

// Verificar se o admin está autenticado
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

// Verificar se o ID da turma foi enviado
if (!isset($_POST['id_turma'])) {
    header("Location: visualizar_sistematica.php");
    exit();
}

$id_turma = $_POST['id_turma'];

include 'views/header_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Bimestres</title>
    <link rel="stylesheet" href="CSS/visualizar_bimestres.css"> <!-- CSS externo para o layout -->
</head>
<body>

    <!-- Conteúdo principal -->
    <div class="main-content">
        <h2>Selecione o Bimestre</h2>

        <div class="bimestre-buttons">
            <form action="visualizar_sistematica_por_bimestre.php" method="POST">
                <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                <input type="hidden" name="bimestre" value="1">
                <button class="bimestre-button">1º Bimestre</button>
            </form>
            <form action="visualizar_sistematica_por_bimestre.php" method="POST">
                <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                <input type="hidden" name="bimestre" value="2">
                <button class="bimestre-button">2º Bimestre</button>
            </form>
            <form action="visualizar_sistematica_por_bimestre.php" method="POST">
                <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                <input type="hidden" name="bimestre" value="3">
                <button class="bimestre-button">3º Bimestre</button>
            </form>
            <form action="visualizar_sistematica_por_bimestre.php" method="POST">
                <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                <input type="hidden" name="bimestre" value="4">
                <button class="bimestre-button">4º Bimestre</button>
            </form>
        </div>
    </div>

</body>
</html>
