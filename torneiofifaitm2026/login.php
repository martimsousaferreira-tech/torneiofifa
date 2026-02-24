<?php
session_start();
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, nome, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            
            // Handle Admin Status safely
            $_SESSION['is_admin'] = isset($user['is_admin']) && $user['is_admin'] == 1;

            header("Location: index.php");
            exit;
        } else {
            $error = "A password está incorreta.";
        }
    } else {
        $error = "Não existe conta com esse email.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Torneio Expo FC26</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="height: 100vh; display: flex; align-items: center; justify-content: center; background: #09090b;">
    <div class="form-container" style="width: 100%; max-width: 400px;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Login</h2>
        <?php if(isset($error)) echo "<p style='color: #ef4444; text-align: center; margin-bottom: 1rem;'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn form-submit">Entrar</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: var(--text-secondary);">
            Ainda não tens conta? <a href="register.php" style="color: var(--accent-color);">Registar</a>
        </p>
        <p style="text-align: center; margin-top: 1rem;">
            <a href="index.php" style="color: var(--text-secondary); font-size: 0.9rem;">Voltar ao site</a>
        </p>
    </div>
</body>
</html>
