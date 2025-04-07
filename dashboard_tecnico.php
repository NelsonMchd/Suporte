<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'tecnico') {
    header("Location: login.php");
    exit();
}
include('db.php');
$techName = $_SESSION['user_nome'];

// Consultas para o dashboard do técnico
$stmtTotal = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE tecnico = ?");
$stmtTotal->bind_param("s", $techName);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$rowTotal = $resultTotal->fetch_assoc();
$totalTickets = $rowTotal['total'] ?? 0;

$stmtAberto = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE tecnico = ? AND estado = 'ABERTO'");
$stmtAberto->bind_param("s", $techName);
$stmtAberto->execute();
$resultAberto = $stmtAberto->get_result();
$rowAberto = $resultAberto->fetch_assoc();
$ticketsAbertos = $rowAberto['total'] ?? 0;

$stmtCurso = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE tecnico = ? AND estado = 'EM CURSO'");
$stmtCurso->bind_param("s", $techName);
$stmtCurso->execute();
$resultCurso = $stmtCurso->get_result();
$rowCurso = $resultCurso->fetch_assoc();
$ticketsEmCurso = $rowCurso['total'] ?? 0;

$stmtResolvido = $conn->prepare("SELECT COUNT(*) AS total FROM ocorrencias WHERE tecnico = ? AND estado = 'RESOLVIDO'");
$stmtResolvido->bind_param("s", $techName);
$stmtResolvido->execute();
$resultResolvido = $stmtResolvido->get_result();
$rowResolvido = $resultResolvido->fetch_assoc();
$ticketsResolvidos = $rowResolvido['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Técnico</title>
  <link rel="stylesheet" href="tecnico.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <style>
    /* Adicione esta regra para cards pequenos */
    .card.small {
      max-width: 200px;
      margin: 10px auto;
      padding: 10px;
      font-size: 1em;
    }
    .card.small h2 {
      font-size: 1.5em;
      margin-bottom: 10px;
    }
    .card.small p {
      font-size: 1.2em;
    }
  </style>
</head>
<body class="tecnico">
  <div class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Técnico</h1>
  </div>
  <div id="menu-container" class="menu-container">
    <div class="menu-item" onclick="location.href='gestao_perfil_tecnico.php';">
      <i class="bi bi-person"></i>
      <span>Gestão de Perfil</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_ocorrencias_tecnico.php';">
      <i class="bi bi-card-checklist"></i>
      <span>Ocorrências</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios_tecnico.php';">
      <i class="bi bi-check2-all"></i>
      <span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='aprovacoes_tecnico.php';">
      <i class="bi bi-check2-all"></i>
      <span>Aprovações</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
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
