<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

// Atualizar produto
if (isset($_POST['atualizar'])) {
    $id = intval($_POST['id']);
    $nome = limparDados($conn, $_POST['nome']);
    $descricao = limparDados($conn, $_POST['descricao']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    
    $updateQuery = "UPDATE produtos SET nome='$nome', descricao='$descricao', quantidade=$quantidade, preco=$preco WHERE id=$id";
    
    if (mysqli_query($conn, $updateQuery)) {
        $mensagem = 'Produto atualizado com sucesso!';
        $tipoMensagem = 'sucesso';
    } else {
        $mensagem = 'Erro ao atualizar produto.';
        $tipoMensagem = 'erro';
    }
}

// Eliminar produto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $deleteQuery = "DELETE FROM produtos WHERE id=$id";
    
    if (mysqli_query($conn, $deleteQuery)) {
        $mensagem = 'Produto eliminado com sucesso!';
        $tipoMensagem = 'sucesso';
    } else {
        $mensagem = 'Erro ao eliminar produto.';
        $tipoMensagem = 'erro';
    }
}

// Buscar todos os produtos
$query = "SELECT * FROM produtos ORDER BY data_adicao DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Produtos - Admin</title>
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
        <?php if ($mensagem): ?>
            <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="admin-container">
            <h2>Gerir Produtos</h2>
            <a href="adicionar_produto.php" class="btn btn-sucesso mt-2">Adicionar Novo Produto</a>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($produto = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $produto['id']; ?></td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td><?php echo htmlspecialchars(substr($produto['descricao'], 0, 50)) . '...'; ?></td>
                            <td>
                                <span class="<?php echo $produto['quantidade'] > 0 ? 'stock-disponivel' : 'stock-indisponivel'; ?>">
                                    <?php echo $produto['quantidade']; ?>
                                </span>
                            </td>
                            <td>€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="editar_produto.php?id=<?php echo $produto['id']; ?>" class="btn btn-primario" style="padding: 0.5rem;">Editar</a>
                                <a href="produtos.php?eliminar=<?php echo $produto['id']; ?>" 
                                   class="btn btn-perigo" 
                                   style="padding: 0.5rem;"
                                   onclick="return confirm('Tem a certeza que deseja eliminar este produto?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>