<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'administrador') {
  header("Location: login.php");
  exit();
}
include('db.php');

$action = $_GET['action'] ?? 'list';

// AÇÃO: CRIAR
if ($action == 'create') {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Descricao_Equipamento = $_POST['Descricao_Equipamento'] ?? '';
    $Obs_Equipamento       = $_POST['Obs_Equipamento'] ?? '';
    $Estado_Equipamento    = $_POST['Estado_Equipamento'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO equipamentos (Descricao_Equipamento, Obs_Equipamento, Estado_Equipamento) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $Descricao_Equipamento, $Obs_Equipamento, $Estado_Equipamento);
    if($stmt->execute()){
      header("Location: gestao_equipamentos.php");
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
    <title>Criar Equipamento</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Criar Equipamento</h1>
    <form method="post" action="?action=create">
      <label>Descrição do Equipamento:</label>
      <input type="text" name="Descricao_Equipamento" required><br>
      <label>Observações:</label>
      <input type="text" name="Obs_Equipamento"><br>
      <label>Estado do Equipamento:</label>
      <input type="text" name="Estado_Equipamento" required><br>
      <button type="submit">Criar</button>
    </form>
    <a href="gestao_equipamentos.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EDITAR
if ($action == 'edit') {
  $id = $_GET['id'] ?? '';
  if (empty($id)) {
    header("Location: gestao_equipamentos.php");
    exit();
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Descricao_Equipamento = $_POST['Descricao_Equipamento'] ?? '';
    $Obs_Equipamento       = $_POST['Obs_Equipamento'] ?? '';
    $Estado_Equipamento    = $_POST['Estado_Equipamento'] ?? '';
    
    $stmt = $conn->prepare("UPDATE equipamentos SET Descricao_Equipamento=?, Obs_Equipamento=?, Estado_Equipamento=? WHERE Cod_Equipamento=?");
    $stmt->bind_param("sssi", $Descricao_Equipamento, $Obs_Equipamento, $Estado_Equipamento, $id);
    if($stmt->execute()){
      header("Location: gestao_equipamentos.php");
      exit();
    } else {
      echo "Erro: " . $conn->error;
    }
  } else {
    $stmt = $conn->prepare("SELECT * FROM equipamentos WHERE Cod_Equipamento=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows==0){
      echo "Equipamento não encontrado!";
      exit();
    }
    $row = $result->fetch_assoc();
  }
  ?>
  <!DOCTYPE html>
  <html lang="pt">
  <head>
    <meta charset="UTF-8">
    <title>Editar Equipamento</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body>
    <h1>Editar Equipamento</h1>
    <form method="post" action="?action=edit&id=<?php echo $id; ?>">
      <label>Descrição do Equipamento:</label>
      <input type="text" name="Descricao_Equipamento" value="<?php echo htmlspecialchars($row['Descricao_Equipamento']); ?>" required><br>
      <label>Observações:</label>
      <input type="text" name="Obs_Equipamento" value="<?php echo htmlspecialchars($row['Obs_Equipamento']); ?>"><br>
      <label>Estado do Equipamento:</label>
      <input type="text" name="Estado_Equipamento" value="<?php echo htmlspecialchars($row['Estado_Equipamento']); ?>" required><br>
      <button type="submit">Atualizar</button>
    </form>
    <a href="gestao_equipamentos.php">Voltar</a>
  </body>
  </html>
  <?php
  exit();
}

// AÇÃO: EXCLUIR
if ($action == 'delete') {
  $id = $_GET['id'] ?? '';
  if (!empty($id)) {
    $stmt = $conn->prepare("DELETE FROM equipamentos WHERE Cod_Equipamento=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
  }
  header("Location: gestao_equipamentos.php");
  exit();
}
?>
<!-- MODO LISTAGEM -->
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Equipamentos</title>
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
    <!-- Itens do menu (iguais aos anteriores) -->
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
    <h1>Gestão de Equipamentos</h1>
    <!-- Botão de adicionar -->
    <a href="?action=create" class="btn"><i class="bi bi-plus"></i></a>
    <table>
      <tr>
        <th>Equipamento</th>
        <th>Observações</th>
        <th>Estado</th>
        <th>Ações</th>
      </tr>
      <?php
        $sql = "SELECT * FROM equipamentos";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".htmlspecialchars($row['Descricao_Equipamento'])."</td>";
          echo "<td>".htmlspecialchars($row['Obs_Equipamento'])."</td>";
          echo "<td>".htmlspecialchars($row['Estado_Equipamento'])."</td>";
          echo "<td>
                  <a href='?action=edit&id=".$row['Cod_Equipamento']."' class='btn'><i class='bi bi-pencil'></i></a>
                  <a href='?action=delete&id=".$row['Cod_Equipamento']."' class='btn' onclick=\"return confirm('Deseja excluir este equipamento?');\"><i class='bi bi-dash'></i></a>
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