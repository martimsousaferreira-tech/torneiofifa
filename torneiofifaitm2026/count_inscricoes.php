<?php
require_once "db_connect.php";
$res = $conn->query("SELECT COUNT(*) as total FROM inscricoes");
echo "Total inscricoes: " . $res->fetch_assoc()['total'] . "\n";
?>
