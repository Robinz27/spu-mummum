<?php
session_start();
include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

// ดึง user มาเช็ค
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

if (mysqli_num_rows($query) == 1) {

    $user = mysqli_fetch_assoc($query);

    if (password_verify($password, $user['password'])) {

        // เก็บข้อมูลลง session
        $_SESSION['user'] = [
            "id" => $user['id'],
            "email" => $user['email'],
            "username" => $user['username']
        ];

        header("Location: ../dashboard");
        exit;
        
    } else {
        echo "wrong_password";
    }

} else {
    echo "no_user";
}
?>
