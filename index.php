<?php
// index.php - Versão simples para testar e corrigir erros
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);   // ← Mostra os erros para vermos o que está mal

require_once 'config/db.php';   // Certifica-te que o caminho está correto!

echo "<h1>Teste - Letudo.pt</h1>";

if (!isset($pdo)) {
    die("ERRO: Não consegui conectar à base de dados (pdo não definido).");
}

// Query segura
$stmt = $pdo->query("SELECT id, titulo, preco, imagem FROM produtos ORDER BY id DESC LIMIT 12");
$livros = $stmt->fetchAll();

echo "<h2>Últimos Livros (" . count($livros) . " encontrados)</h2>";

if (empty($livros)) {
    echo "<p>Não há livros ou a tabela 'produtos' está vazia.</p>";
} else {
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">';
    
    foreach ($livros as $livro) {
        echo '<div style="border: 1px solid #ccc; padding: 15px; text-align: center;">';
        
        if (!empty($livro['imagem'])) {
            echo '<img src="' . htmlspecialchars($livro['imagem']) . '" style="max-width:100%; height:auto;"><br>';
        }
        
        echo '<h3>' . htmlspecialchars($livro['titulo']) . '</h3>';
        echo '<p><strong>' . number_format($livro['preco'], 2, ',', ' ') . ' €</strong></p>';
        echo '<a href="comprar.php?id=' . $livro['id'] . '">Comprar</a>';
        echo '</div>';
    }
    echo '</div>';
}
?>

<p><a href="pages/registo.php">Registar</a> | <a href="pages/login.php">Login</a></p>