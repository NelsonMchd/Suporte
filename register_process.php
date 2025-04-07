<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome    = trim($_POST['nome']);
    $login   = trim($_POST['login']);
    $pass    = trim($_POST['pass']);
    $confirm = trim($_POST['confirm']);

    if (empty($nome) || empty($login) || empty($pass) || empty($confirm)) {
        $_SESSION['register_error'] = "Todos os campos são obrigatórios.";
        header("Location: index.html");
        exit();
    } elseif ($pass !== $confirm) {
        $_SESSION['register_error'] = "As senhas não conferem.";
        header("Location: index.html");
        exit();
    } else {
        // Verifica se o login já existe
        $stmt = $conn->prepare("SELECT id FROM utilizadores WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $_SESSION['register_error'] = "Login já existe. Por favor, escolha outro.";
            header("Location: index.html");
            exit();
        } else {
            // Insere o novo usuário com status 'inativo' e nível 'utilizador'
            $passHash = md5($pass);
            $status   = 'inativo';
            $nivel    = 'utilizador';
            $stmt2 = $conn->prepare("INSERT INTO utilizadores (nome, login, pass, status, nivel) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("sssss", $nome, $login, $passHash, $status, $nivel);
            if ($stmt2->execute()) {
                // Define uma mensagem de sucesso e redireciona para a página principal
                $_SESSION['register_success'] = "Cadastro realizado com sucesso! Aguarde a aprovação do administrador.";
                header("Location: index.html");
                exit();
            } else {
                $_SESSION['register_error'] = "Erro ao cadastrar: " . $conn->error;
                header("Location: index.html");
                exit();
            }
        }
    }
} else {
    header("Location: index.html");
    exit();
}
?>