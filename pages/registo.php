<?php require '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta | Letudo</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="pagina-login"> <div class="login-card">
        <div class="login-header">
            <h2>Registo de Utilizador</h2>
            <p>Preencha os dados para comprar os seus livros</p>
        </div>

        <form action="processar_registo.php" method="POST" class="login-body">
            <div class="form-group">
                <label>Nome de Usuário</label>
                <input type="text" name="usuario_nome" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Morada</label>
                <input type="text" name="morada" class="form-control">
            </div>

            <div class="form-group">
                <label>NIF</label>
                <input type="text" name="nif" class="form-control" maxlength="9">
            </div>

            <button type="submit" class="btn btn-primary w-100">Criar Conta</button>
        </form>

        <div class="login-footer">
            <a href="../index.php">&larr; Voltar à loja</a>
        </div>
    </div>

</body>
</html>