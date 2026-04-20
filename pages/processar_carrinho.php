<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';
$redir = '../index.php';

if ($acao === 'adicionar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (int)($_POST['produto_id'] ?? 0);
    $qtd = max(1, (int)($_POST['quantidade'] ?? 1));
    $stmt = $pdo->prepare("SELECT quantidade_disponivel FROM produtos WHERE id=?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) { header("Location: $redir"); exit; }
    if ($prod['quantidade_disponivel'] <= 0) { header("Location: $redir?msg=esgotado"); exit; }
    $atual = $_SESSION['carrinho'][$id] ?? 0;
    if (($atual + $qtd) > $prod['quantidade_disponivel']) {
        header("Location: $redir?msg=sem_stock"); exit;
    }
    $_SESSION['carrinho'][$id] = $atual + $qtd;
    header("Location: $redir?msg=ok_add#catalogo"); exit;
}

if ($acao === 'atualizar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id  = (int)($_POST['produto_id'] ?? 0);
    $qtd = max(0, (int)($_POST['quantidade'] ?? 0));
    if ($qtd === 0) {
        unset($_SESSION['carrinho'][$id]);
    } else {
        $stmt = $pdo->prepare("SELECT quantidade_disponivel FROM produtos WHERE id=?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch();
        if ($prod && $qtd <= $prod['quantidade_disponivel']) {
            $_SESSION['carrinho'][$id] = $qtd;
        } else {
            header("Location: checkout.php?msg=sem_stock"); exit;
        }
    }
    header("Location: checkout.php"); exit;
}

if ($acao === 'remover') {
    $id = (int)($_GET['id'] ?? $_POST['produto_id'] ?? 0);
    unset($_SESSION['carrinho'][$id]);
    header("Location: checkout.php"); exit;
}

if ($acao === 'limpar') {
    $_SESSION['carrinho'] = [];
    header("Location: checkout.php"); exit;
}

header("Location: $redir");
