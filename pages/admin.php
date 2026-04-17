<?php
require 'config/db.php';
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }

// Logica para atualizar stock/preco via POST
if(isset($_POST['update_stock'])) {
    $stmt = $pdo->prepare("UPDATE produtos SET quantidade_disponivel = ?, preco_unidade = ? WHERE id = ?");
    $stmt->execute([$_POST['n_stock'], $_POST['n_preco'], $_POST['id_prod']]);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin | Livraria Letudo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-admin">

    <!-- Header Admin -->
    <header class="admin-header">
        <div class="container">
            <div>
                <h2>Painel de Gestao</h2>
                <span class="admin-subtitle">Livraria Letudo</span>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm">Terminar Sessao</a>
        </div>
    </header>

    <!-- Conteudo -->
    <div class="admin-content">

        <!-- Gestao de Stock -->
        <div class="admin-secao">
            <h3 class="admin-secao-titulo">&#128218; Gestao de Stock de Livros</h3>
            <div class="tabela-responsive">
                <table class="tabela-admin">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Preco (&euro;)</th>
                            <th>Stock</th>
                            <th style="width: 100px;">Acao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $prods = $pdo->query("SELECT * FROM produtos")->fetchAll();
                        foreach($prods as $p): ?>
                        <tr>
                            <form method="POST">
                            <td><strong><?= $p['nome'] ?></strong></td>
                            <td><input type="number" step="0.01" name="n_preco" value="<?= $p['preco_unidade'] ?>" class="form-control"></td>
                            <td><input type="number" name="n_stock" value="<?= $p['quantidade_disponivel'] ?>" class="form-control"></td>
                            <td>
                                <input type="hidden" name="id_prod" value="<?= $p['id'] ?>">
                                <button type="submit" name="update_stock" class="btn btn-success btn-sm">Guardar</button>
                            </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lista de Encomendas -->
        <div class="admin-secao">
            <h3 class="admin-secao-titulo">&#128230; Encomendas Recebidas</h3>
            <div class="tabela-responsive">
                <table class="tabela-admin">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Data Nasc.</th>
                            <th>Morada</th>
                            <th>Total</th>
                            <th>Data/Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $encs = $pdo->query("SELECT * FROM encomendas ORDER BY data_encomenda DESC")->fetchAll();
                        if(count($encs) > 0):
                            foreach($encs as $e): ?>
                            <tr>
                                <td><?= $e['cliente_nome'] ?></td>
                                <td><?= $e['data_nascimento'] ?></td>
                                <td><?= $e['morada'] ?></td>
                                <td><strong>&euro; <?= number_format($e['total'], 2) ?></strong></td>
                                <td><?= date('d/m/Y H:i', strtotime($e['data_encomenda'])) ?></td>
                            </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--cor-texto-claro); padding: 2rem;">
                                    Ainda nenhuma encomenda registada.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>