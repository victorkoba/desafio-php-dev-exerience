<?php
session_start();

// Inclui conexão
include __DIR__ . 'conexao.php';



$id_usuario = ['id_usuario'];

// Busca informações do usuário
$sql = "SELECT id_usuario, nome_usuario, email_usuario, foto_perfil FROM usuarios WHERE id_usuario = ?";
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
        <form action="alterar-foto.php" method="POST" enctype="multipart/form-data" id="form-foto">
            <label for="foto-perfil">
                <img src="<?php echo $usuario['foto_perfil'] ? $usuario['foto_perfil'] : '../img/logo-tasksync.png'; ?>" 
                     alt="Foto de perfil" class="foto-perfil-clicavel">
            </label>
            <input type="file" name="foto_perfil" id="foto-perfil" accept="image/*" style="display:none;" onchange="document.getElementById('form-foto').submit()">
        </form>

        <h1 class="h1-login-cadastro"><?php echo htmlspecialchars($usuario['nome_usuario']); ?></h1>
        <p><?php echo htmlspecialchars($usuario['email_usuario']); ?></p>

        <div class="acoes-perfil">
            <a class="button-editar" href="editar-perfil.php">Editar Perfil</a>
            <a class="button-sair" href="logout.php">Sair</a>
        </div>
    </div>
</div>
</body>
</html>
