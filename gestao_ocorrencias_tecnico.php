<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'tecnico') {
  header("Location: login.php");
  exit();
}
include('db.php');

$action = $_GET['action'] ?? 'list';

// AÇÃO: CRIAR
if ($action == 'create') {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idutil         = $_POST['idutil'] ?? '';
    $contato        = $_POST['contato'] ?? '';
    $prob_utilizador= $_POST['prob_utilizador'] ?? '';
    $prob_encontrado= $_POST['prob_encontrado'] ?? '';
    $solucao        = $_POST['solucao'] ?? '';
    $estado         = $_POST['estado'] ?? 'ABERTO';
    $tecnico        = $_POST['tecnico'] ?? '';
    $equipamento    = $_POST['equipamento'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO ocorrencias (idutil, contato, prob_utilizador, prob_encontrado, solucao, estado, tecnico, equipamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $idutil, $contato, $prob_utilizador, $prob_encontrado, $solucao, $estado, $tecnico, $equipamento);
    if($stmt->execute()){
      header("Location: gestao_ocorrencias_tecnico.php");
      exit();
    } else {
      echo "Erro: " . $conn->error;
    }
  }
  // Exibe o formulário de criação
  ?>
  <!DOCTYPE html>
  <html lang="pt">
  <head>
    <meta charset="UTF-8">
    <title>Criar Ocorrência</title>
    <link rel="stylesheet" href="tecnico.css">
    <!-- Ícones do Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Criar Ocorrência</h1>
    <form method="post" action="?action=create">
      <label>ID Util:</label>
      <input type="number" name="idutil" required><br>
      <label>Contato:</label>
      <input type="text" name="contato" required><br>
      <label>Problema Utilizador:</label>
      <textarea name="prob_utilizador" required></textarea><br>
      <label>Problema Encontrado:</label>
      <textarea name="prob_encontrado"></textarea><br>
      <label>Solução:</label>
      <textarea name="solucao"></textarea><br>
      <label>Estado:</label>
      <select name="estado">
        <option value="ABERTO">ABERTO</option>
        <option value="EM CURSO">EM CURSO</option>
        <option value="RESOLVIDO">RESOLVIDO</option>
      </select><br>
      <label>Técnico:</label>
      <input type="text" name="tecnico"><br>
      <label>Equipamento:</label>
      <input type="text" name="equipamento"><br>
      <button type="submit">Criar</button>
    </form>
    <a href="gestao_ocorrencias_tecnico.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EDITAR
if ($action == 'edit') {
  $id = $_GET['id'] ?? '';
  if (empty($id)) {
    header("Location: gestao_ocorrencias_tecnico.php");
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idutil         = $_POST['idutil'] ?? '';
    $contato        = $_POST['contato'] ?? '';
    $prob_utilizador= $_POST['prob_utilizador'] ?? '';
    $prob_encontrado= $_POST['prob_encontrado'] ?? '';
    $solucao        = $_POST['solucao'] ?? '';
    $estado         = $_POST['estado'] ?? 'ABERTO';
    $tecnico        = $_POST['tecnico'] ?? '';
    $equipamento    = $_POST['equipamento'] ?? '';
    
    $stmt = $conn->prepare("UPDATE ocorrencias SET idutil=?, contato=?, prob_utilizador=?, prob_encontrado=?, solucao=?, estado=?, tecnico=?, equipamento=? WHERE id_ocorrencia=?");
    $stmt->bind_param("isssssssi", $idutil, $contato, $prob_utilizador, $prob_encontrado, $solucao, $estado, $tecnico, $equipamento, $id);
    if($stmt->execute()){
      header("Location: gestao_ocorrencias_tecnico.php");
      exit();
    } else {
      echo "Erro: " . $conn->error;
    }
  } else {
    $stmt = $conn->prepare("SELECT * FROM ocorrencias WHERE id_ocorrencia=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 0){
      echo "Ocorrência não encontrada!";
      exit();
    }
    $row = $result->fetch_assoc();
  }
  ?>
  <!DOCTYPE html>
  <html lang="pt">
  <head>
    <meta charset="UTF-8">
    <title>Editar Ocorrência</title>
    <link rel="stylesheet" href="tecnico.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Editar Ocorrência</h1>
    <form method="post" action="?action=edit&id=<?php echo $id; ?>">
      <label>ID Util:</label>
      <input type="number" name="idutil" value="<?php echo $row['idutil']; ?>" required><br>
      <label>Contato:</label>
      <input type="text" name="contato" value="<?php echo htmlspecialchars($row['contato']); ?>" required><br>
      <label>Problema Utilizador:</label>
      <textarea name="prob_utilizador" required><?php echo htmlspecialchars($row['prob_utilizador']); ?></textarea><br>
      <label>Problema Encontrado:</label>
      <textarea name="prob_encontrado"><?php echo htmlspecialchars($row['prob_encontrado']); ?></textarea><br>
      <label>Solução:</label>
      <textarea name="solucao"><?php echo htmlspecialchars($row['solucao']); ?></textarea><br>
      <label>Estado:</label>
      <select name="estado">
        <option value="ABERTO" <?php if($row['estado']=='ABERTO') echo 'selected'; ?>>ABERTO</option>
        <option value="EM CURSO" <?php if($row['estado']=='EM CURSO') echo 'selected'; ?>>EM CURSO</option>
        <option value="RESOLVIDO" <?php if($row['estado']=='RESOLVIDO') echo 'selected'; ?>>RESOLVIDO</option>
      </select><br>
      <label>Técnico:</label>
      <input type="text" name="tecnico" value="<?php echo htmlspecialchars($row['tecnico']); ?>"><br>
      <label>Equipamento:</label>
      <input type="text" name="equipamento" value="<?php echo htmlspecialchars($row['equipamento']); ?>"><br>
      <button type="submit">Atualizar</button>
    </form>
    <a href="gestao_ocorrencias_tecnico.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EXCLUIR
if ($action == 'delete') {
  $id = $_GET['id'] ?? '';
  if(!empty($id)) {
    $stmt = $conn->prepare("DELETE FROM ocorrencias WHERE id_ocorrencia=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
  }
  header("Location: gestao_ocorrencias_tecnico.php");
  exit();
}
?>
<!-- MODO LISTAGEM -->
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Ocorrências</title>
  <link rel="stylesheet" href="tecnico.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
  <!-- Header e Menu -->
  <header class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Administrador</h1>
  </header>
  <div id="menu-container" class="menu-container">
    <!-- Itens do menu (inalterados) -->
    <div class="menu-item" onclick="location.href='dashboard_tecnico.php';">
      <i class="bi bi-people"></i>
      <span>Dashboard</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_perfil_tecnico.php';">
      <i class="bi bi-people"></i><span>Gestão de Perfil</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_ocorrencias_tecnico.php';">
      <i class="bi bi-card-checklist"></i><span>Gestão de Ocorrências</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios_tecnico.php';">
      <i class="bi bi-check2-all"></i>
      <span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='aprovacoes_tecnico.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Pedidos Registro</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i><span>Logout</span>
    </div>
  </div>
  
  <div class="container fade-in">
    <h1>Gestão de Ocorrências</h1>
    <!-- Botão de adicionar -->
    <a href="?action=create" class="btn"><i class="bi bi-plus"></i></a>
    <table>
      <tr>
        <th>ID</th>
        <th>ID Util</th>
        <th>Contato</th>
        <th>Problema Utilizador</th>
        <th>Estado</th>
        <th>Técnico</th>
        <th>Equipamento</th>
        <th>Ações</th>
      </tr>
      <?php
        $sql = "SELECT * FROM ocorrencias";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".$row['id_ocorrencia']."</td>";
          echo "<td>".$row['idutil']."</td>";
          echo "<td>".htmlspecialchars($row['contato'])."</td>";
          echo "<td>".htmlspecialchars($row['prob_utilizador'])."</td>";
          echo "<td>".$row['estado']."</td>";
          echo "<td>".htmlspecialchars($row['tecnico'])."</td>";
          echo "<td>".htmlspecialchars($row['equipamento'])."</td>";
          echo "<td>
                  <a href='?action=edit&id=".$row['id_ocorrencia']."' class='btn'><i class='bi bi-pencil'></i></a>
                  <a href='?action=delete&id=".$row['id_ocorrencia']."' class='btn' onclick=\"return confirm('Deseja excluir esta ocorrência?');\"><i class='bi bi-dash'></i></a>
                </td>";
          echo "</tr>";
        }
      ?>
    </table>
  </div>
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    let menuOpen = false;
    menuToggle.addEventListener('click', function() {
      menuOpen = !menuOpen;
      if (menuOpen) {
        menuContainer.classList.add('open');
        gsap.fromTo(menuContainer, {opacity: 0, y: -20}, {duration: 0.5, opacity: 1, y: 0});
        menuToggle.innerHTML = '<i class="bi bi-x"></i>';
      } else {
        gsap.to(menuContainer, {duration: 0.3, opacity: 0, y: -20, onComplete: function() {
          menuContainer.classList.remove('open');
        }});
        menuToggle.innerHTML = '<i class="bi bi-list"></i>';
      }
    });
    gsap.to(".header", { duration: 1, opacity: 1, ease: "power3.out" });
  </script>
</body>
</html>


