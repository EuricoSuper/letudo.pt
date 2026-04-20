<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=faz_login_para_comprar");
    exit;
}

// 1. Se o carrinho estiver vazio, nem deixa entrar no checkout
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("Location: ../index.php");
    exit;
}

// 2. SÓ OBRIGA LOGIN AQUI: Se tentar finalizar sem estar logado, vai para o login
if (!isset($_SESSION['user_id'])) {
    // Guarda uma mensagem para mostrar no login
    $_SESSION['erro_login'] = "Por favor, faz login para finalizar a tua compra.";
    header("Location: login.php");
    exit;
}

// 3. BUSCAR PRODUTOS DO CARRINHO
$ids = array_keys($_SESSION['carrinho']);
$placeholder = str_repeat('?,', count($ids) - 1) . '?';
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id IN ($placeholder)");
$stmt->execute($ids);
$produtos_no_carrinho = $stmt->fetchAll();

$total_encomenda = 0;
foreach ($produtos_no_carrinho as $p) {
    $quantidade = $_SESSION['carrinho'][$p['id']];
    $total_encomenda += $p['preco_unidade'] * $quantidade;
}

$erro = "";

// Preencher dados se o utilizador estiver logado
$nome_preenchido = "";
$morada_preenchida = "";
if (isset($_SESSION['user_id'])) {
    $stmt_u = $pdo->prepare("SELECT nome, morada FROM utilizadores WHERE id = ?");
    $stmt_u->execute([$_SESSION['user_id']]);
    $user_data = $stmt_u->fetch();
    if ($user_data) {
        $nome_preenchido = $user_data['nome'];
        $morada_preenchida = $user_data['morada'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Protecao contra XSS
    $nome = htmlspecialchars(strip_tags($_POST['nome']));
    $morada = htmlspecialchars(strip_tags($_POST['morada']));
    $data_nasc = $_POST['data_nasc'];

    // Validacao 18 anos
    $nascimento = new DateTime($data_nasc);
    $hoje = new DateTime();
    $idade = $hoje->diff($nascimento)->y;

    if ($idade < 18) {
        $erro = "Erro: Deves ter pelo menos 18 anos para comprar.";
    } elseif (empty($nome) || empty($morada)) {
        $erro = "Erro: Todos os campos são obrigatórios.";
    } else {
        // Guardar Encomenda
        $user_id_fk = $_SESSION['user_id'] ?? null;
       
        // Guardar Encomenda
        $stmt = $pdo->prepare("INSERT INTO encomendas (cliente_nome, data_nascimento, morada, total, usuario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $data_nasc, $morada, $total_encomenda, $_SESSION['user_id']]);

        // Limpar o carrinho após a compra com sucesso
        unset($_SESSION['carrinho']);

        // Atualizar Stock
        $stmt = $pdo->prepare("UPDATE produtos SET quantidade_disponivel = quantidade_disponivel - 1 WHERE id = ?");
        $stmt->execute([$id]);

        echo "<script>alert('Sucesso! Encomenda registada.'); window.location.href='../index.php';</script>";
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
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="pagina-checkout">

    <div class="checkout-card">
        <div class="checkout-header">
            <h2>Finalizar Compra</h2>
            <?php foreach ($produtos_no_carrinho as $p): 
                $qtd = $_SESSION['carrinho'][$p['id']];
            ?>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 5px 0;">
                <span><?= htmlspecialchars($p['nome']) ?> (x<?= $qtd ?>)</span>
                <span>&euro; <?= number_format($p['preco_unidade'] * $qtd, 2, ',', ' ') ?></span>
            </div>
            <?php endforeach; ?>
            <h4 style="margin-top: 10px;">Total: &euro; <?= number_format($total_encomenda, 2, ',', ' ') ?></h4>
        </div>

        <div class="checkout-body">
            <?php if($erro): ?>
                <div style="color: red; margin-bottom: 15px;"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="form-control" value="<?= htmlspecialchars($nome_preenchido) ?>" placeholder="O teu nome completo" required>
                </div>

                <div class="form-group">
                    <label for="data_nasc">Data de Nascimento</label>
                    <input type="date" id="data_nasc" name="data_nasc" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="morada">Morada de Envio</label>
                    <textarea id="morada" name="morada" class="form-control" rows="3" placeholder="Rua, número, código postal, cidade" required><?= htmlspecialchars($morada_preenchida) ?></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100">Confirmar Pagamento</button>
                <a href="../index.php" class="btn btn-link w-100 mt-2" style="display: block; text-align: center; margin-top: 10px; text-decoration: none; color: #666;">&larr; Voltar à loja</a>
            </form>
        </div>
    </div>

</body>
</html>
