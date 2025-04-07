<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'utilizador') {
  header("Location: login.php");
  exit();
}
include('db.php');

$userId = $_SESSION['user_id'];

// Consultas para o dashboard do utilizador
$stmtTotal = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE idutil = ?");
$stmtTotal->bind_param("i", $userId);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$rowTotal = $resultTotal->fetch_assoc();
$totalTickets = $rowTotal['total'] ?? 0;

$stmtAberto = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE idutil = ? AND estado = 'ABERTO'");
$stmtAberto->bind_param("i", $userId);
$stmtAberto->execute();
$resultAberto = $stmtAberto->get_result();
$rowAberto = $resultAberto->fetch_assoc();
$ticketsAbertos = $rowAberto['total'] ?? 0;

$stmtCurso = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE idutil = ? AND estado = 'EM CURSO'");
$stmtCurso->bind_param("i", $userId);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();
$rowCurso = $resultCurso->fetch_assoc();
$ticketsEmCurso = $rowCurso['total'] ?? 0;

$stmtResolvido = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE idutil = ? AND estado = 'RESOLVIDO'");
$stmtResolvido->bind_param("i", $userId);
$stmtResolvido->execute();
$resultResolvido = $stmtResolvido->get_result();
$rowResolvido = $resultResolvido->fetch_assoc();
$ticketsResolvidos = $rowResolvido['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Utilizador</title>
  <link rel="stylesheet" href="style-utilizador.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body class="utilizador">
  <div class="header utilizador">
    <button id="menu-toggle" class="menu-toggle utilizador">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Utilizador</h1>
  </div>
  <div id="menu-container" class="menu-container utilizador">
    <div class="menu-item utilizador" onclick="location.href='gestao_perfil_utilizador.php';">
      <i class="bi bi-person"></i>
      <span>Gestão de Perfil</span>
    </div>
    <div class="menu-item utilizador" onclick="location.href='gerar_ocorrencia.php';">
      <i class="bi bi-pencil-square"></i>
      <span>Gerar Ocorrência</span>
    </div>
    <div class="menu-item utilizador" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </div>
  </div>
  <div class="container utilizador fade-in">
    <div class="row">
      <div class="col-md-3 mb-3">
        <div class="card utilizador">
          <h5>Total de Tickets</h5>
          <p class="display-4"><?php echo $totalTickets; ?></p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card utilizador">
          <h5>Tickets Abertos</h5>
          <p class="display-4"><?php echo $ticketsAbertos; ?></p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card utilizador">
          <h5>Tickets em Curso</h5>
          <p class="display-4"><?php echo $ticketsEmCurso; ?></p>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card utilizador">
          <h5>Tickets Resolvidos</h5>
          <p class="display-4"><?php echo $ticketsResolvidos; ?></p>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    const menuToggleUtil = document.getElementById('menu-toggle');
    const menuContainerUtil = document.getElementById('menu-container');
    let menuOpenUtil = false;
    menuToggleUtil.addEventListener('click', function() {
      menuOpenUtil = !menuOpenUtil;
      if(menuOpenUtil) {
        menuContainerUtil.classList.add('open');
        gsap.fromTo(menuContainerUtil, {opacity: 0, y: -20}, {duration: 0.5, opacity: 1, y: 0});
        menuToggleUtil.innerHTML = '<i class="bi bi-x"></i>';
      } else {
        gsap.to(menuContainerUtil, {duration: 0.3, opacity: 0, y: -20, onComplete: function() {
          menuContainerUtil.classList.remove('open');
        }});
        menuToggleUtil.innerHTML = '<i class="bi bi-list"></i>';
      }
    });
    gsap.to(".header.utilizador", {duration: 1, opacity: 1, ease: "power3.out"});
  </script>
</body>
</html>
