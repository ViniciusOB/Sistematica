<?php
// Informações de conexão ao banco de dados
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'sistematica_vcjf';

try {
    // Cria uma nova instância de PDO para conectar ao banco de dados
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUsername, $dbPassword);
    // Define o modo de erro do PDO para lançar exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Define o modo de busca para FETCH_ASSOC (retorna apenas os valores associativos)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $conn = $pdo;

} catch (PDOException $e) {
    // Exibe uma mensagem de erro com detalhes do problema
    echo "<h3>Erro na conexão</h3>";
    echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Banco de Dados:</strong> $dbName</p>";
    echo "<p><strong>Host:</strong> $dbHost</p>";
    echo "<p><strong>Usuário:</strong> $dbUsername</p>";
}
