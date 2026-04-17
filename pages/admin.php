<?php
include('../config.php');
// Aqui deverias pedir login, mas para o projeto:
$encomendas = $pdo->query("SELECT * FROM encomendas")->fetchAll();
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Administração | Livraria Top</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header><h1>Painel de Administração</h1></header>
    <div class="container">
        <h2>Histórico de Encomendas</h2>
        <table border="1" style="width:100%; background:white; margin-bottom:30px;">
            <tr><th>Cliente</th><th>Data Nasc.</th><th>Morada</th></tr>
            <?php foreach($encomendas as $e): ?>
            <tr><td><?= htmlspecialchars($e['nome_cliente']) ?></td><td><?= $e['data_nascimento'] ?></td><td><?= htmlspecialchars($e['morada']) ?></td></tr>
            <?php endforeach; ?>
        </table>

        <h2>Gestão de Stock de Produtos</h2>
        <table border="1" style="width:100%; background:white;">
            <tr><th>Produto</th><th>Stock</th><th>Preço</th></tr>
            <?php foreach($produtos as $p): ?>
            <tr><td><?= htmlspecialchars($p['nome']) ?></td><td><?= $p['quantidade_disponivel'] ?></td><td><?= $p['preco_unidade'] ?>€</td></tr>
            <?php endforeach; ?>
        </table>
        <br>
        <a href="../index.php">⬅ Sair do Painel</a>
    </div>
</body>
</html>