<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almedina.net | Livraria Online</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- BARRA SUPERIOR -->
    <div class="top-utility-bar">
        <div class="container">
            <div class="left-links">
                <a href="#">Apoio ao Cliente</a>
                <a href="#">Livrarias</a>
                <a href="#">Blog</a>
            </div>
            <div class="right-links">
                <span>Portugal (PT)</span>
                <a href="#"><i class="fa fa-user"></i> Iniciar Sessão</a>
            </div>
        </div>
    </div>

    <!-- HEADER PRINCIPAL -->
    <header class="main-header">
        <div class="container header-flex">
            <a href="index.php" class="logo">
                <img src="img/logo.png" alt="LêTudo" style="height: 45px;"> 
                <!-- Se não tiver logo em imagem, use o texto estilizado -->
            </a>

            <div class="search-container">
                <form action="pesquisa.php">
                    <input type="text" placeholder="Pesquisar por título, autor, tema, ISBN...">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>

            <div class="header-icons">
                <a href="#" class="icon-link">
                    <i class="fa fa-heart"></i>
                    <span class="label">Favoritos</span>
                </a>
                <a href="#" class="icon-link cart-link">
                    <i class="fa fa-shopping-basket"></i>
                    <span class="cart-count">0</span>
                    <span class="label">Carrinho</span>
                </a>
            </div>
        </div>
    </header>

    <!-- MENU DE CATEGORIAS (Mega Menu) -->
    <nav class="mega-menu">
        <div class="container">
            <ul>
                <li><a href="#" class="active">DIREITO</a></li>
                <li><a href="#">LITERATURA</a></li>
                <li><a href="#">EDUCAÇÃO</a></li>
                <li><a href="#">GESTÃO</a></li>
                <li><a href="#">INFANTIL</a></li>
                <li><a href="#">CIÊNCIAS SOCIAIS</a></li>
                <li class="promo"><a href="#">PROMOÇÕES</a></li>
            </ul>
        </div>
    </nav>