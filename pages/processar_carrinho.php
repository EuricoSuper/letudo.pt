<?php
session_start();

if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["produto_id"])) {
    $id = intval($_POST["produto_id"]);
    
    // Adiciona ou incrementa a quantidade do produto no carrinho
    $_SESSION["carrinho"][$id] = isset($_SESSION["carrinho"][$id]) ? $_SESSION["carrinho"][$id] + 1 : 1;
    
    // Calcula o total de itens no carrinho
    $total_itens = array_sum($_SESSION["carrinho"]);
    
    // Responde ao JavaScript com o novo total de itens
    header("Content-Type: application/json");
    echo json_encode(["status" => "success", "total_itens" => $total_itens]);
    exit;
}

// Se não for uma requisição POST válida, pode redirecionar ou retornar um erro
header("Location: ../index.php");
exit;
?>