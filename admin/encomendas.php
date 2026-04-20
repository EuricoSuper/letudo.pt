<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    header('Location: login.php');
    exit;
}

$query = "SELECT e.*, u.nome as nome_utilizador, u.email 
          FROM encomendas e 
          LEFT JOIN utilizadores u ON e.utilizador_id = u.id 
          ORDER BY e.data_encomenda DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomendas - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <h1><a href="index.php" style="color: white; text-decoration: none;">Admin - Letudo.pt</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="produtos.php">Produtos</a></li>
                    <li><a href="encomendas.php">Encomendas</a></li>
                    <li><a href="../index.php">Site</a></li>
                    <li><a href="logout.php">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="admin-container">
            <h2>Encomendas Realizadas</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Produtos</th>
                        <th>Quantidades</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($encomenda = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $encomenda['id']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($encomenda['data_encomenda'])); ?></td>
                            <td><?php echo htmlspecialchars($encomenda['nome_cliente']); ?></td>
                            <td><?php echo $encomenda['email'] ? htmlspecialchars($encomenda['email']) : 'Visitante'; ?></td>
                            <td><?php echo htmlspecialchars($encomenda['produtos']); ?></td>
                            <td><?php echo htmlspecialchars($encomenda['quantidades']); ?></td>
                            <td><strong>€<?php echo number_format($encomenda['preco_total'], 2, ',', '.'); ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>