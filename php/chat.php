<?php
session_start();
include __DIR__ . '/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Lista amigos
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

// Mensagens com amigo selecionado
$chat_id = $_GET['chat'] ?? null;
$mensagens = [];
if ($chat_id) {
    $sql2 = "SELECT m.*, u.email_usuario
             FROM mensagens m
             JOIN usuarios u ON m.id_remetente = u.id_usuario
             WHERE (m.id_remetente = ? AND m.id_destinatario = ?) 
                OR (m.id_remetente = ? AND m.id_destinatario = ?)
             ORDER BY m.data_envio ASC";
    $stmt2 = $conexao->prepare($sql2);
    $stmt2->bind_param("iiii", $id_usuario, $chat_id, $chat_id, $id_usuario);
    $stmt2->execute();
    $mensagens = $stmt2->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Chat</title>
<link rel="stylesheet" href="../css/chat.css">
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
<bodyb class="body-chat">
<div class="chat-container">

    <div class="amigos-sidebar">
        <h2>Amigos</h2>
        <?php while ($a = $amigos->fetch_assoc()): ?>
            <a href="chat.php?chat=<?php echo $a['id_usuario']; ?>" class="amigo-card">
                <img src="<?php echo $a['foto_perfil'] ?: 'img/default.png'; ?>" alt="">
                <span><?php echo htmlspecialchars($a['email_usuario']); ?></span>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="chat-box">
        <?php if ($chat_id): ?>
            <div class="mensagens">
                <?php while ($m = $mensagens->fetch_assoc()): ?>
                    <div class="mensagem <?php echo $m['id_remetente'] == $id_usuario ? 'enviado' : 'recebido'; ?>">
                        <p><?php echo htmlspecialchars($m['mensagem']); ?></p>
                        <span><?php echo date('d/m H:i', strtotime($m['data_envio'])); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <form method="POST" action="../php/enviar-mensagem.php">
                <input type="hidden" name="id_destinatario" value="<?php echo $chat_id; ?>">
                <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
                <button type="submit" class="btn-enviar">Enviar</button>
            </form>
        <?php else: ?>
            <p>Selecione um amigo para iniciar o chat</p>
        <?php endif; ?>
    </div>

</div>
</body>
</html>
