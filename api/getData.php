<?php
include "db.php";

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

$sql = "INSERT INTO users (`username`, `password`, `email`) VALUES ('$username', '$password', '$email')";
$result = $conn->query($sql);

echo $result ? "success" : "error";
?>
