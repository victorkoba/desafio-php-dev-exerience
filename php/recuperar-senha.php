<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verifica se o email existe
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $codigo = rand(100000, 999999); // Gera código de 6 dígitos
        $_SESSION['codigo'] = $codigo;
        $_SESSION['email_recovery'] = $email;

        // Envia email com PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.seuprovedor.com'; // ex: smtp.gmail.com
            $mail->SMTPAuth = true;
            $mail->Username = 'seuemail@dominio.com';
            $mail->Password = 'sua_senha_ou_app_password';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('seuemail@dominio.com', 'LinkUp');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de senha';
            $mail->Body    = "Seu código de recuperação de senha é: <b>$codigo</b>";
            $mail->AltBody = "Seu código de recuperação de senha é: $codigo";

            $mail->send();

            $_SESSION['msg_success'] = "Código enviado! Verifique seu email.";
            header("Location: verificar-codigo.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['msg_error'] = "Erro ao enviar email: {$mail->ErrorInfo}";
            header("Location: recuperar-senha.php");
            exit;
        }

    } else {
        $_SESSION['msg_error'] = "Email não encontrado!";
        header("Location: recuperar-senha.php");
        exit;
    }

    $stmt->close();
    $conexao->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar senha</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="body-cadastro-login">
    <div class="container-cadastro-login">
        <h1 class="h1-login-cadastro">Recuperar senha</h1>
        <form method="POST">
            <label class="label-form" for="email">Digite seu email:</label>
            <input class="input-form" type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
            <div class="alinhamento-button">
                <button class="button-entrar" type="submit">Enviar código</button>
            </div>
        </form>
        <a id="texto-cadastro" href="../index.php">Voltar ao login</a>
    </div>

    <script>
    // Mostra alertas se existir mensagem
    <?php if(isset($_SESSION['msg_error'])) { ?>
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: '<?= $_SESSION['msg_error'] ?>'
        });
        <?php unset($_SESSION['msg_error']); ?>
    <?php } ?>
    </script>
</body>
</html>
