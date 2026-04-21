<?php
/**
 * Configuração da Base de Dados - Letudo.pt
 * Conexão MySQLi com proteção básica
 */

// Definições da base de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'letudo_db');
define('DB_CHARSET', 'utf8mb4');

// Criar conexão
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexão
if (!$conn) {
    die("❌ Erro de conexão à base de dados: " . mysqli_connect_error());
}

// Definir charset
mysqli_set_charset($conn, DB_CHARSET);

// Iniciar sessão (se ainda não estiver iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Função para limpar e sanitizar dados (prevenir XSS e SQL Injection)
 * @param mixed $dados Dados a limpar
 * @return mixed Dados sanitizados
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
 * @param string $dataNascimento Data no formato YYYY-MM-DD
 * @return bool True se tiver 18+ anos
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
 * Formata preço para exibição em Euros
 * @param float $preco Valor do preço
 * @return string Preço formatado (ex: 29,99 €)
 */
function formatarPreco($preco) {
    return number_format(floatval($preco), 2, ',', '.') . ' €';
}

/**
 * Verifica se utilizador está logado como cliente
 * @return bool
 */
function estaLogado() {
    return isset($_SESSION['utilizador_id']) && $_SESSION['utilizador_id'] > 0;
}

/**
 * Verifica se utilizador é administrador
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

/**
 * Gera token CSRF para proteção de formulários
 * @return string
 */
function gerarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida token CSRF
 * @param string $token Token recebido
 * @return bool
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
?>