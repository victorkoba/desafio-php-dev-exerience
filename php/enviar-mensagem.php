<?php
session_start();
include __DIR__ . '/conexao.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;
$id_destinatario = $_POST['id_destinatario'] ?? null;
$mensagem = $_POST['mensagem'] ?? '';

if ($id_usuario && $id_destinatario && $mensagem) {
    $sql = "INSERT INTO mensagens (id_remetente, id_destinatario, mensagem) VALUES (?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iis", $id_usuario, $id_destinatario, $mensagem);
    $stmt->execute();
}

header("Location: ../php/chat.php?chat=" . $id_destinatario);
exit;
