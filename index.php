<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro_Transport</title>
    <link rel="stylesheet" href="./style/index.css">
    <link rel="stylesheet" href="./style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- เพิ่ม jQuery -->
    <script src="../script/script.js"></script> <!-- เพิ่มไฟล์ JavaScript -->
</head>

<body>
    <?php
    if (isset($_SESSION['user_id'])) {
        require 'nav_user.php';
    } else if (isset($_SESSION['dr_id'])) {
        require 'index_driver.php'; // ใช้ nav_driver.php สำหรับผู้ใช้ที่ล็อกอินในฐานะคนขับ
    } else {
        require 'nav.php';
    }
    ?>
    <div id="content" class="banner-container">
        <div class="r-site">
            <img src="./img/delivery.png" alt="" class="img-scooter">
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>
