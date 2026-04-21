<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Verificar se é admin
requerAdmin();

$mensagem = '';
$tipo = '';

// Adicionar novo produto
if (isset($_POST['adicionar_produto'])) {
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $mensagem = 'Erro de segurança.';
        $tipo = 'erro';
    } else {
        $nome = limparDados($_POST['nome']);
        $descricao = limparDados($_POST['descricao']);
        $quantidade = intval($_POST['quantidade']);
        $preco = floatval($_POST['preco']);
        $categoria = limparDados($_POST['categoria']);
        $imagem = limparDados($_POST['imagem'] ?? 'default.jpg');
        
        if (!empty($nome) && $preco > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO produtos (nome, descricao, quantidade_disponivel, preco_unidade, imagem, categoria) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssidis", $nome, $descricao, $quantidade, $preco, $imagem, $categoria);
            
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = 'Produto adicionado com sucesso!';
                $tipo = 'sucesso';
            }
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = 'Preencha os campos obrigatórios.';
            $tipo = 'erro';
        }
    }
}

// Atualizar produto
if (isset($_POST['atualizar_produto'])) {
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $mensagem = 'Erro de segurança.';
        $tipo = 'erro';
    } else {
        $id = intval($_POST['id']);
        $quantidade = intval($_POST['quantidade']);
        $preco = floatval($_POST['preco']);
        
        $stmt = mysqli_prepare($conn, "UPDATE produtos SET quantidade_disponivel = ?, preco_unidade = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "idi", $quantidade, $preco, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $mensagem = 'Produto atualizado com sucesso!';
            $tipo = 'sucesso';
        }
        mysqli_stmt_close($stmt);
    }
}

// Eliminar produto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if (mysqli_query($conn, "DELETE FROM produtos WHERE id = $id")) {
        $mensagem = 'Produto eliminado!';
        $tipo = 'sucesso';
    }
}

// Buscar dados
$encomendas = mysqli_query($conn, "SELECT * FROM encomendas ORDER BY data_encomenda DESC");
$produtos = mysqli_query($conn, "SELECT * FROM produtos ORDER BY nome");
$total_encomendas = mysqli_num_rows($encomendas);
$total_produtos = mysqli_num_rows($produtos);

// Calcular faturação total
$faturacao = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(preco_total) as total FROM encomendas"));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Letudo.pt</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #2563eb 100%);
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 12px;
        }
        .admin-header h1 { margin: 0; font-size: 2rem; }
        .admin-header p { margin: 0.5rem 0 0 0; opacity: 0.9; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--sombra-suave);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            color: var(--cor-primaria);
            margin: 0;
        }
        .stat-card p {
            color: #6b7280;
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }
        .section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: var(--sombra-suave);
            margin-bottom: 2rem;
        }
        .section h2 {
            color: var(--cor-primaria);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid var(--cor-destaque);
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .admin-table th {
            background: #f9fafb;
            font-weight: 600;
            color: var(--cor-texto);
        }
        .admin-table tr:hover { background: #f9fafb; }
        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            margin-right: 0.5rem;
            transition: var(--transicao);
        }
        .btn-edit { background: #f59e0b; color: white; }
        .btn-edit:hover { background: #d97706; }
        .btn-delete { background: #dc2626; color: white; }
        .btn-delete:hover { background: #b91c1c; }
        .form-inline {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .form-inline .form-group { flex: 1; min-width: 150px; }
        .stock-ok { color: #059669; font-weight: 600; }
        .stock-baixo { color: #f59e0b; font-weight: 600; }
        .stock-zero { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-header">
            <h1>👋 Bem-vindo, <?= htmlspecialchars($_SESSION['admin_nome']) ?>!</h1>
            <p>Painel de Administração - Letudo.pt</p>
        </div>
        
        <?php 
        if (!empty($mensagem)) {
            mostrarMensagem($mensagem, $tipo);
        }
        ?>
        
        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= $total_produtos ?></h3>
                <p>Produtos</p>
            </div>
            <div class="stat-card">
                <h3><?= $total_encomendas ?></h3>
                <p>Encomendas</p>
            </div>
            <div class="stat-card">
                <h3><?= formatarPreco($faturacao['total'] ?? 0) ?></h3>
                <p>Faturação Total</p>
            </div>
        </div>
        
        <!-- Adicionar Produto -->
        <div class="section">
            <h2>➕ Adicionar Novo Produto</h2>
            <form method="POST" class="form-inline">
                <?php campoCSRF(); ?>
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="nome" required placeholder="Nome do produto">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="descricao" placeholder="Breve descrição">
                </div>
                <div class="form-group">
                    <label>Quantidade *</label>
                    <input type="number" name="quantidade" min="0" required placeholder="0">
                </div>
                <div class="form-group">
                    <label>Preço (€) *</label>
                    <input type="number" name="preco" step="0.01" min="0" required placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <input type="text" name="categoria" placeholder="Ex: Livros">
                </div>
                <div class="form-group">
                    <label>Imagem</label>
                    <input type="text" name="imagem" placeholder="nome.jpg">
                </div>
                <button type="submit" name="adicionar_produto" class="btn-adicionar">Adicionar</button>
            </form>
        </div>
        
        <!-- Lista de Produtos -->
        <div class="section">
            <h2>📦 Lista de Produtos</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Stock</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = mysqli_fetch_assoc($produtos)): 
                        $classe_stock = $p['quantidade_disponivel'] == 0 ? 'stock-zero' : ($p['quantidade_disponivel'] < 10 ? 'stock-baixo' : 'stock-ok');
                    ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td class="<?= $classe_stock ?>"><?= $p['quantidade_disponivel'] ?></td>
                        <td><?= formatarPreco($p['preco_unidade']) ?></td>
                        <td>
                            <form method="POST" class="form-inline" style="display:inline-flex;">
                                <?php campoCSRF(); ?>
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <input type="number" name="quantidade" value="<?= $p['quantidade_disponivel'] ?>" min="0" style="width:80px;">
                                <input type="number" name="preco" value="<?= $p['preco_unidade'] ?>" step="0.01" style="width:80px;">
                                <button type="submit" name="atualizar_produto" class="btn-small btn-edit">Atualizar</button>
                            </form>
                            <a href="?eliminar=<?= $p['id'] ?>" class="btn-small btn-delete" onclick="return confirm('Eliminar este produto?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Lista de Encomendas -->
        <div class="section">
            <h2>📋 Encomendas Realizadas</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Produtos</th>
                        <th>Quantidades</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($e = mysqli_fetch_assoc($encomendas)): ?>
                    <tr>
                        <td><?= $e['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($e['data_encomenda'])) ?></td>
                        <td><?= htmlspecialchars($e['nome_cliente']) ?><br>
                            <small><?= date('Y-m-d', strtotime($e['data_nascimento'])) ?></small>
                        </td>
                        <td><?= htmlspecialchars($e['produtos']) ?></td>
                        <td><?= htmlspecialchars($e['quantidades']) ?></td>
                        <td><strong><?= formatarPreco($e['preco_total']) ?></strong></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align:center; margin-top:2rem;">
            <a href="../index.php" class="btn-voltar">← Voltar ao Site</a>
            <a href="logout.php" class="btn-delete" style="padding:1rem 2rem; text-decoration:none; border-radius:8px; color:white; display:inline-block;">Sair</a>
        </div>
    </div>
</body>
</html>