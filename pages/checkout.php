<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$carrinho = $_SESSION['carrinho'] ?? [];
$produtos_carrinho = [];
$total = 0;

if (!empty($carrinho)) {
    $ids = array_keys($carrinho);
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($ph)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $p) {
        $p['quantidade_carrinho'] = $carrinho[$p['id']];
        $p['subtotal'] = $p['preco_unidade'] * $p['quantidade_carrinho'];
        $total += $p['subtotal'];
        $produtos_carrinho[] = $p;
    }
}

$portes = ($total > 0 && $total < 30) ? 3.90 : 0;
$total_final = $total + $portes;

$erro_msg = '';
$sucesso_msg = '';
if (($_GET['msg'] ?? '') === 'sem_stock') $erro_msg = 'Quantidade superior ao stock disponivel.';

// Pré-preencher dados do utilizador logado
$user_data = ['nome'=>'', 'morada'=>'', 'data_nasc'=>''];
if (isset($_SESSION['user_id'])) {
    $s = $pdo->prepare("SELECT nome, morada, data_nascimento FROM utilizadores WHERE id=?");
    $s->execute([$_SESSION['user_id']]);
    if ($u = $s->fetch()) {
        $user_data['nome']      = $u['nome'];
        $user_data['morada']    = $u['morada'] ?? '';
        $user_data['data_nasc'] = $u['data_nascimento'] ?? '';
    }
}

// Processar finalização
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    if (empty($produtos_carrinho)) { $erro_msg = 'O teu carrinho esta vazio.'; }
    else {
        // Protecao XSS - strip_tags + htmlspecialchars
        $nome      = trim(htmlspecialchars(strip_tags($_POST['nome']      ?? ''), ENT_QUOTES, 'UTF-8'));
        $morada    = trim(htmlspecialchars(strip_tags($_POST['morada']    ?? ''), ENT_QUOTES, 'UTF-8'));
        $data_nasc = trim($_POST['data_nasc'] ?? '');

        if ($nome === '' || $morada === '' || $data_nasc === '') {
            $erro_msg = 'Todos os campos sao obrigatorios.';
        } elseif (!DateTime::createFromFormat('Y-m-d', $data_nasc)) {
            $erro_msg = 'Data de nascimento invalida.';
        } else {
            $idade = (new DateTime())->diff(new DateTime($data_nasc))->y;
            if ($idade < 18) {
                $erro_msg = 'Tens de ter pelo menos 18 anos para concluir a compra.';
            } else {
                // Validar stock novamente
                foreach ($produtos_carrinho as $p) {
                    if ($p['quantidade_carrinho'] > $p['quantidade_disponivel']) {
                        $erro_msg = 'Stock insuficiente para &quot;' . htmlspecialchars($p['nome']) . '&quot;.';
                        break;
                    }
                }
                if (!$erro_msg) {
                    try {
                        $pdo->beginTransaction();
                        $ins = $pdo->prepare("INSERT INTO encomendas (utilizador_id, cliente_nome, data_nascimento, morada, total) VALUES (?,?,?,?,?)");
                        $ins->execute([$_SESSION['user_id'] ?? null, $nome, $data_nasc, $morada, $total_final]);
                        $enc_id = $pdo->lastInsertId();
                        $ins_it = $pdo->prepare("INSERT INTO encomenda_itens (encomenda_id, produto_id, nome_produto, quantidade, preco_unidade) VALUES (?,?,?,?,?)");
                        $upd_st = $pdo->prepare("UPDATE produtos SET quantidade_disponivel = quantidade_disponivel - ? WHERE id=?");
                        foreach ($produtos_carrinho as $p) {
                            $ins_it->execute([$enc_id, $p['id'], $p['nome'], $p['quantidade_carrinho'], $p['preco_unidade']]);
                            $upd_st->execute([$p['quantidade_carrinho'], $p['id']]);
                        }
                        $pdo->commit();
                        $_SESSION['carrinho'] = [];
                        $_SESSION['ultima_encomenda'] = ['id'=>$enc_id, 'total'=>$total_final, 'nome'=>$nome];
                        header("Location: sucesso.php"); exit;
                    } catch (Throwable $e) {
                        $pdo->rollBack();
                        $erro_msg = 'Erro ao processar encomenda: ' . htmlspecialchars($e->getMessage());
                    }
                }
            }
        }
    }
}

$titulo = 'Carrinho &amp; Finalizar Compra | Letudo.pt';
$base = '../';
$pagina_atual = 'checkout';
require __DIR__ . '/../includes/header.php';
?>
<div class="container" style="padding:30px 20px 0;">
    <h1 style="font-size:2.2rem; margin-bottom:6px;">O teu carrinho</h1>
    <p style="color:var(--cinza-500);">Revê os teus livros e preenche os dados para finalizar a compra.</p>
</div>

<?php if (empty($produtos_carrinho)): ?>
    <div class="container">
        <div class="empty-state">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <h3>O teu carrinho esta vazio</h3>
            <p>Descobre os nossos livros e comeca a ler hoje.</p>
            <a href="../index.php" class="btn btn-primary" data-testid="empty-cart-cta">Explorar catalogo</a>
        </div>
    </div>
<?php else: ?>
<div class="container">
    <?php if ($erro_msg): ?><div class="alert alert-danger" data-testid="checkout-error"><?= $erro_msg ?></div><?php endif; ?>
    <div class="cart-layout">
        <!-- Lista de itens -->
        <div>
            <div class="cart-box" data-testid="cart-items">
                <h3>Livros no carrinho (<?= array_sum($carrinho) ?>)</h3>
                <?php foreach ($produtos_carrinho as $p): ?>
                    <div class="cart-item">
                        <img src="../img/<?= htmlspecialchars($p['imagem'] ?: 'placeholder.jpg') ?>" alt="<?= htmlspecialchars($p['nome']) ?>" onerror="this.style.display='none'">
                        <div class="cart-item-info">
                            <h4><?= htmlspecialchars($p['nome']) ?></h4>
                            <p class="author"><?= htmlspecialchars($p['autor']) ?></p>
                            <p class="unit"><?= number_format($p['preco_unidade'], 2, ',', '') ?>&euro; / unidade</p>
                            <form method="POST" action="processar_carrinho.php" style="display:inline;">
                                <input type="hidden" name="acao" value="remover">
                                <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="cart-remove" data-testid="remove-item-<?= (int)$p['id'] ?>">Remover</button>
                            </form>
                        </div>
                        <form method="POST" action="processar_carrinho.php" class="cart-qty">
                            <input type="hidden" name="acao" value="atualizar">
                            <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" name="quantidade" value="<?= max(0, $p['quantidade_carrinho']-1) ?>" aria-label="Diminuir">&minus;</button>
                            <span data-testid="qty-<?= (int)$p['id'] ?>"><?= (int)$p['quantidade_carrinho'] ?></span>
                            <button type="submit" name="quantidade" value="<?= min($p['quantidade_disponivel'], $p['quantidade_carrinho']+1) ?>" aria-label="Aumentar">&plus;</button>
                        </form>
                        <div class="cart-line-total"><?= number_format($p['subtotal'], 2, ',', '') ?>&euro;</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Formulario de dados -->
            <div class="cart-box" style="margin-top:24px;">
                <h3>Dados de envio</h3>
                <form method="POST" style="padding:20px 24px 24px;">
                    <div class="form-group">
                        <label>Nome completo *</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($user_data['nome']) ?>" required minlength="3" data-testid="checkout-nome">
                    </div>
                    <div class="form-group">
                        <label>Data de nascimento * (tens de ter 18 anos ou mais)</label>
                        <input type="date" name="data_nasc" class="form-control" value="<?= htmlspecialchars($user_data['data_nasc']) ?>" required max="<?= date('Y-m-d') ?>" data-testid="checkout-data-nasc">
                    </div>
                    <div class="form-group">
                        <label>Morada completa *</label>
                        <textarea name="morada" class="form-control" required rows="3" data-testid="checkout-morada"><?= htmlspecialchars($user_data['morada']) ?></textarea>
                        <span class="form-hint">Rua, numero, codigo postal, cidade</span>
                    </div>
                    <button type="submit" name="finalizar" value="1" class="btn btn-primary btn-block" style="margin-top:10px;" data-testid="checkout-submit">Finalizar encomenda &middot; <?= number_format($total_final, 2, ',', '') ?>&euro;</button>
                </form>
            </div>
        </div>

        <!-- Resumo -->
        <aside class="summary-box">
            <h3>Resumo da encomenda</h3>
            <div class="summary-row"><span>Subtotal</span><span><?= number_format($total, 2, ',', '') ?>&euro;</span></div>
            <div class="summary-row"><span>Portes de envio</span><span><?= $portes == 0 ? 'Gratis' : number_format($portes,2,',','').'&euro;' ?></span></div>
            <?php if ($portes > 0): ?>
                <div class="summary-row" style="font-size:12px; color:var(--dourado);"><span>&#9825; Adiciona mais <?= number_format(30-$total,2,',','') ?>&euro; para portes gratis</span></div>
            <?php endif; ?>
            <div class="summary-row total"><span>Total</span><span data-testid="cart-total"><?= number_format($total_final, 2, ',', '') ?>&euro;</span></div>
            <a href="../index.php" class="btn btn-secondary btn-block" style="margin-top:16px;">Continuar a comprar</a>
            <a href="processar_carrinho.php?acao=limpar" class="btn btn-danger btn-block" style="margin-top:8px;" data-testid="clear-cart-btn">Limpar carrinho</a>
        </aside>
    </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
