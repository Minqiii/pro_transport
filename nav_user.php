<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ผู้ใช้ไม่ได้ล็อกอิน, เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: login.php");
    exit;
}

require 'conDB.php'; // แน่ใจว่าคุณมีไฟล์นี้เพื่อเชื่อมต่อกับฐานข้อมูล

$user_id = $_SESSION['user_id'];

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $con->prepare("SELECT name, img_profile FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $img_profile = $user['img_profile'];
} else {
    echo 'ไม่พบข้อมูลผู้ใช้.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/nav.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <style>
       
        .navbar {
            display: flex;
            align-items: center;
        }

        .navbar-menu {
            display: flex;
            flex: 1;
        }

        .navbar-profile {
            display: flex;
            align-items: center;
            margin-left: auto;
            position: relative;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            cursor: pointer;
        }

        .btn-light.dropdown-toggle {
            display: flex;
            align-items: center;
            border: none;
            background: none;
            padding: 0;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
            width: 200px;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
            text-align: center;
            margin: 5px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            
            color: #000;
            /* เปลี่ยนสีฟอนต์เป็นสีดำ */
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }

        .profile-info {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 10px;
        }

        .profile-info img {
            margin-right: 10px;
        }

        .profile-info div {
            display: flex;
            flex-direction: column;
        }

        .profile-info div span {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="index.php" class="Deliver">Deliver</a>
        <nav class="navbar">
            <div class="navbar-menu">
                <a href="transport.php">Transport</a>
                <a href="order.php">Order</a>
                <a href="#">History</a>
                <a href="#">Typecar</a>
                <a href="#">Contact</a>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="navbar-profile">
                    <div class="dropdown">
                        <!-- ปุ่ม Dropdown ที่ใช้แสดงรูปโปรไฟล์และชื่อผู้ใช้ -->
                        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
                            aria-expanded="false" onclick="toggleDropdown()">
                            <img src="img_profile/<?php echo htmlspecialchars($img_profile); ?>" alt="Profile"
                                class="profile-img">
                        </button>
                        <!-- เมนู Dropdown -->
                        <div id="dropdownMenu" class="dropdown-menu">
                            <div class="profile-info">
                                <img src="img_profile/<?php echo htmlspecialchars($img_profile); ?>" alt="Profile"
                                    class="profile-img">
                                <div>
                                    <span><?php echo htmlspecialchars($name); ?></span>
                                    <small>ผู้ใช้งานทั่วไป</small> <!-- สามารถเพิ่มชื่อผู้ใช้หรือข้อมูลอื่นๆ ได้ -->
                                </div>
                            </div >
                            <a class="dropdown-item" href="./user/user_profile.php">โปรไฟล์</a>
                            <a class="dropdown-item" href="#">Order</a>
                            <a class="dropdown-item" href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </nav>
    </header>
    <script src="./script/nav.js"></script>
    <script>
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdownMenu");
            dropdownMenu.classList.toggle("show");
        }

        // ปิด dropdown เมื่อคลิกข้างนอก
        window.onclick = function (event) {
            if (!event.target.matches('.dropdown-toggle, .dropdown-toggle *')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>