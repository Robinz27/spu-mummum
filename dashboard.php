<?php
session_start();
include "api/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: index");
    exit();
}
$user = $_SESSION['user'];

// ดึงประกาศล่าสุด 5 รายการ
$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

// ดึงร้านแนะนำแบบสุ่ม 5 ร้าน
$random_restaurants = mysqli_query($conn, "
    SELECT 
        r.*,
        COALESCE(AVG(rv.rating), 0) as avg_rating,
        COUNT(rv.id) as review_count
    FROM restaurants r
    LEFT JOIN reviews rv ON r.id = rv.restaurant_id
    GROUP BY r.id
    ORDER BY RAND()
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPU MUM-MUM - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="assets/mascot.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Big Box - Announcements */
        .big-box {
            background: #fff;
            margin: 30px 20px;
            height: auto;
            border-radius: 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
            overflow: hidden;
        }

        /* Header */
        .announcement-header {
            background: #cf8f4f;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .announcement-header i {
            font-size: 26px;
            color: #fff;
        }
        .announcement-header h3 {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        /* List */
        .announcement-list {
            max-height: 520px;
            overflow-y: auto;
            padding: 18px;
        }

        .announcement-item {
            padding: 20px;
            border-radius: 18px;
            background: #fafafa;
            margin-bottom: 16px;
            border: 1px solid #f0f0f0;
            transition: 0.2s;
        }
        .announcement-item:hover {
            background: #fff;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        }

        /* Top Meta */
        .announcement-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .announcement-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fff4e6;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .announcement-avatar img {
            width: 40px;
            height: 40px;
        }

        .announcement-name {
            font-size: 16px;
            font-weight: 650;
            color: #333;
        }
        .announcement-date {
            font-size: 13px;
            color: #999;
        }

        /* Title & Content */
        .announcement-title {
            font-size: 17px;
            font-weight: 700;
            color: #cf8f4f;
            margin-bottom: 8px;
        }
        .announcement-content {
            font-size: 15px;
            color: #555;
            line-height: 1.7;
        }

        /* Responsive Image */
        .announcement-image {
            width: 50%;
            height: 50%;
            object-fit: cover;
            border-radius: 16px;
            margin-top: 12px;
        }

        @media (max-width: 768px) {
            .announcement-image {
                width: 100%;
                height: auto;
                object-fit: cover;
                border-radius: 16px;
                margin-top: 12px;
            }
        }

        /* Empty State */
        .announcement-empty {
            text-align: center;
            padding: 70px 20px;
            color: #aaa;
        }
        .announcement-empty i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 18px;
        }

        /* Recommended Section */
        .section-header {
            padding: 0 20px;
            margin: 30px 0 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-link {
            font-size: 14px;
            color: #cf8f4f;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .recommended-list {
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .recommended-card {
            background: #fff;
            border-radius: 20px;
            padding: 15px;
            display: flex;
            gap: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-decoration: none;
            color: inherit;
            transition: 0.3s;
        }

        .recommended-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        .recommended-img {
            width: 100px;
            height: 100px;
            border-radius: 15px;
            object-fit: cover;
        }

        .recommended-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .recommended-name {
            font-size: 18px;
            font-weight: 600;
            color: #cf8f4f;
            margin-bottom: 4px;
        }

        .recommended-type {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }

        .recommended-rating {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .recommended-rating i {
            font-size: 14px;
            color: #ffcc33;
        }

        .recommended-rating i.empty {
            color: #ddd;
        }

        .rating-text {
            font-size: 12px;
            color: #666;
            margin-left: 5px;
        }

        .logout-btn {
            margin: 40px 20px 20px;
            display: block;
            text-align: center;
            padding: 12px;
            background: #f5f5f5;
            color: #666;
            text-decoration: none;
            border-radius: 15px;
            font-size: 15px;
        }
    </style>
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

    <!-- Announcements Box -->
    <div class="big-box">
        <div class="announcement-header">
            <i class="ri-notification-3-fill"></i>
            <h3>ประกาศและกิจกรรม</h3>
        </div>
        
        <div class="announcement-list">
            <?php if (mysqli_num_rows($announcements) > 0): ?>
                <?php while ($announcement = mysqli_fetch_assoc($announcements)): ?>
                    <div class="announcement-item">
                        <div class="announcement-top">
                            <div class="announcement-avatar">
                                <img src="assets/mascot.png" alt="SPU MUM-MUM">
                            </div>
                            <div class="announcement-meta">
                                <div class="announcement-name">SPU MUM-MUM</div>
                                <div class="announcement-date">
                                    <?php
                                    $date = new DateTime($announcement['created_at']);
                                    echo $date->format('j M Y • H:i');
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                        <div class="announcement-content"><?= nl2br(htmlspecialchars($announcement['content'])) ?></div>
                        
                        <?php if (!empty($announcement['image'])): ?>
                            <img src="uploads/<?= $announcement['image'] ?>" alt="Announcement" class="announcement-image">
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="announcement-empty">
                    <i class="ri-notification-off-line"></i>
                    <p>ยังไม่มีประกาศในขณะนี้</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recommended Restaurants -->
    <div class="section-header">
        <div class="recommended-name">
            <i class="ri-store-2-line"></i>
            ร้านแนะนำ
        </div>
        <a href="restaurants" class="section-link">
            ดูทั้งหมด <i class="ri-arrow-right-s-line"></i>
        </a>
    </div>

    <div class="recommended-list">
        <?php while ($restaurant = mysqli_fetch_assoc($random_restaurants)): ?>
            <a href="restaurant/<?= $restaurant['id'] ?>" class="recommended-card">
                <img src="uploads/<?= $restaurant['image'] ?>" alt="<?= $restaurant['name'] ?>" class="recommended-img">
                
                <div class="recommended-info">
                    <div>
                        <div class="recommended-name"><?= htmlspecialchars($restaurant['name']) ?></div>
                        <div class="recommended-type"><?= htmlspecialchars($restaurant['type']) ?></div>
                    </div>
                    
                    <div class="recommended-rating">
                        <?php 
                        $avg = round($restaurant['avg_rating']);
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $avg) {
                                echo '<i class="ri-star-fill"></i>';
                            } else {
                                echo '<i class="ri-star-fill empty"></i>';
                            }
                        }
                        ?>
                        <?php if ($restaurant['review_count'] > 0): ?>
                            <span class="rating-text">(<?= number_format($restaurant['avg_rating'], 1) ?>) • <?= $restaurant['review_count'] ?> รีวิว</span>
                        <?php else: ?>
                            <span class="rating-text">(ยังไม่มีรีวิว)</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>

    <a href="api/logout" class="logout-btn">
        <i class="ri-logout-box-line"></i> ออกจากระบบ
    </a>
</body>
</html>