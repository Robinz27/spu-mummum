<?php
$host = "localhost";
$user = "root"; 
$pass = "";
$db   = "spu_mummum";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed");
}

mysqli_set_charset($conn, "utf8");
?>
