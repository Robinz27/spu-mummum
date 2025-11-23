<?php
include "db.php";
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่า login แล้วหรือยัง
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'error' => 'กรุณาเข้าสู่ระบบก่อนรีวิว']);
        exit;
    }

    $restaurant_id = mysqli_real_escape_string($conn, $_POST['restaurant_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $user_name = $_SESSION['user']['username']; // เอาชื่อจาก session
    
    // บันทึกชื่อเต็มลงฐานข้อมูล (ไม่เบลอ)
    $query = "INSERT INTO reviews (restaurant_id, user_name, rating, comment) 
              VALUES ('$restaurant_id', '$user_name', '$rating', '$comment')";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>