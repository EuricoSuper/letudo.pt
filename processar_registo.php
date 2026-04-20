<?php
// processar_registo.php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $morada   = trim($_POST['morada'] ?? '');
    $nif      = trim($_POST['nif'] ?? '');

    $erros = [];

    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido (mínimo 3 caracteres).";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Email inválido.";
    }

    // Validação robusta de senha: 
    // Mínimo 8 caracteres, pelo menos uma maiúscula, uma minúscula, um número e um caractere especial.
    if (strlen($password) < 8) {
        $erros[] = "A password deve ter pelo menos 8 caracteres.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $erros[] = "A password deve conter pelo menos uma letra maiúscula.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $erros[] = "A password deve conter pelo menos uma letra minúscula.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $erros[] = "A password deve conter pelo menos um número.";
    }
    if (!preg_match('/[\W_]/', $password)) {
        $erros[] = "A password deve conter pelo menos um caractere especial (ex: @, #, $, %, etc.).";
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
        header("Location: pages/registo.php");
        exit;
    }

    // Registar utilizador
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO utilizadores (nome, email, password, morada, nif, data_registo) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $hash, $morada, $nif]);

        $_SESSION['sucesso'] = "Registo efetuado com sucesso! Podes fazer login.";
        header("Location: pages/login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['erros_registo'] = ["Erro ao processar o registo. Tente novamente mais tarde."];
        header("Location: pages/registo.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
