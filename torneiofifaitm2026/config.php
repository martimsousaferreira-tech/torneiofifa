<?php
$servername = "shortline.proxy.rlwy.net"; // host público do Railway
$port       = 52908;                       // porta pública
$username   = "root";                       // user da DB
$password   = "HUarSvHYQiPxsSAkNjkQCeJELPBiteou"; // password da DB
$dbname     = "torneiofifa";                    // nome da DB

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verifica conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define charset UTF-8
$conn->set_charset("utf8mb4");
?>