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
    <title>Perfil - LinkUp</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
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
            border-radius: 20px;
            padding: 40px 30px;
            width: 380px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            animation: fadeIn 0.6s ease-in-out;
        }

        /* Foto de perfil */
        .foto-perfil-clicavel {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #2a5298;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .foto-perfil-clicavel:hover {
            transform: scale(1.05);
            box-shadow: 0 0 12px rgba(42,82,152,0.6);
        }

        /* Esconde input file */
        #foto { display: none; }

        .foto-label {
            display: inline-block;
            cursor: pointer;
            position: relative;
            margin-bottom: 20px;
        }

        .foto-label::after {
            content: "📷";
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: #2a5298;
            color: #fff;
            font-size: 16px;
            padding: 6px;
            border-radius: 50%;
            border: 2px solid #fff;
            transition: background 0.3s;
        }

        .foto-label:hover::after { background: #1e3c72; }

        /* Nome/ID */
        .h1-login-cadastro {
            font-size: 22px;
            font-weight: 600;
            color: #2a2a2a;
            margin: 10px 0 5px 0;
        }

        .perfil-card p {
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        /* Botões */
        .acoes-perfil {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .acoes-perfil a {
            flex: 1;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .button-editar { background: #2a5298; }
        .button-editar:hover { background: #1e3c72; transform: translateY(-2px); }

        .button-sair { background: #e63946; }
        .button-sair:hover { background: #b71c1c; transform: translateY(-2px); }

        /* Animação suave */
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
    <script>
        // Preview instantâneo da foto antes do envio
        function previewFoto(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('preview-img').src = URL.createObjectURL(file);
                event.target.form.submit(); // já envia após escolher
            }
        }
    </script>
</head>
<body>
<div class="container-perfil">
    <div class="perfil-card">
        <!-- Foto de perfil -->
        <form action="upload-foto.php" method="post" enctype="multipart/form-data">
            <label for="foto" class="foto-label">
                <img id="preview-img" src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil" class="foto-perfil-clicavel">
            </label>
            <input type="file" id="foto" name="foto" accept="image/*" onchange="previewFoto(event)">
        </form>

        <h1 class="h1-login-cadastro"><?php echo htmlspecialchars($usuario['email_usuario']); ?></h1>
        <p>ID: <?php echo htmlspecialchars($usuario['id_usuario']); ?></p>

        <div class="acoes-perfil">
            <a class="button-editar" href="editar-perfil.php">Editar Perfil</a>
            <a class="button-sair" href="feed.php">Sair</a>
        </div>
    </div>
</div>
</body>
</html>
