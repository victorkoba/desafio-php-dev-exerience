<?php
session_start();
include __DIR__ . '/conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_amigo = $_POST['id_amigo'] ?? null;

if ($id_amigo) {
    $sql = "INSERT INTO amigos (id_usuario, id_amigo, status) VALUES (?, ?, 'pendente')";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $id_usuario, $id_amigo);
    $stmt->execute();
}

header("Location: amigos.php");
exit;
