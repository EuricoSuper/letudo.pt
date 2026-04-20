<?php
session_start();

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $id = intval($_POST['produto_id']);
    $_SESSION['carrinho'][$id] = isset($_SESSION['carrinho'][$id]) ? $_SESSION['carrinho'][$id] + 1 : 1;
}

// Volta para a página onde o utilizador estava (index.php)
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;