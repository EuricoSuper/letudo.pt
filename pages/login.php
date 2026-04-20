<?php 
session_start();
require '../config/db.php'; 

if (isset($_SESSION['user_id'])) {
    header("Location: ../perfil.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Entrar | Letudo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="pagina-login">
    <div class="login-card">
        <div class="login-header">
            <h2>Bem-vindo de volta</h2>
            <p>Faça login para continuar</p>
        </div>

        <?php if (isset($_SESSION['sucesso'])): ?>
            <div style="color: green; margin-bottom: 10px;">
                <?= htmlspecialchars($_SESSION['sucesso']); unset($_SESSION['sucesso']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['erros_login'])): ?>
            <div style="color: red; margin-bottom: 10px;">
                <?php foreach ($_SESSION['erros_login'] as $erro): ?>
                    <p><?= htmlspecialchars($erro) ?></p>
                <?php endforeach; unset($_SESSION['erros_login']); ?>
            </div>
        <?php endif; ?>

        <form action="../processar_login.php" method="POST" class="login-body">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>

        <div class="login-footer">
            <a href="../index.php">&larr; Voltar à loja</a> | <a href="registo.php">Criar nova conta</a>
        </div>
    </div>
</body>
</html>
