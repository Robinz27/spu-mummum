<?php
session_start();
include "db.php";

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// เช็ค email ซ้ำ
$check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
if (mysqli_num_rows($check) > 0) {
    echo "exist";
    exit;
}

// บันทึกข้อมูล
$sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
if (mysqli_query($conn, $sql)) {

    $id = mysqli_insert_id($conn);

    // เก็บ session
    $_SESSION['user'] = [
        "id" => $id,
        "email" => $email
    ];

    header("Location: ../dashboard.php");
    exit();

} else {
    echo "error";
}
?>
