<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Usuário não está logado.");
}

// Busca dados do usuário (incluindo foto)
$sql = "SELECT email_usuario, foto_perfil FROM usuarios WHERE id_usuario = ?";
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

$temFoto = !empty($usuario['foto_perfil']);
$foto = $temFoto ? "uploads/" . $usuario['foto_perfil'] : "";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil - LinkUp</title>
<link rel="stylesheet" href="../css/perfil.css">
<style>
    .perfil-container {
        max-width: 400px;
        margin: 50px auto;
        text-align: center;
        font-family: Arial, sans-serif;
    }

    .foto-perfil-container {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .foto-perfil-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 3px solid #007bff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
        background-color: #f0f0f0; /* cor de fundo quando não há foto */
    }

    .foto-perfil-circle:hover {
        opacity: 0.8;
    }

    .foto-perfil-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .foto-text {
        text-align: center;
        color: #007bff;
        font-weight: bold;
        padding: 10px;
    }

    .perfil-info {
        margin-top: 20px;
        text-align: left;
    }

    .perfil-info span {
        font-weight: bold;
    }

    .acoes {
        margin-top: 20px;
    }

    .acoes a {
        text-decoration: none;
        color: white;
        background-color: #007bff;
        padding: 10px 20px;
        border-radius: 5px;
        transition: 0.3s;
    }

    .acoes a:hover {
        background-color: #0056b3;
    }
</style>
</head>
<body>

<div class="perfil-container">
    <h2>Meu Perfil</h2>

    <div class="foto-perfil-container">
        <form action="upload-foto.php" method="POST" enctype="multipart/form-data" id="form-foto">
            <div class="foto-perfil-circle" id="foto-perfil-circle">
                <?php if ($temFoto): ?>
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" id="foto-perfil">
                <?php else: ?>
                    <span class="foto-text">Foto de Perfil</span>
                <?php endif; ?>
            </div>
            <input type="file" name="foto_perfil" id="input-foto" accept="image/*" style="display:none" onchange="document.getElementById('form-foto').submit()">
        </form>
    </div>

    <div class="perfil-info">
        <p><span>Email:</span> <?php echo htmlspecialchars($usuario['email_usuario']); ?></p>
    </div>

    <div class="acoes">
        <a href="editar-perfil.php">Editar Perfil</a>
    </div>
</div>

<script>
    // Clicar no círculo da foto abre o seletor de arquivos
    document.getElementById('foto-perfil-circle').addEventListener('click', function() {
        document.getElementById('input-foto').click();
    });
</script>

</body>
</html>
