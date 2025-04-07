<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'utilizador') {
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
  <title>Gestão de Perfil Utilizador</title>
  <link rel="stylesheet" href="style-utilizador.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body class="utilizador">
  <div class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Gestão de Perfil Utilizador</h1>
  </div>
  <div id="menu-container" class="menu-container">
    <div class="menu-item" onclick="location.href='gestao_perfil_utilizador.php';">
      <i class="bi bi-person"></i>
      <span>Gestão de Perfil</span>
      <div class="submenu">Visualizar Meu Perfil</div>
    </div>
    <div class="menu-item" onclick="location.href='dashboard_utilizador.php';">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </div>
    <div class="menu-item" onclick="location.href='gerar_ocorrencia.php';">
      <i class="bi bi-pencil-square"></i>
      <span>Gerar Ocorrência</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </div>
  </div>
  <div class="content">
    <h1>Gestão de Perfil Utilizador</h1>
    <form action="update_profile_utilizador.php" method="POST" enctype="multipart/form-data">
      <label>Nome:</label>
      <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8'); ?>" required>
      <label>Login:</label>
      <input type="text" name="login" value="<?php echo htmlspecialchars($user['login'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
      <label>Status:</label>
      <select name="status" required>
          <option value="ativo" <?php echo ($user['status'] === 'ativo') ? 'selected' : ''; ?>>Ativo</option>
          <option value="inativo" <?php echo ($user['status'] === 'inativo') ? 'selected' : ''; ?>>Inativo</option>
      </select>
      <label>Nível:</label>
      <select name="nivel" required>
          <option value="utilizador" <?php echo ($user['nivel'] === 'utilizador') ? 'selected' : ''; ?>>Utilizador</option>
          <option value="administrador" <?php echo ($user['nivel'] === 'administrador') ? 'selected' : ''; ?>>Administrador</option>
          <option value="tecnico" <?php echo ($user['nivel'] === 'tecnico') ? 'selected' : ''; ?>>Técnico</option>
      </select>
      <label>Foto de Perfil:</label>
      <input type="file" name="perfil_foto" accept="image/*">
      <button type="submit" class="btn">Atualizar Perfil</button>
    </form>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    let menuOpen = false;
    menuToggle.addEventListener('click', function() {
      menuOpen = !menuOpen;
      if(menuOpen) {
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
    gsap.to(".header", {duration: 1, opacity: 1, ease: "power3.out"});
  </script>
</body>
</html>
