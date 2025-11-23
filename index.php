<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPU MUM-MUM - Login</title>

    <link rel="icon" type="image/x-icon" href="assets/mascot.png">
    <link rel="stylesheet" href="style.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body>

    <div class="container">

        <!-- โลโก้ -->
        <img src="assets/logo.png" alt="Logo" class="logo">

        <!-- ฟอร์ม login -->
        <div class="login-card">

            <div class="drag-bar"></div>

            <h1 class="title"><i class="ri-login-circle-line"></i> Login - account</h1>
            <p class="subtitle">Enter your email below to login your account</p>

            <form action="api/login" method="POST">

                <div class="input-group">
                    <i class="ri-mail-line input-icon"></i>
                    <input type="email" placeholder="Email" id="email" name="email" required>
                </div>

                <div class="input-group">
                    <i class="ri-lock-line input-icon"></i>
                    <input type="password" placeholder="Password" id="password" name="password" required>
                </div>

                <a href="" class="forgot">Forgot Password</a>

                <div>
                    <button class="btn-login" type="submit">Login</button>
                </div>
                
                <button class="btn-login" type="button" onclick="window.location.href='signup'">
                    Sign up
                </button>

            </form>
        </div>

    </div>

    <script src="script.js"></script>
</body>
</html>
