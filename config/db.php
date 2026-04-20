<?php
// config/db.php - Conexao PDO a MariaDB/MySQL
$host     = getenv('DB_HOST') ?: '127.0.0.1';
$dbname   = getenv('DB_NAME_LETUDO') ?: 'letudo';
$username = getenv('DB_USER') ?: 'letudo';
$password = getenv('DB_PASS') ?: 'letudo_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Erro de conexao a BD: ' . htmlspecialchars($e->getMessage()));
}
