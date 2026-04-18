<?php
// index.php - Versão de emergência (sem depender do db.php)
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Letudo.pt - Modo Diagnóstico</h1>";
echo "<p style='color:orange;'>A tentar carregar...</p>";

if (file_exists('config/db.php')) {
    require_once 'config/db.php';
    echo "<p style='color:green;'>✓ config/db.php encontrado e carregado.</p>";
    
    if (isset($pdo)) {
        echo "<p style='color:green;'>✓ Conexão à BD OK.</p>";
    } else {
        echo "<p style='color:red;'>✗ Variável \$pdo não definida.</p>";
    }
} else {
    echo "<p style='color:red;'>✗ ERRO: config/db.php NÃO existe!<br>";
    echo "Cria a pasta config/ e o ficheiro db.php dentro dela.</p>";
}

echo "<hr>";
echo "<p><a href='pages/registo.php'>Registo</a> | <a href='pages/login.php'>Login</a></p>";
echo "<p><small>Última atualização: " . date('H:i:s') . "</small></p>";
?>