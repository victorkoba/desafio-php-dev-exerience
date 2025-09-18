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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<style>
/* Header azul igual feed.php */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(90deg, #0056b3, #007bff);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    z-index: 1000;
}

#header-div {
    max-width: 1200px;
    margin: 0 auto;
    padding: 10px 20px;

    display: flex;
    align-items: center;
    justify-content: space-between;
}

#img-logo-header {
    height: 60px;
}

#header-div ul {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 30px;
    margin: 0;
    padding: 0;
}

#header-div ul a {
    text-decoration: none;
    color: white;
    font-weight: 500;
    font-size: 16px;
    transition: 0.3s ease;
    padding: 8px 12px;
    border-radius: 6px;
}

#header-div ul a:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Logout estilo igual feed.php */
.logout {
    font-size:20px;
    color:white;
    cursor:pointer;
}
.logout:hover { color:#ffc107; }

/* ===== Perfil Container branco ===== */
.perfil-container {
    max-width: 800px;
    margin: 350px auto 50px auto; /* espaço topo para header fixo */
    padding: 20px;
    background: #ffffff; /* fundo branco */
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-align: center;
    font-family: 'Segoe UI', sans-serif;
}

/* Foto de perfil redonda */
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
    transition:0.3s;
    background-color: #f0f0f0;
}
.foto-perfil-circle:hover { opacity: 0.8; }
.foto-perfil-circle img { width: 100%; height: 100%; object-fit: cover; }
.foto-text { color: #007bff; font-weight: bold; text-align: center; }

/* Informações do usuário */
.perfil-info {
    text-align: left;
    font-size: 16px;
    color: #333;
}
.perfil-info span { font-weight: bold; }

/* Botão editar */
.acoes {
    margin-top: 20px;
}
.acoes a {
    text-decoration: none;
    color: white;
    background-color: #007bff;
    padding: 10px 20px;
    border-radius: 8px;
    transition: 0.3s;
}
.acoes a:hover { background-color: #0056b3; }

/* Responsividade */
@media (max-width:600px){
    .perfil-container { margin: 140px 10px 50px 10px; padding: 15px; }
    #header-div ul { flex-direction: column; gap: 10px; }
}
</style>
</head>
<body>

<header>
    <nav>
        <div id="header-div">
            <a href="feed.php"><img id="img-logo-header" src="../img/logo.png" alt="Logo"></a>
            <ul>
                <a href="feed.php"><li>Feed</li></a>
                <a href="perfil.php"><li>Perfil</li></a>
                <a href="chat.php"><li>Chat</li></a>
                <a href="amigos.php"><li>Amigos</li></a>
            </ul>
        </div>
    </nav>
</header>

<div class="perfil-container">
    <h2>Meu Perfil
        <i class="fas fa-arrow-right-from-bracket logout" onclick="sairConfirm()"></i>
    </h2>

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
        <a href="editar-perfil.php"><i class="fas fa-edit"></i> Editar Perfil</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('foto-perfil-circle').addEventListener('click', function() {
    document.getElementById('input-foto').click();
});

function sairConfirm() {
    Swal.fire({
        title: 'Deseja sair?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sair',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href='../index.php?logout=1';
        }
    });
}
</script>

</body>
</html>
