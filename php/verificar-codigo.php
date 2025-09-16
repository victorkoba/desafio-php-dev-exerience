<?php
session_start();

if (!isset($_SESSION['codigo'])) {
    header("Location: recuperar-senha.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_usuario = $_POST['codigo'];

    if ($codigo_usuario == $_SESSION['codigo']) {
        $_SESSION['codigo_validado'] = true;
        header("Location: redefinir-senha.php");
        exit;
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Código incorreto!',
                confirmButtonText: 'Tentar novamente'
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Verificar código</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Digite o código enviado</h1>
        <form method="POST">
            <label class="label-form" for="codigo">Código:</label>
            <input class="input-form" type="text" id="codigo" name="codigo" placeholder="000000" required>
            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Verificar</button>
            </div>
        </form>
    </div>
</body>
</html>
