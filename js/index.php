<?php 
require_once 'config/db.php';
$pageTitle = "Loja Online - Letudo.pt";
include 'includes/header.php'; 

// Buscar produtos
$query = "SELECT id, nome, descricao, quantidade_disponivel, preco_unidade, imagem FROM produtos WHERE quantidade_disponivel > 0 ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!-- Banner Rotativo -->
<section class="banner-home">
    <div class="banner-slider">
        <img src="img/banner1.jpg" alt="Promoção Especial">
        <img src="img/banner2.jpg" alt="Novidades">
        <img src="img/banner3.jpg" alt="Mais Vendidos">
    </div>
</section>

<div class="container">
    <h1>Nossos Produtos</h1>
    <p class="subtitle">Descubra os melhores produtos ao melhor preço</p>
    
    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="produtos-grid">
            <?php while($p = mysqli_fetch_assoc($result)): ?>
                <div class="produto-card" data-preco="<?= $p['preco_unidade'] ?>" data-id="<?= $p['id'] ?>">
                    <img src="img/<?= htmlspecialchars($p['imagem']) ?>" 
                         alt="<?= htmlspecialchars($p['nome']) ?>"
                         onerror="this.src='img/default.jpg'">
                    
                    <div class="produto-info">
                        <h3><?= htmlspecialchars($p['nome']) ?></h3>
                        <p class="descricao"><?= htmlspecialchars($p['descricao']) ?></p>
                        
                        <div class="preco"><?= number_format($p['preco_unidade'], 2, ',', '.') ?></div>
                        
                        <div class="quantidade-wrapper">
                            <label for="qty-<?= $p['id'] ?>">Quantidade:</label>
                            <input type="number" 
                                   id="qty-<?= $p['id'] ?>"
                                   class="qty-input" 
                                   min="1" 
                                   max="<?= $p['quantidade_disponivel'] ?>" 
                                   value="1" 
                                   data-stock="<?= $p['quantidade_disponivel'] ?>">
                            <span class="stock-msg">✓ Stock: <?= $p['quantidade_disponivel'] ?> unidades</span>
                        </div>
                        
                        <button class="btn-adicionar" 
                                onclick="adicionarAoCarrinho(<?= $p['id'] ?>, '<?= addslashes($p['nome']) ?>', <?= $p['preco_unidade'] ?>)">
                            🛒 Adicionar ao Carrinho
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="carrinho-flutuante">
            <div>
                <p>Total: <span id="total-carrinho">0,00 €</span></p>
                <small>Os portes de envio serão calculados no checkout</small>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="checkout.php" class="btn-concluir">✓ Concluir Compra</a>
                <a href="admin/login.php" class="link-admin">⚙️ Área de Administração</a>
            </div>
        </div>
    <?php else: ?>
        <div class="aviso">
            <p>📦 Não existem produtos disponíveis de momento.</p>
            <p class="mt-2">Volte mais tarde!</p>
        </div>
    <?php endif; ?>
</div>

<script>
let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];

function atualizarTotal() {
    let total = 0;
    carrinho.forEach(item => total += item.preco * item.qtd);
    document.getElementById('total-carrinho').textContent = total.toFixed(2).replace('.', ',') + ' €';
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
}

function adicionarAoCarrinho(id, nome, preco) {
    const input = document.querySelector(`.produto-card[data-id="${id}"] .qty-input`);
    const qtd = parseInt(input.value);
    const stock = parseInt(input.dataset.stock);
    
    if(qtd > stock) {
        alert('⚠️ Quantidade superior ao stock disponível!');
        return;
    }
    
    const existente = carrinho.find(i => i.id === id);
    if(existente) {
        existente.qtd += qtd;
    } else {
        carrinho.push({id, nome, preco, qtd});
    }
    
    atualizarTotal();
    
    // Animação de sucesso
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '✓ Adicionado!';
    btn.style.background = 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
    }, 1500);
    
    input.value = 1;
}

document.addEventListener('DOMContentLoaded', atualizarTotal);
</script>

<?php include 'includes/footer.php'; ?>