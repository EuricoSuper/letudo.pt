<?php
session_start();
include 'includes/db.php';
// Lógica de verificação de login admin aqui...

// Listar Encomendas (Requisito PDF)
$encomendas = mysqli_query($conn, "SELECT * FROM encomendas");
?>

<div class="container admin-panel">
    <h1>Painel de Administração</h1>
    
    <h3>Lista de Encomendas Realizadas</h3>
    <table border="1" width="100%">
        <tr>
            <th>ID</th><th>Cliente</th><th>Total</th><th>Morada</th>
        </tr>
        <?php while($e = mysqli_fetch_assoc($encomendas)): ?>
        <tr>
            <td>#<?php echo $e['id']; ?></td>
            <td><?php echo $e['nome_cliente']; ?></td>
            <td><?php echo $e['preco_total']; ?>€</td>
            <td><?php echo $e['morada']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Gestão de Produtos (Preço e Stock)</h3>
    <!-- Formulário para atualizar produtos aqui -->
</div>