<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include('db.php');
// Aqui você implementaria as lógicas de envio e listagem de mensagens
// Exemplo simplificado:
$userId = $_SESSION['user_id'];
// Se o formulário for enviado:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $destinatario = $_POST['destinatario'] ?? '';
  $mensagem = $_POST['mensagem'] ?? '';
  // Inserir na tabela mensagens (supondo que exista)
  $stmt = $conn->prepare("INSERT INTO mensagens (remetente, destinatario, mensagem, data_envio) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("iis", $userId, $destinatario, $mensagem);
  $stmt->execute();
  header("Location: mensagens.php");
  exit();
}
// Buscar mensagens recebidas pelo usuário:
$stmtMsg = $conn->prepare("SELECT m.*, u.nome as remetente_nome FROM mensagens m JOIN utilizadores u ON m.remetente = u.id WHERE m.destinatario = ? ORDER BY m.data_envio DESC");
$stmtMsg->bind_param("i", $userId);
$stmtMsg->execute();
$resultMsg = $stmtMsg->get_result();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Sistema de Mensagens</title>
  <link rel="stylesheet" href="style-admin.css"> <!-- Ou um estilo neutro -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
  <div class="container fade-in">
    <h1>Sistema de Mensagens</h1>
    <form action="mensagens.php" method="POST">
      <label>Enviar mensagem para (ID do destinatário):</label>
      <input type="number" name="destinatario" required>
      <label>Mensagem:</label>
      <textarea name="mensagem" required></textarea>
      <button type="submit" class="btn">Enviar</button>
    </form>
    <hr>
    <h2>Mensagens Recebidas</h2>
    <?php if($resultMsg && $resultMsg->num_rows > 0): ?>
      <table>
        <tr>
          <th>Remetente</th>
          <th>Mensagem</th>
          <th>Data de Envio</th>
        </tr>
        <?php while($msg = $resultMsg->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($msg['remetente_nome']); ?></td>
          <td><?php echo htmlspecialchars($msg['mensagem']); ?></td>
          <td><?php echo $msg['data_envio']; ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>Você não possui mensagens.</p>
    <?php endif; ?>
  </div>
</body>
</html>
