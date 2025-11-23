<?php
session_start();
include "db.php";

$email = $_POST['email'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// เช็ค email ซ้ำ
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
if (mysqli_num_rows($check) > 0) {
    echo "exist";
    exit;
}

// บันทึกข้อมูล
$sql = "INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$password')";
if (mysqli_query($conn, $sql)) {

    $id = mysqli_insert_id($conn);

    // เก็บ session
    $_SESSION['user'] = [
        "id" => $id,
        "email" => $email,
        "username" => $username
    ];

    header("Location: ../dashboard");
    exit();

} else {
    echo "error";
}
?>