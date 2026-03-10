<?php
// Detetar se estamos no Railway (Produção) ou no PC local (WAMP)
$is_railway = getenv('MYSQLHOST') !== false;

if ($is_railway) {
    $servername = getenv('MYSQLHOST');
    $port       = getenv('MYSQLPORT');
    $username   = getenv('MYSQLUSER');
    $password   = getenv('MYSQLPASSWORD');
    $dbname     = getenv('MYSQLDATABASE');
} else {
    // Dados para ligação local (WAMP)
    $servername = "localhost"; 
    $port       = 3306;
    $username   = "root";
    $password   = "";
    $dbname     = "torneiofifa"; 
}

// Configurar o MySQLi para reportar erros mas não mandar o site abaixo com exceções fatais logo de início
mysqli_report(MYSQLI_REPORT_OFF);

// Criar a ligação
$conn = @new mysqli($servername, $username, $password, $dbname, $port);

// Verifica ligação com uma mensagem informativa
if ($conn->connect_error) {
    echo "<div style='background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 1rem; border-radius: 8px; font-family: sans-serif; margin: 10px;'>";
    echo "<h3>⚠️ Erro de Ligação à Base de Dados</h3>";
    echo "<p>Não foi possível estabelecer ligação ao servidor do Railway.</p>";
    echo "<ul>";
    echo "<li><b>Host:</b> $servername</li>";
    echo "<li><b>Porta:</b> $port</li>";
    echo "<li><b>Erro:</b> " . htmlspecialchars($conn->connect_error) . "</li>";
    echo "</ul>";
    echo "<p><b>Como resolver:</b> No teu dashboard do Railway, clica no serviço MySQL e verifica se existe um <b>triângulo amarelo de aviso</b>. Se sim, tenta <b>reiniciar (Restart)</b> o serviço ou <b>re-gerar o Public TCP Proxy</b>.</p>";
    echo "</div>";
    die();
}

// Define charset UTF-8
$conn->set_charset("utf8mb4");
?>

