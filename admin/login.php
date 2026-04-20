<?php
session_start();
require_once '../config/database.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = limparDados($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipoMensagem = 'erro';
    } else {
        $query = "SELECT * FROM utilizadores WHERE username = '$username' AND tipo = 'admin'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $utilizador = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $utilizador['password'])) {
                $_SESSION['admin_id'] = $utilizador['id'];
                $_SESSION['admin_nome'] = $utilizador['nome'];
                $_SESSION['admin_logado'] = true;
                
                header('Location: index.php');
                exit;
            } else {
                $mensagem = 'Password incorreta.';
                $tipoMensagem = 'erro';
            }
        } else {
            $mensagem = 'Utilizador não encontrado ou não tem privilégios de administrador.';
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Letudo.pt</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Área de Administração</h1>
        
        <?php if ($mensagem): ?>
            <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username">Username de Administrador</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primario">Aceder</button>
                <p class="text-center mt-2"><a href="../index.php">Voltar ao site</a></p>
            </form>
        </div>
    </div>
</body>
</html>