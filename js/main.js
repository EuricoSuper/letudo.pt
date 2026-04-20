// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('nav ul');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Validação do formulário de checkout
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const dataNascimento = document.getElementById('data_nascimento').value;
            const nome = document.getElementById('nome').value.trim();
            const morada = document.getElementById('morada').value.trim();

            // Verificar campos vazios
            if (!nome || !dataNascimento || !morada) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios.');
                return false;
            }

            // Verificar idade
            const nascimento = new Date(dataNascimento);
            const hoje = new Date();
            const idade = hoje.getFullYear() - nascimento.getFullYear();
            const mesDif = hoje.getMonth() - nascimento.getMonth();
            
            let idadeFinal = idade;
            if (mesDif < 0 || (mesDif === 0 && hoje.getDate() < nascimento.getDate())) {
                idadeFinal--;
            }

            if (idadeFinal < 18) {
                e.preventDefault();
                alert('Deve ter pelo menos 18 anos para realizar uma compra.');
                return false;
            }

            return true;
        });
    }

    // Atualizar quantidade de produtos
    const quantityInputs = document.querySelectorAll('.produto-quantidade input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            let valor = parseInt(this.value);
            const maxStock = parseInt(this.getAttribute('max'));
            
            if (valor < 1) valor = 1;
            if (valor > maxStock) {
                valor = maxStock;
                alert('Quantidade máxima disponível: ' + maxStock);
            }
            
            this.value = valor;
            atualizarTotal();
        });
    });
});

function atualizarTotal() {
    const produtos = document.querySelectorAll('.produto-card');
    let total = 0;

    produtos.forEach(produto => {
        const preco = parseFloat(produto.getAttribute('data-preco'));
        const quantidade = parseInt(produto.querySelector('.produto-quantidade input').value) || 0;
        total += preco * quantidade;
    });

    const totalElement = document.getElementById('carrinho-total');
    if (totalElement) {
        totalElement.textContent = 'Total: €' + total.toFixed(2);
    }

    // Guardar no localStorage
    localStorage.setItem('carrinhoTotal', total.toFixed(2));
}

function adicionarAoCarrinho(produtoId, nome, preco, quantidade) {
    const maxStock = parseInt(document.querySelector(`#produto-${produtoId} .produto-quantidade input`).getAttribute('max'));
    
    if (quantidade > maxStock) {
        alert('Quantidade indisponível. Stock máximo: ' + maxStock);
        return false;
    }

    if (maxStock === 0) {
        alert('Produto esgotado!');
        return false;
    }

    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    
    const produtoExistente = carrinho.find(item => item.id === produtoId);
    if (produtoExistente) {
        produtoExistente.quantidade += quantidade;
    } else {
        carrinho.push({
            id: produtoId,
            nome: nome,
            preco: preco,
            quantidade: quantidade
        });
    }

    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    atualizarContadorCarrinho();
    alert('Produto adicionado ao carrinho!');
}

function atualizarContadorCarrinho() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    const totalItens = carrinho.reduce((sum, item) => sum + item.quantidade, 0);
    
    const contador = document.getElementById('carrinho-contador');
    if (contador) {
        contador.textContent = totalItens;
    }
}

// Atualizar contador ao carregar a página
document.addEventListener('DOMContentLoaded', atualizarContadorCarrinho);

function removerDoCarrinho(produtoId) {
    let carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    carrinho = carrinho.filter(item => item.id !== produtoId);
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
    location.reload();
}

function finalizarCompra() {
    const carrinho = JSON.parse(localStorage.getItem('carrinho')) || [];
    
    if (carrinho.length === 0) {
        alert('O seu carrinho está vazio!');
        return false;
    }

    // Redirecionar para checkout
    window.location.href = 'checkout.php';
    return true;
}