<?php
// Lógica que pediste antes, integrada no teu projeto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Proteção contra Injeção JS (Pág. 5)
    $nome = htmlspecialchars($_POST['nome']); 
    $morada = htmlspecialchars($_POST['morada']);
    $data_nasc = $_POST['data_nasc'];

    // 2. Validação: Campos Vazios (Pág. 6)
    if (empty($nome) || empty($morada) || empty($data_nasc)) {
        echo "Todos os campos devem ser preenchidos.";
        exit;
    }

    // 3. Validação: Maior de 18 anos (Pág. 6)
    $idade = (new DateTime())->diff(new DateTime($data_nasc))->y;
    if ($idade < 18) {
        echo "Erro: Apenas maiores de 18 anos podem comprar.";
        exit;
    }

    // Se passar, gravas na tabela "Encomendas" aqui...
    echo "Encomenda feita com sucesso!";
}
?>