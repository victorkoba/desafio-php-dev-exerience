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
    <link rel="stylesheet" href="../css/perfil.css">
    <title>Perfil - LinkUp</title>
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
