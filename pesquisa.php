<?php
session_start();
// Aqui, a página de pesquisa pode ser acessada por qualquer usuário logado
include('db.php');
$query = $_GET['query'] ?? '';
$sql = "SELECT id, nome, login, nivel FROM utilizadores WHERE nivel IN ('tecnico', 'administrador')";
if (!empty($query)) {
  $query_like = "%$query%";
  $sql .= " AND (nome LIKE ? OR login LIKE ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $query_like, $query_like);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Pesquisa de Técnicos e Admins</title>
  <link rel="stylesheet" href="style-admin.css"> <!-- Página neutra ou de admin -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
</head>
<body>
  <div class="container fade-in">
    <h1>Pesquisa de Técnicos e Administradores</h1>
    <form action="pesquisa.php" method="GET">
      <input type="text" name="query" placeholder="Pesquisar por nome ou login" value="<?php echo htmlspecialchars($query); ?>">
      <button type="submit" class="btn">Pesquisar</button>
    </form>
    <hr>
    <?php if($result && $result->num_rows > 0): ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Login</th>
          <th>Nível</th>
          <th>Ação</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id']; ?></td>
          <td><?php echo htmlspecialchars($row['nome']); ?></td>
          <td><?php echo htmlspecialchars($row['login']); ?></td>
          <td><?php echo $row['nivel']; ?></td>
          <td>
            <a href="ver_perfil.php?id=<?php echo $row['id']; ?>" class="btn">Ver Perfil</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>Nenhum resultado encontrado.</p>
    <?php endif; ?>
  </div>
</body>
</html>
