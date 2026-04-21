<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

$pageTitle = "Painel de Administração";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1><a href="index.php" style="color: white; text-decoration: none;">Admin - Letudo.pt</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="encomendas.php">Encomendas</a></li>
                    <li><a href="../index.php">Site</a></li>
                    <li><a href="logout.php">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="admin-container">
            <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_nome']); ?>!</h2>
            <p class="mt-2">Selecione uma opção no menu para gerir a loja:</p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <div class="produto-card">
                    <div class="produto-info text-center">
                        <h3>📦 Gerir Produtos</h3>
                        <p>Adicionar, editar e remover produtos</p>
                        <a href="produtos.php" class="btn btn-primario mt-2">Ver Produtos</a>
                    </div>
                </div>
                
                <div class="produto-card">
                    <div class="produto-info text-center">
                        <h3>📋 Encomendas</h3>
                        <p>Visualizar todas as encomendas</p>
                        <a href="encomendas.php" class="btn btn-primario mt-2">Ver Encomendas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>