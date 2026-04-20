<?php require_once 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Letudo.pt - A sua loja online de confiança">
    <meta name="keywords" content="loja online, compras, produtos">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Letudo.pt</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1><a href="index.php" style="color: white; text-decoration: none;">Letudo.pt</a></h1>
            </div>
            
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="index.php#produtos">Produtos</a></li>
                    <li><a href="carrinho.php">Carrinho <span id="carrinho-contador">0</span></a></li>
                    <?php if(isset($_SESSION['utilizador_id'])): ?>
                        <li><a href="logout.php">Sair (<?php echo $_SESSION['utilizador_nome']; ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="registro.php">Registar</a></li>
                    <?php endif; ?>
                    <li><a href="admin/login.php">Administração</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>