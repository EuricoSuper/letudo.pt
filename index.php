<?php
// ==================== TOPO DO index.php ====================
session_start();
require_once 'config/db.php';   // ← Isto é obrigatório

// Query segura - mostra os últimos 20 livros
$stmt = $pdo->query("SELECT id, titulo, preco, imagem FROM produtos ORDER BY id DESC LIMIT 20");
$livros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letudo.pt - Livraria Online</title>
    
    <!-- Ajusta o caminho do CSS se estiver noutra pasta -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <header>
        <h1>Letudo.pt</h1>
        <nav>
            <a href="index.php">Início</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="perfil.php">Olá, <?= htmlspecialchars($_SESSION['user_nome'] ?? '') ?></a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="pages/registo.php">Registar</a>
                <a href="pages/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Últimos Livros em Stock</h2>

        <div class="catalogo">
            <?php if (empty($livros)): ?>
                <p>Não há livros disponíveis de momento.</p>
            <?php else: ?>
                <?php foreach ($livros as $livro): ?>
                    <div class="livro">
                        <?php if (!empty($livro['imagem'])): ?>
                            <img src="<?= htmlspecialchars($livro['imagem']) ?>" 
                                 alt="<?= htmlspecialchars($livro['titulo']) ?>">
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($livro['titulo']) ?></h3>
                        <p class="preco"><?= number_format($livro['preco'], 2, ',', ' ') ?> €</p>
                        
                        <a href="comprar.php?id=<?= $livro['id'] ?>" class="btn">Comprar Agora</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> Letudo.pt</p>
    </footer>

</body>
</html>