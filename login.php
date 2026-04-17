<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE username = ?");
    $stmt->execute([$user]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
    } else {
        $erro = "Utilizador ou Palavra-passe incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head><meta charset="UTF-8"><title>Login Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-dark text-white">
<div class="container py-5" style="max-width: 400px;">
    <div class="card text-dark">
        <div class="card-body text-center">
            <h3>Login Administrativo</h3>
            <form method="POST">
                <input type="text" name="user" class="form-control mb-3" placeholder="Utilizador" required>
                <input type="password" name="pass" class="form-control mb-3" placeholder="Password" required>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
            <a href="index.php" class="small mt-3 d-block">Voltar à loja</a>
        </div>
    </div>
</div>
</body>
</html>