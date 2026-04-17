<?php require 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraria Online | Descobre os Melhores Livros</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navegacao -->
<nav class="navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php">&#128218; Livraria Letudo</a>
        <div class="nav-buttons">
            <?php if(isset($_SESSION['admin'])): ?>
                <a href="admin.php" class="btn btn-warning btn-sm">Painel Admin</a>
                <a href="logout.php" class="btn btn-outline btn-sm">Sair</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline btn-sm">Login Admin</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-icon">&#128218;</span>
    <h1>Livraria Letudo</h1>
        <p class="hero-subtitle">Descobre historias que transformam vidas. Encontra o teu proximo livro favorito.</p>
    </div>
</section>

<!-- Catalogo de Livros -->
<section class="secao-livros">
    <div class="secao-titulo">
        <h2>O Nosso Catalogo</h2>
        <p class="subtitulo">12 titulos cuidadosamente selecionados para ti</p>
    </div>

    <div class="livros-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM produtos");
        while ($p = $stmt->fetch()):
        ?>
        <div class="livro-card">
            <?php if($p['quantidade_disponivel'] <= 0): ?>
                <span class="badge-esgotado">Esgotado</span>
            <?php endif; ?>

            <div class="livro-imagem">
                &#128214;
            </div>

            <div class="livro-conteudo">
                <h3 class="livro-titulo"><?= $p['nome'] ?></h3>
                <p class="livro-preco">
                    <span class="moeda">&euro;</span><?= number_format($p['preco_unidade'], 2) ?>
                </p>
                <p class="livro-stock <?= $p['quantidade_disponivel'] > 0 ? 'stock-disponivel' : 'stock-esgotado' ?>">
                    <?= $p['quantidade_disponivel'] > 0 ? 'Em stock' : 'Fora de stock' ?> &middot; <?= $p['quantidade_disponivel'] ?> unid.
                </p>

                <?php if($p['quantidade_disponivel'] > 0): ?>
                    <a href="checkout.php?id=<?= $p['id'] ?>" class="btn btn-primary">Comprar Agora</a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Esgotado</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <p>&copy; 2026 Livraria Letudo. Todos os direitos reservados.</p>
</footer>

</body>
</html>