<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se email já existe
    $sql_verifica = "SELECT id FROM usuarios WHERE email = ?";
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
                }).then(() => window.location.href = 'cadastro-usuario.php');
            </script>
        </body>
        </html>";
        exit;
    }

    // Insere usuário
    $sql = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
    $stmt = $conexao->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ss", $email, $senha_hash);
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
    <title>Cadastro</title>
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <img class="logo-linkup" src="../img/logo-com-nome.png" alt="Logo LinkUp">
        <h1 class="h1-login-cadastro">Criar uma conta</h1>
        <form action="" method="POST">
            <label class="label-form" for="email">Email:</label>
            <input class="input-form" type="email" id="email" name="email" placeholder="Digite seu email" required>

            <label class="label-form" for="senha">Senha:</label>
            <input class="input-form" type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Cadastrar</button>
            </div>
        </form>
        <a id="texto-cadastro" href="../index.php">Já tem cadastro? Entrar na sua conta</a>
    </div>
</body>
</html>
