<?php
// processar_login.php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, nome, password FROM utilizadores WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login bem sucedido
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_nome'] = $user['nome'];

        header("Location: ../perfil.php");
        exit;
    } else {
        $_SESSION['erros_login'] = ["Email ou password incorretos."];
        header("Location: login.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>