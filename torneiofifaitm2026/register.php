<?php
session_start();
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        $error = "Este email já está registado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (nome, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $email, $password_hash);
        
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $nome;
            header("Location: ./");
            exit;
        } else {
            $error = "Erro ao criar conta.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar - Torneio Expo FC26</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="height: 100vh; display: flex; align-items: center; justify-content: center; background: #09090b;">
    <div class="form-container" style="width: 100%; max-width: 400px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Criar Conta</h2>
        <?php if(isset($error)) echo "<p style='color: #ef4444; text-align: center; margin-bottom: 1rem;'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn form-submit">Registar</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: var(--text-secondary);">
            Já tens conta? <a href="login" style="color: var(--accent-color);">Login</a>
        </p>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="./" style="color: var(--text-secondary); font-size: 0.9rem;">Voltar ao site</a>
        </p>
    </div>
</body>
</html>
