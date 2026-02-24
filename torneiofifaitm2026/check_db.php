<?php
require_once "db_connect.php";

function showSchema($conn, $table) {
    echo "\nSchema for $table:\n";
    $res = $conn->query("DESCRIBE $table");
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
}

showSchema($conn, 'users');
showSchema($conn, 'inscricoes');
?>

