<?php 
session_start();
require '../config/db.php'; 
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta | Letudo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="pagina-login">
    <div class="login-card">
        <div class="login-header">
            <h2>Registo de Utilizador</h2>
            <p>Preencha os dados para comprar os seus livros</p>
        </div>

        <?php if (isset($_SESSION['erros_registo'])): ?>
            <div style="color: red; margin-bottom: 15px; border: 1px solid red; padding: 10px; border-radius: 5px; background: #fff5f5;">
                <?php foreach ($_SESSION['erros_registo'] as $erro): ?>
                    <p style="margin: 0; font-size: 0.9em;">⚠️ <?= htmlspecialchars($erro) ?></p>
                <?php endforeach; unset($_SESSION['erros_registo']); ?>
            </div>
        <?php endif; ?>

        <form action="../processar_registo.php" method="POST" class="login-body">
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" name="nome" class="form-control" required minlength="3">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="8">

                <label for="confirm_password" style="margin-top: 15px;">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                
                <small style="color:#666; font-size:0.85em; margin-top:8px; display:block;">
                    Mínimo 8 caracteres • 1 maiúscula • 1 minúscula • 1 número • 1 símbolo
                </small>
            </div>

            <div class="form-group">
                <label>Morada</label>
                <input type="text" name="morada" class="form-control">
            </div>

            <div class="form-group">
                <label>NIF (opcional)</label>
                <input type="text" name="nif" class="form-control" maxlength="9">
            </div>

            <button type="submit" class="btn btn-primary w-100" style="margin-top: 15px; padding: 10px; cursor: pointer;">Criar Conta</button>
        </form>

        <div class="login-footer" style="margin-top: 20px; text-align: center;">
            <a href="../index.php" style="text-decoration: none; color: #666;">&larr; Voltar à loja</a> | <a href="login.php" style="text-decoration: none; color: #007bff;">Já tenho conta</a>
        </div>
    </div>
</body>
</html>
