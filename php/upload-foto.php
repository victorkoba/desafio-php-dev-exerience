<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) die("Usuário não está logado.");

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $pasta = "uploads/";
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid() . "." . $ext;
    $caminho = $pasta . $nomeArquivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
        $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id_usuario = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("si", $caminho, $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: perfil.php");
exit;
