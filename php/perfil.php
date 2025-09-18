<?php
session_start();

// Inclui conexão
include __DIR__ . '/conexao.php';

// Pegando id do usuário da sessão
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuário não está logado.");
}

// Busca informações do usuário
$sql = "SELECT id_usuario, email_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conexao->close();

// Se usuário não encontrado
if (!$usuario) {
    die("Usuário não encontrado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - LinkUp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container-perfil {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .perfil-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        .perfil-card img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #2a5298;
            margin-bottom: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .perfil-card img:hover {
            transform: scale(1.05);
        }

        .h1-login-cadastro {
            font-size: 22px;
            color: #333;
            margin: 10px 0 5px 0;
        }

        p {
            color: #555;
            margin: 5px 0 20px 0;
        }

        .acoes-perfil {
            display: flex;
            justify-content: space-around;
        }

        .acoes-perfil a {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            transition: 0.3s;
        }

        .button-editar {
            background: #2a5298;
            color: #fff;
        }

        .button-editar:hover {
            background: #1e3c72;
        }

        .button-sair {
            background: #e63946;
            color: #fff;
        }

        .button-sair:hover {
            background: #b71c1c;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
<div class="container-perfil">
    <div class="perfil-card">
        <!-- Exibe só um logo padrão (já que não tem foto_perfil no banco) -->
        <img src="../img/logo-tasksync.png" alt="Foto de perfil" class="foto-perfil-clicavel">

        <!-- Como não existe nome_usuario, vamos mostrar o email -->
        <h1 class="h1-login-cadastro"><?php echo htmlspecialchars($usuario['email_usuario']); ?></h1>
        <p>ID: <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>

        <div class="acoes-perfil">
            <a class="button-editar" href="editar-perfil.php">Editar Perfil</a>
            <a class="button-sair" href="logout.php">Sair</a>
        </div>
    </div>
</div>
</body>
</html>
