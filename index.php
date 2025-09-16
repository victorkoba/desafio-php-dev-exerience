<?php
session_start();
include './php/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['senha'];

    // Consulta correta com os nomes das colunas do banco
    $sql = "SELECT * FROM usuarios WHERE email_usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifica senha
        if (password_verify($password, $user['senha_usuario'])) {
            $_SESSION['usuario'] = $user['email_usuario']; // pode ser usado como nome
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['tipo'] = $user['tipo_usuario'];

            header('Location: ./php/feed.php'); // redireciona para feed
            exit;
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    Swal.fire({
                        title: 'Senha incorreta!',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then(() => window.location.href = 'index.php');
                });
            </script>";
        }
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                Swal.fire({
                    title: 'Usuário não encontrado!',
                    icon: 'error',
                    confirmButtonText: 'Tente fazer um cadastro'
                }).then(() => window.location.href = './php/cadastro.php');
            });
        </script>";
    }

    $stmt->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<<<<<<< HEAD
<html lang="pt-br">
=======
<html lang="pt-BR">
>>>>>>> 48994870217367e815d79878fd26e472f9a90bc2
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
<<<<<<< HEAD
    <title>Login - LinkUp</title>
</head>
<body class="body-cadastro-login">
   <div class="container-cadastro-login">
    <form class="form-cadastro-login" action="" method="POST">
        <img class="logo-linkup" src="./img/logo-com-nome.png" alt="Logo LinkUp">
        <h1 class="h1-login-cadastro">Entrar na sua conta</h1>
        <label class="label-form" for="email">Email:</label>
        <input class="input-form" type="email" id="email" name="email" required>
        <label class="label-form" for="senha">Senha:</label>
        <input class="input-form" type="password" id="senha" name="senha" required>
        <div class="alinhamento-button">
            <button class="button-entrar" type="submit">Entrar</button>
        </div>
        <a id="texto-cadastro" href="./php/esqueceu-senha.php">Esqueceu a senha?</a>
        <a id="texto-cadastro" href="./php/cadastro.php">Não tem uma conta? Fazer cadastro</a>
    </form>
   </div>
=======
    <title>Login</title>
</head>
<body>
    <div class="container-cadastro-login">
        <img class="logo-linkup" src="./img/logo-com-nome.png" alt="Logo LinkUp">
        <h1 class="h1-login-cadastro">Entrar na sua conta</h1>
        <form action="" method="POST">
            <label class="label-form" for="email">Email:</label>
            <input class="input-form" type="email" id="email" name="email" placeholder="Digite seu email" required>

            <label class="label-form" for="senha">Senha:</label>
            <input class="input-form" type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Entrar</button>
            </div>
        </form>
        <a id="texto-cadastro" href="./php/recuperar-senha.php">Esqueceu a senha?</a>
        <a id="texto-cadastro" href="./php/cadastro.php">Não tem uma conta? Cadastre-se</a>
    </div>
>>>>>>> 48994870217367e815d79878fd26e472f9a90bc2
</body>
</html>
