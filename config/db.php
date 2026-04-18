<?php
// config/db.php - Conexão à base de dados

$host = 'localhost';
$dbname = 'letudo';        // ← muda se o nome da tua base de dados for diferente
$username = 'root';        // ← muda para o utilizador da tua BD (normalmente root)
$password = '';            // ← mete aqui a password da tua BD (se não tiveres, deixa vazio)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro fatal de conexão à BD: " . $e->getMessage());
}
?>