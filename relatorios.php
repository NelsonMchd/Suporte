<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] != 'administrador') {
    header("Location: login.php");
    exit();
}
include('db.php');

// Para o admin, busca todos os relatórios
$sql = "SELECT r.id_relatorio, r.titulo, r.data_criacao, r.data_atualizacao, u.nome AS tecnico_nome
        FROM relatorios r
        JOIN utilizadores u ON r.tecnico_id = u.id
        ORDER BY r.data_criacao DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatórios - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #121212;
      color: #fff;
      margin: 0;
      padding: 0;
    }
    .header {
      background: rgba(0,0,0,0.8);
      padding: 20px;
      text-align: center;
      margin-bottom: 20px;
    }
    .header h1 {
      font-size: 2.5rem;
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .container {
      max-width: 800px;
      margin: auto;
      background: #1a202c;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #4a5568;
    }
    th, td {
      padding: 10px;
      text-align: center;
    }
    .btn {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      border: none;
      color: #fff;
      padding: 5px 10px;
      transition: transform 0.3s ease;
      font-size: 0.9em;
    }
    .btn:hover {
      transform: scale(1.05);
    }
    a {
      color: #8b5cf6;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Relatórios Criados</h1>
  </div>
  <div class="container">
    <?php if ($result->num_rows == 0): ?>
      <p>Nenhum relatório encontrado.</p>
    <?php else: ?>
      <table class="table table-bordered">
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Técnico</th>
          <th>Data Criação</th>
          <th>Data Atualização</th>
          <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_relatorio']; ?></td>
            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
            <td><?php echo htmlspecialchars($row['tecnico_nome']); ?></td>
            <td><?php echo $row['data_criacao']; ?></td>
            <td><?php echo $row['data_atualizacao']; ?></td>
            <td>
              <!-- Botões para editar ou excluir relatório (opcional) -->
              <a href="editar_relatorio_admin.php?id=<?php echo $row['id_relatorio']; ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil"></i> Editar</a>
              <a href="excluir_relatorio_admin.php?id=<?php echo $row['id_relatorio']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este relatório?');"><i class="bi bi-trash"></i> Excluir</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
    <a href="dashboard_admin.php" class="btn btn-secondary">Voltar ao Dashboard</a>
  </div>
</body>
</html>
