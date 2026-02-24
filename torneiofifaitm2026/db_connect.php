<?php
$servername = "localhost";
$username = "root"; // Default WampServer user
$password = "";     // Default WampServer password
$dbname = "torneiofifa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
