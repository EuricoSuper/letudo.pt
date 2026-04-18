<?php
// ==================== TOPO DO FICHEIRO ====================
session_start();
require_once 'config/db.php';   // ← Conexão à base de dados

// Query segura para mostrar os livros
$stmt = $pdo->query("SELECT id, titulo, preco, imagem FROM produtos ORDER BY id DESC LIMIT 20");
$livros = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letudo.pt - Livraria Online</title>
    <link rel="stylesheet" href="css/style.css">   <!-- ajusta se o teu CSS estiver noutro sítio -->
</head>
<body>

    <header>
        <h1>Bem-vindo à Letudo.pt</h1>
        <nav>
            <a href="index.php">Início</a>
            <a href="pages/registo.php">Registar</a>
            <a href="pages/login.php">Login</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="perfil.php">Olá, <?= htmlspecialchars($_SESSION['user_nome'] ?? 'Utilizador') ?></a>
                <a href="logout.php">Sair</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Últimos Livros Disponíveis</h2>

        <!-- ==================== AQUI VAI O TEU CÓDIGO ==================== -->
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
                        
                        <a href="comprar.php?id=<?= $livro['id'] ?>" class="btn-comprar">Comprar Agora</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- ==================== FIM DO TEU CÓDIGO ==================== -->

    </main>

    <footer>
        <p>&copy; 2026 Letudo.pt - Todos os direitos reservados</p>
    </footer>

</body>
</html>