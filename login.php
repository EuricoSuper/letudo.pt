<?php
$pageTitle = "Login";
require_once 'includes/header.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = limparDados($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $mensagem = 'Por favor, preencha todos os campos.';
        $tipoMensagem = 'erro';
    } else {
        $query = "SELECT * FROM utilizadores WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) === 1) {
            $utilizador = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $utilizador['password'])) {
                $_SESSION['utilizador_id'] = $utilizador['id'];
                $_SESSION['utilizador_nome'] = $utilizador['nome'];
                $_SESSION['utilizador_tipo'] = $utilizador['tipo'];
                
                header('Location: index.php');
                exit;
            } else {
                $mensagem = 'Password incorreta.';
                $tipoMensagem = 'erro';
            }
        } else {
            $mensagem = 'Utilizador não encontrado.';
            $tipoMensagem = 'erro';
        }
    }
}
?>

<div class="container">
    <h1 class="text-center">Login</h1>
    
    <?php if ($mensagem): ?>
        <div class="mensagem mensagem-<?php echo $tipoMensagem; ?>">
            <?php echo $mensagem; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primario">Entrar</button>
            <p class="text-center mt-2">Ainda não tem conta? <a href="registro.php">Registe-se</a></p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>