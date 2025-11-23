<?php
session_start();
include "api/db.php";

// Query ร้านอาหารพร้อมสถิติรีวิว
$query = "
    SELECT 
        r.*,
        COUNT(rv.id) as review_count,
        COALESCE(AVG(rv.rating), 0) as avg_rating
    FROM restaurants r
    LEFT JOIN reviews rv ON r.id = rv.restaurant_id
    GROUP BY r.id
    ORDER BY review_count DESC, avg_rating DESC
    LIMIT 20
";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ร้านอาหารยอดนิยม - SPU MUM-MUM</title>
    <link rel="icon" type="image/x-icon" href="assets/mascot.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* Popular Page Styles */
        .popular-tabs {
            display: flex;
            gap: 10px;
            padding: 0 20px;
            margin-top: 25px;
            margin-bottom: 25px;
            overflow-x: auto;
        }

        .popular-tab {
            flex: 1;
            min-width: 140px;
            background: #fff;
            padding: 12px 20px;
            border-radius: 25px;
            text-align: center;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: 0.3s;
            font-size: 15px;
            color: #666;
        }

        .popular-tab.active {
            background: #cf8f4f;
            color: #fff;
            border-color: #cf8f4f;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-weight: 600;
            font-size: 16px;
            margin-right: 10px;
        }

        .rank-badge.gold {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #b8860b;
            box-shadow: 0 3px 10px rgba(255, 215, 0, 0.4);
        }

        .rank-badge.silver {
            background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
            color: #707070;
            box-shadow: 0 3px 10px rgba(192, 192, 192, 0.4);
        }

        .rank-badge.bronze {
            background: linear-gradient(135deg, #cd7f32 0%, #e39b5f 100%);
            color: #8b4513;
            box-shadow: 0 3px 10px rgba(205, 127, 50, 0.4);
        }

        .rank-badge.other {
            background: #f5f5f5;
            color: #999;
        }

        .popular-stats {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            color: #666;
            background: #f9f9f9;
            padding: 4px 10px;
            border-radius: 12px;
        }

        .stat-item i {
            color: #cf8f4f;
        }

        .crown-icon {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 24px;
            color: #ffd700;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .popular-restaurant-card {
            position: relative;
        }

        .empty-popular {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 30px;
            margin: 0 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .empty-popular i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-popular h3 {
            font-size: 20px;
            color: #999;
            margin-bottom: 10px;
        }

        .empty-popular p {
            font-size: 15px;
            color: #bbb;
        }
    </style>
</head>
<body>
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-text">
            <h2><i class="ri-fire-fill"></i> ร้านอาหารยอดนิยม</h2>
            <p>จัดอันดับจากรีวิวและคะแนนดาว ></p>
            <button class="back-btn" onclick="document.referrer ? history.back() : window.location.href='restaurants'">
                <i class="ri-arrow-left-line"></i> back
            </button>
        </div>

        <div class="dashboard-avatar">
            <div class="avatar-circle"></div>
            <img src="assets/mascot.png" alt="Mascot">
        </div>
    </div>

    <div class="popular-tabs">
        <div class="popular-tab active" onclick="filterByReviews()">
            <i class="ri-chat-3-fill"></i> รีวิวมากสุด
        </div>
        <div class="popular-tab" onclick="filterByRating()">
            <i class="ri-star-fill"></i> คะแนนสูงสุด
        </div>
    </div>

    <div class="food-list" id="restaurantList">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php 
            $rank = 1;
            while ($restaurant = mysqli_fetch_assoc($result)): 
                $badge_class = 'other';
                if ($rank == 1) $badge_class = 'gold';
                elseif ($rank == 2) $badge_class = 'silver';
                elseif ($rank == 3) $badge_class = 'bronze';
            ?>
                <div class="food-card popular-restaurant-card" 
                     data-reviews="<?= $restaurant['review_count'] ?>" 
                     data-rating="<?= number_format($restaurant['avg_rating'], 1) ?>">
                    <?php if ($rank == 1): ?>
                        <i class="ri-vip-crown-fill crown-icon"></i>
                    <?php endif; ?>
                    
                    <span class="rank-badge <?= $badge_class ?>"><?= $rank ?></span>
                    
                    <img src="uploads/<?= $restaurant['image'] ?>" alt="<?= $restaurant['name'] ?>">
                    
                    <div class="food-info">
                        <div class="food-name"><?= htmlspecialchars($restaurant['name']) ?></div>
                        <div class="food-type"><?= htmlspecialchars($restaurant['type']) ?></div>
                        
                        <div class="rating">
                            <?php 
                            $avg = round($restaurant['avg_rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avg) {
                                    echo '<i class="ri-star-fill"></i>';
                                } else {
                                    echo '<i class="ri-star-line" style="color: #ddd;"></i>';
                                }
                            }
                            ?>
                            <span style="margin-left: 5px; font-size: 13px; color: #666;">
                                (<?= number_format($restaurant['avg_rating'], 1) ?>)
                            </span>
                        </div>

                        <div class="popular-stats">
                            <div class="stat-item">
                                <i class="ri-chat-3-line"></i>
                                <span><?= $restaurant['review_count'] ?> รีวิว</span>
                            </div>
                            <div class="stat-item">
                                <i class="ri-star-line"></i>
                                <span><?= number_format($restaurant['avg_rating'], 1) ?> ดาว</span>
                            </div>
                        </div>

                        <button class="more-btn" onclick="window.location.href='restaurant/<?= $restaurant['id'] ?>'">
                            ดูเมนู
                        </button>
                    </div>
                </div>
            <?php 
            $rank++;
            endwhile; 
            ?>
        <?php else: ?>
            <div class="empty-popular">
                <i class="ri-restaurant-line"></i>
                <h3>ยังไม่มีข้อมูลร้านอาหาร</h3>
                <p>กรุณารอสักครู่ ระบบกำลังรวบรวมข้อมูล</p>
            </div>
        <?php endif; ?>
    </div>

    <div style="height: 80px;"></div>

    <script>
        function filterByReviews() {
            // เรียงตามจำนวนรีวิว
            const tabs = document.querySelectorAll('.popular-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            tabs[0].classList.add('active');

            const cards = Array.from(document.querySelectorAll('.popular-restaurant-card'));
            cards.sort((a, b) => {
                const reviewsA = parseInt(a.dataset.reviews);
                const reviewsB = parseInt(b.dataset.reviews);
                return reviewsB - reviewsA;
            });

            const list = document.getElementById('restaurantList');
            cards.forEach((card, index) => {
                const badge = card.querySelector('.rank-badge');
                badge.textContent = index + 1;
                
                // อัปเดตสี badge
                badge.className = 'rank-badge ';
                if (index === 0) badge.classList.add('gold');
                else if (index === 1) badge.classList.add('silver');
                else if (index === 2) badge.classList.add('bronze');
                else badge.classList.add('other');

                // อัปเดต crown
                const crown = card.querySelector('.crown-icon');
                if (crown) crown.remove();
                if (index === 0) {
                    const newCrown = document.createElement('i');
                    newCrown.className = 'ri-vip-crown-fill crown-icon';
                    card.insertBefore(newCrown, card.firstChild);
                }

                list.appendChild(card);
            });
        }

        function filterByRating() {
            // เรียงตามคะแนนดาว
            const tabs = document.querySelectorAll('.popular-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            tabs[1].classList.add('active');

            const cards = Array.from(document.querySelectorAll('.popular-restaurant-card'));
            cards.sort((a, b) => {
                const ratingA = parseFloat(a.dataset.rating);
                const ratingB = parseFloat(b.dataset.rating);
                if (ratingB !== ratingA) return ratingB - ratingA;
                // ถ้าคะแนนเท่ากันให้เรียงตามจำนวนรีวิว
                return parseInt(b.dataset.reviews) - parseInt(a.dataset.reviews);
            });

            const list = document.getElementById('restaurantList');
            cards.forEach((card, index) => {
                const badge = card.querySelector('.rank-badge');
                badge.textContent = index + 1;
                
                // อัปเดตสี badge
                badge.className = 'rank-badge ';
                if (index === 0) badge.classList.add('gold');
                else if (index === 1) badge.classList.add('silver');
                else if (index === 2) badge.classList.add('bronze');
                else badge.classList.add('other');

                // อัปเดต crown
                const crown = card.querySelector('.crown-icon');
                if (crown) crown.remove();
                if (index === 0) {
                    const newCrown = document.createElement('i');
                    newCrown.className = 'ri-vip-crown-fill crown-icon';
                    card.insertBefore(newCrown, card.firstChild);
                }

                list.appendChild(card);
            });
        }
    </script>
</body>
</html>