<?php
// config/db.php - Conexão corrigida para a tua BD "letudo.pt"

$host       = 'localhost';
$dbname     = 'letudo.pt';     // ← Nome exato com o ponto
$username   = 'root';
$password   = '';              // deixa vazio se não puseste password no root

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Erro de conexão à BD:<br><strong>" . htmlspecialchars($e->getMessage()) . "</strong>");
}
?>