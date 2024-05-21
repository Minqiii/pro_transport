<?php
session_start();
require 'conDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $user_password = password_hash($_POST['user_password'], PASSWORD_DEFAULT); // แปลงรหัสผ่านเป็นแฮช
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $surname = mysqli_real_escape_string($con, $_POST['surname']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $Ref_prov_id = mysqli_real_escape_string($con, $_POST['Ref_prov_id']);
    $Ref_dist_id = mysqli_real_escape_string($con, $_POST['Ref_dist_id']);
    $Ref_subdist_id = mysqli_real_escape_string($con, $_POST['Ref_subdist_id']);
    $zipcode = mysqli_real_escape_string($con, $_POST['zip_code']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $img_profile = "";



    // ตรวจสอบอีเมลซ้ำ
    $email_check_query = "SELECT * FROM delivery_driver WHERE dr_email = '$dr_email' LIMIT 1";
    $result = mysqli_query($con, $email_check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "อีเมลนี้ถูกใช้ไปแล้ว";
        exit;
    }

    // ตรวจสอบว่ารหัสผ่านและการยืนยันรหัสผ่านตรงกัน
    if ($_POST['user_password'] !== $_POST['confirmPassword']) {
        echo "<div class='notification'>
                <p>รหัสผ่านไม่ตรงกัน</p>
              </div>
              <script>
                setTimeout(function() {
                    window.location.href = 'register_user.php';
                }, 5000);
              </script>";
        exit;
    }

    // ตรวจสอบว่ามีการอัพโหลดไฟล์ภาพโปรไฟล์เข้ามาหรือไม่
    if (isset($_FILES['img_profile']) && $_FILES['img_profile']['error'] === UPLOAD_ERR_OK) {
        $target_directory = "img_profile/";
        $target_file = $target_directory . basename($_FILES["img_profile"]["name"]);
        $uploadOk = true;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

        // ตรวจสอบชนิดของไฟล์ว่าเป็นชนิดที่อนุญาตหรือไม่
        if (!in_array($_FILES['img_profile']['type'], $allowed_types)) {
            $uploadOk = false;
            echo "<div class='notification'>
                    <p>ขออภัย, ไฟล์ที่อนุญาตเฉพาะ JPG, JPEG, PNG & GIF.</p>
                  </div>";
        }

        // ตรวจสอบขนาดของไฟล์
        if ($_FILES["img_profile"]["size"] > 5000000) {
            $uploadOk = false;
            echo "<div class='notification'>
                    <p>ขออภัย, ไฟล์ของคุณใหญ่เกินไป.</p>
                  </div>";
        }

        // ถ้าไม่มีข้อผิดพลาดในการอัปโหลดไฟล์ และไฟล์อัปโหลดสำเร็จ
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["img_profile"]["tmp_name"], $target_file)) {
                $img_profile = basename($_FILES["img_profile"]["name"]);
            } else {
                echo "<div class='notification'>
                        <p>ขออภัย, มีข้อผิดพลาดในการอัปโหลดไฟล์ของคุณ.</p>
                      </div>";
            }
        }
    }

    // บันทึกข้อมูลเข้าฐานข้อมูล
    $sql = "INSERT INTO users (email, user_password, name, surname, address, Ref_prov_id, Ref_dist_id, Ref_subdist_id, zipcode, phone, added_date, img_profile) 
        VALUES ('$email', '$user_password', '$name', '$surname', '$address', '$Ref_prov_id', '$Ref_dist_id', '$Ref_subdist_id', '$zipcode', '$phone', NOW(), '$img_profile')";

    if (mysqli_query($con, $sql)) {
        // แจ้งเตือนสำเร็จและเปลี่ยนเส้นทางไปยังหน้า login_user.php
        echo "<div class='notification'>
                <p>สมัครสมาชิกสำเร็จ</p>
              </div>
              <script>
                setTimeout(function() {
                    window.location.href = 'login_user.php';
                }, 2000);
              </script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Fetch provinces data
$sql_provinces = "SELECT id, name_th FROM provinces ORDER BY name_th ASC";
$provincesQuery = mysqli_query($con, $sql_provinces);
if (!$provincesQuery) {
    echo "Error fetching provinces: " . mysqli_error($con);
    exit();
}
$provinces = mysqli_fetch_all($provincesQuery, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/register_user.css">
    <link rel="stylesheet" href="./style/style.css">
    <title>สมัครสมาชิกผู้ใช้ทั่วไป</title>
</head>
<body>
    <div class="register-container">
        <div class="register-title">สมัครสมาชิก</div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="mb-3 mt-3">
                <label for="email">อีเมล</label>
                <input type="text" class="form-control" id="email" placeholder="เพิ่มอีเมล" name="email" required>
            </div>
            <div class="mb-3">
                <label for="user_password">รหัสผ่าน</label>
                <input type="password" class="form-control" id="user_password" placeholder="กรอกรหัสผ่าน" name="user_password" required>
            </div>
            <div id="passwordError" style="color: red;"></div>
            <div class="mb-3">
                <label for="confirmPassword">ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" id="confirmPassword" placeholder="ยืนยันรหัสผ่าน" name="confirmPassword">
                <div id="passwordError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="name">ชื่อ</label>
                <input type="text" class="form-control" id="name" placeholder="กรอกชื่อ" name="name" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="surname">นามสกุล</label>
                <input type="text" class="form-control" id="surname" placeholder="กรอกนามสกุล" name="surname" required>
            </div>
            <!-- Address -->
            <div class="mb-3 mt-3">
                <label for="address">ที่อยู่</label>
                <input type="text" class="form-control" id="address" placeholder="เพิ่มที่อยู่" name="address" required>
            </div>
            <!-- Province, District, Sub-district Selection -->
            <div class="form-group">
                <label for="provinces">จังหวัด:</label>
                <select class="form-control" name="Ref_prov_id" id="provinces" required>
                    <option value="" selected disabled>-กรุณาเลือกจังหวัด-</option>
                    <?php foreach ($provinces as $value) { ?>
                        <option value="<?= $value['id'] ?>"><?= $value['name_th'] ?></option>
                    <?php } ?>
                </select>
                <br>
                <label for="amphures">อำเภอ:</label>
                <select class="form-control" name="Ref_dist_id" id="amphures"></select>
                <br>
                <label for="districts">ตำบล:</label>
                <select class="form-control" name="Ref_subdist_id" id="districts"></select>
                <br>
                <label for="zip_code">รหัสไปรษณีย์:</label>
                <input type="text" name="zip_code" id="zip_code" class="form-control">
            </div>
            <!-- Phone -->
            <div class="mb-3 mt-3">
                <label for="phone">หมายเลขโทรศัพท์</label>
                <input type="text" class="form-control" id="phone" placeholder="กรอกหมายเลขโทรศัพท์" name="phone" required>
            </div>
            <div id="phoneError" style="color: red;"></div>
            <!-- Profile Image -->
            <div class="mb-3 mt-3">
                <label for="img_profile">รูปภาพโปรไฟล์</label>
                <input type="file" class="form-control" id="img_profile" name="img_profile" accept="image/*" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        </form>
    </div>
    <!-- JavaScript and jQuery -->
    <script src="./script/register_user.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        type = "text/javascript"
        $('#provinces').change(function () {
            var id_province = $(this).val();

            $.ajax({
                type: "POST",
                url: "ajax_db.php",
                data: { id: id_province, function: 'provinces' },
                success: function (data) {
                    $('#amphures').html(data);
                    $('#districts').html(' ');
                    $('#districts').val(' ');
                    $('#zip_code').val(' ');
                }
            });
        });

        $('#amphures').change(function () {
            var id_amphures = $(this).val();

            $.ajax({
                type: "POST",
                url: "ajax_db.php",
                data: { id: id_amphures, function: 'amphures' },
                success: function (data) {
                    $('#districts').html(data);
                }
            });
        });

        $('#districts').change(function () {
            var id_districts = $(this).val();

            $.ajax({
                type: "POST",
                url: "ajax_db.php",
                data: { id: id_districts, function: 'districts' },
                success: function (data) {
                    $('#zip_code').val(data)
                }
            });

        });
    </script>
</body>
</html>
