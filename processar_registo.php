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

    
    // === VALIDAÇÃO FORTE DA PASSWORD ===

    if (strlen($password) < 8) {
        $erros[] = "A senha deve ter no mínimo 8 caracteres.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $erros[] = "A senha deve conter pelo menos 1 letra maiúscula (A-Z).";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $erros[] = "A senha deve conter pelo menos 1 letra minúscula (a-z).";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $erros[] = "A senha deve conter pelo menos 1 número (0-9).";
    }

    if (!preg_match('/[\W_]/', $password)) {   // caractere especial
        $erros[] = "A senha deve conter pelo menos 1 caractere especial (@ # $ % ! etc).";
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

    // Registar utilizador com hash seguro
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
