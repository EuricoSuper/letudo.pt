<?php
// pages/login.php

session_start();
require_once '../config/db.php';

$mensagem_erro = '';

// Verifica se veio do checkout com mensagem de erro
if (isset($_GET["msg"]) && $_GET["msg"] === "faz_login_para_comprar") {
    $mensagem_erro = "Por favor, faça login para finalizar a sua compra.";
}

// Processamento do formulário de login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Aqui vai a tua lógica de verificação de login (adapta ao teu sistema)
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    $login_bem_sucedido = false;

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $login_bem_sucedido = true;
    }

    // Redirecionamento inteligente
    if ($login_bem_sucedido) {
        if (isset($_GET["msg"]) && $_GET["msg"] === "faz_login_para_comprar") {
            // Veio do checkout → volta para o checkout
            header("Location: checkout.php");
            exit;
        } else {
            // Login normal → vai para a página inicial ou perfil
            header("Location: ../index.php");
            exit;
        }
    } else {
        $mensagem_erro = "Nome de utilizador ou palavra-passe incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LeTudo.pt</title>
    <!-- Inclui aqui os teus CSS (Bootstrap, etc.) -->
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Iniciar Sessão</h2>

            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($mensagem_erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= isset($_GET['msg']) ? '?msg=' . htmlspecialchars($_GET['msg']) : '' ?>">
                
                <div class="mb-3">
                    <label for="username" class="form-label">Nome de utilizador ou Email</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Palavra-passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>

            <div class="text-center mt-3">
                <a href="registo.php">Não tens conta? Regista-te aqui</a>
            </div>
        </div>
    </div>
</div>

<!-- Inclui os teus scripts -->
<script src="../js/script.js"></script>
</body>
</html>