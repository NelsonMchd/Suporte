<?php
// db.php - Conexão com o banco de dados
$host = "localhost";
$user = "root";       // ajuste se necessário
$password = "";       // ajuste se necessário
$dbname = "basedadosv13fev";  // nome atualizado

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
