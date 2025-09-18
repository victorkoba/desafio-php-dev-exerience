<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Usuário não está logado.");
}

// Busca dados do usuário (incluindo foto)
$sql = "SELECT id_usuario, email_usuario, foto_perfil FROM usuarios WHERE id_usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conexao->close();

if (!$usuario) {
    die("Usuário não encontrado.");
}

// Se não tiver foto, mostra padrão
$foto = !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : "../img/logo-tasksync.png";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil - LinkUp</title>
<!-- Conecta o CSS externo -->
<link rel="stylesheet" href="../css/perfil.css">
<script>
    function previewFoto(event) {
        const [file] = event.target.files;
        if(file){
            document.getElementById('preview-img').src = URL.createObjectURL(file);
            event.target.form.submit();
        }
    }
</script>
</head>
<body>
<div class="perfil-container">
    <h2>Meu Perfil</h2>
    
    <form action="upload-foto.php" method="post" enctype="multipart/form-data">
        <label class="foto-label">
            <img id="preview-img" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" class="foto-perfil">
            <input type="file" name="foto" accept="image/*" onchange="previewFoto(event)">
        </label>
    </form>

    <div class="perfil-info">
        <p><span>Email:</span> <?php echo htmlspecialchars($usuario['email_usuario']); ?></p>
        <p><span>ID:</span> <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>
    </div>

    <div class="acoes">
        <a href="editar-perfil.php">Editar Perfil</a>
        <a href="feed.php" class="sair">Voltar</a>
    </div>
</div>
</body>
</html>
