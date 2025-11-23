<?php
include "../api/db.php";

if (!isset($_GET['id'])) {
    die("ไม่พบร้านค้า");
}

$restaurant_id = $_GET['id'];

// ดึงข้อมูลร้าน
$res = mysqli_query($conn, "SELECT * FROM restaurants WHERE id = $restaurant_id");
$restaurant = mysqli_fetch_assoc($res);

if (!$restaurant) {
    die("ไม่พบข้อมูลร้าน");
}

// ดึงเมนูทั้งหมดของร้าน
$menu_query = mysqli_query($conn, "SELECT * FROM restaurant_menu WHERE restaurant_id = $restaurant_id");
$menu_count = mysqli_num_rows($menu_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPU MUM-MUM -<?= $restaurant['name'] ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/mascot.png">
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* CSS สำหรับ empty state */
        .menu-empty {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 30px;
            margin: 0 20px 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .menu-empty i {
            font-size: 100px;
            color: #cf8f4f;
            opacity: 0.3;
            margin-bottom: 25px;
        }

        .menu-empty h3 {
            font-size: 22px;
            color: #cf8f4f;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .menu-empty p {
            font-size: 16px;
            color: #999;
        }
    </style>
</head>
<body>

<div class="dashboard-header">
    <div class="dashboard-text">
        <h2>เมนูอาหารของ <?= $restaurant['name'] ?></h2>
        <p>เลือกเมนูที่ต้องการ ></p>
    <button class="back-btn" onclick="window.location.href='../restaurants'">
        <i class="ri-arrow-left-line"></i> back
    </button>
    </div>

    <div class="dashboard-avatar">
        <div class="avatar-circle"></div>
        <img src="../assets/mascot.png" alt="Mascot">
    </div>
</div>

<div class="food-list">
    <div class="food-card">
        <img src="../uploads/<?= $restaurant['image'] ?>" class="header-img">
        <div class="info">
            <h2 class="food-name"><?= $restaurant['name'] ?></h2>
            <p><?= $restaurant['type'] ?></p>
                <div class="rating">
                    <?php 
                        for ($i=1; $i <= $restaurant['rating']; $i++) {
                            echo "<i class='ri-star-fill'></i>";
                        }
                    ?>
                </div>
            <button class="more-btn" onclick="window.location.href='<?= $restaurant_id ?>/review'">
        review
    </button>
        </div>
    </div>
</div>

<div class="menu-text">
    <h2 class="food-name">เมนูทั้งหมด (<?= $menu_count ?>)</h2>
</div>

<?php if ($menu_count > 0): ?>
    <div class="food-list">
        <div class="food-card">
            <?php while ($menu = mysqli_fetch_assoc($menu_query)) : ?>
                <div class="food-card-menu">
                <img src="../uploads/<?= $menu['image'] ?>" alt="food" class="header-img">
                <div class="info">
                    <div class="food-name"><?= $menu['menu_name']; ?></div>
                    <div class="food-type"><?= $menu['price'] ?> บาท</div>
                </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php else: ?>
    <!-- แสดงเมื่อยังไม่มีเมนู -->
    <div class="menu-empty">
        <i class="ri-restaurant-2-line"></i>
        <h3>ยังไม่มีเมนูของร้านในขณะนี้</h3>
        <p>ร้านนี้ยังไม่ได้เพิ่มเมนูอาหาร กรุณากลับมาใหม่ภายหลัง</p>
    </div>
<?php endif; ?>

</body>
</html>