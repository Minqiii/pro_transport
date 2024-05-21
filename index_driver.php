<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่
if (!isset($_SESSION['dr_id'])) {
    // ผู้ใช้ไม่ได้ล็อกอิน, เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: ../login.php");
    exit;
}

require 'conDB.php'; // แน่ใจว่าคุณมีไฟล์นี้เพื่อเชื่อมต่อกับฐานข้อมูล

$user_id = $_SESSION['dr_id'];

// ดึงข้อมูลจากฐานข้อมูล
$stmt = $con->prepare("SELECT dr_surname, dr_image FROM delivery_driver WHERE dr_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $surname = $user['dr_surname'];
    $dr_image = $user['dr_image'];
} else {
    echo 'ไม่พบข้อมูลผู้ใช้.';
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Transport</title>
    <link rel="stylesheet" href="./style/nav_driver.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>TRANSPORT</h2>
            <div class="profile">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($dr_image); ?>" alt="Profile" class="profile-img">
                <div class="profile-name"><?php echo htmlspecialchars($surname); ?></div>
            </div>
            <div class="sidebar-menu">
                <label class="menu-label" onclick="toggleMenu(this)">ข้อมูลพื้นฐาน</label>
                <div class="menu-content">
                    <a href="javascript:void(0)" onclick="loadContent('Pickup_location.php', this)">สถานที่รับขนส่ง</a>
                </div>
                <label class="menu-label" onclick="toggleMenu(this)">ข้อมูลงานที่ต้องจัดส่ง</label>
                <div class="menu-content">
                    <a href="javascript:void(0)" onclick="loadContent('index1.php', this)">งานเรียกเข้า</a>
                    <a href="javascript:void(0)" onclick="loadContent('work_in_progress.php', this)">งานที่อยู่ระหว่างการทำงาน</a>
                    <a href="javascript:void(0)" onclick="loadContent('history.php', this)">ประวัติ</a>
                </div>
                <label class="menu-label" onclick="toggleMenu(this)">บัญชี</label>
                <div class="menu-content">
                    <a href="javascript:void(0)" onclick="loadContent('driver/dr_profile.php', this)">โปรไฟล์</a>
                    <a href="javascript:void(0)" onclick="loadContent('my_car.php', this)">รถของฉัน</a>
                    <a href="logout.php">ออกจากระบบ</a>
                </div>
            </div>
        </div>
        <div class="main-content">
            <!-- ส่วนนี้จะเป็นที่แสดงเนื้อหาที่โหลดจากลิงค์ต่างๆ -->
            <iframe id="contentFrame" src="Pickup_location.php" frameborder="0"></iframe>
        </div>
    </div>

    <script>
        function loadContent(page, element) {
            // อัพเดต src ของ iframe
            document.getElementById('contentFrame').src = page;
            
            // ทำให้ลิงค์ทั้งหมดไม่มี active class
            var links = document.querySelectorAll('.menu-content a');
            links.forEach(link => link.classList.remove('active'));
            
            // เพิ่ม active class ให้กับลิงค์ที่ถูกคลิก
            element.classList.add('active');
        }

        function toggleMenu(label) {
            var menuContent = label.nextElementSibling;
            var allMenus = document.querySelectorAll('.menu-content');
            
            // ซ่อนเมนูอื่นๆ ทั้งหมด
            allMenus.forEach(menu => {
                if (menu !== menuContent) {
                    menu.style.display = "none";
                }
            });
            
            // แสดง/ซ่อนเมนูที่ถูกคลิก
            if (menuContent.style.display === "block") {
                menuContent.style.display = "none";
            } else {
                menuContent.style.display = "block";
            }
        }
    </script>
</body>
</html>
