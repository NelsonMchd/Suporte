
<?php
// register.php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registre-se - Gestão de Ocorrências de Tickets</title>
  <!-- Google Fonts (Poppins) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- GSAP para animações -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <style>
    /* Mantém o mesmo estilo de fundo e modal do login */
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Poppins', sans-serif;
    }
    .video-bg {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
      filter: brightness(0.2);
    }
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }
    .modal-content {
      background: #1a202c;
      color: white;
      border: none;
      border-radius: 1rem;
    }
    .btn-register {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      border: none;
    }
    .btn-register:hover {
      background: linear-gradient(to right, #2563eb, #7c3aed);
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center text-white">
  <!-- Fundo com vídeo -->
  <video class="video-bg" autoplay loop muted>
    <source src="DJI_0263.MP4" type="video/mp4">
    Seu navegador não suporta o elemento de vídeo.
  </video>
  <!-- Overlay para escurecer o fundo -->
  <div class="overlay"></div>

  <!-- Modal de Registre-se -->
  <div class="modal fade show" id="registerModal" tabindex="-1" style="display: block;" aria-labelledby="registerModalLabel" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content p-4">
        <div class="modal-header border-0">
          <!-- Botão para fechar e retornar à página principal -->
          <a href="index.html" class="btn-close" aria-label="Close"></a>
        </div>
        <div class="modal-body">
          <form action="register_process.php" method="POST">
            <div class="mb-3">
              <label for="nome" class="form-label">Nome</label>
              <input type="text" class="form-control" id="nome" name="nome" placeholder="Seu nome completo" required>
            </div>
            <div class="mb-3">
              <label for="login" class="form-label">Login</label>
              <input type="text" class="form-control" id="login" name="login" placeholder="Escolha um usuário" required>
            </div>
            <div class="mb-3">
              <label for="pass" class="form-label">Senha</label>
              <input type="password" class="form-control" id="pass" name="pass" placeholder="Crie uma senha" required>
            </div>
            <div class="mb-3">
              <label for="confirm" class="form-label">Confirmar Senha</label>
              <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirme sua senha" required>
            </div>
            <button type="submit" class="btn btn-register w-100">Registrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    gsap.from('.modal-content', { duration: 1, y: -50, opacity: 0, ease: 'power2.out' });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>