<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erro = 'Erro de segurança. Tente novamente.';
    } else {
        $username = limparDados($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $erro = 'Preencha todos os campos.';
        } else {
            // Buscar utilizador admin
            $stmt = mysqli_prepare($conn, "SELECT id, password, nome FROM utilizadores WHERE username = ? AND tipo = 'admin'");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) === 1) {
                mysqli_stmt_bind_result($stmt, $id, $hash, $nome);
                mysqli_stmt_fetch($stmt);
                
                if (password_verify($password, $hash)) {
                    // Login bem-sucedido
                    $_SESSION['admin_id'] = $id;
                    $_SESSION['admin_nome'] = $nome;
                    $_SESSION['admin_logado'] = true;
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $erro = 'Palavra-passe incorreta.';
                }
            } else {
                $erro = 'Utilizador não encontrado ou não tem privilégios de administrador.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Letudo.pt</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: var(--cor-primaria);
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: #6b7280;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--cor-texto);
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transicao);
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--cor-primaria) 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transicao);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
        }
        .link-voltar {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--cor-primaria);
            text-decoration: none;
        }
        .link-voltar:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🔐 Área de Administração</h1>
            <p>Aceda para gerir a loja</p>
        </div>
        
        <?php if (!empty($erro)): ?>
            <div class="mensagem mensagem-erro"><?= $erro ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <?php campoCSRF(); ?>
            
            <div class="form-group">
                <label for="username">Nome de Utilizador</label>
                <input type="text" id="username" name="username" required 
                       autofocus placeholder="admin">
            </div>
            
            <div class="form-group">
                <label for="password">Palavra-passe</label>
                <input type="password" id="password" name="password" required 
                       placeholder="••••••••">
            </div>
            
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        
        <a href="../index.php" class="link-voltar">← Voltar ao site</a>
    </div>
</body>
</html>