<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'administrador') {
  header("Location: login.php");
  exit();
}
include('db.php');

$action = $_GET['action'] ?? 'list';

// AÇÕES de CRUD (create, edit, delete) para salas...
// [Aqui o código de criação, edição e exclusão, se houver]

?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Gestão de Salas</title>
  <link rel="stylesheet" href="style.css">
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
    <!-- Itens do menu permanecem inalterados -->
    <div class="menu-item" onclick="location.href='dashboard_admin.php';">
      <i class="bi bi-people"></i><span>Dashboard</span>
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
    <h1>Gestão de Salas</h1>
    <!-- Botão de adicionar (ícone de plus) -->
    <a href="?action=create" class="btn"><i class="bi bi-plus"></i></a>
    <?php
      // Consulta atualizada: usamos a coluna "Observações" (com acento)
      $sql = "SELECT s.cod_sala, s.Nome_sala, b.descricao_bloco, p.Descricao_piso, s.Observações, s.Estado 
              FROM salas s
              LEFT JOIN blocos b ON s.Bloco_sala = b.cod_bloco
              LEFT JOIN pisos p ON s.Piso_sala = p.Cod_piso
              ORDER BY s.cod_sala";
      $result = $conn->query($sql);
      
      if ($result->num_rows == 0) {
        echo "<p>Nenhuma sala encontrada.</p>";
      } else {
    ?>
    <table>
      <tr>
        <th>Sala</th>
        <th>Bloco</th>
        <th>Piso</th>
        <th>Observações</th>
        <th>Estado</th>
        <th>Ações</th>
      </tr>
      <?php
        while ($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row['Nome_sala']) . "</td>";
          echo "<td>" . htmlspecialchars($row['descricao_bloco']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Descricao_piso']) . "</td>";
          echo "<td>" . htmlspecialchars($row['Observações']) . "</td>";
          echo "<td>" . $row['Estado'] . "</td>";
          // Botões de editar (ícone lápis) e excluir (ícone menos)
          echo "<td>
                  <a href='?action=edit&id=" . $row['cod_sala'] . "' class='btn'><i class='bi bi-pencil'></i></a>
                  <a href='?action=delete&id=" . $row['cod_sala'] . "' class='btn' onclick=\"return confirm('Deseja excluir esta sala?');\"><i class='bi bi-dash'></i></a>
                </td>";
          echo "</tr>";
        }
      ?>
    </table>
    <?php } ?>
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