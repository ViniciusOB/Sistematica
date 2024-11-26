<?php
// config/db.php
$host = 'localhost';
$port = '3006'; // Porta do MySQL
$db   = 'sistematica';
$user = 'root'; // Altere para o seu usuário do MySQL
$pass = '';     // Altere para a sua senha do MySQL

try {
    // Adicionando a porta na string de conexão PDO
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
