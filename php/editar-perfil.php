<?php
session_start();
include __DIR__ . '/conexao.php';

// Verifica se o usuário está logado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Usuário não está logado.");
}

// Se enviou o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $novo_email = $_POST['email_usuario'] ?? '';
    $nova_senha = $_POST['senha_usuario'] ?? '';

    if (!empty($novo_email)) {
        if (!empty($nova_senha)) {
            // Atualiza email e senha
            $hash = password_hash($nova_senha, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET email_usuario = ?, senha_usuario = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssi", $novo_email, $hash, $id_usuario);
        } else {
            // Atualiza só email
            $sql = "UPDATE usuarios SET email_usuario = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $novo_email, $id_usuario);
        }
        if ($stmt->execute()) {
            echo "<script>alert('Perfil atualizado com sucesso!'); window.location.href='perfil.php';</script>";
        } else {
            echo "Erro ao atualizar: " . $conexao->error;
        }
    }
}

// Busca dados atuais do usuário
$sql = "SELECT email_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 320px;
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #2a5298;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #2a5298;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #1e3c72;
        }
        a {
            display: block;
            margin-top: 15px;
            text-align: center;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Perfil</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email_usuario" value="<?php echo htmlspecialchars($usuario['email_usuario']); ?>" required>
            
            <label>Nova senha (opcional):</label>
            <input type="password" name="senha_usuario" placeholder="Digite a nova senha">
            
            <button type="submit">Salvar Alterações</button>
        </form>
        <a href="perfil.php">Voltar</a>
    </div>
</body>
</html>
