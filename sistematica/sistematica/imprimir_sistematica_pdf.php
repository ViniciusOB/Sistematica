<?php
session_start();
include 'conexao.php';
require_once('TCPDF-main/tcpdf.php');  // Caminho correto para incluir o TCPDF

// Verificar se o admin está autenticado
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

$id_turma = $_POST['id_turma'] ?? $_GET['id_turma'];
$bimestre = $_POST['bimestre'] ?? $_GET['bimestre'];
$ano = date("Y"); // Define o ano atual ou utilize um campo POST para ano se disponível

// Consulta para buscar todas as sistemáticas da turma e bimestre
$stmt = $pdo->prepare("
    SELECT s.*, d.nome AS disciplina_nome, p.nome_professor, t.nome AS turma_nome, 
           av1.descricao AS av1_desc, av1.data_limite AS av1_data, 
           av2.descricao AS av2_desc, av2.data_limite AS av2_data,
           rec.descricao AS rec_desc, rec.data_limite AS rec_data,
           GROUP_CONCAT(pd.descricao ORDER BY pd.data_limite ASC SEPARATOR '||') AS pd_descs,
           GROUP_CONCAT(pd.data_limite ORDER BY pd.data_limite ASC SEPARATOR '||') AS pd_datas
    FROM Sistematica s
    JOIN professordisciplinaturma pdt ON s.id_pdt = pdt.id_pdt
    JOIN disciplina d ON pdt.id_disciplina = d.id
    JOIN professores p ON pdt.id_professor = p.id
    JOIN turma t ON pdt.id_turma = t.id
    LEFT JOIN AV1 av1 ON av1.id_sistematica = s.id
    LEFT JOIN AV2 av2 ON av2.id_sistematica = s.id
    LEFT JOIN Recuperacao rec ON rec.id_sistematica = s.id
    LEFT JOIN PD pd ON pd.id_sistematica = s.id
    WHERE s.bimestre = :bimestre AND s.ano = :ano AND pdt.id_turma = :id_turma
    GROUP BY s.id
");

$stmt->execute(['bimestre' => $bimestre, 'ano' => $ano, 'id_turma' => $id_turma]);
$sistematicas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Criar uma nova instância do TCPDF
$pdf = new TCPDF();
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Adicionar o título
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Sistemáticas Aprovadas - ' . htmlspecialchars($bimestre) . ' - ' . htmlspecialchars($ano), 0, 1, 'C');
$pdf->Ln(10);

// Adicionar o conteúdo das sistemáticas aprovadas
foreach ($sistematicas as $sistematica) {
    if ($sistematica['aprovada'] == 1) { // Somente sistemáticas aprovadas
        
        // Adicionar o nome da disciplina e o nome do professor
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Disciplina: ' . htmlspecialchars($sistematica['disciplina_nome']), 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Professor: ' . htmlspecialchars($sistematica['nome_professor']), 0, 1);
        $pdf->Cell(0, 10, 'Turma: ' . htmlspecialchars($sistematica['turma_nome']), 0, 1);
        $pdf->Cell(0, 10, 'Bimestre: ' . htmlspecialchars($sistematica['bimestre']), 0, 1);
        $pdf->Cell(0, 10, 'Ano: ' . htmlspecialchars($sistematica['ano']), 0, 1);
        $pdf->Ln(5);  // Adiciona um espaço extra

        // Avaliação 1 (AV1)
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'AV1', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        // Garantir que a descrição e a data fiquem na mesma linha
        $pdf->MultiCell(0, 10, 'Descrição: ' . htmlspecialchars($sistematica['av1_desc']), 0, 'L');
        $pdf->Cell(0, 10, 'Data Limite: ' . htmlspecialchars($sistematica['av1_data']), 0, 1);
        $pdf->Ln(5);  // Adiciona um espaço extra

        // Avaliação 2 (AV2)
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'AV2', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, 'Descrição: ' . htmlspecialchars($sistematica['av2_desc']), 0, 'L');
        $pdf->Cell(0, 10, 'Data Limite: ' . htmlspecialchars($sistematica['av2_data']), 0, 1);
        $pdf->Ln(5);  // Adiciona um espaço extra

        // Projetos de Desenvolvimento (PD)
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Projetos de Desenvolvimento (PD)', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pd_descs = explode('||', $sistematica['pd_descs']);
        $pd_datas = explode('||', $sistematica['pd_datas']);
        foreach ($pd_descs as $index => $pd_desc) {
            $pdf->MultiCell(0, 10, 'Descrição: ' . htmlspecialchars($pd_desc), 0, 'L');
            $pdf->Cell(0, 10, 'Data Limite: ' . htmlspecialchars($pd_datas[$index]), 0, 1);
        }
        $pdf->Ln(5);  // Adiciona um espaço extra

        // Recuperação
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Recuperação', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->MultiCell(0, 10, 'Descrição: ' . htmlspecialchars($sistematica['rec_desc']), 0, 'L');
        $pdf->Cell(0, 10, 'Data Limite: ' . htmlspecialchars($sistematica['rec_data']), 0, 1);
        $pdf->Ln(10);  // Adiciona um espaço extra entre as sistemáticas
    }
}

// Fechar e gerar o PDF
$pdf->Output('sistematica_aprovada.pdf', 'I');
?>
