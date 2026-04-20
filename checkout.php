<?php
include 'includes/db.php';
include 'includes/header.php';

// Proteção contra injeção de script (requisito do PDF)
function clean($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aqui processaríamos os itens vindos do index para mostrar o resumo
}
?>

<div class="container form-container">
    <h2>Finalizar Encomenda</h2>
    <form action="processar_final.php" method="POST">
        <label>Nome Completo:</label>
        <input type="text" name="nome" required>

        <label>Data de Nascimento:</label>
        <input type="date" name="data_nascimento" id="data_n" required>

        <label>Morada de Envio:</label>
        <textarea name="morada" required></textarea>

        <button type="submit" class="btn-final">CONFIRMAR E PAGAR</button>
    </form>
</div>

<script>
// Validação de 18 anos (requisito do PDF)
document.querySelector('form').onsubmit = function(e) {
    let dataNasc = new Date(document.getElementById('data_n').value);
    let hoje = new Date();
    let idade = hoje.getFullYear() - dataNasc.getFullYear();
    if (idade < 18) {
        alert("Erro: Deve ter pelo menos 18 anos para comprar.");
        e.preventDefault();
    }
};
</script>