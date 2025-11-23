<?php
session_start();
include "../api/db.php";

// ตรวจสอบว่า login แล้วหรือยัง
if (!isset($_SESSION['user'])) {
    header("Location: ../../");
    exit;
}

if (!isset($_GET['id'])) {
    die("ไม่พบร้านค้า");
}

$id = $_GET['id'];

// ดึงข้อมูลร้าน
$res = mysqli_query($conn, "SELECT * FROM restaurants WHERE id=$id");
$restaurant = mysqli_fetch_assoc($res);

if (!$restaurant) {
    die("ไม่พบข้อมูลร้าน");
}

$reviews_query = mysqli_query($conn, "SELECT * FROM reviews WHERE restaurant_id=$id ORDER BY created_at DESC");
$review_count = mysqli_num_rows($reviews_query);

// ฟังก์ชันเบลอชื่อ
function blurName($name) {
    $name_length = mb_strlen($name);
    if ($name_length <= 2) {
        return str_repeat('*', $name_length);
    }
    $first = mb_substr($name, 0, 1);
    $last = mb_substr($name, -1);
    $stars = str_repeat('*', $name_length - 2);
    return $first . $stars . $last;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีวิว - <?= $restaurant['name'] ?></title>
    <link rel="icon" type="image/x-icon" href="../../assets/mascot.png">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../review.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        /* เพิ่ม CSS สำหรับ empty state */
        .review-empty {
            text-align: center;
            padding: 80px 20px;
            background: #fff;
            border-radius: 30px;
            margin: 0 20px 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .review-empty i {
            font-size: 100px;
            color: #cf8f4f;
            opacity: 0.3;
            margin-bottom: 25px;
        }

        .review-empty h3 {
            font-size: 22px;
            color: #cf8f4f;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .review-empty p {
            font-size: 16px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="dashboard-text">
            <h2>รีวิวของร้าน <?= $restaurant['name'] ?></h2>
            <p>ที่นี่จะแสดงรายการรีวิว ></p>
        <button class="back-btn"
            onclick="window.location.href='../<?= $id ?>'">
            <i class="ri-arrow-left-line"></i> back
        </button>
        </div>

        <div class="dashboard-avatar">
            <div class="avatar-circle"></div>
            <img src="../../assets/mascot.png" alt="Mascot">
        </div>
    </div>

    <div class="food-list">
        <div class="food-card">
            <img src="../../uploads/<?= $restaurant['image'] ?>" class="header-img">
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
                <button class="more-btn" onclick="openReviewPopup()">
                    review
                </button>
            </div>
        </div>
    </div>

    <div class="menu-text">
        <h2 class="food-name">รีวิวของร้านทั้งหมด (<?= $review_count ?>)</h2>
    </div>

    <div class="review-list">
        <?php if ($review_count > 0): ?>
            <?php while ($review = mysqli_fetch_assoc($reviews_query)): ?>
                <div class="review-item">
                    <div class="review-user"><?= blurName($review['user_name']) ?></div>
                    
                    <div class="review-stars">
                        <?php 
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['rating']) {
                                echo '<i class="ri-star-fill"></i>';
                            } else {
                                echo '<i class="ri-star-fill empty"></i>';
                            }
                        }
                        ?>
                    </div>

                    <div class="review-comment"><?= htmlspecialchars($review['comment']) ?></div>

                    <div class="review-date">
                        <?php
                        $date = new DateTime($review['created_at']);
                        echo 'วันที่ ' . $date->format('j') . ' พ.ย. ' . ($date->format('y')) . ' ' . $date->format('H:i');
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
    </div>
    <?php else: ?>
        <!-- แสดงเมื่อยังไม่มีรีวิว -->
        <div class="review-empty">
            <i class="ri-chat-3-line"></i>
            <h3>ยังไม่มีรีวิวสำหรับร้านนี้</h3>
            <p>เป็นคนแรกที่เขียนรีวิวให้ร้านนี้สิ!</p>
        </div>
    <?php endif; ?>

    <!-- Review Popup -->
    <div class="review-popup-overlay" id="reviewPopup">
        <div class="review-popup">
            <div class="review-popup-header">
                <h3>เขียนรีวิว</h3>
                <p>แบ่งปันประสบการณ์ของคุณ</p>
            </div>

            <form id="reviewForm">
                <input type="hidden" name="restaurant_id" value="<?= $id ?>">
                
                <div class="review-form-group">
                    <label class="review-form-label">ให้คะแนน</label>
                    <div class="review-star-input" id="starRating">
                        <i class="ri-star-fill" data-rating="1"></i>
                        <i class="ri-star-fill" data-rating="2"></i>
                        <i class="ri-star-fill" data-rating="3"></i>
                        <i class="ri-star-fill" data-rating="4"></i>
                        <i class="ri-star-fill" data-rating="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingValue" required>
                </div>

                <div class="review-form-group">
                    <label class="review-form-label">เขียนรีวิว</label>
                    <textarea name="comment" class="review-textarea" placeholder="แบ่งปันความคิดเห็นของคุณ..." required></textarea>
                </div>

                <div class="review-popup-buttons">
                    <button type="button" class="review-cancel-btn" onclick="closeReviewPopup()">ยกเลิก</button>
                    <button type="submit" class="review-submit-btn">ส่งรีวิว</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Review Page Script
        let selectedRating = 0;
        let stars;

        // ฟังก์ชัน Popup
        function openReviewPopup() {
            document.getElementById('reviewPopup').classList.add('active');
        }

        function closeReviewPopup() {
            document.getElementById('reviewPopup').classList.remove('active');
            document.getElementById('reviewForm').reset();
            selectedRating = 0;
            updateStars();
        }

        function updateStars() {
            if (stars) {
                stars.forEach(star => {
                    if (star.dataset.rating <= selectedRating) {
                        star.classList.add('active');
                    } else {
                        star.classList.remove('active');
                    }
                });
            }
        }

        // รอให้ DOM โหลดเสร็จ
        document.addEventListener('DOMContentLoaded', function() {
            const reviewForm = document.getElementById('reviewForm');
            const reviewPopup = document.getElementById('reviewPopup');
            const starRating = document.getElementById('starRating');
            
            if (!reviewForm || !reviewPopup || !starRating) {
                return;
            }

            // Star Rating
            stars = document.querySelectorAll('#starRating i');
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectedRating = this.dataset.rating;
                    document.getElementById('ratingValue').value = selectedRating;
                    updateStars();
                });
            });

            // Submit Form
            reviewForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                if (selectedRating === 0) {
                    alert('กรุณาให้คะแนน');
                    return;
                }

                const formData = new FormData(this);

                try {
                    const response = await fetch('../../api/add_review.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('ส่งรีวิวสำเร็จ!');
                        location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (result.error || 'ไม่สามารถส่งรีวิวได้'));
                    }
                } catch (error) {
                    alert('เกิดข้อผิดพลาด: ' + error.message);
                }
            });

            // Close popup when clicking outside
            reviewPopup.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReviewPopup();
                }
            });
        });
    </script>
</body>
</html>