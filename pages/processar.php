<?php
include('../db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. SEGURANÇA: htmlspecialchars contra Injeção de Scripts (Pág 5)
    $nome = htmlspecialchars($_POST['nome']);
    $morada = htmlspecialchars($_POST['morada']);
    $data_nasc = $_POST['data_nasc'];

    // 2. VALIDAÇÃO: Idade >= 18 anos (Pág 6)
    $idade = (new DateTime())->diff(new DateTime($data_nasc))->y;

    if ($idade < 18) {
        die("<script>alert('Aviso: É necessário ter pelo menos 18 anos para concluir a transação.'); window.history.back();</script>");
    }

    if (empty($nome) || empty($morada)) {
        die("Erro: Por favor, preencha todos os campos obrigatórios.");
    }

    // 3. GRAVAÇÃO: Inserir na tabela Encomendas (Pág 5)
    try {
        $sql = "INSERT INTO encomendas (nome_cliente, data_nascimento, morada) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $data_nasc, $morada]);

        echo "<div style='text-align:center; padding:150px; font-family:serif;'>
                <h1 style='font-size:4rem; font-style:italic;'>Obrigado.</h1>
                <p style='text-transform:uppercase; letter-spacing:5px; color:#888;'>A sua encomenda foi registada com sucesso.</p>
                <br><br>
                <a href='../index.php' style='color:#000; text-decoration:none; border-bottom:1px solid #000; padding-bottom:5px; font-size:12px;'>VOLTAR À GALERIA</a>
              </div>";
    } catch (Exception $e) {
        die("Erro ao processar base de dados: " . $e->getMessage());
    }
}
?>