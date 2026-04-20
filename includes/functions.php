<?php
/**
 * Letudo.pt - Funções Globais
 * Ficheiro com funções utilitárias para todo o projeto
 */

/**
 * Limpa e sanitiza dados para prevenir XSS e SQL Injection
 * @param mysqli $conn Conexão à base de dados
 * @param string $dados Dados a limpar
 * @return string Dados sanitizados
 */
function limparDados($conn, $dados) {
    if (is_array($dados)) {
        foreach ($dados as $key => $value) {
            $dados[$key] = limparDados($conn, $value);
        }
        return $dados;
    }
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($dados, ENT_QUOTES, 'UTF-8')));
}

/**
 * Verifica se o utilizador tem pelo menos 18 anos
 * @param string $dataNascimento Data no formato YYYY-MM-DD
 * @return bool True se tiver 18+ anos, false caso contrário
 */
function verificarIdade($dataNascimento) {
    if (empty($dataNascimento)) return false;
    
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    $idade = $nascimento->diff($hoje)->y;
    
    return $idade >= 18;
}

/**
 * Formata preço para exibição em Euros
 * @param float|decimal $preco Valor do preço
 * @return string Preço formatado (ex: 29,99 €)
 */
function formatarPreco($preco) {
    return number_format(floatval($preco), 2, ',', '.') . ' €';
}

/**
 * Verifica se o utilizador está autenticado como cliente
 * @return bool True se estiver logado como cliente
 */
function estaLogadoCliente() {
    return isset($_SESSION['utilizador_id']) && $_SESSION['utilizador_id'] > 0;
}

/**
 * Verifica se o utilizador está autenticado como administrador
 * @return bool True se estiver logado como admin
 */
function estaLogadoAdmin() {
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

/**
 * Redireciona para página de login se não estiver autenticado
 * @param string $redirectUrl URL para redirecionar após login
 */
function requerAutenticacao($redirectUrl = '') {
    if (!estaLogadoCliente() && !estaLogadoAdmin()) {
        if (!empty($redirectUrl)) {
            $_SESSION['redirect_after_login'] = $redirectUrl;
        }
        header('Location: login.php');
        exit;
    }
}

/**
 * Redireciona para página de admin se não for administrador
 */
function requerAdmin() {
    if (!estaLogadoAdmin()) {
        header('Location: ../login.php');
        exit;
    }
}

/**
 * Obtém o carrinho de compras do localStorage via POST ou sessão
 * @return array Array com itens do carrinho
 */
function obterCarrinho() {
    if (isset($_POST['carrinho']) && is_string($_POST['carrinho'])) {
        $carrinho = json_decode($_POST['carrinho'], true);
        return is_array($carrinho) ? $carrinho : [];
    }
    return [];
}

/**
 * Verifica stock disponível para um produto
 * @param mysqli $conn Conexão à base de dados
 * @param int $produtoId ID do produto
 * @param int $quantidade Quantidade pretendida
 * @return array ['disponivel' => bool, 'stock_atual' => int, 'mensagem' => string]
 */
function verificarStock($conn, $produtoId, $quantidade) {
    $produtoId = intval($produtoId);
    $quantidade = intval($quantidade);
    
    $query = "SELECT quantidade, nome FROM produtos WHERE id = $produtoId";
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) === 0) {
        return [
            'disponivel' => false,
            'stock_atual' => 0,
            'mensagem' => 'Produto não encontrado.'
        ];
    }
    
    $produto = mysqli_fetch_assoc($result);
    $stockAtual = intval($produto['quantidade']);
    
    if ($stockAtual === 0) {
        return [
            'disponivel' => false,
            'stock_atual' => 0,
            'mensagem' => 'Produto "' . htmlspecialchars($produto['nome']) . '" esgotado.'
        ];
    }
    
    if ($quantidade > $stockAtual) {
        return [
            'disponivel' => false,
            'stock_atual' => $stockAtual,
            'mensagem' => 'Stock insuficiente para "' . htmlspecialchars($produto['nome']) . '". Disponível: ' . $stockAtual
        ];
    }
    
    return [
        'disponivel' => true,
        'stock_atual' => $stockAtual,
        'mensagem' => 'Stock disponível.'
    ];
}

/**
 * Calcula o total do carrinho
 * @param mysqli $conn Conexão à base de dados
 * @param array $carrinho Array com itens do carrinho
 * @return array ['total' => float, 'itens_validos' => array, 'erros' => array]
 */
function calcularTotalCarrinho($conn, $carrinho) {
    $total = 0;
    $itensValidos = [];
    $erros = [];
    
    foreach ($carrinho as $item) {
        $produtoId = intval($item['id'] ?? 0);
        $quantidade = intval($item['quantidade'] ?? 0);
        
        if ($produtoId <= 0 || $quantidade <= 0) continue;
        
        $verificacao = verificarStock($conn, $produtoId, $quantidade);
        
        if (!$verificacao['disponivel']) {
            $erros[] = $verificacao['mensagem'];
            continue;
        }
        
        $query = "SELECT id, nome, preco FROM produtos WHERE id = $produtoId";
        $result = mysqli_query($conn, $query);
        $produto = mysqli_fetch_assoc($result);
        
        if ($produto) {
            $subtotal = floatval($produto['preco']) * $quantidade;
            $total += $subtotal;
            
            $itensValidos[] = [
                'id' => $produto['id'],
                'nome' => $produto['nome'],
                'preco' => floatval($produto['preco']),
                'quantidade' => $quantidade,
                'subtotal' => $subtotal
            ];
        }
    }
    
    return [
        'total' => $total,
        'itens_validos' => $itensValidos,
        'erros' => $erros
    ];
}

/**
 * Insere uma nova encomenda na base de dados
 * @param mysqli $conn Conexão à base de dados
 * @param int|null $utilizadorId ID do utilizador (pode ser null para visitantes)
 * @param string $nomeCliente Nome do cliente
 * @param string $dataNascimento Data de nascimento
 * @param string $morada Morada completa
 * @param array $itens Itens da encomenda
 * @param float $precoTotal Preço total da encomenda
 * @return array ['sucesso' => bool, 'encomenda_id' => int|null, 'mensagem' => string]
 */
function inserirEncomenda($conn, $utilizadorId, $nomeCliente, $dataNascimento, $morada, $itens, $precoTotal) {
    global $mensagem, $tipoMensagem;
    
    // Preparar arrays para armazenamento
    $nomesProdutos = [];
    $quantidadesProdutos = [];
    
    foreach ($itens as $item) {
        $nomesProdutos[] = $item['nome'];
        $quantidadesProdutos[] = $item['quantidade'];
        
        // Atualizar stock do produto
        $updateStock = "UPDATE produtos SET quantidade = quantidade - {$item['quantidade']} WHERE id = {$item['id']}";
        mysqli_query($conn, $updateStock);
    }
    
    $produtosStr = implode(', ', $nomesProdutos);
    $quantidadesStr = implode(', ', $quantidadesProdutos);
    $utilizadorIdSafe = $utilizadorId ?? 'NULL';
    
    // Inserir encomenda com prepared statement para segurança
    $insertQuery = "INSERT INTO encomendas (utilizador_id, nome_cliente, data_nascimento, morada, produtos, quantidades, preco_total) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insertQuery);
    
    if (!$stmt) {
        return [
            'sucesso' => false,
            'encomenda_id' => null,
            'mensagem' => 'Erro interno ao processar a encomenda.'
        ];
    }
    
    // Bind parameters: i = integer, s = string, d = decimal
    mysqli_stmt_bind_param($stmt, "isssssd", 
        $utilizadorId, 
        $nomeCliente, 
        $dataNascimento, 
        $morada, 
        $produtosStr, 
        $quantidadesStr, 
        $precoTotal
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $encomendaId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        
        return [
            'sucesso' => true,
            'encomenda_id' => $encomendaId,
            'mensagem' => 'Encomenda #' . $encomendaId . ' realizada com sucesso!'
        ];
    } else {
        mysqli_stmt_close($stmt);
        return [
            'sucesso' => false,
            'encomenda_id' => null,
            'mensagem' => 'Erro ao registar a encomenda. Tente novamente.'
        ];
    }
}

/**
 * Gera token CSRF para proteção de formulários
 * @return string Token CSRF
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 * @param string $token Token recebido do formulário
 * @return bool True se válido
 */
function validarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Mostra campo CSRF em formulários
 */
function campoCSRF() {
    echo '<input type="hidden" name="csrf_token" value="' . gerarTokenCSRF() . '">';
}

/**
 * Redireciona com mensagem de feedback
 * @param string $url URL de destino
 * @param string $mensagem Mensagem a mostrar
 * @param string $tipo Tipo de mensagem (sucesso, erro, aviso)
 */
function redirecionarComMensagem($url, $mensagem, $tipo = 'sucesso') {
    $_SESSION['flash_message'] = [
        'mensagem' => $mensagem,
        'tipo' => $tipo
    ];
    header('Location: ' . $url);
    exit;
}

/**
 * Mostra mensagens flash (session-based)
 */
function mostrarMensagensFlash() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        echo '<div class="mensagem mensagem-' . htmlspecialchars($msg['tipo']) . '">';
        echo htmlspecialchars($msg['mensagem']);
        echo '</div>';
        unset($_SESSION['flash_message']);
    }
}

/**
 * Obtém URL amigável a partir de string (para SEO)
 * @param string $texto Texto original
 * @return string Texto formatado para URL
 */
function slugify($texto) {
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $texto = preg_replace('/[^a-z0-9\s-]/', '', $texto);
    $texto = preg_replace('/[\s-]+/', '-', $texto);
    return trim($texto, '-');
}

/**
 * Meta tags para SEO
 * @param string $titulo Título da página
 * @param string $descricao Descrição da página
 * @param string $palavrasChave Palavras-chave separadas por vírgula
 * @return string HTML com meta tags
 */
function gerarMetaTagsSEO($titulo, $descricao, $palavrasChave = '') {
    $tituloSafe = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
    $descricaoSafe = htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8');
    
    $output = '<title>' . $tituloSafe . ' - Letudo.pt</title>' . "\n";
    $output .= '<meta name="description" content="' . $descricaoSafe . '">' . "\n";
    
    if (!empty($palavrasChave)) {
        $keywordsSafe = htmlspecialchars($palavrasChave, ENT_QUOTES, 'UTF-8');
        $output .= '<meta name="keywords" content="' . $keywordsSafe . '">' . "\n";
    }
    
    $output .= '<meta name="robots" content="index, follow">' . "\n";
    $output .= '<meta property="og:title" content="' . $tituloSafe . '">' . "\n";
    $output .= '<meta property="og:description" content="' . $descricaoSafe . '">' . "\n";
    $output .= '<meta property="og:type" content="website">' . "\n";
    
    return $output;
}

/**
 * Valida email
 * @param string $email Email a validar
 * @return bool True se válido
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida password (mínimo 6 caracteres)
 * @param string $password Password a validar
 * @return bool True se válida
 */
function validarPassword($password) {
    return strlen($password) >= 6;
}

/**
 * Hash de password seguro
 * @param string $password Password em texto claro
 * @return string Password com hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica password contra hash
 * @param string $password Password em texto claro
 * @param string $hash Hash armazenado na BD
 * @return bool True se corresponder
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Limita texto para exibição (com reticências)
 * @param string $texto Texto original
 * @param int $limite Número máximo de caracteres
 * @return string Texto truncado
 */
function limitarTexto($texto, $limite = 100) {
    if (mb_strlen($texto) <= $limite) return $texto;
    return mb_substr($texto, 0, $limite) . '...';
}

/**
 * Regista atividade de log para auditoria (opcional)
 * @param mysqli $conn Conexão à BD
 * @param string $acao Tipo de ação (login, compra, etc.)
 * @param int|null $utilizadorId ID do utilizador
 */
function registarLog($conn, $acao, $utilizadorId = null) {
    // Tabela opcional: CREATE TABLE logs (id INT AUTO_INCREMENT PRIMARY KEY, acao VARCHAR(50), utilizador_id INT, ip VARCHAR(45), data TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $acaoSafe = limparDados($conn, $acao);
    
    // Descomentar se criar a tabela logs
    // $query = "INSERT INTO logs (acao, utilizador_id, ip) VALUES ('$acaoSafe', " . ($utilizadorId ?? 'NULL') . ", '$ip')";
    // mysqli_query($conn, $query);
}
?>