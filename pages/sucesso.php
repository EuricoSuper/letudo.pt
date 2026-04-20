<?php
session_start();
$enc = $_SESSION['ultima_encomenda'] ?? null;
if (!$enc) { header("Location: ../index.php"); exit; }
unset($_SESSION['ultima_encomenda']);
require_once __DIR__ . '/../config/db.php';

$titulo = 'Encomenda confirmada | Letudo.pt';
$base = '../';
$pagina_atual = 'sucesso';
require __DIR__ . '/../includes/header.php';
?>
<div class="container" style="padding:80px 20px;">
    <div style="max-width:640px; margin:0 auto; text-align:center; background:var(--branco); padding:56px 40px; border:1px solid var(--cinza-200); border-radius:var(--radius-lg); box-shadow:var(--shadow-md);">
        <div style="width:90px; height:90px; background:var(--sucesso); border-radius:50%; display:grid; place-items:center; margin:0 auto 24px;">
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h1 style="font-size:2.6rem; margin-bottom:10px;" data-testid="success-title">Encomenda confirmada!</h1>
        <p style="font-size:1.1rem; color:var(--cinza-700); margin-bottom:24px;">Obrigado, <strong><?= htmlspecialchars($enc['nome']) ?></strong>. A tua encomenda <strong>#<?= (int)$enc['id'] ?></strong> foi registada com sucesso.</p>
        <p style="font-size:1rem; color:var(--cinza-500); margin-bottom:30px;">Total pago: <strong style="color:var(--bordo); font-size:1.4rem; font-family:var(--font-serif);"><?= number_format($enc['total'], 2, ',', '') ?>&euro;</strong></p>
        <p style="color:var(--cinza-500); margin-bottom:30px;">Enviaremos um email com os detalhes da entrega em breve.</p>
        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="../index.php" class="btn btn-primary">Continuar a comprar</a>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../perfil.php" class="btn btn-secondary">Ver as minhas encomendas</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
