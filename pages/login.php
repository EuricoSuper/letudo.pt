<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$erro = $_SESSION['erro_login'] ?? '';
$sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['erro_login'], $_SESSION['sucesso']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ident = trim($_POST['identificador'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt  = $pdo->prepare("SELECT * FROM utilizadores WHERE email=? OR username=?");
    $stmt->execute([$ident, $ident]);
    $u = $stmt->fetch();
    if ($u && password_verify($pass, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $u['id'];
        $_SESSION['user_nome'] = $u['nome'];
        $_SESSION['user_tipo'] = $u['tipo'];
        if ($u['tipo'] === 'admin') { header('Location: ../admin/index.php'); exit; }
        header('Location: ../index.php'); exit;
    } else {
        $erro = 'Credenciais invalidas. Verifica o email/utilizador e a password.';
    }
}

$titulo = 'Iniciar sessao | Letudo.pt';
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap">
    <div class="auth-card">
        <h2>Iniciar sessao</h2>
        <p class="sub">Entra na tua conta Letudo para acompanhar encomendas.</p>
        <?php if ($erro): ?><div class="alert alert-danger" data-testid="login-error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
        <?php if ($sucesso): ?><div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email ou nome de utilizador</label>
                <input type="text" name="identificador" class="form-control" required autofocus data-testid="login-identificador">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required data-testid="login-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" data-testid="login-submit">Entrar</button>
        </form>
        <div class="auth-footer">
            Nao tens conta? <a href="registo.php">Regista-te aqui</a>
        </div>
        <div class="auth-footer" style="margin-top:14px; font-size:12px;">
            <strong>Demo:</strong> admin / admin123 &nbsp;&middot;&nbsp; cliente / cliente123
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
