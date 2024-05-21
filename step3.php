<?php
session_start();
require 'conDB.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Debug: แสดงค่าใน session
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

$error_message = "";

// Function for handling file uploads
function handleFileUpload($file, &$error_message)
{
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $error_message .= "ไฟล์ไม่ใช่รูปภาพ.<br>";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $error_message .= "ขออภัย ไฟล์มีอยู่แล้ว.<br>";
        $uploadOk = 0;
    }

    if ($file["size"] > 500000) {
        $error_message .= "ขออภัย ไฟล์ของคุณมีขนาดใหญ่เกินไป.<br>";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $error_message .= "ขออภัย อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.<br>";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $error_message .= "ขออภัย ไฟล์ของคุณไม่ได้อัปโหลด.<br>";
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            $error_message .= "ขออภัย มีข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.<br>";
            return false;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    if (isset($_FILES["img_product"]) && $_FILES["img_product"]["error"] == 0) {
        $img_product = handleFileUpload($_FILES["img_product"], $error_message);
        if ($img_product) {
            $_SESSION['img_product'] = $img_product;
        }
    }

    if (!empty($_POST['tel'])) {
        $_SESSION['tel'] = htmlspecialchars($_POST['tel']);
    }

    $requiredFields = [
        'pickup_location',
        'pickup_street',
        'pickup_city',
        'pickup_provinces',
        'pickup_code',
        'pickup_county',
        'dropoff_location',
        'dropoff_street',
        'dropoff_city',
        'dropoff_provinces',
        'dropoff_code',
        'dropoff_county',
        'type_trans',
        'distance',
        'duration',
        'pickup_time',
        'id_type_product',
        'quantity',
        'id_type_car',
        'id_unit',
        'order_weight',
        'order_price',
        'tel',
        'img_product'
    ];

    $missing_fields = [];

    foreach ($requiredFields as $field) {
        if (empty($_SESSION[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $error_message = "Missing fields: " . implode(', ', $missing_fields) . "<br>All fields are required.";
    } else {
        $optionalFields = ['box_wide', 'box_long', 'box_high', 'box_weight'];

        foreach ($optionalFields as $field) {
            $_SESSION[$field] = isset($_SESSION[$field]) && $_SESSION[$field] !== '' ? htmlspecialchars($_SESSION[$field]) : '0';
        }

        $stmt = $con->prepare("INSERT INTO orders (
            user_id, pickup_location, pickup_street, pickup_city,
            pickup_provinces, pickup_code, pickup_county, dropoff_location,
            dropoff_street, dropoff_city, dropoff_provinces, dropoff_code,
            dropoff_county, type_trans, distance, duration, pickup_time,
            id_type_product, quantity, id_type_car, id_unit, order_weight, 
            box_wide, box_long, box_high, order_price, tel, img_product
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            $error_message .= 'เตรียมคำสั่งล้มเหลว: ' . htmlspecialchars($con->error);
        } else {
            $user_id = $_SESSION['user_id'];
            $pickup_location = $_SESSION['pickup_location'];
            $pickup_street = $_SESSION['pickup_street'];
            $pickup_city = $_SESSION['pickup_city'];
            $pickup_provinces = $_SESSION['pickup_provinces'];
            $pickup_code = $_SESSION['pickup_code'];
            $pickup_county = $_SESSION['pickup_county'];
            $dropoff_location = $_SESSION['dropoff_location'];
            $dropoff_street = $_SESSION['dropoff_street'];
            $dropoff_city = $_SESSION['dropoff_city'];
            $dropoff_provinces = $_SESSION['dropoff_provinces'];
            $dropoff_code = $_SESSION['dropoff_code'];
            $dropoff_county = $_SESSION['dropoff_county'];
            $type_trans = $_SESSION['type_trans'];
            $distance = $_SESSION['distance'];
            $duration = $_SESSION['duration'];
            $pickup_time = $_SESSION['pickup_time'];
            $id_type_product = $_SESSION['id_type_product'];
            $quantity = $_SESSION['quantity'];
            $id_type_car = $_SESSION['id_type_car'];
            $id_unit = $_SESSION['id_unit'];
            $order_weight = $_SESSION['order_weight'];
            $box_wide = $_SESSION['box_wide'];
            $box_long = $_SESSION['box_long'];
            $box_high = $_SESSION['box_high'];
            $order_price = $_SESSION['order_price'];
            $tel = $_SESSION['tel'];
            $img_product = $_SESSION['img_product'];

            $stmt->bind_param(
                "isssssssssssssssiiiiiiiisss",
                $user_id,
                $pickup_location,
                $pickup_street,
                $pickup_city,
                $pickup_provinces,
                $pickup_code,
                $pickup_county,
                $dropoff_location,
                $dropoff_street,
                $dropoff_city,
                $dropoff_provinces,
                $dropoff_code,
                $dropoff_county,
                $type_trans,
                $distance,
                $duration,
                $pickup_time,
                $id_type_product,
                $quantity,
                $id_type_car,
                $id_unit,
                $order_weight,
                $box_wide,
                $box_long,
                $box_high,
                $order_price,
                $tel,
                $img_product
            );
            if (!$stmt->execute()) {
                $error_message .= "Execute failed: " . htmlspecialchars($stmt->error); // แสดงข้อผิดพลาดการ execute
            } else {
                // ตรวจสอบว่าข้อมูลถูกบันทึกลงฐานข้อมูลแล้ว
                $stmt->close();
                $con->close();
                // ส่งผู้ใช้ไปยังหน้า step1.php เพื่อกรอกข้อมูลใหม่
                header("Location: step1.php");
                exit();
            }
            // ปิด statement
            $stmt->close();
        }
    }
}
// ปิดการเชื่อมต่อฐานข้อมูล
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 3</title>
    <style>
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            margin-top: 20px;
        }

        form h1 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        form div {
            margin-bottom: 15px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="file"],
        form button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        form button {
            background-color: rgb(41, 88, 155);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table th,
        .info-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .info-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <form method="POST" id="step3Form">
        <div>
            <?php
            if (!isset($_SESSION['order_price'])) {
                echo "Price is not set.";
            } else {
                echo "Price calculated from previous page is: " . $_SESSION['order_price'];
            }
            ?>
        </div>
        <div class="box">
            <h2>ข้อมูลทั้งหมดที่กรอก:</h2>
            <table class="info-table">
                <caption>Order Information</caption>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>สถานที่รับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_location']) ? $_SESSION['pickup_location'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>ถนนรับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_street']) ? $_SESSION['pickup_street'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>เมืองรับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_city']) ? $_SESSION['pickup_city'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>จังหวัดรับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_provinces']) ? $_SESSION['pickup_provinces'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>รหัสไปรษณีย์รับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_code']) ? $_SESSION['pickup_code'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>เขตรับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_county']) ? $_SESSION['pickup_county'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>สถานที่ส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_location']) ? $_SESSION['dropoff_location'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>ถนนส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_street']) ? $_SESSION['dropoff_street'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>เมืองส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_city']) ? $_SESSION['dropoff_city'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>จังหวัดส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_provinces']) ? $_SESSION['dropoff_provinces'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>รหัสไปรษณีย์ส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_code']) ? $_SESSION['dropoff_code'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>เขตส่งของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['dropoff_county']) ? $_SESSION['dropoff_county'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>ประเภทการขนส่ง</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['type_trans']) ? $_SESSION['type_trans'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>ระยะทาง</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['distance']) ? $_SESSION['distance'] : ''); ?></td>
                </tr>
                <tr>
                    <td>ระยะเวลา</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['duration']) ? $_SESSION['duration'] : ''); ?></td>
                </tr>
                <tr>
                    <td>เวลารับของ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['pickup_time']) ? $_SESSION['pickup_time'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>ประเภทสินค้า</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['id_type_product']) ? $_SESSION['id_type_product'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>จำนวน</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['quantity']) ? $_SESSION['quantity'] : ''); ?></td>
                </tr>
                <tr>
                    <td>ประเภทรถ</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['id_type_car']) ? $_SESSION['id_type_car'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>หน่วย</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['id_unit']) ? $_SESSION['id_unit'] : ''); ?></td>
                </tr>
                <tr>
                    <td>น้ำหนัก</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['order_weight']) ? $_SESSION['order_weight'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>กว้างของกล่อง</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['box_wide']) ? $_SESSION['box_wide'] : ''); ?></td>
                </tr>
                <tr>
                    <td>ยาวของกล่อง</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['box_long']) ? $_SESSION['box_long'] : ''); ?></td>
                </tr>
                <tr>
                    <td>สูงของกล่อง</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['box_high']) ? $_SESSION['box_high'] : ''); ?></td>
                </tr>
                <tr>
                    <td>ราคา</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['order_price']) ? $_SESSION['order_price'] : ''); ?>
                    </td>
                </tr>
                <tr>
                    <td>โทรศัพท์</td>
                    <td><?php echo htmlspecialchars(isset($_SESSION['tel']) ? $_SESSION['tel'] : ''); ?></td>
                </tr>
                <tr>
                    <td>ภาพสินค้า</td>
                    <td><img src="<?php echo htmlspecialchars(isset($_SESSION['img_product']) ? $_SESSION['img_product'] : ''); ?>"
                            alt="Product Image" width="100"></td>
                </tr>
            </table>
        </div>
        <button type="submit">ยืนยัน</button>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </form>
    <script src="./script/transport.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
