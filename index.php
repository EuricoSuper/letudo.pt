<?php require 'config/db.php'; ?>

// Verifica se existe alguém logado
$logado = isset($_SESSION['usuario_id']);
?>

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
<nav class="nav-buttons">
    <?php if(isset($_SESSION['usuario_id'])): ?>
        <a href="pages/perfil.php" class="btn btn-sm">👤 Minha Conta</a>
        
        <?php if(isset($_SESSION['admin'])): ?>
            <a href="admin.php" class="btn btn-warning btn-sm">Painel Admin</a>
        <?php endif; ?>

        <a href="pages/logout.php" class="btn btn-outline btn-sm">Sair</a>

    <?php else: ?>
        <a href="pages/login.php" class="btn">Entrar</a>
        <a href="pages/registo.php" class="btn">Registar</a>
    <?php endif; ?>
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
                <?php if(!empty($p['imagem'])): ?>
                <img src="img/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" style="width: 100%; height: 100%; object-fit: contain;">
                <?php else: ?>
                <span style="font-size: 50px;">📖</span>
                <?php endif; ?>
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