<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'administrador') {
  header("Location: login.php");
  exit();
}
include('db.php');

$action = $_GET['action'] ?? 'list';

// AÇÃO: CRIAR (semelhante a gestao_ocorrencias.php – poderá filtrar o estado se necessário)
if ($action == 'create') {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idutil         = $_POST['idutil'] ?? '';
    $contato        = $_POST['contato'] ?? '';
    $prob_utilizador= $_POST['prob_utilizador'] ?? '';
    $prob_encontrado= $_POST['prob_encontrado'] ?? '';
    $solucao        = $_POST['solucao'] ?? '';
    $estado         = $_POST['estado'] ?? 'EM CURSO'; // padrão para reparações
    $tecnico        = $_POST['tecnico'] ?? '';
    $equipamento    = $_POST['equipamento'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO ocorrencias (idutil, contato, prob_utilizador, prob_encontrado, solucao, estado, tecnico, equipamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $idutil, $contato, $prob_utilizador, $prob_encontrado, $solucao, $estado, $tecnico, $equipamento);
    if($stmt->execute()){
      header("Location: gestao_reparacoes.php");
      exit();
    } else {
      echo "Erro: " . $conn->error;
    }
  }
  ?>
  <!DOCTYPE html>
  <html lang="pt">
  <head>
    <meta charset="UTF-8">
    <title>Criar Reparação</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Criar Reparação</h1>
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
        <option value="EM CURSO">EM CURSO</option>
        <option value="RESOLVIDO">RESOLVIDO</option>
      </select><br>
      <label>Técnico:</label>
      <input type="text" name="tecnico"><br>
      <label>Equipamento:</label>
      <input type="text" name="equipamento"><br>
      <button type="submit">Criar</button>
    </form>
    <a href="gestao_reparacoes.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EDITAR
if ($action == 'edit') {
  $id = $_GET['id'] ?? '';
  if (empty($id)) {
    header("Location: gestao_reparacoes.php");
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idutil         = $_POST['idutil'] ?? '';
    $contato        = $_POST['contato'] ?? '';
    $prob_utilizador= $_POST['prob_utilizador'] ?? '';
    $prob_encontrado= $_POST['prob_encontrado'] ?? '';
    $solucao        = $_POST['solucao'] ?? '';
    $estado         = $_POST['estado'] ?? 'EM CURSO';
    $tecnico        = $_POST['tecnico'] ?? '';
    $equipamento    = $_POST['equipamento'] ?? '';
    
    $stmt = $conn->prepare("UPDATE ocorrencias SET idutil=?, contato=?, prob_utilizador=?, prob_encontrado=?, solucao=?, estado=?, tecnico=?, equipamento=? WHERE id_ocorrencia=?");
    $stmt->bind_param("isssssssi", $idutil, $contato, $prob_utilizador, $prob_encontrado, $solucao, $estado, $tecnico, $equipamento, $id);
    if($stmt->execute()){
      header("Location: gestao_reparacoes.php");
      exit();
    } else {
      echo "Erro: " . $conn->error;
    }
  } else {
    $stmt = $conn->prepare("SELECT * FROM ocorrencias WHERE id_ocorrencia=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows==0){
      echo "Reparação não encontrada!";
      exit();
    }
    $row = $result->fetch_assoc();
  }
  ?>
  <!DOCTYPE html>
  <html lang="pt">
  <head>
    <meta charset="UTF-8">
    <title>Editar Reparação</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Editar Reparação</h1>
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
        <option value="EM CURSO" <?php if($row['estado']=='EM CURSO') echo 'selected'; ?>>EM CURSO</option>
        <option value="RESOLVIDO" <?php if($row['estado']=='RESOLVIDO') echo 'selected'; ?>>RESOLVIDO</option>
      </select><br>
      <label>Técnico:</label>
      <input type="text" name="tecnico" value="<?php echo htmlspecialchars($row['tecnico']); ?>"><br>
      <label>Equipamento:</label>
      <input type="text" name="equipamento" value="<?php echo htmlspecialchars($row['equipamento']); ?>"><br>
      <button type="submit">Atualizar</button>
    </form>
    <a href="gestao_reparacoes.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EXCLUIR
if ($action == 'delete') {
  $id = $_GET['id'] ?? '';
  if (!empty($id)) {
    $stmt = $conn->prepare("DELETE FROM ocorrencias WHERE id_ocorrencia=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
  }
  header("Location: gestao_reparacoes.php");
  exit();
}
?>
<!-- MODO LISTAGEM -->
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Reparações</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
  <header class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Administrador</h1>
  </header>
  <div id="menu-container" class="menu-container">
    <!-- Itens do menu -->
    <div class="menu-item" onclick="location.href='dashboard_admin.php';">
      <i class="bi bi-people"></i>
      <span>Dashboard</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_perfil.php';">
      <i class="bi bi-people"></i><span>Gestão de Perfil</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_utilizadores.php';">
      <i class="bi bi-people"></i><span>Gestão de Utilizadores</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_ocorrencias.php';">
      <i class="bi bi-card-checklist"></i><span>Gestão de Ocorrências</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_salas.php';">
      <i class="bi bi-card-checklist"></i><span>Gestão de Salas</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios.php';">
      <i class="bi bi-card-checklist"></i><span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_equipamentos.php';">
      <i class="bi bi-speedometer2"></i><span>Gestão de Equipamentos</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_reparacoes.php';">
      <i class="bi bi-wrench"></i><span>Gestão de Reparações</span>
    </div>
    <div class="menu-item" onclick="location.href='aprovacoes.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Pedidos Registro</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i><span>Logout</span>
    </div>
  </div>
  
  <div class="container fade-in">
    <h1>Gestão de Reparações</h1>
    <!-- Botão de adicionar -->
    <a href="?action=create" class="btn"><i class="bi bi-plus"></i></a>
    <table>
      <tr>
        <th>ID</th>
        <th>Técnico</th>
        <th>Problema Encontrado</th>
        <th>Solução</th>
        <th>Estado</th>
        <th>Data Decorrer</th>
        <th>Data Finalizada</th>
        <th>Tempo de Resolução</th>
        <th>Ações</th>
      </tr>
      <?php
        $sql = "SELECT * FROM ocorrencias WHERE estado IN ('EM CURSO','RESOLVIDO')";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".$row['id_ocorrencia']."</td>";
          echo "<td>".htmlspecialchars($row['tecnico'])."</td>";
          echo "<td>".htmlspecialchars($row['prob_encontrado'])."</td>";
          echo "<td>".htmlspecialchars($row['solucao'])."</td>";
          echo "<td>".$row['estado']."</td>";
          echo "<td>".$row['data_decorrer']."</td>";
          echo "<td>".$row['data_finalizada']."</td>";
          $tempoResolucao = "N/A";
          if (!empty($row['data_abertura']) && !empty($row['data_finalizada'])) {
            $dataAbertura = new DateTime($row['data_abertura']);
            $dataFinalizada = new DateTime($row['data_finalizada']);
            $interval = $dataAbertura->diff($dataFinalizada);
            $tempoResolucao = $interval->format('%a dias, %h horas, %i minutos');
          }
          echo "<td>".$tempoResolucao."</td>";
          echo "<td>
                  <a href='?action=edit&id=".$row['id_ocorrencia']."' class='btn'><i class='bi bi-pencil'></i></a>
                  <a href='?action=delete&id=".$row['id_ocorrencia']."' class='btn' onclick=\"return confirm('Deseja excluir esta reparação?');\"><i class='bi bi-dash'></i></a>
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