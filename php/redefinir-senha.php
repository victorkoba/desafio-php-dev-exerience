<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['codigo_validado']) || !$_SESSION['codigo_validado']) {
    header("Location: recuperar-senha.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['senha'];
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $email = $_SESSION['email_recovery'];

    $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ss", $senha_hash, $email);

    if ($stmt->execute()) {
        session_unset();
        session_destroy();
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Senha redefinida!',
                confirmButtonText: 'Voltar ao login'
            }).then(() => window.location.href = '../index.php');
        </script>";
        exit;
    } else {
        echo "Erro ao atualizar senha: " . $stmt->error;
    }
    $stmt->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir senha</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Crie uma nova senha</h1>
        <form method="POST">
            <label class="label-form" for="senha">Nova senha:</label>
            <input class="input-form" type="password" id="senha" name="senha" placeholder="Digite sua nova senha" required>
            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Redefinir senha</button>
            </div>
        </form>
    </div>
</body>
</html>
