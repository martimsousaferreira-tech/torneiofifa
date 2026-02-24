<?php
session_start();
unset($_SESSION['admin_auth']);
header("Location: backoffice.php");
exit;
?>
