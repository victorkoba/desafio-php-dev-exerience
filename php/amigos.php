<?php
session_start();
include __DIR__ . '/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

/* Buscar amigos aceitos */
$sql = "SELECT u.id_usuario, u.email_usuario, u.foto_perfil 
        FROM amigos a
        JOIN usuarios u ON (u.id_usuario = a.id_amigo OR u.id_usuario = a.id_usuario)
        WHERE (a.id_usuario = ? OR a.id_amigo = ?)
        AND a.status = 'aceito'
        AND u.id_usuario != ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iii", $id_usuario, $id_usuario, $id_usuario);
$stmt->execute();
$amigos = $stmt->get_result();

/* Buscar usuários que ainda não são amigos */
$sql2 = "SELECT id_usuario, email_usuario, foto_perfil
         FROM usuarios 
         WHERE id_usuario != ? 
         AND id_usuario NOT IN (
             SELECT CASE WHEN id_usuario = ? THEN id_amigo ELSE id_usuario END
             FROM amigos
             WHERE (id_usuario = ? OR id_amigo = ?)
         )";
$stmt2 = $conexao->prepare($sql2);
$stmt2->bind_param("iiii", $id_usuario, $id_usuario, $id_usuario, $id_usuario);
$stmt2->execute();
$possiveis = $stmt2->get_result();

/* Buscar solicitações pendentes */
$sql3 = "SELECT a.id_amizade, u.email_usuario, u.foto_perfil
         FROM amigos a
         JOIN usuarios u ON u.id_usuario = a.id_usuario
         WHERE a.id_amigo = ? AND a.status = 'pendente'";
$stmt3 = $conexao->prepare($sql3);
$stmt3->bind_param("i", $id_usuario);
$stmt3->execute();
$solicitacoes = $stmt3->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Amigos</title>
    <link rel="stylesheet" href="../css/amigos.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<header>
    <nav>
        <div id="header-div">
            <a href="feed.php"><img id="img-logo-header" src="../img/logo.png" alt=""></a>
        <ul>
            <a href="feed.php"><li>Feed</li></a>
            <a href="perfil.php"><li>Perfil</li></a>
            <a href="chat.php"><li>Chat</li></a>
            <a href="amigos.php"><li>Amigos</li></a>
        </ul>
        </div>
    </nav>
</header>
<body class="body-amigos">
    <div class="container-amigos">


        <h1 class="titulo-principal">Meus Amigos</h1>
        <?php if ($amigos->num_rows > 0): ?>
            <div class="lista-amigos">
                <?php while ($a = $amigos->fetch_assoc()): ?>
                    <div class="card-amigo">
                        <img src="<?php echo $a['foto_perfil'] ?: 'img/default.png'; ?>" alt="Foto" class="card-amigo-foto">
                        <p class="card-amigo-nome"><?php echo htmlspecialchars($a['email_usuario']); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazio">Você ainda não tem amigos</p>
        <?php endif; ?>

        <h2 class="titulo-secundario">Solicitações de amizade</h2>
        <?php if ($solicitacoes->num_rows > 0): ?>
            <div class="lista-pedidos">
                <?php while ($s = $solicitacoes->fetch_assoc()): ?>
                    <div class="card-pedido">
                        <img src="<?php echo $s['foto_perfil'] ?: 'img/default.png'; ?>" alt="Foto" class="card-pedido-foto">
                        <p class="card-pedido-nome"><?php echo htmlspecialchars($s['email_usuario']); ?></p>
                        <form method="POST" action="../php/responder-solicitacao.php">
                            <input type="hidden" name="id_amizade" value="<?php echo $s['id_amizade']; ?>">
                            <button type="submit" name="acao" value="aceitar" class="btn-aceitar">Aceitar</button>
                            <button type="submit" name="acao" value="recusar" class="btn-recusar">Recusar</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazio">Nenhuma solicitação no momento.</p>
        <?php endif; ?>

        <h2 class="titulo-secundario">Enviar pedido de amizade</h2>
        <?php if ($possiveis->num_rows > 0): ?>
            <div class="lista-pedidos">
                <?php while ($p = $possiveis->fetch_assoc()): ?>
                    <div class="card-pedido">
                        <img src="<?php echo $p['foto_perfil'] ?: 'img/default.png'; ?>" alt="Foto" class="card-pedido-foto">
                        <p class="card-pedido-nome"><?php echo htmlspecialchars($p['email_usuario']); ?></p>
                        <form method="POST" action="../php/enviar-pedido.php">
                            <input type="hidden" name="id_amigo" value="<?php echo $p['id_usuario']; ?>">
                            <button type="submit" class="btn-enviar">Enviar Pedido</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="mensagem-vazio">Nenhum usuário disponível para amizade.</p>
        <?php endif; ?>

    </div>
</body>
</html>
