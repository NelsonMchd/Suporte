<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] != 'tecnico') {
    header("Location: login.php");
    exit();
}
include('db.php');

// Determina o tipo: 'registro' ou 'ocorrencia'. Default para registros.
$type = $_GET['type'] ?? 'registro';
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

if ($action == 'aprovar' && !empty($id)) {
    if ($type == 'registro') {
        // Aprova registro: seta status para 'ativo'
        $stmt = $conn->prepare("UPDATE utilizadores SET status = 'ativo' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($type == 'ocorrencia') {
        // Aprova ocorrência: seta estado para 'ACEITO'
        $stmt = $conn->prepare("UPDATE ocorrencias SET estado = 'ACEITO' WHERE id_ocorrencia = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: aprovacoes_tecnico.php?type=$type");
    exit();
} elseif ($action == 'rejeitar' && !empty($id)) {
    if ($type == 'registro') {
        // Rejeita registro: exclui o registro
        $stmt = $conn->prepare("DELETE FROM utilizadores WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($type == 'ocorrencia') {
        // Rejeita ocorrência: seta estado para 'RECUSADO'
        $stmt = $conn->prepare("UPDATE ocorrencias SET estado = 'RECUSADO' WHERE id_ocorrencia = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: aprovacoes_tecnico.php?type=$type");
    exit();
}

// Busca os itens pendentes, conforme o tipo selecionado
if ($type == 'registro') {
    // Registros com status inativo
    $sql = "SELECT id, nome, login FROM utilizadores WHERE status = 'inativo'";
    $result = $conn->query($sql);
} elseif ($type == 'ocorrencia') {
    // Ocorrências pendentes: supondo que ocorrências com estado 'ABERTO' estejam pendentes
    $sql = "SELECT id_ocorrencia AS id, idutil, contato, prob_utilizador FROM ocorrencias WHERE estado = 'ABERTO'";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Aprovações</title>
  <!-- Bootstrap e fontes -->
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
      background: linear-gradient(45deg, #FFA500, #FFFF00);
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
      background: linear-gradient(45deg, #FFA500, #FFFF00);
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
    .tabs {
      text-align: center;
      margin-bottom: 20px;
    }
    .tabs a {
      padding: 10px 20px;
      margin: 0 5px;
      border: 1px solid #4a5568;
      border-radius: 5px;
      background: #1e1e1e;
      color: #fff;
      text-decoration: none;
      transition: background 0.3s;
    }
    .tabs a.active, .tabs a:hover {
      background: linear-gradient(45deg, #FFA500, #FFFF00);
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Aprovações</h1>
  </div>
  <div class="container">
    <div class="tabs">
      <a href="aprovacoes_tecnico.php?type=registro" class="<?php echo ($type=='registro') ? 'active' : ''; ?>">Registros</a>
      <a href="aprovacoes_tecnico.php?type=ocorrencia" class="<?php echo ($type=='ocorrencia') ? 'active' : ''; ?>">Ocorrências</a>
    </div>
    <?php if ($result->num_rows == 0): ?>
      <p>Nenhum item pendente de aprovação.</p>
    <?php else: ?>
      <table class="table table-bordered">
        <tr>
          <th>ID</th>
          <?php if ($type == 'registro'): ?>
            <th>Nome</th>
            <th>Login</th>
          <?php else: ?>
            <th>ID Util</th>
            <th>Contato</th>
            <th>Problema Utilizador</th>
          <?php endif; ?>
          <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <?php if ($type == 'registro'): ?>
              <td><?php echo htmlspecialchars($row['nome']); ?></td>
              <td><?php echo htmlspecialchars($row['login']); ?></td>
            <?php else: ?>
              <td><?php echo $row['idutil']; ?></td>
              <td><?php echo htmlspecialchars($row['contato']); ?></td>
              <td><?php echo htmlspecialchars($row['prob_utilizador']); ?></td>
            <?php endif; ?>
            <td>
              <a href="?action=aprovar&type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">
                <i class="bi bi-check"></i> Aprovar
              </a>
              <a href="?action=rejeitar&type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja rejeitar este item?');">
                <i class="bi bi-x"></i> Rejeitar
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
    <a href="dashboard_tecnico.php" class="btn btn-secondary">Voltar ao Dashboard</a>
  </div>
</body>
</html>
