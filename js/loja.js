document.querySelectorAll('.qty-input').forEach(input => {
    input.addEventListener('change', function() {
        let total = 0;
        document.querySelectorAll('.qty-input').forEach(i => {
            total += i.value * i.dataset.price;
        });
        document.getElementById('display-total').innerText = total.toFixed(2);
        
        // Validação de stock imediata
        let max = parseInt(this.getAttribute('max'));
        if(this.value > max) {
            alert("Quantidade superior ao stock disponível!");
            this.value = max;
        }
    });
});