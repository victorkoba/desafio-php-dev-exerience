<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nome = $_POST['nome_usuario'];
    $foto = null;
    $foto_type = null;

    // Processa a imagem de perfil, se enviada
    if (isset($_FILES['foto_usuario']) && $_FILES['foto_usuario']['error'] === 0) {
        $foto = file_get_contents($_FILES['foto_usuario']['tmp_name']);
        $foto_type = $_FILES['foto_usuario']['type']; // Pode salvar tipo MIME se quiser
    }

    // Criptografa a senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o e-mail já está cadastrado
    $sql_verifica = "SELECT id_usuario FROM usuarios WHERE email_usuario = ?";
    $stmt_verifica = $conexao->prepare($sql_verifica);
    $stmt_verifica->bind_param("s", $email);
    $stmt_verifica->execute();
    $stmt_verifica->store_result();

    if ($stmt_verifica->num_rows > 0) {
        echo "<!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'E-mail já cadastrado!'
                }).then(() => window.location.href = 'cadastro.php');
            </script>
        </body>
        </html>";
        exit;
    }

    // Insere o novo usuário
    $sql = "INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario, foto_usuario) VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $foto);
        if ($stmt->execute()) {
            echo "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
                <meta charset='UTF-8'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Cadastro realizado com sucesso!'
                    }).then(() => window.location.href = '../index.php');
                </script>
            </body>
            </html>";
        } else {
            echo "Erro ao cadastrar: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta.";
    }

    $stmt_verifica->close();
    $conexao->close();
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Cadastro - LinkUp</title>
</head>
<body class="body-cadastro-login">
   <div class="container-cadastro-login">
    <form class="form-cadastro-login" action="" method="POST" enctype="multipart/form-data">
    <img class="logo-linkup" src="../img/logo-com-nome.png" alt="Logo LinkUp">
    <h1 class="h1-login-cadastro">Criar uma conta</h1>

    <label class="label-form" for="nome_usuario">Nome:</label>
    <input class="input-form" type="text" id="nome_usuario" name="nome_usuario" placeholder="Digite seu nome" required>

    <label class="label-form" for="email">Email:</label>
    <input class="input-form" type="email" id="email" name="email" placeholder="Digite seu email" required>

    <label class="label-form" for="senha">Senha:</label>
    <input class="input-form" type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

    <label class="label-form" for="foto_usuario">Foto de perfil:</label>
    <input class="input-form" type="file" id="foto_usuario" name="foto_usuario" accept="image/*">

    <div class="alinhamento-button">
        <button class="button-entrar" type="submit">Cadastrar</button>
    </div>
    
    <br>
    <center><a id="texto-cadastro" href="../index.php">Já tem cadastro? Entrar na sua conta</a></center>
</form>

   </div>
</body>
</html>
