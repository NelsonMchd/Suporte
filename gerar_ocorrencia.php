<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'utilizador') {
  header("Location: login.php");
  exit();
}
include('db.php');

$action = $_GET['action'] ?? 'create';
if ($action == 'create') {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // O id do utilizador vem da sessão
    $idutil = $_SESSION['user_id'];
    $contato = $_POST['contato'] ?? '';
    $prob_utilizador = $_POST['prob_utilizador'] ?? '';
    $prob_encontrado = $_POST['prob_encontrado'] ?? '';
    $solucao = $_POST['solucao'] ?? '';
    $estado = $_POST['estado'] ?? 'ABERTO';
    // Para ocorrências geradas pelo utilizador, o campo 'tecnico' fica vazio
    $tecnico = "";
    $equipamento = $_POST['equipamento'] ?? '';
    
    $stmt = $conn->prepare("INSERT INTO ocorrencias (idutil, contato, prob_utilizador, prob_encontrado, solucao, estado, tecnico, equipamento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $idutil, $contato, $prob_utilizador, $prob_encontrado, $solucao, $estado, $tecnico, $equipamento);
    if($stmt->execute()){
      header("Location: dashboard_user.php");
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
    <title>Gerar Ocorrência - Utilizador</title>
    <link rel="stylesheet" href="style-utilizador.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  </head>
  <body class="utilizador">
    <!-- Header -->
    <div class="header">
      <button id="menu-toggle" class="menu-toggle">
        <i class="bi bi-list"></i>
      </button>
      <h1>Gerar Ocorrência</h1>
    </div>
    <!-- Menu -->
    <div id="menu-container" class="menu-container">
      <div class="menu-item" onclick="location.href='gestao_perfil_utilizador.php';">
        <i class="bi bi-person"></i>
        <span>Gestão de Perfil</span>
      </div>
      <div class="menu-item" onclick="location.href='dashboard_user.php';">
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
    <!-- Conteúdo -->
    <div class="content">
      <h1>Gerar Ocorrência</h1>
      <form method="post" action="?action=create">
        <!-- Os campos idutil e tecnico não são exibidos, pois são obtidos da sessão -->
        <label>Contato:</label>
        <input type="text" name="contato" required><br>
        <label>Problema Utilizador:</label>
        <textarea name="prob_utilizador" required></textarea><br>
        <br>
        <label>Equipamento:</label>
        <input type="text" name="equipamento"><br>
        <button type="submit" class="btn">Criar</button>
      </form>
      <a href="dashboard_user.php">Voltar</a>
    </div>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
      gsap.to(".header", {duration: 1, opacity: 1, ease: "power3.out"});
    </script>
  </body>
  </html>
  <?php
  exit();
}
?>
