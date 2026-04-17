<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Envio | Livraria Top</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&family=Montserrat:wght@300;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background: #fff; color: #000; text-align: center; padding: 100px 5%; }
        h2 { font-family: 'Playfair Display', serif; font-style: italic; font-size: 3rem; margin-bottom: 50px; }
        form { max-width: 500px; margin: 0 auto; text-align: left; }
        label { display: block; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; margin-top: 30px; }
        input, textarea { width: 100%; padding: 15px; border: 1px solid #000; margin-top: 10px; font-family: inherit; font-size: 1rem; }
        .btn-confirm { width: 100%; background: #000; color: #fff; border: none; padding: 20px; margin-top: 50px; text-transform: uppercase; letter-spacing: 4px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-confirm:hover { background: #c5a059; }
    </style>
</head>
<body>
    <h2>Detalhes da Entrega</h2>
    <form action="processar.php" method="POST">
        <label>Nome Completo do Destinatário</label>
        <input type="text" name="nome" required>

        <label>Data de Nascimento (Mínimo 18 anos)</label>
        <input type="date" name="data_nasc" required>

        <label>Morada de Envio Completa</label>
        <textarea name="morada" rows="4" required></textarea>

        <button type="submit" class="btn-confirm">Confirmar e Pagar</button>
    </form>
</body>
</html>