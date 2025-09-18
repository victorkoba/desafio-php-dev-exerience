<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die("Usuário não está logado.");
}

if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {

    $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','gif'];

    if (!in_array($extensao, $permitidas)) {
        die("Tipo de arquivo não permitido.");
    }

    $novo_nome = uniqid() . '.' . $extensao;
    $caminho_destino = __DIR__ . '/uploads/' . $novo_nome;

    if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_destino)) {
        $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $novo_nome, $id_usuario);
        $stmt->execute();
        $stmt->close();
        $conexao->close();
        header("Location: perfil.php");
        exit;
    } else {
        echo "Erro ao mover arquivo.";
    }
} else {
    echo "Nenhum arquivo enviado ou erro no upload.";
}
?>
