// letudo.pt - scripts.js
document.addEventListener('DOMContentLoaded', () => {
    // Animação de entrada dos cards
    document.querySelectorAll('.product-card').forEach((c, i) => {
        c.style.opacity = '0';
        c.style.transform = 'translateY(12px)';
        c.style.transition = 'opacity .5s ease, transform .5s ease';
        setTimeout(() => { c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 40 * i);
    });

    // Feedback nos formulários add-to-cart
    document.querySelectorAll('form.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', e => {
            const btn = form.querySelector('button[type=submit]');
            if (btn) { btn.disabled = true; btn.textContent = 'A adicionar...'; }
        });
    });

    // Newsletter feedback handled inline
});
