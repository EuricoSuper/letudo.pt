<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$erros = $_SESSION['erros_registo'] ?? [];
unset($_SESSION['erros_registo']);
$dados = $_SESSION['dados_registo'] ?? [];
unset($_SESSION['dados_registo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim(htmlspecialchars(strip_tags($_POST['nome'] ?? '')));
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $morada    = trim(htmlspecialchars(strip_tags($_POST['morada'] ?? '')));

    if (strlen($nome) < 3) $erros[] = 'Nome tem de ter pelo menos 3 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'Email invalido.';
    if (strlen($username) < 3) $erros[] = 'Nome de utilizador tem de ter pelo menos 3 caracteres.';
    if (strlen($password) < 8) $erros[] = 'Password deve ter no minimo 8 caracteres.';
    if (!preg_match('/[A-Z]/', $password)) $erros[] = 'Password deve conter 1 maiuscula.';
    if (!preg_match('/[a-z]/', $password)) $erros[] = 'Password deve conter 1 minuscula.';
    if (!preg_match('/[0-9]/', $password)) $erros[] = 'Password deve conter 1 numero.';
    if ($password !== $confirm) $erros[] = 'As passwords nao coincidem.';

    if (empty($erros)) {
        $check = $pdo->prepare("SELECT id FROM utilizadores WHERE email=? OR username=?");
        $check->execute([$email, $username]);
        if ($check->fetch()) { $erros[] = 'Email ou nome de utilizador ja registado.'; }
    }
    if (empty($erros)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO utilizadores (nome,email,username,password,morada,tipo) VALUES (?,?,?,?,?,'cliente')");
        $ins->execute([$nome, $email, $username, $hash, $morada]);
        $_SESSION['sucesso'] = 'Registo efetuado com sucesso! Faz login para continuar.';
        header('Location: login.php'); exit;
    } else {
        $_SESSION['erros_registo'] = $erros;
        $_SESSION['dados_registo'] = $_POST;
        header('Location: registo.php'); exit;
    }
}

$titulo = 'Criar conta | Letudo.pt';
$base = '../';
require __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap">
    <div class="auth-card wide">
        <h2>Cria a tua conta</h2>
        <p class="sub">E gratis. Acompanha as tuas encomendas e recebe novidades.</p>
        <?php if ($erros): ?>
            <div class="alert alert-danger" data-testid="register-errors">
                <?php foreach ($erros as $e): ?><div>&middot; <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group"><label>Nome completo *</label><input type="text" name="nome" class="form-control" required minlength="3" value="<?= htmlspecialchars($dados['nome'] ?? '') ?>" data-testid="reg-nome"></div>
                <div class="form-group"><label>Nome de utilizador *</label><input type="text" name="username" class="form-control" required minlength="3" value="<?= htmlspecialchars($dados['username'] ?? '') ?>" data-testid="reg-username"></div>
            </div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($dados['email'] ?? '') ?>" data-testid="reg-email"></div>
            <div class="form-grid">
                <div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" required minlength="8" data-testid="reg-password"><span class="form-hint">Min. 8, 1 maiuscula, 1 numero</span></div>
                <div class="form-group"><label>Confirmar password *</label><input type="password" name="confirm_password" class="form-control" required data-testid="reg-password-confirm"></div>
            </div>
            <div class="form-group"><label>Morada</label><input type="text" name="morada" class="form-control" value="<?= htmlspecialchars($dados['morada'] ?? '') ?>" data-testid="reg-morada"></div>
            <button type="submit" class="btn btn-primary btn-block" style="margin-top:10px;" data-testid="reg-submit">Criar conta</button>
        </form>
        <div class="auth-footer">Ja tens conta? <a href="login.php">Inicia sessao</a></div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
