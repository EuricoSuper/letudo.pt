<?php
session_start();
require_once __DIR__ . '/config/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: pages/login.php'); exit; }

$s = $pdo->prepare("SELECT * FROM utilizadores WHERE id=?");
$s->execute([$_SESSION['user_id']]);
$u = $s->fetch();
if (!$u) { session_destroy(); header('Location: index.php'); exit; }

$encs = $pdo->prepare("SELECT * FROM encomendas WHERE utilizador_id=? ORDER BY data_encomenda DESC");
$encs->execute([$_SESSION['user_id']]);
$encomendas = $encs->fetchAll();

$titulo = 'O meu perfil | Letudo.pt';
$base = '';
require __DIR__ . '/includes/header.php';
?>
<div class="container" style="padding:40px 20px;">
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:30px;" class="profile-layout">
        <aside class="admin-card">
            <div style="text-align:center; margin-bottom:20px;">
                <div style="width:80px; height:80px; background:var(--bordo); color:var(--branco); border-radius:50%; display:grid; place-items:center; font-family:var(--font-serif); font-size:32px; margin:0 auto 12px;"><?= strtoupper(substr($u['nome'],0,1)) ?></div>
                <h3 style="font-size:1.3rem;"><?= htmlspecialchars($u['nome']) ?></h3>
                <p style="font-size:13px; color:var(--cinza-500);"><?= htmlspecialchars($u['email']) ?></p>
            </div>
            <dl style="font-size:14px; color:var(--cinza-700);">
                <dt style="font-weight:600; margin-top:12px;">Utilizador</dt><dd><?= htmlspecialchars($u['username']) ?></dd>
                <dt style="font-weight:600; margin-top:12px;">Morada</dt><dd><?= htmlspecialchars($u['morada'] ?: 'Nao definida') ?></dd>
                <dt style="font-weight:600; margin-top:12px;">Tipo de conta</dt><dd><?= htmlspecialchars($u['tipo']) ?></dd>
                <dt style="font-weight:600; margin-top:12px;">Desde</dt><dd><?= date('d/m/Y', strtotime($u['data_registo'])) ?></dd>
            </dl>
            <a href="logout.php" class="btn btn-danger btn-block" style="margin-top:20px;" data-testid="profile-logout">Terminar sessao</a>
        </aside>
        <section class="admin-card">
            <h3>As minhas encomendas</h3>
            <?php if (!$encomendas): ?>
                <p style="color:var(--cinza-500); padding:20px 0;">Ainda nao fizeste nenhuma encomenda. <a href="index.php" style="color:var(--bordo);">Descobre o catalogo</a>.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>#</th><th>Data</th><th>Morada</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($encomendas as $e): ?>
                        <tr>
                            <td>#<?= (int)$e['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($e['data_encomenda'])) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($e['morada'], 0, 40, '...')) ?></td>
                            <td style="font-weight:700; color:var(--bordo);"><?= number_format($e['total'],2,',','') ?>&euro;</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
