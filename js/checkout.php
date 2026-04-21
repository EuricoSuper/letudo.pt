<?php 
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle = "Finalizar Compra";
include 'includes/header.php'; 

$mensagem = '';
$tipo = '';

// Obter carrinho do localStorage via POST ou cookie
$carrinho_json = $_POST['carrinho_json'] ?? $_COOKIE['carrinho'] ?? '[]';
$carrinho = json_decode($carrinho_json, true) ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $mensagem = 'Erro de segurança. Tente novamente.';
        $tipo = 'erro';
    }
    // Validação 1: Campos não vazios
    elseif (empty($_POST['nome']) || empty($_POST['nascimento']) || empty($_POST['morada'])) {
        $mensagem = 'Por favor, preencha todos os campos obrigatórios.';
        $tipo = 'erro';
    }
    // Validação 2: Carrinho não vazio
    elseif (empty($carrinho)) {
        $mensagem = 'O seu carrinho está vazio.';
        $tipo = 'erro';
    }
    // Validação 3: Idade >= 18 anos
    elseif (!verificarIdade($_POST['nascimento'])) {
        $mensagem = 'Deve ter pelo menos 18 anos para efetuar uma compra.';
        $tipo = 'erro';
    } else {
        // Proteção XSS
        $nome = htmlspecialchars($_POST['nome'], ENT_QUOTES, 'UTF-8');
        $nascimento = htmlspecialchars($_POST['nascimento'], ENT_QUOTES, 'UTF-8');
        $morada = htmlspecialchars($_POST['morada'], ENT_QUOTES, 'UTF-8');
        
        $produtos_nomes = [];
        $produtos_qtds = [];
        $total = 0;
        $erro_stock = false;
        $mensagem_erro = '';
        
        // Verificar stock e calcular total
        foreach ($carrinho as $item) {
            $verificacao = verificarStock($conn, $item['id'], $item['qtd']);
            
            if (!$verificacao['disponivel']) {
                $erro_stock = true;
                $mensagem_erro = $verificacao['mensagem'];
                break;
            }
            
            // Obter preço atual da BD
            $stmt = mysqli_prepare($conn, "SELECT preco_unidade FROM produtos WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $item['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $preco_db);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            
            $produtos_nomes[] = $item['nome'];
            $produtos_qtds[] = $item['qtd'];
            $total += $preco_db * $item['qtd'];
            
            // Atualizar stock
            $update = mysqli_prepare($conn, "UPDATE produtos SET quantidade_disponivel = quantidade_disponivel - ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "ii", $item['qtd'], $item['id']);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        }
        
        if (!$erro_stock) {
            // Inserir encomenda
            $produtos_str = implode(', ', $produtos_nomes);
            $qtds_str = implode(', ', $produtos_qtds);
            $utilizador_id = $_SESSION['utilizador_id'] ?? null;
            
            $stmt = mysqli_prepare($conn, "INSERT INTO encomendas (utilizador_id, nome_cliente, data_nascimento, morada, produtos, quantidades, preco_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssdd", $utilizador_id, $nome, $nascimento, $morada, $produtos_str, $qtds_str, $total);
            
            if (mysqli_stmt_execute($stmt)) {
                $mensagem = '🎉 Encomenda realizada com sucesso! Obrigado pela sua compra. O número da sua encomenda é: ' . mysqli_insert_id($conn);
                $tipo = 'sucesso';
                
                // Limpar carrinho
                echo '<script>localStorage.removeItem("carrinho"); document.cookie="carrinho=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";</script>';
            } else {
                $mensagem = 'Erro ao registar encomenda. Tente novamente.';
                $tipo = 'erro';
            }
            mysqli_stmt_close($stmt);
        } else {
            $mensagem = $mensagem_erro;
            $tipo = 'erro';
        }
    }
}

// Calcular total para exibição
$total_exibicao = calcularTotalCarrinho($carrinho);
?>

<div class="container checkout-container">
    <h1>Finalizar Compra</h1>
    <p class="subtitle">Preencha os dados para envio da encomenda</p>
    
    <?php 
    if (!empty($mensagem)) {
        mostrarMensagem($mensagem, $tipo);
    }
    mostrarMensagensFlash();
    ?>
    
    <?php if ($tipo !== 'sucesso'): ?>
    <div class="resumo-compra">
        <h3>Resumo da Compra</h3>
        <p>Total: <strong><?= formatarPreco($total_exibicao) ?></strong></p>
        <p>Itens: <?= count($carrinho) ?></p>
    </div>
    
    <form method="POST" class="form-checkout">
        <?php campoCSRF(); ?>
        <input type="hidden" name="carrinho_json" value='<?= htmlspecialchars(json_encode($carrinho)) ?>'>
        
        <div class="form-group">
            <label for="nome">Nome Completo *</label>
            <input type="text" id="nome" name="nome" required 
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"
                   placeholder="Ex: João Silva">
        </div>
        
        <div class="form-group">
            <label for="nascimento">Data de Nascimento *</label>
            <input type="date" id="nascimento" name="nascimento" required 
                   value="<?= htmlspecialchars($_POST['nascimento'] ?? '') ?>">
            <small>Deve ter pelo menos 18 anos</small>
        </div>
        
        <div class="form-group">
            <label for="morada">Morada Completa *</label>
            <textarea id="morada" name="morada" rows="4" required 
                      placeholder="Ex: Rua Exemplo, 123, 1000-001 Lisboa"><?= htmlspecialchars($_POST['morada'] ?? '') ?></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-concluir">✓ Concluir Transação</button>
            <a href="index.php" class="btn-voltar">← Voltar à Loja</a>
        </div>
    </form>
    <?php else: ?>
    <div class="sucesso-compra">
        <p class="text-center mt-2">
            <a href="index.php" class="btn-concluir">Continuar Compras</a>
        </p>
    </div>
    <?php endif; ?>
</div>

<style>
.checkout-container { max-width: 700px; margin: 0 auto; }
.resumo-compra {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    border-left: 4px solid var(--cor-primaria);
}
.form-checkout {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: var(--sombra-suave);
}
.form-group { margin-bottom: 1.5rem; }
.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--cor-texto);
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transicao);
}
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--cor-primaria);
    box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
}
.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: #6b7280;
    font-size: 0.85rem;
}
.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    flex-wrap: wrap;
}
.btn-concluir {
    flex: 1;
    min-width: 200px;
}
.btn-voltar {
    display: inline-block;
    padding: 1rem 2rem;
    background: #6b7280;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transicao);
}
.btn-voltar:hover { background: #4b5563; }
.sucesso-compra {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 12px;
    box-shadow: var(--sombra-media);
}
</style>

<?php include 'includes/footer.php'; ?>