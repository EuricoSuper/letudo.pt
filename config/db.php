<?php
// config/db.php - Conexão segura com PDO

$host = 'localhost';
$dbname = 'letudo';        // muda para o nome da tua BD
$username = 'root';        // muda para o teu user
$password = '';            // muda para a tua password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão à base de dados. Contacta o administrador.");
}
?>