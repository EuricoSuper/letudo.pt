<?php 
include 'includes/db.php'; 
include 'includes/header.php'; 
?>

<main class="container">
    <div class="almedina-banner">
        <h1>Livraria LêTudo</h1>
        <p>Projeto Final - Avançado em Desenho e Programação</p>
    </div>

    <section class="store-section">
        <h2 class="section-title">Livros Disponíveis</h2>
        <form id="shop-form" action="checkout.php" method="POST">
            <div class="book-grid">
                <?php
                $res = mysqli_query($conn, "SELECT * FROM produtos");
                while($row = mysqli_fetch_assoc($res)):
                ?>
                <div class="book-card" data-id="<?php echo $row['id']; ?>">
                    <img src="img/<?php echo $row['imagem']; ?>" alt="Capa">
                    <h3><?php echo $row['nome']; ?></h3>
                    <p class="stock">Stock: <span class="stock-qty"><?php echo $row['quantidade_stock']; ?></span></p>
                    <p class="price-tag"><?php echo $row['preco']; ?>€</p>
                    
                    <?php if($row['quantidade_stock'] > 0): ?>
                        <input type="number" name="qty[<?php echo $row['id']; ?>]" 
                               class="qty-input" min="0" 
                               max="<?php echo $row['quantidade_stock']; ?>" 
                               data-price="<?php echo $row['preco']; ?>" value="0">
                    <?php else: ?>
                        <p class="no-stock">Sem Stock Disponível</p>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- BARRA DE COMPRA FIXA (ESTILO MODERNO) -->
            <div class="cart-summary-bar">
                <div class="total-info">
                    Total da Compra: <span id="display-total">0.00</span>€
                </div>
                <button type="submit" class="btn-checkout">CONCLUIR COMPRA</button>
            </div>
        </form>
    </section>
</main>

<script src="js/loja.js"></script>
<?php include 'includes/footer.php'; ?>