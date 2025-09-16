<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Função para mostrar alert e redirecionar
function alerta($tipo, $titulo, $msg, $redirecionar) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function(){
            Swal.fire({
                icon: '$tipo',
                title: '$titulo',
                text: '$msg'
            }).then(function(){ window.location.href='$redirecionar'; });
        });
    </script>";
}

// Criar post
if (isset($_POST['criar_post'])) {
    $texto = $_POST['texto_post'];
    $imagem_nome = null;

    // Verificar se enviou imagem
if(isset($_FILES['imagem_post']) && $_FILES['imagem_post']['error'] == 0){
    $ext = pathinfo($_FILES['imagem_post']['name'], PATHINFO_EXTENSION);
    $imagem_nome = uniqid() . "." . $ext;

    // Criar pasta caso não exista
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    move_uploaded_file($_FILES['imagem_post']['tmp_name'], "uploads/" . $imagem_nome);
}

    $stmt = $conexao->prepare("INSERT INTO posts (id_usuario, texto_post, imagem_post) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['id_usuario'], $texto, $imagem_nome);
    if($stmt->execute()){
        alerta('success', 'Post criado!', 'Seu post foi publicado.', 'feed.php');
    }
    $stmt->close();
    exit;
}

// Editar post
if (isset($_POST['editar_post'])) {
    $id_post = $_POST['id_post'];
    $texto = $_POST['texto_post'];
    $stmt = $conexao->prepare("UPDATE posts SET texto_post = ? WHERE id_post = ? AND id_usuario = ?");
    $stmt->bind_param("sii", $texto, $id_post, $_SESSION['id_usuario']);
    if($stmt->execute()){
        alerta('success', 'Post editado!', 'Seu post foi atualizado.', 'feed.php');
    }
    $stmt->close();
    exit;
}

// Excluir post
if (isset($_GET['excluir'])) {
    $id_post = $_GET['excluir'];
    $stmt = $conexao->prepare("DELETE FROM posts WHERE id_post = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_post, $_SESSION['id_usuario']);
    if($stmt->execute()){
        alerta('success', 'Post excluído!', 'O post foi removido.', 'feed.php');
    }
    $stmt->close();
    exit;
}

// Curtir post
if (isset($_GET['curtir'])) {
    $id_post = $_GET['curtir'];
    $stmt = $conexao->prepare("SELECT * FROM curtidas WHERE id_post = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_post, $_SESSION['id_usuario']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt_del = $conexao->prepare("DELETE FROM curtidas WHERE id_post = ? AND id_usuario = ?");
        $stmt_del->bind_param("ii", $id_post, $_SESSION['id_usuario']);
        $stmt_del->execute();
        $stmt_del->close();
    } else {
        $stmt_add = $conexao->prepare("INSERT INTO curtidas (id_post, id_usuario) VALUES (?, ?)");
        $stmt_add->bind_param("ii", $id_post, $_SESSION['id_usuario']);
        $stmt_add->execute();
        $stmt_add->close();
    }

    $stmt->close();
    header('Location: feed.php');
    exit;
}

// Adicionar comentário
if (isset($_POST['comentar'])) {
    $id_post = $_POST['id_post'];
    $texto = $_POST['comentario'];
    $stmt = $conexao->prepare("INSERT INTO comentarios (id_post, id_usuario, texto_comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_post, $_SESSION['id_usuario'], $texto);
    if($stmt->execute()){
        alerta('success', 'Comentário adicionado!', 'Seu comentário foi publicado.', 'feed.php');
    }
    $stmt->close();
    exit;
}

// Buscar posts
$posts = $conexao->query("
    SELECT p.*, u.email_usuario,
        (SELECT COUNT(*) FROM curtidas c WHERE c.id_post = p.id_post) as total_curtidas
    FROM posts p
    JOIN usuarios u ON p.id_usuario = u.id_usuario
    ORDER BY p.data_post DESC
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Feed - LinkUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body {background:#f0f2f5;padding:20px;}
        .container {max-width:800px;margin:auto;}
        h1 {text-align:center;color:#333;margin-bottom:20px;}
        .logout {float:right;font-size:20px;color:#555;transition:0.3s;}
        .logout:hover {color:#d9534f;}
        .novo-post textarea {width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-bottom:10px;font-size:14px;resize:none;}
        .novo-post input[type=file] {margin-bottom:10px;}
        .novo-post button {background:#007bff;color:white;border:none;padding:10px 15px;border-radius:8px;cursor:pointer;transition:0.3s;}
        .novo-post button:hover {background:#0056b3;}
        .post {background:white;padding:15px;margin-bottom:20px;border-radius:12px;box-shadow:0 4px 8px rgba(0,0,0,0.1);}
        .post-header {display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
        .post-header strong {color:#333;}
        .post-content {margin-bottom:10px;font-size:15px;color:#444;}
        .acoes {display:flex;gap:15px;font-size:14px;margin-bottom:10px;}
        .acoes a {color:#555;text-decoration:none;display:flex;align-items:center;gap:5px;transition:0.3s;}
        .acoes a:hover {color:#007bff;}
        .comentarios {margin-top:10px;padding-left:10px;border-left:2px solid #eee;}
        .comentarios p {margin-bottom:5px;font-size:14px;color:#555;}
        .comentarios form {display:flex;gap:5px;margin-top:5px;}
        .comentarios input[type=text] {flex:1;padding:6px 10px;border-radius:6px;border:1px solid #ccc;font-size:14px;}
        .comentarios button {padding:6px 10px;background:#28a745;border:none;color:white;border-radius:6px;cursor:pointer;transition:0.3s;}
        .comentarios button:hover {background:#218838;}
        .editar {margin-top:10px;display:flex;gap:5px;}
        .editar textarea {flex:1;padding:6px;border-radius:6px;border:1px solid #ccc;font-size:14px;}
        .editar button {background:#ffc107;color:white;border:none;padding:6px 10px;border-radius:6px;cursor:pointer;transition:0.3s;}
        .editar button:hover {background:#e0a800;}
        .excluir {color:#d9534f;text-decoration:none;font-size:14px;transition:0.3s;margin-left:5px;}
        .excluir:hover {text-decoration:underline;}
        @media (max-width:600px){body{padding:10px;}.acoes{flex-direction:column;align-items:flex-start;}.editar{flex-direction:column;}}
    </style>
</head>
<body>
    <div class="container">
        <h1>Feed 
            <a href="../index.php?logout=1" class="logout" onclick="return sairConfirm()"><i class="fas fa-arrow-right-from-bracket"></i></a>
        </h1>

        <!-- Criar post -->
        <div class="novo-post">
            <form method="POST" enctype="multipart/form-data">
                <textarea name="texto_post" placeholder="O que você está pensando?" required></textarea>
                <input type="file" name="imagem_post" accept="image/*">
                <button type="submit" name="criar_post"><i class="fas fa-paper-plane"></i> Postar</button>
            </form>
        </div>

        <!-- Posts -->
        <?php while($post = $posts->fetch_assoc()): ?>
            <div class="post">
                <div class="post-header">
                    <strong><?php echo $post['email_usuario']; ?></strong>
                    <span><?php echo date('d/m/Y H:i', strtotime($post['data_post'])); ?></span>
                </div>
                <div class="post-content">
                    <?php echo nl2br(htmlspecialchars($post['texto_post'])); ?>
                    <?php if($post['imagem_post']): ?>
                        <div style="margin-top:10px;">
                            <img src="uploads/<?php echo $post['imagem_post']; ?>" alt="Imagem do post" style="max-width:100%; border-radius:8px;">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ações -->
                <div class="acoes">
                    <a href="feed.php?curtir=<?php echo $post['id_post']; ?>">
                        <i class="fas fa-heart"></i> Curtir (<?php echo $post['total_curtidas']; ?>)
                    </a>
                    <a href="#comentar-<?php echo $post['id_post']; ?>">
                        <i class="fas fa-comment"></i> Comentar
                    </a>
                </div>

                <!-- Comentários -->
                <div class="comentarios" id="comentar-<?php echo $post['id_post']; ?>">
                    <?php
                    $comentarios = $conexao->query("SELECT c.*, u.email_usuario FROM comentarios c JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.id_post = {$post['id_post']} ORDER BY data_comentario ASC");
                    while($com = $comentarios->fetch_assoc()):
                    ?>
                        <p><strong><?php echo $com['email_usuario']; ?></strong>: <?php echo htmlspecialchars($com['texto_comentario']); ?></p>
                    <?php endwhile; ?>

                    <form method="POST">
                        <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                        <input type="text" name="comentario" placeholder="Escreva um comentário..." required>
                        <button type="submit" name="comentar"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>

                <!-- Editar e excluir -->
                <?php if($post['id_usuario'] == $_SESSION['id_usuario']): ?>
                    <div class="editar">
                        <form method="POST">
                            <input type="hidden" name="id_post" value="<?php echo $post['id_post']; ?>">
                            <textarea name="texto_post"><?php echo htmlspecialchars($post['texto_post']); ?></textarea>
                            <button type="submit" name="editar_post"><i class="fas fa-edit"></i> Editar</button>
                        </form>
                        <a href="feed.php?excluir=<?php echo $post['id_post']; ?>" class="excluir" onclick="return excluirConfirm()">
                            <i class="fas fa-trash"></i> Excluir
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function excluirConfirm() {
    return Swal.fire({
        title: 'Tem certeza?',
        text: "Deseja realmente excluir este post?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        return result.isConfirmed;
    });
}

function sairConfirm() {
    return Swal.fire({
        title: 'Deseja sair?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sair',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href='../index.php?logout=1';
        }
        return false;
    });
}
</script>

</body>
</html>
