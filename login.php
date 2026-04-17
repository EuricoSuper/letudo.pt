<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE nome_utilizador = ? AND nivel = 'admin'");
    $stmt->execute([$user]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['palavra_passe'])) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $erro = "Utilizador ou Palavra-passe incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo | Livraria Letudo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-login">

    <div class="login-card">
        <div class="login-header">
            <div class="icon">&#128274;</div>
            <h2>Acesso Administrativo</h2>
            <p>Painel de gestao da livraria</p>
        </div>

        <div class="login-body">
            <?php if(isset($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="user">Utilizador</label>
                    <input type="text" id="user" name="user" class="form-control" placeholder="Nome de utilizador" required>
                </div>

                <div class="form-group">
                    <label for="pass">Palavra-passe</label>
                    <input type="password" id="pass" name="pass" class="form-control" placeholder="A tua password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>

        <div class="login-footer">
            <a href="index.php">&larr; Voltar a loja</a>
        </div>
    </div>

</body>
</html>