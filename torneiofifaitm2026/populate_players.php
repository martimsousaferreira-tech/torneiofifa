<?php
require_once "db_connect.php";

$allowed_anos = ['10º', '11º', '12º'];
$allowed_cursos = ['AM', 'AGD', 'AQB', 'CGE', 'CM', 'DP-AE', 'EIA', 'ETC', 'ITM', 'MDI', 'TDS', 'TSA', 'TSI'];

// Get current count
$res = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
$row = $res->fetch_assoc();
$current_total = $row['total'];

$to_add = 31 - $current_total;

echo "Current inscriptions: $current_total. Adding $to_add more...\n";

for ($i = 0; $i < $to_add; $i++) {
    $nome = "Player " . ($current_total + $i + 1);
    $email = "player" . ($current_total + $i + 1) . "@example.com";
    $password = password_hash("password123", PASSWORD_DEFAULT);
    $ano = $allowed_anos[array_rand($allowed_anos)];
    $curso = $allowed_cursos[array_rand($allowed_cursos)];
    $turma = "$ano $curso";
    $numero = rand(1, 30);

    // Create user
    $stmt = $conn->prepare("INSERT INTO users (nome, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $password);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();

    // Create inscription
    $stmt = $conn->prepare("INSERT INTO inscricoes (user_id, nome, turma, numero, email, pago) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("issis", $user_id, $nome, $turma, $numero, $email);
    $stmt->execute();
    $stmt->close();
}

echo "Done! Total inscriptions should now be 31.\n";
?>
