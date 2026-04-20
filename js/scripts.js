// Função para calcular o total em tempo real (Exigência Pág. 5)
function calcularTotal() {
    let total = 0;
    const linhas = document.querySelectorAll('.produto-item'); // ajusta para a tua classe
    
    linhas.forEach(linha => {
        const preco = parseFloat(linha.querySelector('.preco').innerText);
        const qtd = parseInt(linha.querySelector('.input-qtd').value);
        
        // Validação de stock (Pág. 5)
        const stockDisponivel = parseInt(linha.dataset.stock);
        if (qtd > stockDisponivel) {
            alert("Quantidade superior à disponível em stock!");
            linha.querySelector('.input-qtd').value = stockDisponivel;
        }
        
        total += preco * qtd;
    });
    
    document.getElementById('valor-total-exibir').innerText = total.toFixed(2) + "€";
}