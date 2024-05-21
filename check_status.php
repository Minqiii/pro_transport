<?php
session_start();
require 'conDB.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="../style/check_status.css">
    <title>Check Status</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap');

body {
    font-family: 'Sarabun', sans-serif;
    background-color: #f0f2f5;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    text-align: center;
    animation: fadeIn 0.5s ease-out;
}

.title-search {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}

.search-form input[type="text"] {
    width: 70%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px 0 0 5px;
    font-size: 16px;
    transition: border-color 0.3s;
}

.search-form input[type="text"]:focus {
    border-color: #29639b;
    outline: none;
}

.btn-search {
    width: 30%;
    padding: 10px;
    background-color: #29639b;
    color: #fff;
    border: none;
    border-radius: 0 5px 5px 0;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.btn-search:hover {
    background-color: #1e4a72;
    transform: scale(1.05);
}

.status-container {
    margin-top: 20px;
}

.status-title {
    font-size: 20px;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.status-body {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.status-name {
    font-size: 18px;
    color: #555;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <p class="title-search">ค้นหาสถานะจากเบอร์โทร</p>
            <form method="post" class="search-form">
                <input type="text" id="search_phone" name="search_phone" placeholder="กรอกเบอร์โทร">
                <button type="submit" class="btn-search">ค้นหา</button>
            </form>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $search_phone = $_POST["search_phone"];

            // โค้ด SQL สำหรับดึงข้อมูลสถานะจากฐานข้อมูล
            $sql = "SELECT * FROM delivery_driver WHERE dr_phone = ?";

            if ($stmt = mysqli_prepare($con, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $search_phone);

                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        // มีข้อมูลพบ
                        $row = mysqli_fetch_assoc($result);

                        if ($row["status_id"] == 1) {
                            $status = "กำลังดำเนินการตรวจสอบ";
                        } elseif ($row["status_id"] == 2) {
                            $status = "การสมัครสมาชิกสำเร็จ";
                            header("Location: login_driver.php");
                            exit(); // ต้องใส่ exit() เพื่อหยุดการทำงานของสคริปต์ในที่นี้
                        } elseif ($row["status_id"] == 3) {
                            $status = "ขออภัย การสมัครสมาชิกไม่สำเร็จ";
                        }
                    } else {
                        $status = "ไม่พบข้อมูลสำหรับเบอร์โทรนี้";
                    }
                } else {
                    $status = "พบข้อผิดพลาดในการดึงข้อมูล";
                }

                mysqli_stmt_close($stmt);
            }

            mysqli_close($con);
        }
        ?>
        <div class="status-container">
            <div class="status-title">
                <p>STATUS</p>
            </div>

            <div class="status-body">
                <p class="status-name" id="statusName"><?php echo isset($status) ? $status : ''; ?></p>
            </div>
        </div>
    </div>

    <script src="../script/check_status.js"></script>
</body>

</html>