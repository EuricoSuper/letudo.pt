<?php 
session_start();
require 'config/db.php';

if(!isset($_SESSION['user_id'])) { 
    header("Location: pages/login.php"); 
    exit; 
}

$user_id = $_SESSION['user_id'];
// Vai buscar os dados do utilizador
$user = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
$user->execute([$user_id]);
$dados = $user->fetch();

if (!$dados) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Vai buscar o histórico de encomendas
// Nota: Certifique-se que a coluna na tabela encomendas é 'usuario_id' ou 'cliente_id'
try {
    $compras = $pdo->prepare("SELECT * FROM encomendas WHERE usuario_id = ? ORDER BY id DESC");
    $compras->execute([$user_id]);
} catch (Exception $e) {
    $compras = null;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Meu Perfil | Letudo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar-custom">
    <div class="container">
        <a href="index.php" class="navbar-brand">📖 Letudo.pt</a>
        <div class="nav-buttons">
            <a href="index.php" class="btn">Voltar à Loja</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container" style="margin-top: 50px;">
    <div class="login-card" style="max-width: 800px; margin: 0 auto;">
        <div class="login-header">
            <h2>Olá, <?= htmlspecialchars($dados['nome']) ?>!</h2>
            <p>Estes são os teus dados e histórico de encomendas</p>
        </div>

        <div class="perfil-info" style="padding: 20px; background: #f9f9f9; border-radius: 8px; margin-bottom: 30px;">
            <p><strong>Email:</strong> <?= htmlspecialchars($dados['email']) ?></p>
            <p><strong>Morada:</strong> <?= htmlspecialchars($dados['morada'] ?? 'Não definida') ?></p>
            <p><strong>NIF:</strong> <?= htmlspecialchars($dados['nif'] ?? 'Não definido') ?></p>
        </div>

        <hr>

        <h3>Minhas Encomendas</h3>
        <?php if ($compras && $compras->rowCount() > 0): ?>
        <table class="table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #eee;">
                    <th style="padding: 10px; text-align: left;">ID</th>
                    <th style="padding: 10px; text-align: left;">Data</th>
                    <th style="padding: 10px; text-align: left;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $compras->fetch()): ?>
                <tr style="border-bottom: 1px solid #ddd;">
                    <td style="padding: 10px;"><?= $c['id'] ?></td>
                    <td style="padding: 10px;"><?= isset($c['data_compra']) ? date('d/m/Y', strtotime($c['data_compra'])) : 'N/A' ?></td>
                    <td style="padding: 10px;"><?= number_format($c['total'] ?? 0, 2) ?>€</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="margin-top: 20px; color: #666;">Ainda não realizaste nenhuma encomenda.</p>
        <?php endif; ?>
    </div>
</div>

<footer class="site-footer" style="margin-top: 50px;">
    <p>&copy; <?= date("Y") ?> Livraria Letudo. Todos os direitos reservados.</p>
</footer>

</body>
</html>
