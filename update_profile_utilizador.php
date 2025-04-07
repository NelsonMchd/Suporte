<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include('db.php');
$nome   = $_POST['nome']   ?? '';
$status = $_POST['status'] ?? '';
$nivel  = $_POST['nivel']  ?? '';
$user_id = $_SESSION['user_id'];
$sql = "UPDATE utilizadores SET nome = ?, status = ?, nivel = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nome, $status, $nivel, $user_id);
if ($stmt->execute()) {
  echo "<script>alert('Perfil atualizado com sucesso!'); window.location.href='gestao_perfil_tecnico.php';</script>";
} else {
  echo "<script>alert('Erro ao atualizar o perfil.'); window.location.href='gestao_perfil_tecnico.php';</script>";
}
?>