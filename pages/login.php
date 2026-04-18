<?php
// pages/login.php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../perfil.php");
    exit;
}

$mensagem = $_SESSION['sucesso'] ?? '';
$erros    = $_SESSION['erros_login'] ?? [];
unset($_SESSION['sucesso'], $_SESSION['erros_login']);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Letudo.pt</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h2>Entrar na tua conta</h2>

    <?php if ($mensagem): ?>
        <p style="color:green;"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <?php foreach ($erros as $erro): ?>
        <p style="color:red;"><?php echo htmlspecialchars($erro); ?></p>
    <?php endforeach; ?>

    <form method="POST" action="processar_login.php">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>Não tens conta? <a href="registo.php">Regista-te aqui</a></p>
</body>
</html>