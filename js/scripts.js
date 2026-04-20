<script>
document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', function() {
        const produtoId = this.getAttribute('data-id');
        
        // Envia para o PHP via AJAX
        fetch('pages/processar_carrinho.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'produto_id=' + produtoId
        })
        .then(response => response.json())
        .then(data => {
            // Atualiza o número no ícone do carrinho
            document.getElementById('cart-count').innerText = data.total_itens;
            
            // Feedback visual (opcional: mudar cor do botão temporariamente)
            this.innerText = '✅ Adicionado!';
            this.classList.add('btn-success');
            setTimeout(() => {
                this.innerText = 'Adicionar ao Carrinho';
                this.classList.remove('btn-success');
            }, 2000);
        });
    });
});
</script>