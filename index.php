<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Suporte a paginas estaticas simples via ?pagina=
$paginas_estaticas = ['sobre', 'contactos', 'ajuda', 'termos', 'privacidade'];
if (isset($_GET['pagina']) && in_array($_GET['pagina'], $paginas_estaticas, true)) {
    $slug = $_GET['pagina'];
    $titulo = ucfirst($slug) . ' | Letudo.pt';
    $base = '';
    $pagina_atual = 'estatica';
    require __DIR__ . '/includes/header.php';
    $conteudos = [
        'sobre'      => ['Quem somos', 'A Letudo.pt nasceu da paixao pelos livros. Somos uma livraria online portuguesa, inspirada nas grandes referencias como a Almedina, dedicada a trazer-te os melhores titulos ao melhor preco.', 'A nossa missao', 'Democratizar o acesso ao conhecimento, promover a leitura e celebrar a cultura escrita em portugues.'],
        'contactos'  => ['Contactos', 'Rua da Leitura, 12, 1000-001 Lisboa', 'Email', 'ola@letudo.pt', 'Telefone', '+351 210 000 000'],
        'ajuda'      => ['Ajuda &amp; FAQ', 'Como faco uma encomenda?', 'Adiciona os livros ao carrinho, clica em "Finalizar compra" e preenche os teus dados.', 'Qual o prazo de entrega?', 'Envios em 2 a 5 dias uteis para todo o continente.'],
        'termos'     => ['Termos e condicoes', 'Ao utilizares a letudo.pt concordas com os nossos termos de utilizacao, incluindo a maioridade legal (18 anos) para realizar compras.'],
        'privacidade'=> ['Politica de privacidade', 'Os teus dados pessoais sao usados exclusivamente para processar encomendas e sao protegidos nos termos do RGPD.'],
    ];
    $c = $conteudos[$slug];
    echo '<div class="simple-page"><h1>' . $c[0] . '</h1>';
    for ($i=1; $i < count($c); $i++) {
        $tag = ($i % 2 === 1 && $i > 1) ? 'h2' : 'p';
        echo "<$tag>" . $c[$i] . "</$tag>";
    }
    echo '</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Filtros
$q    = trim($_GET['q'] ?? '');
$cat  = trim($_GET['cat'] ?? '');
$ord  = $_GET['ord'] ?? 'recentes';
$dest = !empty($_GET['destaque']);

$where = []; $params = [];
if ($q !== '')   { $where[] = '(nome LIKE ? OR autor LIKE ? OR descricao LIKE ?)'; $params[]="%$q%"; $params[]="%$q%"; $params[]="%$q%"; }
if ($cat !== '') { $where[] = 'categoria = ?'; $params[] = $cat; }
if ($dest)       { $where[] = 'destaque = 1'; }
$wsql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$order = match ($ord) { 'preco_asc'=>'preco_unidade ASC', 'preco_desc'=>'preco_unidade DESC', 'nome'=>'nome ASC', default=>'id DESC' };

$stmt = $pdo->prepare("SELECT * FROM produtos $wsql ORDER BY $order");
$stmt->execute($params);
$livros = $stmt->fetchAll();

// Destaques para hero (top 3 imagens)
$destaques = $pdo->query("SELECT imagem, nome FROM produtos WHERE destaque=1 AND imagem IS NOT NULL LIMIT 3")->fetchAll();
if (count($destaques) < 3) {
    $destaques = $pdo->query("SELECT imagem, nome FROM produtos WHERE imagem IS NOT NULL LIMIT 3")->fetchAll();
}

$titulo = 'Letudo.pt | A sua livraria online';
$base = '';
$pagina_atual = 'home';
require __DIR__ . '/includes/header.php';
?>

<?php if (empty($q) && empty($cat) && !$dest): ?>
<!-- Hero -->
<section class="hero">
    <div class="container hero-inner">
        <div>
            <span class="hero-eyebrow">Novidades &amp; Destaques</span>
            <h1>Le tudo. Descobre hoje o teu proximo livro.</h1>
            <p class="hero-sub">Milhares de titulos cuidadosamente selecionados, desde ficcao a biografia, infantis, autoajuda e poesia. Envios rapidos para todo o pais.</p>
            <div class="hero-actions">
                <a href="#catalogo" class="btn btn-primary" data-testid="hero-explore-btn">Explorar catalogo</a>
                <a href="index.php?destaque=1" class="btn btn-outline-light" data-testid="hero-featured-btn">Ver destaques</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="book-stack">
                <?php foreach ($destaques as $d): ?>
                    <img src="img/<?= htmlspecialchars($d['imagem']) ?>" alt="<?= htmlspecialchars($d['nome']) ?>">
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Info Strip -->
<section class="info-strip">
    <div class="container info-strip-grid">
        <div class="info-item">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            <div><strong>Envios rapidos</strong><span>2 a 5 dias uteis</span></div>
        </div>
        <div class="info-item">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12V8H6a2 2 0 0 1 0-4h12.5"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
            <div><strong>Pagamento seguro</strong><span>MB Way, cartao, MB</span></div>
        </div>
        <div class="info-item">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <div><strong>Trocas gratis</strong><span>Ate 14 dias</span></div>
        </div>
        <div class="info-item">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z"/></svg>
            <div><strong>Apoio dedicado</strong><span>Em portugues, sempre</span></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Catalogo -->
<section class="section" id="catalogo">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow"><?= $dest ? 'Os nossos destaques' : ($q!=='' ? 'Resultados' : 'O nosso catalogo') ?></span>
                <h2>
                    <?php if ($q!==''): ?>Pesquisa: &ldquo;<?= htmlspecialchars($q) ?>&rdquo;
                    <?php elseif ($cat!==''): ?><?= htmlspecialchars($cat) ?>
                    <?php elseif ($dest): ?>Livros em destaque
                    <?php else: ?>Livros que vais adorar
                    <?php endif; ?>
                </h2>
                <p>Uma selecao pensada para quem ama ler. Adiciona ao carrinho e finaliza em dois passos.</p>
            </div>
        </div>

        <div class="filters-bar">
            <div class="result-count" data-testid="result-count"><?= count($livros) ?> livro<?= count($livros)!==1?'s':'' ?> <?= count($livros) ? 'disponiveis' : '' ?></div>
            <form method="GET" style="display:flex; gap:10px; align-items:center;">
                <?php foreach (['q','cat','destaque'] as $k) if (!empty($_GET[$k])) echo '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($_GET[$k]).'">'; ?>
                <label for="ord" style="font-size:13px;color:var(--cinza-500);">Ordenar:</label>
                <select name="ord" id="ord" onchange="this.form.submit()" data-testid="sort-select">
                    <option value="recentes"  <?= $ord==='recentes'?'selected':'' ?>>Mais recentes</option>
                    <option value="nome"      <?= $ord==='nome'?'selected':'' ?>>Titulo A-Z</option>
                    <option value="preco_asc" <?= $ord==='preco_asc'?'selected':'' ?>>Preco crescente</option>
                    <option value="preco_desc"<?= $ord==='preco_desc'?'selected':'' ?>>Preco decrescente</option>
                </select>
            </form>
        </div>

        <?php if (empty($livros)): ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h3>Nenhum livro encontrado</h3>
                <p>Tenta outra pesquisa ou explora o catalogo completo.</p>
                <a href="index.php" class="btn btn-primary">Ver todos os livros</a>
            </div>
        <?php else: ?>
        <?php if (isset($_GET['msg'])): $msgs = ['ok_add'=>['success','Livro adicionado ao carrinho!'], 'sem_stock'=>['danger','Sem stock suficiente para este livro.'], 'esgotado'=>['danger','Este livro esta esgotado.']]; if(isset($msgs[$_GET['msg']])): ?>
            <div class="alert alert-<?= $msgs[$_GET['msg']][0] ?>" data-testid="flash-msg"><?= $msgs[$_GET['msg']][1] ?></div>
        <?php endif; endif; ?>
        <div class="products-grid" data-testid="products-grid">
            <?php foreach ($livros as $p): ?>
                <article class="product-card" data-testid="product-card-<?= (int)$p['id'] ?>">
                    <div class="product-media">
                        <?php if ($p['destaque']): ?><span class="badge-feature">Destaque</span><?php endif; ?>
                        <?php if ($p['quantidade_disponivel'] <= 0): ?><span class="badge-out">Esgotado</span><?php endif; ?>
                        <?php if (!empty($p['imagem'])): ?>
                            <img src="img/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>" loading="lazy">
                        <?php else: ?>
                            <span class="placeholder">&#128218;</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-body">
                        <span class="product-cat"><?= htmlspecialchars($p['categoria']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($p['nome']) ?></h3>
                        <p class="product-author"><?= htmlspecialchars($p['autor'] ?: 'Autor desconhecido') ?></p>
                        <div class="product-price-row">
                            <span class="product-price"><?= number_format($p['preco_unidade'], 2, ',', '') ?>&euro;</span>
                            <?php if ($p['quantidade_disponivel'] > 0): ?>
                                <span class="product-stock in">Em stock (<?= (int)$p['quantidade_disponivel'] ?>)</span>
                            <?php else: ?>
                                <span class="product-stock out">Esgotado</span>
                            <?php endif; ?>
                        </div>
                        <form class="add-to-cart-form product-actions" method="POST" action="pages/processar_carrinho.php">
                            <input type="hidden" name="acao" value="adicionar">
                            <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                            <?php if ($p['quantidade_disponivel'] > 0): ?>
                                <input type="number" class="qty-input" name="quantidade" value="1" min="1" max="<?= (int)$p['quantidade_disponivel'] ?>" data-testid="qty-input-<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn-primary" style="flex:1;" data-testid="add-cart-btn-<?= (int)$p['id'] ?>">Adicionar</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary btn-block" disabled>Esgotado</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
