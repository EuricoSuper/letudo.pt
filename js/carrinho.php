<?php
$pageTitle = "Carrinho de Compras";
require_once 'includes/header.php';
?>

<div class="container">
    <h1 class="text-center">Carrinho de Compras</h1>
    
    <div class="carrinho-container" id="carrinho-itens">
        <!-- Os itens serão carregados via JavaScript -->
    </div>
    
    <div class="carrinho-total" id="carrinho-total-container" style="display: none;">
        <p id="carrinho-total">Total: €0.00</p>
        <a href="checkout.php" class="btn btn-sucesso mt-2">Finalizar Compra</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    const carrinhoItens = document.getElementById('carrinho-itens');
    const carrinhoTotalContainer = document.getElementById('carrinho-total-container');
    
    if (carrinho.length === 0) {
        carrinhoItens.innerHTML = '<div class="mensagem mensagem-aviso"><p>O seu carrinho está vazio.</p></div>';
        return;
    }
    
    let total = 0;
    let html = '';
    
    carrinho.forEach(item => {
        const subtotal = item.preco * item.quantidade;
        total += subtotal;
        
        html += `
            <div class="carrinho-item">
                <div>
                    <h3>${item.nome}</h3>
                    <p>Preço unitário: €${item.preco.toFixed(2)}</p>
                    <p>Quantidade: ${item.quantidade}</p>
                </div>
                <div>
                    <p><strong>Subtotal: €${subtotal.toFixed(2)}</strong></p>
                    <button class="btn btn-perigo" onclick="removerDoCarrinho(${item.id})" style="margin-top: 10px;">Remover</button>
                </div>
            </div>
        `;
    });
    
    carrinhoItens.innerHTML = html;
    document.getElementById('carrinho-total').textContent = 'Total: €' + total.toFixed(2);
    carrinhoTotalContainer.style.display = 'block';
});
</script>

<?php require_once 'includes/footer.php'; ?>