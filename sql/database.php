<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'letudo_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

session_start();

function limparDados($conn, $dados) {
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($dados)));
}

function verificarIdade($dataNascimento) {
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    $idade = $nascimento->diff($hoje)->y;
    return $idade >= 18;
}
?>