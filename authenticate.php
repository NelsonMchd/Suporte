<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtém os dados do formulário
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    
    // As senhas na tabela utilizadores estão em MD5
    $passwordHash = md5($password);
    
    // Consulta para verificar se o usuário existe, está ativo e com as credenciais corretas
    $sql = "SELECT * FROM utilizadores WHERE login = ? AND pass = ? AND status = 'ativo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $login, $passwordHash);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        // Inicializa a sessão com os dados do usuário
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_nome']  = $user['nome'];
        $_SESSION['user_nivel'] = $user['nivel'];
        
        // Redireciona para a dashboard de acordo com o nível
        if ($user['nivel'] == 'administrador') {
            header("Location: dashboard_admin.php");
        } elseif ($user['nivel'] == 'tecnico') {
            header("Location: dashboard_tecnico.php");
        } elseif ($user['nivel'] == 'utilizador') {
            header("Location: dashboard_user.php");
        } else {
            header("Location: dashboard_user.php");
        }
        exit();
    } else {
        echo "<script>alert('Login ou senha incorretos!'); window.location.href = 'login.php';</script>";
    }
}
?>