<?php 
require_once 'config/db.php';
$pageTitle = "Finalizar Compra";
include 'includes/header.php';

$mensagem = '';
$tipo = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $nascimento = trim($_POST['nascimento']);
    $morada = trim($_POST['morada']);
    $carrinho = json_decode($_POST['carrinho_json'], true) ?? [];
    
    // ✅ Validação 1: Campos não vazios
    if(empty($nome) || empty($nascimento) || empty($morada) || empty($carrinho)) {
        $mensagem = 'Por favor, preencha todos os campos e tenha produtos no carrinho.';
        $tipo = 'erro';
    }
    // ✅ Validação 2: Idade >= 18
    elseif(!verificarIdade($nascimento)) {
        $mensagem = 'Deve ter pelo menos 18 anos para efetuar uma compra.';
        $tipo = 'erro';
    } else {
        // ✅ Proteção XSS + Preparar dados
        $nome = htmlspecialchars($nome, ENT_QUOTES, 'UTF-8');
        $morada = htmlspecialchars($morada, ENT_QUOTES, 'UTF-8');
        
        $produtos_nomes = [];
        $produtos_qtds = [];
        $total = 0;
        $erro_stock = false;
        
        foreach($carrinho as $item) {
            $stmt = mysqli_prepare($conn, "SELECT quantidade_disponivel, preco_unidade FROM produtos WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $item['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $stock, $preco_db);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            if($item['qtd'] > $stock) {
                $mensagem = "Stock insuficiente para o produto ID {$item['id']}.";
                $tipo = 'erro';
                $erro_stock = true;
                break;
            }
            if($stock == 0) {
                $mensagem = "Produto esgotado.";
                $tipo = 'erro';
                $erro_stock = true;
                break;
            }
            
            $produtos_nomes[] = $item['nome'];
            $produtos_qtds[] = $item['qtd'];
            $total += $preco_db * $item['qtd'];
            
            // Atualizar stock
            $update = mysqli_prepare($conn, "UPDATE produtos SET quantidade_disponivel = quantidade_disponivel - ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "ii", $item['qtd'], $item['id']);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        }
        
        if(!$erro_stock) {
            $produtos_str = implode(', ', $produtos_nomes);
            $qtds_str = implode(', ', $produtos_qtds);
            $utilizador_id = $_SESSION['utilizador_id'] ?? null;
            
            $stmt = mysqli_prepare($conn, "INSERT INTO encomendas (utilizador_id, nome_cliente, data_nascimento, morada, produtos, quantidades, preco_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssdd", $utilizador_id, $nome, $nascimento, $morada, $produtos_str, $qtds_str, $total);
            
            if(mysqli_stmt_execute($stmt)) {
                $mensagem = '🎉 Encomenda realizada com sucesso! Obrigado pela sua compra.';
                $tipo = 'sucesso';
                echo '<script>localStorage.removeItem("carrinho");</script>';
            } else {
                $mensagem = 'Erro ao registar encomenda. Tente novamente.';
                $tipo = 'erro';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="container checkout-container">
    <h1 class="text-center">Finalizar Compra</h1>
    
    <?php if($mensagem): ?>
        <div class="mensagem mensagem-<?= $tipo ?>"><?= $mensagem ?></div>
    <?php endif; ?>
    
    <form method="POST" class="form-checkout">
        <input type="hidden" name="carrinho_json" value="<?= htmlspecialchars(json_encode(json_decode($_COOKIE['carrinho'] ?? '[]', true) ?? [])) ?>">
        <?php campoCSRF(); ?>
        
        <div class="form-group">
            <label>Nome Completo *</label>
            <input type="text" name="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Data de Nascimento *</label>
            <input type="date" name="nascimento" required value="<?= htmlspecialchars($_POST['nascimento'] ?? '') ?>">
            <small>Mínimo 18 anos</small>
        </div>
        
        <div class="form-group">
            <label>Morada Completa *</label>
            <textarea name="morada" rows="3" required><?= htmlspecialchars($_POST['morada'] ?? '') ?></textarea>
        </div>
        
        <button type="submit" class="btn-concluir">Concluir Transação</button>
        <a href="index.php" class="btn-voltar">Voltar à Loja</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>