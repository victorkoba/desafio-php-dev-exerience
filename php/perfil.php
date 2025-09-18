<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Usuário não está logado.");
}

// Se veio info de upload via GET (após redirect), use para alertas
$alerta = $_GET['upload'] ?? null;

// BUSCA O NOME DO ARQUIVO ATUAL (para poder apagar depois do upload)
$stmt = $conexao->prepare("SELECT foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario_atual = $result->fetch_assoc();
$stmt->close();

if (!$usuario_atual) {
    die("Usuário não encontrado.");
}

// PROCESSAR UPLOAD (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','gif'];

        if (!in_array($extensao, $permitidas)) {
            // redireciona com aviso
            header("Location: perfil.php?upload=tipo_invalido");
            exit;
        }

        // garante que a pasta uploads exista
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $novo_nome = uniqid('perfil_', true) . '.' . $extensao;
        $caminho_destino = $upload_dir . $novo_nome;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_destino)) {
            // atualiza no banco
            $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("si", $novo_nome, $id_usuario);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok) {
                // apaga o arquivo antigo (se existir e estiver na pasta uploads)
                if (!empty($usuario_atual['foto_perfil'])) {
                    $arquivo_antigo = $upload_dir . $usuario_atual['foto_perfil'];
                    if (file_exists($arquivo_antigo) && is_file($arquivo_antigo)) {
                        @unlink($arquivo_antigo);
                    }
                }
                // redirect para evitar reenvio do formulário e mostrar alerta
                header("Location: perfil.php?upload=sucesso");
                exit;
            } else {
                // falha ao atualizar o DB
                // remove o arquivo recém movido (opcional)
                if (file_exists($caminho_destino)) {
                    @unlink($caminho_destino);
                }
                header("Location: perfil.php?upload=erro_upload");
                exit;
            }
        } else {
            header("Location: perfil.php?upload=erro_upload");
            exit;
        }
    } else {
        header("Location: perfil.php?upload=erro_upload");
        exit;
    }
}

// BUSCAR DADOS ATUALIZADOS DO USUÁRIO PARA EXIBIÇÃO
$stmt = $conexao->prepare("SELECT id_usuario, email_usuario, foto_perfil FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();
$conexao->close();

if (!$usuario) {
    die("Usuário não encontrado.");
}

// caminho da foto (se não tiver, usa imagem padrão)
$temFoto = !empty($usuario['foto_perfil']);
$foto = $temFoto ? "uploads/" . $usuario['foto_perfil'] : "../img/logo-tasksync.png";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil - LinkUp</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../css/style.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
/* aqui vai seu css (mantive o estilo que você já tinha) */
header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(90deg, #0056b3, #007bff);
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    z-index: 1000;
}
#header-div { max-width: 1200px; margin:0 auto; padding:10px 20px; display:flex; align-items:center; justify-content:space-between;}
#img-logo-header { height:60px; }
#header-div ul { list-style:none; display:flex; gap:30px; margin:0; padding:0; }
#header-div ul a { text-decoration:none; color:white; font-weight:500; font-size:16px; padding:8px 12px; border-radius:6px;}
#header-div ul a:hover { background: rgba(255,255,255,0.2); }
.logout { font-size:20px; color:white; cursor:pointer; }
.logout:hover { color:#ffc107; }

.perfil-container { max-width:800px; margin:350px auto 50px auto; padding:20px; background:#fff; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.1); text-align:center; font-family:'Segoe UI', sans-serif; }
.foto-perfil-container { display:flex; justify-content:center; margin-bottom:20px; }
.foto-perfil-circle { width:150px; height:150px; border-radius:50%; border:3px solid #007bff; overflow:hidden; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:0.3s; background:#f0f0f0; }
.foto-perfil-circle:hover { opacity:0.8; }
.foto-perfil-circle img { width:100%; height:100%; object-fit:cover; }
.foto-text { color:#007bff; font-weight:bold; text-align:center; }
.perfil-info { text-align:left; font-size:16px; color:#333; }
.perfil-info span { font-weight:bold; }
.acoes { margin-top:20px; }
.acoes a { text-decoration:none; color:white; background-color:#007bff; padding:10px 20px; border-radius:8px; transition:0.3s; }
.acoes a:hover { background-color:#0056b3; }
@media (max-width:600px){ .perfil-container{ margin:140px 10px 50px 10px; padding:15px; } #header-div ul{ flex-direction:column; gap:10px; } }

.foto-perfil { width:100px; height:100px; border-radius:50%; object-fit:cover; display:block; margin: 0 auto 10px; }
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
        <!-- Formulário POST para este mesmo arquivo -->
        <form method="POST" enctype="multipart/form-data" id="form-foto">
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

// Mostra alert conforme upload status vindo por GET (ex: ?upload=sucesso)
<?php if ($alerta === "sucesso"): ?>
Swal.fire({ icon: 'success', title: 'Foto atualizada!', timer: 1600, showConfirmButton: false });
<?php elseif ($alerta === "erro_upload"): ?>
Swal.fire({ icon: 'error', title: 'Erro no upload!', text: 'Tente novamente.' });
<?php elseif ($alerta === "tipo_invalido"): ?>
Swal.fire({ icon: 'warning', title: 'Formato inválido!', text: 'Use JPG, JPEG, PNG ou GIF.' });
<?php endif; ?>
</script>

</body>
</html>
