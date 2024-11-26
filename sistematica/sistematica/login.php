<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_usuario = $_POST['email'];
    $senha_usuario = $_POST['senha'];
    try {
        $isAdmin = false;
        $isProfessor = false;

        // Verifica na tabela de admin
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email_admin = :email");
        $stmt->execute(['email' => $email_usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se não encontrar na tabela de admin, verifica na tabela de professores
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM professores WHERE email_professor = :email");
            $stmt->execute(['email' => $email_usuario]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $isProfessor = $user ? true : false;
        } else {
            $isAdmin = true;
        }

        if ($user) {
            // Define a senha correta de acordo com o tipo de usuário
            if ($isAdmin) {
                $hashedPassword = $user['senha_admin'];
            } elseif ($isProfessor) {
                $hashedPassword = $user['senha_professor'];
            }

            // Verifica a senha
            if (password_verify($senha_usuario, $hashedPassword) || $senha_usuario === $hashedPassword) {
                // Define a sessão e o redirecionamento com base na tabela de origem
                $_SESSION['email'] = $isAdmin ? $user['email_admin'] : $user['email_professor'];
                $_SESSION['role'] = $isAdmin ? 'admin' : 'professor';

                // Armazena IDs separadamente
                if ($isAdmin) {
                    $_SESSION['id_admin'] = $user['id_admin'];
                    header("Location: dashboard_admin.php");
                } elseif ($isProfessor) {
                    $_SESSION['id_professor'] = $user['id'];
                    header("Location: dashboard_professor.php");
                }
                exit();
            } else {
                echo "Senha incorreta.";
            }
        } else {
            echo "Usuário não encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro ao acessar o banco de dados: " . $e->getMessage();
    }
}
?>
