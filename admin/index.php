<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Proteção: só admins
if (!isset($_SESSION['user_id']) || ($_SESSION['user_tipo'] ?? '') !== 'admin') {
    header('Location: ../pages/login.php'); exit;
}

$msg = ''; $tipo_msg = 'success';

// Adicionar produto
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['acao']) && $_POST['acao']==='adicionar') {
    $nome = trim(htmlspecialchars(strip_tags($_POST['nome'] ?? '')));
    $autor = trim(htmlspecialchars(strip_tags($_POST['autor'] ?? '')));
    $cat = trim(htmlspecialchars(strip_tags($_POST['categoria'] ?? 'Geral')));
    $desc = trim(htmlspecialchars(strip_tags($_POST['descricao'] ?? '')));
    $qtd = max(0, (int)($_POST['quantidade'] ?? 0));
    $preco = max(0, (float)($_POST['preco'] ?? 0));
    $destaque = isset($_POST['destaque']) ? 1 : 0;
    $imagem = null;
    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $fname = 'livro_' . time() . '_' . rand(1000,9999) . '.' . $ext;
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], __DIR__ . '/../img/' . $fname)) {
                $imagem = $fname;
            }
        }
    }
    if ($imagem === null) $imagem = trim($_POST['imagem_existente'] ?? '');
    if ($nome && $preco > 0) {
        $ins = $pdo->prepare("INSERT INTO produtos (nome,autor,categoria,descricao,quantidade_disponivel,preco_unidade,imagem,destaque) VALUES (?,?,?,?,?,?,?,?)");
        $ins->execute([$nome,$autor,$cat,$desc,$qtd,$preco,$imagem,$destaque]);
        $msg = 'Livro adicionado com sucesso.';
    } else { $msg='Nome e preco sao obrigatorios.'; $tipo_msg='danger'; }
}

// Atualizar
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['acao']) && $_POST['acao']==='atualizar') {
    $id = (int)$_POST['id'];
    $qtd = (int)$_POST['quantidade'];
    $preco = (float)$_POST['preco'];
    $upd = $pdo->prepare("UPDATE produtos SET quantidade_disponivel=?, preco_unidade=? WHERE id=?");
    $upd->execute([$qtd,$preco,$id]);
    $msg = 'Produto atualizado.';
}

// Eliminar
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['acao']) && $_POST['acao']==='eliminar') {
    $id = (int)$_POST['id'];
    $del = $pdo->prepare("DELETE FROM produtos WHERE id=?");
    $del->execute([$id]);
    $msg = 'Produto eliminado.';
}

$produtos = $pdo->query("SELECT * FROM produtos ORDER BY id DESC")->fetchAll();
$encomendas = $pdo->query("SELECT e.*, u.username FROM encomendas e LEFT JOIN utilizadores u ON u.id = e.utilizador_id ORDER BY e.data_encomenda DESC LIMIT 50")->fetchAll();
$utilizadores = $pdo->query("SELECT * FROM utilizadores ORDER BY id DESC")->fetchAll();

$stats = [
    'produtos' => count($produtos),
    'encomendas' => (int)$pdo->query("SELECT COUNT(*) c FROM encomendas")->fetch()['c'],
    'utilizadores' => count($utilizadores),
    'receita' => (float)($pdo->query("SELECT COALESCE(SUM(total),0) t FROM encomendas")->fetch()['t'] ?? 0),
    'stock_baixo' => (int)$pdo->query("SELECT COUNT(*) c FROM produtos WHERE quantidade_disponivel < 5")->fetch()['c'],
];

$tab = $_GET['tab'] ?? 'produtos';
?>
<!DOCTYPE html>
<html lang="pt"><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | Letudo.pt</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Libre+Franklin:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="admin-shell">
    <aside class="admin-side">
        <a href="../index.php" class="brand" style="color:var(--branco);">
            <span class="brand-mark">L</span><span class="brand-text">letudo<span class="brand-dot" style="color:var(--dourado);">.pt</span></span>
        </a>
        <nav data-testid="admin-nav">
            <a href="?tab=produtos"    class="<?= $tab==='produtos'?'active':'' ?>" data-testid="admin-tab-produtos">Produtos</a>
            <a href="?tab=adicionar"   class="<?= $tab==='adicionar'?'active':'' ?>" data-testid="admin-tab-adicionar">Adicionar produto</a>
            <a href="?tab=encomendas"  class="<?= $tab==='encomendas'?'active':'' ?>" data-testid="admin-tab-encomendas">Encomendas</a>
            <a href="?tab=utilizadores"class="<?= $tab==='utilizadores'?'active':'' ?>" data-testid="admin-tab-utilizadores">Utilizadores</a>
            <a href="../index.php" style="margin-top:auto;">&larr; Voltar ao site</a>
            <a href="../logout.php" style="color:#ffb3b3;" data-testid="admin-logout">Terminar sessao</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-top">
            <div>
                <h1>Painel de administracao</h1>
                <p style="color:var(--cinza-500); font-size:14px;">Bem-vindo, <?= htmlspecialchars($_SESSION['user_nome']) ?></p>
            </div>
        </div>

        <?php if ($msg): ?><div class="alert alert-<?= $tipo_msg ?>" data-testid="admin-msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card"><div class="label">Produtos</div><div class="value"><?= $stats['produtos'] ?></div></div>
            <div class="stat-card"><div class="label">Encomendas</div><div class="value"><?= $stats['encomendas'] ?></div></div>
            <div class="stat-card"><div class="label">Utilizadores</div><div class="value"><?= $stats['utilizadores'] ?></div></div>
            <div class="stat-card"><div class="label">Receita total</div><div class="value"><?= number_format($stats['receita'],2,',','') ?>&euro;</div></div>
            <div class="stat-card" style="border-left-color:var(--dourado);"><div class="label">Stock baixo (&lt;5)</div><div class="value"><?= $stats['stock_baixo'] ?></div></div>
        </div>

        <?php if ($tab === 'produtos'): ?>
        <div class="admin-card">
            <h3>Gestao de produtos</h3>
            <div style="overflow-x:auto;">
            <table class="data-table">
                <thead><tr><th>ID</th><th>Imagem</th><th>Nome</th><th>Categoria</th><th>Stock</th><th>Preco (&euro;)</th><th>Acoes</th></tr></thead>
                <tbody>
                <?php foreach ($produtos as $p): ?>
                    <form method="POST"><tr>
                        <td>#<?= (int)$p['id'] ?></td>
                        <td><?php if ($p['imagem']): ?><img src="../img/<?= htmlspecialchars($p['imagem']) ?>" style="width:40px; height:55px; object-fit:cover;" onerror="this.style.display='none'"><?php endif; ?></td>
                        <td><strong><?= htmlspecialchars($p['nome']) ?></strong><br><small style="color:var(--cinza-500);"><?= htmlspecialchars($p['autor']) ?></small></td>
                        <td><?= htmlspecialchars($p['categoria']) ?></td>
                        <td><input type="number" name="quantidade" value="<?= (int)$p['quantidade_disponivel'] ?>" min="0" data-testid="admin-qty-<?= (int)$p['id'] ?>"></td>
                        <td><input type="number" name="preco" value="<?= $p['preco_unidade'] ?>" min="0" step="0.01" data-testid="admin-preco-<?= (int)$p['id'] ?>"></td>
                        <td>
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" name="acao" value="atualizar" class="btn btn-primary btn-sm" data-testid="admin-save-<?= (int)$p['id'] ?>">Guardar</button>
                            <button type="submit" name="acao" value="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este produto?')">&times;</button>
                        </td>
                    </tr></form>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php elseif ($tab === 'adicionar'): ?>
        <div class="admin-card">
            <h3>Adicionar novo produto</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="acao" value="adicionar">
                <div class="form-grid">
                    <div class="form-group"><label>Nome / Titulo *</label><input type="text" name="nome" class="form-control" required data-testid="admin-add-nome"></div>
                    <div class="form-group"><label>Autor</label><input type="text" name="autor" class="form-control" data-testid="admin-add-autor"></div>
                    <div class="form-group"><label>Categoria</label><input type="text" name="categoria" class="form-control" value="Geral" data-testid="admin-add-categoria"></div>
                    <div class="form-group"><label>Preco (&euro;) *</label><input type="number" name="preco" class="form-control" step="0.01" min="0" required data-testid="admin-add-preco"></div>
                    <div class="form-group"><label>Quantidade *</label><input type="number" name="quantidade" class="form-control" min="0" required value="10" data-testid="admin-add-qtd"></div>
                    <div class="form-group"><label>Imagem (ficheiro)</label><input type="file" name="imagem" class="form-control" accept="image/*" data-testid="admin-add-img"></div>
                </div>
                <div class="form-group"><label>Ou nome de imagem existente (ex: livro_casa.jpg)</label><input type="text" name="imagem_existente" class="form-control" placeholder="livro_casa.jpg" data-testid="admin-add-img-existente"></div>
                <div class="form-group"><label>Descricao</label><textarea name="descricao" class="form-control" rows="3" data-testid="admin-add-desc"></textarea></div>
                <div class="form-group"><label><input type="checkbox" name="destaque" value="1"> Marcar como destaque</label></div>
                <button type="submit" class="btn btn-primary" data-testid="admin-add-submit">Adicionar livro</button>
            </form>
        </div>
        <?php elseif ($tab === 'encomendas'): ?>
        <div class="admin-card">
            <h3>Encomendas recebidas</h3>
            <div style="overflow-x:auto;">
            <table class="data-table">
                <thead><tr><th>#</th><th>Cliente</th><th>User</th><th>Data Nasc.</th><th>Morada</th><th>Total</th><th>Data</th></tr></thead>
                <tbody>
                <?php foreach ($encomendas as $e): ?>
                    <tr>
                        <td>#<?= (int)$e['id'] ?></td>
                        <td><?= htmlspecialchars($e['cliente_nome']) ?></td>
                        <td><?= htmlspecialchars($e['username'] ?: '-') ?></td>
                        <td><?= date('d/m/Y', strtotime($e['data_nascimento'])) ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($e['morada'],0,50,'...')) ?></td>
                        <td><strong style="color:var(--bordo);"><?= number_format($e['total'],2,',','') ?>&euro;</strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($e['data_encomenda'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$encomendas): ?><tr><td colspan="7" style="text-align:center; padding:30px; color:var(--cinza-500);">Ainda sem encomendas.</td></tr><?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php elseif ($tab === 'utilizadores'): ?>
        <div class="admin-card">
            <h3>Utilizadores registados</h3>
            <table class="data-table">
                <thead><tr><th>#</th><th>Nome</th><th>Username</th><th>Email</th><th>Tipo</th><th>Registo</th></tr></thead>
                <tbody>
                <?php foreach ($utilizadores as $u): ?>
                    <tr>
                        <td>#<?= (int)$u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span style="background:<?= $u['tipo']==='admin'?'var(--bordo)':'var(--cinza-200)' ?>; color:<?= $u['tipo']==='admin'?'var(--branco)':'var(--preto)' ?>; padding:3px 10px; border-radius:30px; font-size:11px; text-transform:uppercase; letter-spacing:.06em; font-weight:600;"><?= htmlspecialchars($u['tipo']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($u['data_registo'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</div>
</body></html>
