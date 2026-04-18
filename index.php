<?php
// index.php - Versão mínima para diagnosticar o erro
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste Letudo.pt - Diagnóstico</h1>";

if (!file_exists('config/db.php')) {
    die("<p style='color:red;'>ERRO: O ficheiro config/db.php NÃO foi encontrado!<br>Verifica se a pasta config existe e o ficheiro db.php está dentro dela.</p>");
}

require_once 'config/db.php';

echo "<p style='color:green;'>✓ config/db.php carregado com sucesso.</p>";

if (!isset($pdo)) {
    die("<p style='color:red;'>ERRO: A variável \$pdo não foi definida. Verifica o ficheiro db.php.</p>");
}

echo "<p style='color:green;'>✓ Conexão PDO OK.</p>";

// Testar se a tabela existe
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
    $total = $stmt->fetchColumn();
    echo "<p>Total de livros na tabela: <strong>$total</strong></p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>ERRO na tabela 'produtos': " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<p><a href="pages/registo.php">Ir para Registo</a> | <a href="pages/login.php">Ir para Login</a></p>