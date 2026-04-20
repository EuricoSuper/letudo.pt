<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    $erros = [];

    // Validação nome e email
    if (strlen($nome) < 3) $erros[] = "O nome deve ter pelo menos 3 caracteres.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "Email inválido.";

    // Validação forte da password
    if (strlen($password) < 8) {
        $erros[] = "A password deve ter no mínimo 8 caracteres.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $erros[] = "A password deve conter pelo menos 1 letra maiúscula.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $erros[] = "A password deve conter pelo menos 1 letra minúscula.";
    }
    if (!preg_match('/[\W_]/', $password)) {   // símbolo especial
        $erros[] = "A password deve conter pelo menos 1 símbolo especial (! @ # $ % etc).";
    }
    if ($password !== $confirm) {
        $erros[] = "As passwords não coincidem.";
    }

    // Verificar se email já existe
    if (empty($erros)) {
        $stmt = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $erros[] = "Este email já está registado.";
        }
    }

    if (!empty($erros)) {
        $_SESSION['erros_registo'] = $erros;
        header("Location: ../pages/registo.php");
        exit;
    }

    // Registar
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, data_registo) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$nome, $email, $hash]);

    $_SESSION['sucesso'] = "Registo efetuado com sucesso! Já podes fazer login.";
    header("Location: ../pages/login.php");
    exit;
}

header("Location: ../index.php");
exit;
?>