<?php
$hostname = 'localhost';
$database   = 'linkup';
$username = 'root';
$password = '';

$conexao = mysqli_connect($hostname, $username, $password, $database);

if (!$conexao) {
    $db_error = "Erro ao conectar no banco de dados.";
    error_log("Erro MySQLi: " . mysqli_connect_error());
}
?>