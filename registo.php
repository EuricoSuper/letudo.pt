<form action="processar_registo.php" method="POST" class="form-perfil">
    <div class="mb-3">
        <label>Nome de Usuário</label>
        <input type="text" name="usuario_nome" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Senha</label>
        <input type="password" name="senha" class="form-control" required>
    </div>

    <div class="row">
        <div class="col-md-8">
            <label>Morada</label>
            <input type="text" name="morada" class="form-control">
        </div>
        <div class="col-md-4">
            <label>NIF</label>
            <input type="text" name="nif" class="form-control" maxlength="9">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Criar Conta</button>
</form>