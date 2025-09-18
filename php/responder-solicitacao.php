<?php
session_start();
include __DIR__ . '/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_amizade = $_POST['id_amizade'] ?? null;
$acao = $_POST['acao'] ?? null;

if ($id_amizade && $acao) {
    if ($acao === "aceitar") {
        $sql = "UPDATE amigos SET status = 'aceito' WHERE id_amizade = ? AND id_amigo = ?";
    } else {
        $sql = "UPDATE amigos SET status = 'recusado' WHERE id_amizade = ? AND id_amigo = ?";
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_amizade, $id_usuario);
    $stmt->execute();
}

header("Location: amigos.php");
exit;
