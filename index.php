<?php include 'includes/db.php'; include 'includes/header.php'; ?>

<!-- BANNER PRINCIPAL (SLIDER) -->
<section class="home-slider">
    <div class="container">
        <div class="slide-item" style="background-image: url('img/banner-promo.jpg'); background-color: #002d58; height: 350px; border-radius: 8px; color: white; display: flex; align-items: center; padding: 40px;">
            <div>
                <h1 style="font-size: 40px;">Novidades Jurídicas</h1>
                <p>As melhores obras com 10% de desconto imediato.</p>
                <a href="#" class="btn-primary">Saber mais</a>
            </div>
        </div>
    </div>
</section>

<!-- ÍCONES DE SERVIÇO (Igual ao site Almedina) -->
<section class="services-bar">
    <div class="container service-flex">
        <div class="service-item">
            <i class="fa fa-truck"></i>
            <div><strong>Portes Grátis</strong><span>Em compras > 15€</span></div>
        </div>
        <div class="service-item">
            <i class="fa fa-clock"></i>
            <div><strong>Entregas 24h</strong><span>Em artigos em stock</span></div>
        </div>
        <div class="service-item">
            <i class="fa fa-exchange-alt"></i>
            <div><strong>Devoluções</strong><span>Até 30 dias</span></div>
        </div>
        <div class="service-item">
            <i class="fa fa-lock"></i>
            <div><strong>Pagamento Seguro</strong><span>MB, Visa, PayPal</span></div>
        </div>
    </div>
</section>

<main class="container">
    <div class="section-header">
        <h2>Destaques Almedina</h2>
        <a href="#">Ver todos ></a>
    </div>

    <div class="book-grid">
        <?php
        $query = "SELECT * FROM livros LIMIT 5";
        $res = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($res)): ?>
            <div class="book-card">
                <div class="badge-promo">-10%</div>
                <img src="img/<?php echo $row['imagem']; ?>" alt="Capa">
                <p class="author"><?php echo $row['autor']; ?></p>
                <h3 class="title"><?php echo $row['titulo']; ?></h3>
                <div class="price-box">
                    <span class="old-price"><?php echo number_format($row['preco']*1.1, 2); ?>€</span>
                    <span class="current-price"><?php echo number_format($row['preco'], 2); ?>€</span>
                </div>
                <button class="add-to-cart">COMPRAR</button>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>