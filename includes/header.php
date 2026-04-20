<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($pdo)) { require_once __DIR__ . '/../config/db.php'; }
$total_carrinho_itens = isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;
$utilizador_logado    = isset($_SESSION['user_id']);
$nome_utilizador      = $_SESSION['user_nome'] ?? '';
$is_admin             = ($_SESSION['user_tipo'] ?? '') === 'admin';
$base                 = $base ?? '';
$pagina_atual         = $pagina_atual ?? '';

// Categorias para menu
try {
    $cats = $pdo->query("SELECT DISTINCT categoria FROM produtos ORDER BY categoria")->fetchAll();
} catch (Throwable $e) { $cats = []; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Letudo.pt - A sua livraria online. Livros de ficcao, biografia, infantis, religiao, autoajuda e muito mais. Envios rapidos para todo o pais.">
<meta name="keywords" content="livraria, livros, comprar livros online, letudo, almedina, ficcao, biografia">
<meta name="author" content="Letudo.pt">
<title><?= htmlspecialchars($titulo ?? 'Letudo.pt | A sua Livraria Online') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Libre+Franklin:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>&#128218;</text></svg>">
</head>
<body>

<!-- Top Bar -->
<div class="topbar">
    <div class="container topbar-inner">
        <span>Envios gratuitos em Portugal para compras superiores a 30&euro;</span>
        <div class="topbar-links">
            <a href="<?= $base ?>index.php?pagina=ajuda">Ajuda</a>
            <a href="<?= $base ?>index.php?pagina=contactos">Contactos</a>
            <?php if ($utilizador_logado): ?>
                <a href="<?= $base ?>perfil.php">Ola, <?= htmlspecialchars(explode(' ', $nome_utilizador)[0]) ?></a>
                <a href="<?= $base ?>logout.php">Sair</a>
            <?php else: ?>
                <a href="<?= $base ?>pages/login.php">Iniciar sessao</a>
                <a href="<?= $base ?>pages/registo.php">Registar</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Header -->
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= $base ?>index.php" class="brand" data-testid="brand-logo">
            <span class="brand-mark">L</span>
            <span class="brand-text">letudo<span class="brand-dot">.pt</span></span>
        </a>

        <form class="search-bar" method="GET" action="<?= $base ?>index.php" role="search">
            <select name="cat" class="search-cat" aria-label="Categoria">
                <option value="">Todas as categorias</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= htmlspecialchars($c['categoria']) ?>" <?= (($_GET['cat'] ?? '') === $c['categoria']) ? 'selected' : '' ?>><?= htmlspecialchars($c['categoria']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Procurar por titulo, autor ou categoria..." data-testid="search-input">
            <button type="submit" aria-label="Procurar" data-testid="search-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>

        <div class="header-actions">
            <?php if ($is_admin): ?>
                <a href="<?= $base ?>admin/index.php" class="icon-link" data-testid="admin-link" title="Painel Admin">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                    <span>Admin</span>
                </a>
            <?php endif; ?>
            <a href="<?= $base ?><?= $utilizador_logado ? 'perfil.php' : 'pages/login.php' ?>" class="icon-link" data-testid="account-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span><?= $utilizador_logado ? 'Conta' : 'Entrar' ?></span>
            </a>
            <a href="<?= $base ?>pages/checkout.php" class="icon-link cart-link" data-testid="cart-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span>Carrinho</span>
                <?php if ($total_carrinho_itens > 0): ?>
                    <span class="cart-badge" data-testid="cart-badge"><?= (int)$total_carrinho_itens ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Nav Categorias -->
    <nav class="cat-nav">
        <div class="container cat-nav-inner">
            <a href="<?= $base ?>index.php" class="<?= $pagina_atual === 'home' ? 'active' : '' ?>">Inicio</a>
            <?php foreach (array_slice($cats, 0, 8) as $c): ?>
                <a href="<?= $base ?>index.php?cat=<?= urlencode($c['categoria']) ?>"><?= htmlspecialchars($c['categoria']) ?></a>
            <?php endforeach; ?>
            <a href="<?= $base ?>index.php?destaque=1" class="destaque-link">Destaques</a>
        </div>
    </nav>
</header>

<main class="main-content">
