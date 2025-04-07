<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Conexão com o banco de dados (nome atualizado e consultas ajustadas)
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "basedadosv13fev"; // nome atualizado

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ==================== Consultas de Tickets ====================

// Total de Tickets (conta todos os registros usando a tabela ocorrencias)
$sqlTotalTickets = "SELECT COUNT(*) AS total FROM ocorrencias";
$result = $conn->query($sqlTotalTickets);
$totalTickets = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

// Tickets Abertos
$sqlTicketsAbertos = "SELECT COUNT(*) AS abertos FROM ocorrencias WHERE estado='ABERTO'";
$result = $conn->query($sqlTicketsAbertos);
$ticketsAbertos = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['abertos'] : 0;

// Tickets em Curso
$sqlTicketsEmCurso = "SELECT COUNT(*) AS emcurso FROM ocorrencias WHERE estado='EM CURSO'";
$result = $conn->query($sqlTicketsEmCurso);
$ticketsEmCurso = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['emcurso'] : 0;

// Tickets Resolvidos
$sqlTicketsResolvidos = "SELECT COUNT(*) AS resolvidos FROM ocorrencias WHERE estado='RESOLVIDO'";
$result = $conn->query($sqlTicketsResolvidos);
$ticketsResolvidos = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['resolvidos'] : 0;

// ==================== Consultas de Utilizadores ====================

// Total de Utilizadores
$sqlTotalUtilizadores = "SELECT COUNT(*) AS total FROM utilizadores";
$result = $conn->query($sqlTotalUtilizadores);
$totalUtilizadores = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : 0;

// Utilizadores Ativos
$sqlUtilizadoresAtivos = "SELECT COUNT(*) AS ativos FROM utilizadores WHERE status='ativo'";
$result = $conn->query($sqlUtilizadoresAtivos);
$utilizadoresAtivos = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['ativos'] : 0;

// Calcula os Inativos
$utilizadoresInativos = $totalUtilizadores - $utilizadoresAtivos;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Administrador - Gestão de Ocorrências</title>
  <!-- Google Fonts (Poppins) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #000;
      color: #fff;
      margin: 0;
      padding: 0;
    }
    /* Header com título centralizado e botão do menu à esquerda */
    .header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.8);
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
    }
    .header h1 {
      margin: 0;
      font-size: 1.8rem;
      text-align: center;
    }
    .menu-toggle {
      position: absolute;
      left: 20px;
      background: none;
      border: none;
      color: #fff;
      font-size: 2rem;
      cursor: pointer;
    }
    /* Menu horizontal (oculto por padrão) */
    .menu-container {
      position: fixed;
      top: 60px;
      left: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.9);
      display: none;
      flex-direction: row;
      justify-content: center;
      padding: 10px 0;
      z-index: 999;
      transform: translateY(-20px);
      opacity: 0;
      transition: all 0.3s ease;
    }
    .menu-container.open {
      display: flex;
      transform: translateY(0);
      opacity: 1;
    }
    .menu-item {
      margin: 0 15px;
      text-align: center;
      cursor: pointer;
      padding: 5px 10px;
      border-radius: 5px;
      transition: background 0.3s ease;
    }
    .menu-item:hover {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      animation: pulse 0.5s infinite;
    }
    .menu-item i {
      font-size: 1.8rem;
      display: block;
      margin-bottom: 5px;
    }
    .menu-item span {
      font-size: 0.9rem;
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    /* Conteúdo centralizado – com padding para compensar header e menu */
    .content {
      padding: 140px 20px 20px;
      opacity: 0;
      text-align: center;
    }
    /* Estilo dos cards com efeito hover */
    .card {
      background: rgba(255,255,255,0.1);
      border: none;
      cursor: pointer;
      transition: transform 0.3s ease;
    }
    .card:hover {
      transform: scale(1.05);
    }
    .card-title {
      color: #fff;
    }
  </style>
</head>
<body>
  <!-- Header com botão de toggle e título -->
  <div class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Dashboard Administrador</h1>
  </div>
  
  <!-- Menu horizontal com os itens completos -->
  <div id="menu-container" class="menu-container">
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
      <i class="bi bi-building"></i>
      <span>Gestão de Salas</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios.php';">
      <i class="bi bi-graph-up"></i>
      <span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_equipamentos.php';">
      <i class="bi bi-speedometer2"></i>
      <span>Gestão de Equipamentos</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_reparacoes.php';">
      <i class="bi bi-wrench"></i>
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
  
  <div class="content container">
    <div class="row mt-4">
      <!-- Card 1: Total de Tickets -->
      <div class="col-md-3 mb-3">
        <div class="card text-center" id="card-total-tickets">
          <div class="card-body">
            <h5 class="card-title">Total de Tickets</h5>
            <p class="card-text display-4"><?php echo $totalTickets; ?></p>
          </div>
        </div>
      </div>
      <!-- Card 2: Tickets Abertos -->
      <div class="col-md-3 mb-3">
        <div class="card text-center" id="card-tickets-abertos">
          <div class="card-body">
            <h5 class="card-title">Tickets Abertos</h5>
            <p class="card-text display-4"><?php echo $ticketsAbertos; ?></p>
          </div>
        </div>
      </div>
      <!-- Card 3: Total de Utilizadores -->
      <div class="col-md-3 mb-3">
        <div class="card text-center" id="card-total-utilizadores">
          <div class="card-body">
            <h5 class="card-title">Total de Utilizadores</h5>
            <p class="card-text display-4"><?php echo $totalUtilizadores; ?></p>
          </div>
        </div>
      </div>
      <!-- Card 4: Utilizadores Ativos -->
      <div class="col-md-3 mb-3">
        <div class="card text-center" id="card-utilizadores-ativos">
          <div class="card-body">
            <h5 class="card-title">Utilizadores Ativos</h5>
            <p class="card-text display-4"><?php echo $utilizadoresAtivos; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal para exibir o gráfico -->
  <div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content bg-dark text-white">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="statsModalLabel">Estatísticas Detalhadas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div style="position: relative; height: 400px;">
            <canvas id="statsChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Scripts: Bootstrap, GSAP e código customizado -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    // Toggle do menu
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
    
    // Animação de entrada
    gsap.to(".header", { duration: 1, opacity: 1, ease: "power3.out" });
    gsap.to(".content", { duration: 1, opacity: 1, delay: 0.5, ease: "power3.out" });
    
    // Variável para identificar qual grupo de dados exibir no gráfico:
    // 'tickets' ou 'utilizadores'
    let selectedCard = '';
    
    // Eventos de clique nos cards – definem o tipo de gráfico a exibir
    document.getElementById('card-total-tickets').addEventListener('click', function() {
      selectedCard = 'tickets';
      let statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
      statsModal.show();
    });
    document.getElementById('card-tickets-abertos').addEventListener('click', function() {
      selectedCard = 'tickets';
      let statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
      statsModal.show();
    });
    document.getElementById('card-total-utilizadores').addEventListener('click', function() {
      selectedCard = 'utilizadores';
      let statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
      statsModal.show();
    });
    document.getElementById('card-utilizadores-ativos').addEventListener('click', function() {
      selectedCard = 'utilizadores';
      let statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
      statsModal.show();
    });
    
    // Variável global para armazenar o gráfico
    let statsChart = null;
    
    // Ao exibir o modal, cria o gráfico conforme o grupo selecionado
    document.getElementById('statsModal').addEventListener('shown.bs.modal', function () {
      var ctx = document.getElementById('statsChart').getContext('2d');
      
      // Se já existir um gráfico, destrói-o
      if (statsChart) {
        statsChart.destroy();
      }
      
      if (selectedCard === 'tickets') {
        // Gráfico de Tickets: distribuição entre Abertos, em Curso e Resolvidos
        statsChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: ['Tickets Abertos', 'Tickets em curso', 'Tickets Resolvidos'],
            datasets: [{
              label: 'Estatísticas dos Tickets',
              data: [<?php echo $ticketsAbertos; ?>, <?php echo $ticketsEmCurso; ?>, <?php echo $ticketsResolvidos; ?>],
              backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(107, 114, 128, 0.8)',
                'rgba(139, 92, 246, 0.8)'
              ],
              borderColor: [
                'rgba(59, 130, 246, 1)',
                'rgba(107, 114, 128, 1)',
                'rgba(139, 92, 246, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#fff' } } },
            elements: { arc: { offset: 0, borderWidth: 1 } }
          }
        });
      } else if (selectedCard === 'utilizadores') {
        // Gráfico de Utilizadores: divisão entre Ativos e Inativos
        statsChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: ['Utilizadores Ativos', 'Utilizadores Inativos'],
            datasets: [{
              label: 'Estatísticas dos Utilizadores',
              data: [<?php echo $utilizadoresAtivos; ?>, <?php echo $utilizadoresInativos; ?>],
              backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)'
              ],
              borderColor: [
                'rgba(16, 185, 129, 1)',
                'rgba(239, 68, 68, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { labels: { color: '#fff' } } },
            elements: { arc: { offset: 0, borderWidth: 1 } }
          }
        });
      }
    });
  </script>
</body>
</html>