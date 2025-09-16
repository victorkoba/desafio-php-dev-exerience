<?php
session_start();

include 'conexao.php';

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['senha'];
    $email = $_SESSION['email_recovery'] ?? "";

    if (!empty($email)) {
        // Criptografa a senha
        $senha_hash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualiza no banco
        $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $senha_hash, $email);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success = "Senha redefinida com sucesso para <b>$email</b>!";
            session_destroy();
        } else {
            $error = "Não foi possível atualizar a senha. Verifique se o email existe.";
        }

        $stmt->close();
    } else {
        $error = "Sessão expirada. Refaça o processo de recuperação.";
    }
}

$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .msg-sucesso {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        .msg-erro {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Redefinir Senha</h1>

        <?php if ($success): ?>
            <div class="msg-sucesso"><?= $success ?></div>
            <a class="button-entrar" href="../index.php">Voltar ao login</a>
        <?php elseif ($error): ?>
            <div class="msg-erro"><?= $error ?></div>
            <a class="button-entrar" href="../recuperar-senha.php">Tentar novamente</a>
        <?php else: ?>
            <form method="POST">
                <label class="label-form" for="senha">Nova senha:</label>
                <input class="input-form" type="password" id="senha" name="senha" required>
                <div class="alinhamento-button">
                    <button class="button-entrar" type="submit">Salvar nova senha</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
