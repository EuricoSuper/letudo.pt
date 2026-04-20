<?php 
require 'config/db.php';
session_start();

if(!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['usuario_id'];
// Vai buscar os dados do utilizador
$user = $pdo->prepare("SELECT * FROM utilizadores WHERE id = ?");
$user->execute([$user_id]);
$dados = $user->fetch();

// Vai buscar o histórico de encomendas (precisas de ter uma tabela 'encomendas')
$compras = $pdo->prepare("SELECT * FROM encomendas WHERE usuario_id = ? ORDER BY data_compra DESC");
$compras->execute([$user_id]);
?>

<div class="container">
    <h2>Olá, <?= $dados['nome'] ?>!</h2>
    <div class="perfil-info">
        <p><strong>Email:</strong> <?= $dados['email'] ?></p>
        <p><strong>Morada:</strong> <?= $dados['morada'] ?></p>
        <p><strong>NIF:</strong> <?= $dados['nif'] ?></p>
    </div>

    <hr>

    <h3>Minhas Encomendas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php while($c = $compras->fetch()): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($c['data_compra'])) ?></td>
                <td><?= number_format($c['total'], 2) ?>€</td>
                <td><span class="badge"><?= $c['status'] ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>