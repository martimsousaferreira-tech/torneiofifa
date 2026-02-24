<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "db_connect.php";

    $nome = isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '';
    $ano = isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : '';
    $turma_letra = isset($_POST['turma_letra']) ? htmlspecialchars($_POST['turma_letra']) : '';
    $turma = "$ano $turma_letra"; // Combine to store in DB
    $numero = isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : '';
    
    // Validation for years and courses
    $allowed_anos = ['10º', '11º', '12º'];
    $allowed_cursos = ['AM', 'AGD', 'AQB', 'CGE', 'CM', 'DP-AE', 'EIA', 'ETC', 'ITM', 'MDI', 'TDS', 'TSA', 'TSI'];

    if (!isset($_POST['agree_rules'])) {
        $message = "Precisas de aceitar o regulamento para te inscreveres.";
        $error = true;
    } elseif (strlen($nome) > 11) {
        $message = "O nome não pode exceder os 11 caracteres.";
        $error = true;
    } elseif (!in_array($ano, $allowed_anos) || !in_array($turma_letra, $allowed_cursos)) {
        $message = "Inscrição inválida. Apenas alunos do 10º ao 12º ano dos cursos técnicos especificados podem participar.";
        $error = true;
    } else {
        // Fetch email from users table
        $user_id = $_SESSION['user_id'];
        $u_stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $u_stmt->bind_param("i", $user_id);
        $u_stmt->execute();
        $u_result = $u_stmt->get_result();
        $user_data = $u_result->fetch_assoc();
        
        if (!$user_data) {
            $message = "Erro: Usuário não encontrado no sistema.";
            $error = true;
        } else {
            $email = $user_data['email'];

            // Check if user is already registered
            $check_user = $conn->prepare("SELECT id FROM inscricoes WHERE user_id = ?");
            $check_user->bind_param("i", $user_id);
            $check_user->execute();
            $check_user->store_result();
            
            if ($check_user->num_rows > 0) {
                $message = "Já te encontras inscrito no torneio!";
                $error = true;
            } else {
                // Check availability (limit 32)
                $result = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
                $row = $result->fetch_assoc();
                
                if ($row['total'] >= 32) {
                    $_SESSION['swal'] = [
                        'title' => 'Inscrições Cheias!',
                        'msg' => 'Desculpa, as inscrições já atingiram o limite de 32 jogadores.',
                        'type' => 'warning'
                    ];
                    header("Location: index.php#inscricao");
                    exit;
                } else {
                    // Prepare and bind
                    $stmt = $conn->prepare("INSERT INTO inscricoes (user_id, nome, turma, numero, email) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issis", $user_id, $nome, $turma, $numero, $email);

                    if ($stmt->execute()) {
                        $message = "Inscrição realizada com sucesso, $nome!";
                    } else {
                        $message = "Erro ao inscrever: " . $stmt->error;
                        $error = true;
                    }
                    $stmt->close();
                }
            }
        }
    }

    if (isset($error) && $error) {
        $_SESSION['toast'] = ['msg' => $message, 'type' => 'error'];
        header("Location: index.php#inscricao");
        exit;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($error) ? 'Erro na Inscrição' : 'Inscrição Confirmada'; ?> - Expo FC26</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #09090b; position: relative; overflow: hidden; padding: 20px;">
    
    <div class="hero-bg-accent" style="top: 50%; left: 50%;"></div>

    <div class="form-container" style="text-align: center; max-width: 600px; z-index: 1;">
        <?php if (isset($error) && $error): ?>
            <div style="margin-bottom: 2rem;">
                <i class="fas fa-times-circle" style="font-size: 5rem; color: #ef4444; text-shadow: 0 0 20px rgba(239, 68, 68, 0.3);"></i>
            </div>
            <h1 style="font-size: 3rem; margin-bottom: 1rem; color: #ef4444;">OOPS!</h1>
            <p style="font-size: 1.25rem; margin-bottom: 2rem; color: var(--text-secondary);"><?php echo $message; ?></p>
        <?php else: ?>
            <div style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle" style="font-size: 5rem; color: var(--accent-color); text-shadow: 0 0 20px var(--accent-glow);"></i>
            </div>
            <h1 style="font-size: 3rem; margin-bottom: 1rem;">SUCESSO!</h1>
            <p style="font-size: 1.25rem; margin-bottom: 0.5rem;"><?php echo $message; ?></p>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">A tua vaga está pré-reservada. Faz o pagamento para confirmar!</p>
            
            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 10px; border: 1px solid var(--glass-border); margin-bottom: 2.5rem; text-align: left;">
                <h4 style="margin-bottom: 0.8rem; color: var(--accent-color);"><i class="fas fa-info-circle"></i> PRÓXIMOS PASSOS:</h4>
                <ul style="color: var(--text-secondary); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li>1. Vais receber uma autorização para o teu EE assinar.</li>
                    <li>2. Entrega a autorização assinada e faz o pagamento (2€) ao staff do Torneio.</li>
                    <li>3. O teu nome aparecerá no bracket oficial!</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="index.php" class="btn">Voltar ao Início</a>
            <?php if(!isset($error)): ?>
                <a href="index.php#classificacoes" class="btn btn-outline" style="clip-path: none; border-radius: 5px;">Ver Bracket</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
