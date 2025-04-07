<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_nivel'] !== 'tecnico') {
    header("Location: login.php");
    exit();
}
include('db.php');
$action = $_GET['action'] ?? 'list';
$tech_id = $_SESSION['user_id'];
$tech_name = $_SESSION['user_nome'];

// AÇÃO: CRIAR
if ($action == 'create') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo = $_POST['titulo'] ?? '';
        $conteudo = $_POST['conteudo'] ?? '';
        $stmt = $conn->prepare("INSERT INTO relatorios (tecnico_id, titulo, conteudo) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $tech_id, $titulo, $conteudo);
        if($stmt->execute()){
            header("Location: relatorios_tecnico.php");
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
      <title>Criar Relatório - Técnico</title>
      <link rel="stylesheet" href="tecnico.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    </head>
    <body class="tecnico">
      <div class="header">
        <button id="menu-toggle" class="menu-toggle"><i class="bi bi-list"></i></button>
        <h1>Criar Relatório</h1>
      </div>
      <div id="menu-container" class="menu-container">
        <div class="menu-item" onclick="location.href='gestao_perfil_tecnico.php';">
          <i class="bi bi-person"></i><span>Gestão de Perfil</span>
        </div>
        <div class="menu-item" onclick="location.href='dashboard_tecnico.php';">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </div>
        <div class="menu-item" onclick="location.href='relatorios_tecnico.php';">
          <i class="bi bi-file-earmark-text"></i><span>Relatórios</span>
        </div>
        <div class="menu-item" onclick="location.href='logout.php';">
          <i class="bi bi-box-arrow-right"></i><span>Logout</span>
        </div>
      </div>
      <div class="content">
        <h1>Criar Relatório</h1>
        <form method="post" action="?action=create">
          <label>Título:</label>
          <input type="text" name="titulo" required><br>
          <label>Conteúdo:</label>
          <textarea name="conteudo" required></textarea><br>
          <button type="submit" class="btn">Criar</button>
        </form>
        <a href="relatorios_tecnico.php" class="btn">Voltar</a>
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
    <?php
    exit();
}

// AÇÃO: EDITAR
if ($action == 'edit') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        header("Location: relatorios_tecnico.php");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titulo = $_POST['titulo'] ?? '';
        $conteudo = $_POST['conteudo'] ?? '';
        $stmt = $conn->prepare("UPDATE relatorios SET titulo = ?, conteudo = ? WHERE id_relatorio = ? AND tecnico_id = ?");
        $stmt->bind_param("ssii", $titulo, $conteudo, $id, $tech_id);
        if($stmt->execute()){
            header("Location: relatorios_tecnico.php");
            exit();
        } else {
            echo "Erro: " . $conn->error;
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM relatorios WHERE id_relatorio = ? AND tecnico_id = ?");
        $stmt->bind_param("ii", $id, $tech_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            echo "Relatório não encontrado!";
            exit();
        }
        $report = $result->fetch_assoc();
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt">
    <head>
        <meta charset="UTF-8">
        <title>Editar Relatório - Técnico</title>
        <link rel="stylesheet" href="tecnico.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    </head>
    <body class="tecnico">
        <div class="header">
            <button id="menu-toggle" class="menu-toggle"><i class="bi bi-list"></i></button>
            <h1>Editar Relatório</h1>
        </div>
        <div id="menu-container" class="menu-container">
            <div class="menu-item" onclick="location.href='gestao_perfil_tecnico.php';">
                <i class="bi bi-person"></i><span>Gestão de Perfil</span>
            </div>
            <div class="menu-item" onclick="location.href='dashboard_tecnico.php';">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </div>
            <div class="menu-item" onclick="location.href='relatorios_tecnico.php';">
                <i class="bi bi-file-earmark-text"></i><span>Relatórios</span>
            </div>
            <div class="menu-item" onclick="location.href='logout.php';">
                <i class="bi bi-box-arrow-right"></i><span>Logout</span>
            </div>
        </div>
        <div class="content">
            <h1>Editar Relatório</h1>
            <form method="post" action="?action=edit&id=<?php echo $id; ?>">
                <label>Título:</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($report['titulo']); ?>" required><br>
                <label>Conteúdo:</label>
                <textarea name="conteudo" required><?php echo htmlspecialchars($report['conteudo']); ?></textarea><br>
                <button type="submit" class="btn">Atualizar</button>
            </form>
            <a href="relatorios_tecnico.php" class="btn">Voltar</a>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <?php
    exit();
}

// Modo Listagem: Listar os relatórios do técnico
$stmt = $conn->prepare("SELECT * FROM relatorios WHERE tecnico_id = ? ORDER BY data_criacao DESC");
$stmt->bind_param("i", $tech_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Relatórios - Técnico</title>
  <link rel="stylesheet" href="tecnico.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <style>
    /* Estilo para cards pequenos (se desejar) */
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
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #444;
      padding: 10px;
      text-align: center;
    }
  </style>
</head>
<body class="tecnico">
  <div class="header">
    <button id="menu-toggle" class="menu-toggle">
      <i class="bi bi-list"></i>
    </button>
    <h1>Relatórios - Técnico</h1>
  </div>
  <div id="menu-container" class="menu-container">
  <div class="menu-item" onclick="location.href='dashboard_tecnico.php';">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_perfil_tecnico.php';">
      <i class="bi bi-people"></i>
      <span>Gestão de Perfil</span>
    </div>
    <div class="menu-item" onclick="location.href='gestao_ocorrencias_tecnico.php';">
      <i class="bi bi-file-earmark-text"></i>
      <span>Ocorrências</span>
    </div>
    <div class="menu-item" onclick="location.href='relatorios_tecnico.php';">
      <i class="bi bi-check2-all"></i>
      <span>Relatórios</span>
    </div>
    <div class="menu-item" onclick="location.href='aprovacoes_tecnico.php';">
      <i class="bi bi-file-earmark-text"></i>
      <span>Aprovações</span>
    </div>
    <div class="menu-item" onclick="location.href='logout.php';">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </div>
  </div>
  <div class="content">
    <h1>Relatórios Criados</h1>
    <a href="?action=create" class="btn">[ + Criar Novo ]</a>
    <?php if ($result->num_rows == 0): ?>
      <p>Nenhum relatório criado.</p>
    <?php else: ?>
      <table class="table table-bordered">
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Data Criação</th>
          <th>Data Atualização</th>
          <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_relatorio']; ?></td>
            <td><?php echo htmlspecialchars($row['titulo']); ?></td>
            <td><?php echo $row['data_criacao']; ?></td>
            <td><?php echo $row['data_atualizacao']; ?></td>
            <td>
              <a href="?action=edit&id=<?php echo $row['id_relatorio']; ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil"></i> Editar
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script>
    const menuToggleList = document.getElementById('menu-toggle');
    const menuContainerList = document.getElementById('menu-container');
    let menuOpenList = false;
    menuToggleList.addEventListener('click', function() {
      menuOpenList = !menuOpenList;
      if (menuOpenList) {
        menuContainerList.classList.add('open');
        gsap.fromTo(menuContainerList, {opacity: 0, y: -20}, {duration: 0.5, opacity: 1, y: 0});
        menuToggleList.innerHTML = '<i class="bi bi-x"></i>';
      } else {
        gsap.to(menuContainerList, {duration: 0.3, opacity: 0, y: -20, onComplete: function() {
          menuContainerList.classList.remove('open');
        }});
        menuToggleList.innerHTML = '<i class="bi bi-list"></i>';
      }
    });
    gsap.to(".header", {duration: 1, opacity: 1, ease: "power3.out"});
  </script>
</body>
</html>
