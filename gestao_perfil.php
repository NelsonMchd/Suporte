<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}
include('db.php');
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM utilizadores WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
  $user = $result->fetch_assoc();
} else {
  echo "<p>Usuário não encontrado!</p>";
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Perfil</title>
  <link rel="stylesheet" href="style.css">
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
    <h1>Gestão de Perfil</h1>
    <form action="update_profile.php" method="POST">
      <label>Nome:</label>
      <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
      <label>Login:</label>
      <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
      <label>Status:</label>
      <select id="status" name="status" required>
          <option value="ativo" <?php echo ($user['status'] === 'ativo') ? 'selected' : ''; ?>>Ativo</option>
          <option value="inativo" <?php echo ($user['status'] === 'inativo') ? 'selected' : ''; ?>>Inativo</option>
      </select>
      <label>Nível:</label>
      <select id="nivel" name="nivel" required>
          <option value="administrador" <?php echo ($user['nivel'] === 'administrador') ? 'selected' : ''; ?>>Administrador</option>
          <option value="utilizador" <?php echo ($user['nivel'] === 'utilizador') ? 'selected' : ''; ?>>Utilizador</option>
      </select>
      <button type="submit">Atualizar Perfil</button>
    </form>
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