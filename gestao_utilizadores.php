<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'administrador') {
  header("Location: login.php");
  exit();
}
include('db.php');

// Verifica a ação (create, edit, delete ou list)
$action = $_GET['action'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Utilizadores</title>
  <link rel="stylesheet" href="style.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
  <!-- Header e Menu (exibido em todas as páginas, exceto index e login) -->
  <header class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Administrador</h1>
  </header>
  <div id="menu-container" class="menu-container">
  <div class="menu-item" onclick="location.href='dashboard_admin.php';">
      <i class="bi bi-people"></i>
      <span>Dashboard</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_perfil.php';">
      <i class="bi bi-people"></i>
      <span>Gestão de Perfil</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_utilizadores.php';">
      <i class="bi bi-people"></i>
      <span>Gestão de Utilizadores</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_ocorrencias.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Gestão de Ocorrências</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_salas.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Gestão de Salas</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_equipamentos.php';">
      <i class="bi bi-speedometer2"></i>
      <span>Gestão de Equipamentos</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_reparacoes.php';">
      <i class="bi bi-speedometer2"></i>
      <span>Gestão de Reparações</span>
    </div>
    <div class="menu-item" onclick="location.href='aprovacoes.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Pedidos Registro</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </div>
  </div>
  
  <div class="container fade-in">
    <?php
    // Ação: Criar Utilizador
    if ($action === 'create') {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome   = $_POST['nome']   ?? '';
        $login  = $_POST['login']  ?? '';
        $pass   = $_POST['pass']   ?? '';
        $status = $_POST['status'] ?? 'ativo';
        $nivel  = $_POST['nivel']  ?? 'utilizador';
        $passHash = md5($pass);
        $sql  = "INSERT INTO utilizadores (nome, login, pass, status, nivel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $login, $passHash, $status, $nivel);
        if ($stmt->execute()) {
          header("Location: gestao_utilizadores.php");
          exit();
        } else {
          echo "<p>Erro ao criar utilizador: " . $conn->error . "</p>";
        }
      }
      ?>
      <h1>Criar Utilizador</h1>
      <form method="POST" action="?action=create">
          <label>Nome:</label>
          <input type="text" name="nome" required>
          <label>Login:</label>
          <input type="text" name="login" required>
          <label>Senha:</label>
          <input type="password" name="pass" required>
          <label>Status:</label>
          <select name="status">
              <option value="ativo">Ativo</option>
              <option value="inativo">Inativo</option>
          </select>
          <label>Nível:</label>
          <select name="nivel">
              <option value="administrador">Administrador</option>
              <option value="utilizador">Utilizador</option>
          </select>
          <button type="submit">Salvar</button>
      </form>
      <br>
      <a href="gestao_utilizadores.php" class="btn">Voltar</a>
      <?php
      exit();
    }
    
    // Ação: Editar Utilizador
    if ($action === 'edit') {
      $id = $_GET['id'] ?? '';
      if (empty($id)) {
        header("Location: gestao_utilizadores.php");
        exit();
      }
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome   = $_POST['nome']   ?? '';
        $status = $_POST['status'] ?? 'ativo';
        $nivel  = $_POST['nivel']  ?? 'utilizador';
        $sql  = "UPDATE utilizadores SET nome = ?, status = ?, nivel = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $status, $nivel, $id);
        if ($stmt->execute()) {
          header("Location: gestao_utilizadores.php");
          exit();
        } else {
          echo "<p>Erro ao atualizar utilizador: " . $conn->error . "</p>";
        }
      }
      $sql  = "SELECT * FROM utilizadores WHERE id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows < 1) {
        echo "<p>Utilizador não encontrado!</p>";
        exit();
      }
      $user = $result->fetch_assoc();
      ?>
      <h1>Editar Utilizador</h1>
      <form method="POST" action="?action=edit&id=<?php echo $id; ?>">
          <label>Nome:</label>
          <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
          <label>Status:</label>
          <select name="status">
              <option value="ativo" <?php if($user['status'] === 'ativo') echo 'selected'; ?>>Ativo</option>
              <option value="inativo" <?php if($user['status'] === 'inativo') echo 'selected'; ?>>Inativo</option>
          </select>
          <label>Nível:</label>
          <select name="nivel">
              <option value="administrador" <?php if($user['nivel'] === 'administrador') echo 'selected'; ?>>Administrador</option>
              <option value="tecnico" <?php if($user['nivel'] === 'tecnico') echo 'selected'; ?>>Técnico</option>
              <option value="utilizador" <?php if($user['nivel'] === 'utilizador') echo 'selected'; ?>>Utilizador</option>
          </select>
          <button type="submit">Salvar</button>
      </form>
      <br>
      <a href="gestao_utilizadores.php" class="btn">Voltar</a>
      <?php
      exit();
    }
    
    // Ação: Excluir
    if ($action === 'delete') {
      $id = $_GET['id'] ?? '';
      if (!empty($id)) {
        $sql  = "DELETE FROM utilizadores WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
      }
      header("Location: gestao_utilizadores.php");
      exit();
    }
    
    // Listar Utilizadores
    ?>
    <h1>Gestão de Utilizadores</h1>
    <a href="?action=create" class="btn">[ + Criar Novo ]</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Login</th>
            <th>Status</th>
            <th>Nível</th>
            <th>Ações</th>
        </tr>
        <?php
        $sql = "SELECT * FROM utilizadores";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>".$row['id']."</td>";
          echo "<td>".htmlspecialchars($row['nome'])."</td>";
          echo "<td>".htmlspecialchars($row['login'])."</td>";
          echo "<td>".$row['status']."</td>";
          echo "<td>".$row['nivel']."</td>";
          echo "<td>
                  <a href='?action=edit&id=".$row['id']."' class='btn'>Editar</a>
                  <a href='?action=delete&id=".$row['id']."' class='btn' onclick=\"return confirm('Deseja excluir este utilizador?');\">Excluir</a>
                </td>";
          echo "</tr>";
        }
        ?>
    </table>
  </div>
  
  <!-- JS do Menu -->
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
    
    // Animação do header
    gsap.to(".header", { duration: 1, opacity: 1, ease: "power3.out" });
  </script>
</body>
</html>


