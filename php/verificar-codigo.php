<?php
session_start();

$erro = "";
$info = "";

if (isset($_SESSION['msg_info'])) {
    $info = $_SESSION['msg_info'];
    unset($_SESSION['msg_info']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoDigitado = $_POST['codigo'];

    if (isset($_SESSION['codigo']) && $codigoDigitado == $_SESSION['codigo']) {
        header("Location: redefinir-senha.php");
        exit;
    } else {
        $erro = "Código incorreto! Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .msg-info {
            background: #e0f2fe;
            color: #075985;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        .msg-erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Verificar Código</h1>

        <?php if ($info): ?>
            <div class="msg-info"><?= $info ?></div>
        <?php endif; ?>

        <form method="POST">
            <label class="label-form" for="codigo">Digite o código recebido:</label>
            <input class="input-form" type="text" id="codigo" name="codigo" required>
            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Verificar</button>
            </div>
        </form>

        <?php if ($erro): ?>
            <div class="msg-erro"><?= $erro ?></div>
        <?php endif; ?>

        <a id="texto-cadastro" href="recuperar-senha.php">Voltar</a>
    </div>
</body>
</html>
