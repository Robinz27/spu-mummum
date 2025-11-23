<?php
include "api/db.php";

// Query ร้านอาหารพร้อมคะแนนเฉลี่ยจากรีวิวจริง
$query = mysqli_query($conn, "
    SELECT 
        r.*,
        COALESCE(AVG(rv.rating), 0) as avg_rating,
        COUNT(rv.id) as review_count
    FROM restaurants r
    LEFT JOIN reviews rv ON r.id = rv.restaurant_id
    GROUP BY r.id
    ORDER BY r.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPU MUM-MUM - Restaurants</title>

    <link rel="icon" type="image/x-icon" href="assets/mascot.png">
    <link rel="stylesheet" href="style.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        /* เพิ่ม style สำหรับแสดงคะแนน */
        .rating {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        
        .rating-number {
            margin-left: 6px;
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        
        .review-count {
            font-size: 12px;
            color: #999;
            margin-left: 4px;
        }
        
        .ri-star-line {
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="dashboard-header">
    <div class="dashboard-text">
        <h2><i class="ri-restaurant-2-line"></i> ร้านอาหารทั้งหมด</h2>
        <p>เลือกเมนูที่ต้องการ ></p>
    <button class="back-btn" onclick="window.location.href='dashboard'">
        <i class="ri-arrow-left-line"></i> back
    </button>
    </div>

    <div class="dashboard-avatar">
        <div class="avatar-circle"></div>
        <img src="assets/mascot.png" alt="Mascot">
    </div>
</div>

<!-- LIST ZONE -->
<div class="food-list">
<?php while ($row = mysqli_fetch_assoc($query)) : ?>

    <div class="food-card">

        <img src="uploads/<?= $row['image'] ?>" alt="food">

        <div class="food-info">
            <div class="food-name"><?= $row['name'] ?></div>
            <div class="food-type"><?= $row['type'] ?></div>

            <div class="rating">
                <?php 
                    // ใช้คะแนนเฉลี่ยจากรีวิวจริง
                    $avg_rating = round($row['avg_rating']); // ปัดเศษเป็นเลขเต็ม
                    $exact_rating = number_format($row['avg_rating'], 1); // คะแนนแบบทศนิยม
                    
                    // แสดงดาว
                    for ($i = 1; $i <= 5; $i++) {
                        echo ($i <= $avg_rating) 
                            ? "<i class='ri-star-fill'></i>" 
                            : "<i class='ri-star-line'></i>";
                    }
                ?>
                
                <!-- แสดงคะแนนตัวเลข -->
                <?php if ($row['review_count'] > 0): ?>
                    <span class="rating-number">(<?= $exact_rating ?>)</span>
                    <span class="review-count"><?= $row['review_count'] ?> รีวิว</span>
                <?php else: ?>
                    <span class="rating-number">(ยังไม่มีรีวิว)</span>
                <?php endif; ?>
            </div>

            <button class="more-btn" 
                    onclick="window.location.href='restaurant/<?= $row['id'] ?>'">
                <i class="ri-information-line"></i> more info
            </button>

        </div>

    </div>

<?php endwhile; ?>
</div>

</body>
</html>