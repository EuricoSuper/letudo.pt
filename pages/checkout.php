<?php
require 'config/db.php';

if(!isset($_GET['id'])) { header("Location: index.php"); exit; }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->execute([$id]);
$produto = $stmt->fetch();

$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Protecao contra XSS
    $nome = htmlspecialchars(strip_tags($_POST['nome']));
    $morada = htmlspecialchars(strip_tags($_POST['morada']));
    $data_nasc = $_POST['data_nasc'];

    // Validacao 18 anos
    $idade = date_diff(date_create($data_nasc), date_create('today'))->y;

    if ($idade < 18) {
        $erro = "Erro: Deves ter pelo menos 18 anos para comprar.";
    } elseif (empty($nome) || empty($morada)) {
        $erro = "Erro: Todos os campos sao obrigatorios.";
    } else {
        // Guardar Encomenda
        $stmt = $pdo->prepare("INSERT INTO encomendas (cliente_nome, data_nascimento, morada, total) VALUES (?,?,?,?)");
        $stmt->execute([$nome, $data_nasc, $morada, $produto['preco_unidade']]);

        // Atualizar Stock
        $stmt = $pdo->prepare("UPDATE produtos SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?");
        $stmt->execute([$id]);

        echo "<script>alert('Sucesso! Encomenda registada.'); window.location.href='index.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra | Livraria Letudo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="pagina-checkout">

    <div class="checkout-card">
        <div class="checkout-header">
            <h2>Finalizar Compra</h2>
            <p class="checkout-produto"><?= $produto['nome'] ?></p>
            <p class="checkout-preco">&euro; <?= number_format($produto['preco_unidade'], 2) ?></p>
        </div>

        <div class="checkout-body">
            <?php if($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="O teu nome completo" required>
                </div>

                <div class="form-group">
                    <label for="data_nasc">Data de Nascimento</label>
                    <input type="date" id="data_nasc" name="data_nasc" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="morada">Morada de Envio</label>
                    <textarea id="morada" name="morada" class="form-control" rows="3" placeholder="Rua, numero, codigo postal, cidade" required></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Confirmar Pagamento</button>
                <a href="index.php" class="btn btn-link w-100 mt-2">&larr; Voltar a loja</a>
            </form>
        </div>
    </div>

</body>
</html>