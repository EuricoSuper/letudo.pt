<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limparDados($conn, $_POST['nome']);
    $descricao = limparDados($conn, $_POST['descricao']);
    $quantidade = intval($_POST['quantidade']);
    $preco = floatval($_POST['preco']);
    $categoria = limparDados($conn, $_POST['categoria']);
    
    if (empty($nome) || empty($preco)) {
        $mensagem = 'Preencha os campos obrigatórios.';
        $tipoMensagem = 'erro';
    } else {
        $insertQuery = "INSERT INTO produtos (nome, descricao, quantidade, preco, categoria) 
                       VALUES ('$nome', '$descricao', $quantidade, $preco, '$categoria')";
        
        if (mysqli_query($conn, $insertQuery)) {
            header('Location: produtos.php');
            exit;
        } else {
            $mensagem = 'Erro ao adicionar produto.';
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - Admin</title>
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
        
        <div class="form-container">
            <h2>Adicionar Novo Produto</h2>
            <form method="POST" action="adicionar_produto.php">
                <div class="form-group">
                    <label for="nome">Nome do Produto *</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" name="categoria">
                </div>
                
                <div class="form-group">
                    <label for="quantidade">Quantidade em Stock *</label>
                    <input type="number" id="quantidade" name="quantidade" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="preco">Preço (€) *</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0" required>
                </div>
                
                <button type="submit" class="btn btn-sucesso">Adicionar Produto</button>
                <a href="produtos.php" class="btn btn-primario" style="margin-top: 10px; display: inline-block;">Cancelar</a>
            </form>
        </div>
    </div>
</body>
</html>