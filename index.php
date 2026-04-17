<?php 
if (!file_exists('db.php')) { die("Erro: db.php não encontrado."); }
include('db.php'); 
try {
    $produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) { $produtos = []; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LETUDO | letudo.pt</title>
    
    <!-- Fonts e Slider CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <style>
        :root { --black: #000; --white: #fff; --grey: #f8f8f8; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background: var(--white); color: var(--black); overflow-x: hidden; }

        /* HEADER */
        header { padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; }
        header h1 { font-family: 'Playfair Display', serif; font-size: 1.5rem; letter-spacing: 2px; text-transform: uppercase; }
        nav a { text-decoration: none; color: #000; font-size: 10px; font-weight: 600; letter-spacing: 2px; }

        /* SLIDER (ESTILO TASCHEN) */
        .swiper { width: 100%; height: 75vh; background: #eee; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
        
        /* BOTÕES DO SLIDER */
        .swiper-button-next, .swiper-button-prev { color: #000; }
        .swiper-pagination-bullet-active { background: #000; }

        /* GRID DE PRODUTOS */
        .container { width: 90%; max-width: 1400px; margin: 80px auto; }
        .grid { display: grid; grid-template-columns: 1fr; gap: 50px; }
        @media (min-width: 768px) { .grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { .grid { grid-template-columns: repeat(4, 1fr); } }

        .card { transition: 0.4s; text-align: center; }
        .card-img-box { width: 100%; height: 350px; background: #f9f9f9; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; }
        .card-img-box img { max-width: 80%; max-height: 90%; box-shadow: 5px 5px 15px rgba(0,0,0,0.1); }
        
        .card h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; margin-bottom: 10px; }
        .price { font-weight: 600; display: block; margin-bottom: 15px; }
        .qty { width: 50px; padding: 8px; border: 1px solid #000; text-align: center; }

        /* CHECKOUT BAR */
        .checkout-bar { position: fixed; bottom: 0; width: 100%; background: #000; color: #fff; padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; z-index: 2000; }
        .btn-buy { background: #fff; color: #000; border: none; padding: 15px 35px; text-transform: uppercase; font-weight: 600; cursor: pointer; letter-spacing: 2px; }
        .btn-buy:disabled { background: #444; color: #888; }

        footer { padding: 50px 0 120px; text-align: center; font-size: 10px; letter-spacing: 3px; color: #ccc; }
    </style>
</head>
<body>

    <header>
        <h1>letudo.pt</h1>
        <nav><a href="pages/admin.php">ADMINISTRAÇÃO</a></nav>
    </header>

    <!-- SLIDER DE IMAGENS (PÁGINA 3/4 DO PDF: MOBILE FIRST & RESPONSIVO) -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="img/banner1.jpg" alt="letudo.pt Hero"></div>
            <div class="swiper-slide"><img src="img/banner2.jpg" alt="Conservação Histórica"></div>
            <div class="swiper-slide"><img src="img/banner3.jpg" alt="Ler faz bem"></div>
            <div class="swiper-slide"><img src="img/banner4.jpg" alt="Feito à medida"></div>
            <div class="swiper-slide"><img src="img/banner5.jpg" alt="Contactos"></div>
            <div class="swiper-slide"><img src="img/banner6.jpg" alt="Feira do Livro"></div>
        </div>
        <!-- Navegação -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>

    <main class="container">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; font-style: italic; margin-bottom: 50px; text-align: center;">Destaques da Curadoria</h2>
        
        <form action="pages/checkout.php" method="POST">
            <div class="grid">
                <?php foreach($produtos as $p): ?>
                <div class="card" data-stock="<?= $p['quantidade_disponivel'] ?>">
                    <div class="card-img-box">
                        <img src="img/<?= $p['imagem'] ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                    </div>
                    <h3><?= htmlspecialchars($p['nome']) ?></h3>
                    <span class="price"><?= number_format($p['preco_unidade'], 2) ?>€</span>
                    
                    <?php if($p['quantidade_disponivel'] > 0): ?>
                        <input type="number" name="qtd[<?= $p['id'] ?>]" class="qty" value="0" min="0" max="<?= $p['quantidade_disponivel'] ?>" oninput="calc()">
                    <?php else: ?>
                        <p style="color:red; font-size:10px;">INDISPONÍVEL</p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="checkout-bar">
                <div>
                    <p style="font-size: 9px; letter-spacing: 2px;">TOTAL DA SELEÇÃO</p>
                    <h4 style="font-family: 'Playfair Display', serif; font-size: 1.8rem;"><span id="total-txt">0.00</span>€</h4>
                </div>
                <button type="submit" class="btn-buy" id="btn-final" disabled>Finalizar Compra</button>
            </div>
        </form>
    </main>

    <footer>&copy; 2023 letudo.pt | Livraria Online</footer>

    <!-- Scripts do Slider e Lógica -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Inicializar o Slider (Efeito Fade Suave estilo Taschen)
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            pagination: { el: ".swiper-pagination", clickable: true },
            navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            effect: "fade", // Transição suave de luxo
        });

        // Lógica de cálculo e validação de stock (Página 5 do PDF)
        function calc() {
            let total = 0;
            let hasItems = false;
            document.querySelectorAll('.card').forEach(card => {
                let p = parseFloat(card.querySelector('.price').innerText);
                let qInput = card.querySelector('.qty');
                if (qInput) {
                    let q = parseInt(qInput.value) || 0;
                    let stock = parseInt(card.dataset.stock);
                    
                    if (q > stock) {
                        alert("Aviso: Quantidade excede o stock!");
                        qInput.value = stock;
                        q = stock;
                    }
                    total += p * q;
                    if (q > 0) hasItems = true;
                }
            });
            document.getElementById('total-txt').innerText = total.toFixed(2);
            document.getElementById('btn-final').disabled = !hasItems;
        }
    </script>
</body>
</html>