<?php
session_start();
require_once 'config/db.php';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraria Letudo | Descobre os Melhores Livros</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navegação -->
<nav class="navbar-custom">
    <!-- NOVO: Contador do Carrinho -->
    <?php 
    $contagem_carrinho = isset($_SESSION['carrinho']) ? array_sum($_SESSION['carrinho']) : 0;
    ?>
    <a href="pages/checkout.php" class="btn btn-outline" style="position: relative;">
        🛒 Carrinho 
        <?php if ($contagem_carrinho > 0): ?>
            <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px; position: absolute; top: -5px; right: -5px;">
                <?= $contagem_carrinho ?>
            </span>
        <?php endif; ?>
    </a>

    <!-- Botões de Login/Perfil que já tinhas -->
    <?php if (isset($_SESSION['user_id'])): ?>
        ...
    <?php endif; ?>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-icon">📚</span>
        <h1>Livraria Letudo</h1>
        <p class="hero-subtitle">Descobre histórias que transformam vidas. Encontra o teu próximo livro favorito.</p>
    </div>
</section>

<!-- Catálogo de Livros -->
<section class="secao-livros">
    <div class="secao-titulo">
        <h2>O Nosso Catálogo</h2>
        <p class="subtitulo">Livros cuidadosamente selecionados para ti</p>
    </div>

    <div class="livros-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT id, nome, preco_unidade, quantidade_disponivel, imagem 
                                 FROM produtos 
                                 ORDER BY id DESC");
            $livros = $stmt->fetchAll();

            if (empty($livros)) {
                echo "<p style='text-align:center; grid-column:1/-1;'>Não há livros disponíveis de momento.</p>";
            } else {
                foreach ($livros as $p):
        ?>
            <div class="livro-card">
                <?php if ($p['quantidade_disponivel'] <= 0): ?>
                    <span class="badge-esgotado">Esgotado</span>
                <?php endif; ?>

                <div class="livro-imagem">
                    <?php if (!empty($p['imagem'])): ?>
                        <img src="img/<?= htmlspecialchars($p['imagem']) ?>" 
                             alt="<?= htmlspecialchars($p['nome']) ?>">
                    <?php else: ?>
                        <span style="font-size: 60px;">📖</span>
                    <?php endif; ?>
                </div>

                <div class="livro-conteudo">
                    <h3 class="livro-titulo"><?= htmlspecialchars($p['nome']) ?></h3>
                    
                    <p class="livro-preco">
                        <span class="moeda">€</span><?= number_format($p['preco_unidade'], 2, ',', ' ') ?>
                    </p>

                    <p class="livro-stock <?= $p['quantidade_disponivel'] > 0 ? 'stock-disponivel' : 'stock-esgotado' ?>">
                        <?= $p['quantidade_disponivel'] > 0 ? 'Em stock' : 'Fora de stock' ?> 
                        · <?= $p['quantidade_disponivel'] ?> unid.
                    </p>

                    <?php if ($p['quantidade_disponivel'] > 0): ?>
                    <form action="pages/processar_carrinho.php" method="POST" style="display:inline;">
                        <input type="hidden" name="produto_id" value="<?= $Sp['id'] ?>">
                        <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
                    </form>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Esgotado</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php
                endforeach;
            }
        } catch (Exception $e) {
            echo "<p style='color:red; grid-column:1/-1;'>Erro ao carregar os livros: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>
</section>

<!-- Footer -->
<footer class="site-footer">
    <p>&copy; <?= date("Y") ?> Livraria Letudo. Todos os direitos reservados.</p>
</footer>

</body>
</html>
