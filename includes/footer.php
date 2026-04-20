</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-col footer-brand-col">
            <a href="<?= $base ?? '' ?>index.php" class="brand brand-footer">
                <span class="brand-mark">L</span>
                <span class="brand-text">letudo<span class="brand-dot">.pt</span></span>
            </a>
            <p class="footer-tagline">A sua livraria online. Descobre, le, partilha.</p>
            <div class="footer-newsletter">
                <label for="news">Subscreve a newsletter</label>
                <form onsubmit="event.preventDefault(); this.querySelector('.ok').hidden=false; this.reset();">
                    <input id="news" type="email" placeholder="O teu email" required>
                    <button type="submit">Subscrever</button>
                    <span class="ok" hidden>Obrigado!</span>
                </form>
            </div>
        </div>
        <div class="footer-col">
            <h4>Loja</h4>
            <a href="<?= $base ?? '' ?>index.php">Todos os livros</a>
            <a href="<?= $base ?? '' ?>index.php?destaque=1">Destaques</a>
            <a href="<?= $base ?? '' ?>index.php?cat=Ficcao">Ficcao</a>
            <a href="<?= $base ?? '' ?>index.php?cat=Infantil">Infantil</a>
            <a href="<?= $base ?? '' ?>index.php?cat=Biografia">Biografia</a>
        </div>
        <div class="footer-col">
            <h4>A Letudo</h4>
            <a href="<?= $base ?? '' ?>index.php?pagina=sobre">Quem somos</a>
            <a href="<?= $base ?? '' ?>index.php?pagina=contactos">Contactos</a>
            <a href="<?= $base ?? '' ?>index.php?pagina=ajuda">Ajuda &amp; FAQ</a>
            <a href="<?= $base ?? '' ?>index.php?pagina=termos">Termos e condicoes</a>
            <a href="<?= $base ?? '' ?>index.php?pagina=privacidade">Politica de privacidade</a>
        </div>
        <div class="footer-col">
            <h4>Contactos</h4>
            <p>Rua da Leitura, 12<br>1000-001 Lisboa</p>
            <p><a href="mailto:ola@letudo.pt">ola@letudo.pt</a></p>
            <p>+351 210 000 000</p>
            <div class="footer-social">
                <a href="#" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.51 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12Z"/></svg></a>
                <a href="#" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4Z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                <a href="#" aria-label="YouTube"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.3 3.6Z"/></svg></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Letudo.pt &middot; Todos os direitos reservados &middot; Feito com cafe em Portugal</p>
        </div>
    </div>
</footer>
<script src="<?= $base ?? '' ?>js/scripts.js"></script>
</body>
</html>
