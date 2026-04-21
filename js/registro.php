<?php
$pageTitle = "Registo";
require_once 'includes/header.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = limparDados($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $nome = limparDados($conn, $_POST['nome']);
    $email = limparDados($conn, $_POST['email']);
    
    if (empty($username) || empty($password) || empty($nome) || empty($email)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipoMensagem = 'erro';
    } elseif ($password !== $confirmPassword) {
        $mensagem = 'As passwords não coincidem.';
        $tipoMensagem = 'erro';
    } elseif (strlen($password) < 6) {
        $mensagem = 'A password deve ter pelo menos 6 caracteres.';
        $tipoMensagem = 'erro';
    } else {
        // Verificar se username ou email já existem
        $checkQuery = "SELECT id FROM utilizadores WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $checkQuery);
        
        if (mysqli_num_rows($result) > 0) {
            $mensagem = 'Username ou email já existem. Escolha outro.';
            $tipoMensagem = 'erro';
        } else {
            // Hash da password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $insertQuery = "INSERT INTO utilizadores (username, password, nome, email, tipo) 
                           VALUES ('$username', '$hashedPassword', '$nome', '$email', 'cliente')";
            
            if (mysqli_query($conn, $insertQuery)) {
                $mensagem = 'Registo realizado com sucesso! Pode agora fazer login.';
                $tipoMensagem = 'sucesso';
            } else {
                $mensagem = 'Erro ao registar. Tente novamente.';
                $tipoMensagem = 'erro';
            }
        }
    }
}
?>

<div class="container">
    <h1 class="text-center">Criar Conta</h1>
    
    <?php if ($mensagem): ?>
        <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="registro.php">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="nome">Nome Completo *</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                <small>Mínimo 6 caracteres</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn btn-primario">Registar</button>
            <p class="text-center mt-2">Já tem conta? <a href="login.php">Faça login</a></p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>