<?php
/**
 * Letudo.pt - Funções Globais
 * Centraliza validações, segurança e funções utilitárias
 */

/**
 * Limpa e sanitiza dados (prevenir XSS e SQL Injection)
 */
function limparDados($dados) {
    global $conn;
    if (is_array($dados)) {
        return array_map('limparDados', $dados);
    }
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($dados, ENT_QUOTES, 'UTF-8')));
}

/**
 * Verifica se o utilizador tem pelo menos 18 anos
 */
function verificarIdade($dataNascimento) {
    if (empty($dataNascimento)) return false;
    
    try {
        $nascimento = new DateTime($dataNascimento);
        $hoje = new DateTime();
        $idade = $nascimento->diff($hoje)->y;
        return $idade >= 18;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Formata preço para Euros
 */
function formatarPreco($preco) {
    return number_format(floatval($preco), 2, ',', '.') . ' €';
}

/**
 * Verifica se utilizador está logado como cliente
 */
function estaLogado() {
    return isset($_SESSION['utilizador_id']) && $_SESSION['utilizador_id'] > 0;
}

/**
 * Verifica se utilizador é administrador
 */
function isAdmin() {
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

/**
 * Requer autenticação (redireciona se não estiver logado)
 */
function requerAutenticacao() {
    if (!estaLogado() && !isAdmin()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Requer privilégios de administrador
 */
function requerAdmin() {
    if (!isAdmin()) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Gera token CSRF para proteção de formulários
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 */
function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Campo CSRF para incluir em formulários
 */
function campoCSRF() {
    echo '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
}

/**
 * Verifica stock disponível
 */
function verificarStock($conn, $produtoId, $quantidade) {
    $stmt = mysqli_prepare($conn, "SELECT quantidade_disponivel, nome FROM produtos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $produtoId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $stock, $nome);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($stock === null) {
        return ['disponivel' => false, 'mensagem' => 'Produto não encontrado.'];
    }
    
    if ($stock === 0) {
        return ['disponivel' => false, 'mensagem' => "Produto '{$nome}' esgotado."];
    }
    
    if ($quantidade > $stock) {
        return ['disponivel' => false, 'mensagem' => "Stock insuficiente para '{$nome}'. Disponível: {$stock}"];
    }
    
    return ['disponivel' => true, 'stock' => $stock, 'mensagem' => 'Stock disponível.'];
}

/**
 * Calcula total do carrinho
 */
function calcularTotalCarrinho($carrinho) {
    $total = 0;
    foreach ($carrinho as $item) {
        $total += $item['preco'] * $item['qtd'];
    }
    return $total;
}

/**
 * Mostra mensagens de feedback
 */
function mostrarMensagem($texto, $tipo = 'sucesso') {
    $classes = [
        'sucesso' => 'mensagem mensagem-sucesso',
        'erro' => 'mensagem mensagem-erro',
        'aviso' => 'mensagem mensagem-aviso'
    ];
    $classe = $classes[$tipo] ?? $classes['sucesso'];
    echo "<div class='{$classe}'>{$texto}</div>";
}

/**
 * Redireciona com mensagem
 */
function redirecionarComMensagem($url, $mensagem, $tipo = 'sucesso') {
    $_SESSION['flash_message'] = ['mensagem' => $mensagem, 'tipo' => $tipo];
    header('Location: ' . $url);
    exit;
}

/**
 * Mostra mensagens flash
 */
function mostrarMensagensFlash() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        mostrarMensagem($msg['mensagem'], $msg['tipo']);
        unset($_SESSION['flash_message']);
    }
}
?>