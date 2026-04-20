<?php
// config/db.php - Conexao PDO a MySQL/MariaDB
// ============================================================
// CREDENCIAIS PARA XAMPP / WAMP / MAMP
// Por defeito: user = root, password = vazio
// Se mudaste a password do root no teu MySQL, edita abaixo.
// ============================================================

$host     = '127.0.0.1';
$dbname   = 'letudo';
$username = 'root';
$password = '';              // <- Se o teu root tem password, poe aqui

// ---- NAO EDITAR ABAIXO ----
// (permite override via variaveis de ambiente, usado no servidor de desenvolvimento)
if (getenv('DB_HOST'))        $host     = getenv('DB_HOST');
if (getenv('DB_NAME_LETUDO')) $dbname   = getenv('DB_NAME_LETUDO');
if (getenv('DB_USER'))        $username = getenv('DB_USER');
if (getenv('DB_PASS') !== false) $password = getenv('DB_PASS');
