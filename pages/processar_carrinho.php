<?php
session_start();

// Se o carrinho não existir, cria um vazio
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $id = intval($_POST['produto_id']);
    
    // Se o livro já estiver no carrinho, aumenta a quantidade, senão adiciona com 1
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]++;
    } else {
        $_SESSION['carrinho'][$id] = 1;
    }
}

// Volta para a página inicial para continuar a comprar
header("Location: ../index.php");
exit;