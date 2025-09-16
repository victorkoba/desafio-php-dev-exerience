<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Aqui você validaria no banco
    $codigo = rand(100000, 999999);

    $_SESSION['codigo'] = $codigo;
    $_SESSION['email_recovery'] = $email;

    $_SESSION['msg_info'] = "Seu código de recuperação é <b>$codigo</b>. 
    (Na versão final, isso seria enviado para seu email.)";

    header("Location: verificar-codigo.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Recuperar Senha</h1>
        <form method="POST">
            <label class="label-form" for="email">Digite seu email:</label>
            <input class="input-form" type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Enviar código</button>
            </div>
        </form>
        <a id="texto-cadastro" href="../index.php">Voltar ao login</a>
    </div>
</body>
</html>
