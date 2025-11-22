<?php
include "api/db.php";

$query = mysqli_query($conn, "SELECT * FROM restaurants ORDER BY id DESC");
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
</head>
<body>

<div class="dashboard-header">
    <div class="dashboard-text">
        <h2>ร้านอาหารทั้งหมด</h2>
        <p>เลือกเมนูที่ต้องการ ></p>
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
                    for ($i = 1; $i <= 5; $i++) {
                        echo ($i <= $row['rating']) 
                            ? "<i class='ri-star-fill'></i>" 
                            : "<i class='ri-star-line'></i>";
                    }
                ?>
            </div>

            <button class="more-btn">more info</button>
        </div>

    </div>

<?php endwhile; ?>
</div>

</body>
</html>
