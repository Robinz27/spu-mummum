<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPU MUM-MUM - Dashboard</title>

    <link rel="icon" type="image/x-icon" href="assets/mascot.png">
    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-header">
        <div class="dashboard-text">
            <h2><i class="ri-thumb-up-line"></i> สวัสดี <?php echo $user['username']; ?></h2>
            <p>เมนูแนะนำวันนี้! ></p>
        </div>

        <div class="dashboard-avatar">
            <div class="avatar-circle"></div>
            <img src="assets/mascot.png" alt="Mascot">
        </div>
    </div>

    <div class="menu-wrapper">
        <a href="restaurants" class="menu-card">
            <i class="ri-restaurant-line"></i>
            <p class="dashboard-p">ร้านอาหาร</p>
        </a>

        <a href="popular" class="menu-card">
            <i class="ri-award-line"></i>
            <p class="dashboard-p">ยอดนิยม</p>
        </a>
    </div>

    <div class="big-box"></div>

    <a href="api/logout" style="margin:20px; display:block; text-align:center;">Logout</a>

</body>
</html>
