<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome   = $_POST['usuario_nome'];
    $email  = $_POST['email'];
    $senha  = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $morada = $_POST['morada'];
    $nif    = $_POST['nif'];

    try {
        $sql = "INSERT INTO utilizadores (nome, email, senha, morada, nif) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha, $morada, $nif]);
        
        echo "Conta criada com sucesso! <a href='login.php'>Fazer Login</a>";
    } catch (PDOException $e) {
        echo "Erro ao criar conta: " . $e->getMessage();
    }
}
?>