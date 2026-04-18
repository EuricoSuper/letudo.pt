<?php
// processar_registo.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $erros = [];

    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido (mínimo 3 caracteres).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }
    if (strlen($password) < 6) {
        $erros[] = "A password deve ter pelo menos 6 caracteres.";
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
        header("Location: ../pages/registo.php"); // ajusta o caminho
        exit;
    }

    // Registar utilizador
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, data_registo) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$nome, $email, $hash]);

    $_SESSION['sucesso'] = "Registo efetuado com sucesso! Podes fazer login.";
    header("Location: ../pages/login.php");
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
?>