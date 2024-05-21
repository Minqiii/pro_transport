<?php
session_start();
require 'conDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $dr_email = $_POST["dr_email"];
    $dr_password = password_hash($_POST["dr_password"], PASSWORD_DEFAULT); // Hash the password
    $dr_confirmPassword = $_POST["dr_confirmPassword"];
    $dr_name = $_POST["dr_name"];
    $dr_surname = $_POST["dr_surname"];
    $dr_address = $_POST["dr_address"];
    $Ref_prov_id = mysqli_real_escape_string($con, $_POST['Ref_prov_id']);
    $Ref_dist_id = mysqli_real_escape_string($con, $_POST['Ref_dist_id']);
    $Ref_subdist_id = mysqli_real_escape_string($con, $_POST['Ref_subdist_id']);
    $zip_code = mysqli_real_escape_string($con, $_POST['zip_code']);
    $dr_age = $_POST["dr_age"];
    $dr_phone = $_POST["dr_phone"];
    $dr_id_card = $_POST["dr_id_card"];
    $dr_id_driving_license = $_POST["dr_id_driving_license"];
    $dr_date_issued = $_POST["dr_date_issued"];
    $dr_expiry_date = $_POST["dr_expiry_date"];
    $dr_type_drive = $_POST["dr_type_drive"];
    $car_number_plate = $_POST["car_number_plate"];
    $dr_image_car = $_FILES["dr_image_car"]["tmp_name"];
    $dr_image = $_FILES["dr_image"]["tmp_name"];

    // ตรวจสอบอีเมลซ้ำ
    $email_check_query = "SELECT * FROM delivery_driver WHERE dr_email = '$dr_email' LIMIT 1";
    $result = mysqli_query($con, $email_check_query);
    if (mysqli_num_rows($result) > 0) {
        echo "อีเมลนี้ถูกใช้ไปแล้ว";
        exit;
    }

    // ตรวจสอบรหัสผ่าน
    if ($_POST["dr_password"] !== $_POST["dr_confirmPassword"]) {
        echo "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
        exit;
    }

    // ตรวจสอบไฟล์ภาพ
    $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
    $uploaded_file_type = strtolower(pathinfo($_FILES["dr_image"]["name"], PATHINFO_EXTENSION));
    if (!in_array($uploaded_file_type, $allowed_types)) {
        echo "อนุญาตเฉพาะไฟล์ภาพเท่านั้น (jpg, jpeg, png, gif)";
        exit;
    }

    // ตรวจสอบขนาดของไฟล์ภาพ
    if ($_FILES["dr_image"]["size"] > 500000) {
        echo "ขนาดไฟล์ภาพมากเกินไป (ไม่เกิน 500 KB)";
        exit;
    }

    // อ่านเนื้อหาไฟล์ภาพโปรไฟล์
    $dr_image_content = addslashes(file_get_contents($dr_image));

    // อ่านเนื้อหาไฟล์ภาพรถ
    $dr_image_car_content = addslashes(file_get_contents($dr_image_car));

    // เพิ่มข้อมูลลงในฐานข้อมูล
    $sql = "INSERT INTO delivery_driver (dr_email, dr_password, dr_name, dr_surname, dr_address, Ref_prov_id, Ref_dist_id, Ref_subdist_id, zipcode, dr_phone, dr_age, dr_id_card, dr_id_driving_license, dr_date_issued, dr_expiry_date, dr_type_drive, car_number_plate, dr_image_car, dr_image, status_id) 
            VALUES ('$dr_email', '$dr_password', '$dr_name', '$dr_surname', '$dr_address', '$Ref_prov_id', '$Ref_dist_id', '$Ref_subdist_id', '$zip_code', '$dr_phone', '$dr_age', '$dr_id_card', '$dr_id_driving_license', '$dr_date_issued', '$dr_expiry_date', '$dr_type_drive', '$car_number_plate', '$dr_image_car_content', '$dr_image_content', '1')";

    if (mysqli_query($con, $sql)) {
        // สามารถเพิ่มข้อมูลสมาชิกได้
        echo "<script>alert('สมัครสมาชิกสำเร็จแล้ว'); window.location.href = 'check_status.php';</script>";
        exit();
    } else {
        // ไม่สามารถเพิ่มข้อมูลสมาชิกได้
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// Fetch provinces
$sql_provinces = "SELECT id, name_th FROM provinces ORDER BY name_th ASC";
$provincesQuery = mysqli_query($con, $sql_provinces);
if (!$provincesQuery) {
    echo "Error fetching provinces: " . mysqli_error($con);
    exit();
}
$provinces = mysqli_fetch_all($provincesQuery, MYSQLI_ASSOC);

// Fetch car types
$sql_car_types = "SELECT name_type_car FROM type_car";
$carTypesQuery = mysqli_query($con, $sql_car_types);
if (!$carTypesQuery) {
    echo "Error fetching car types: " . mysqli_error($con);
    exit();
}
$carTypes = mysqli_fetch_all($carTypesQuery, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/register_dr.css">
    <title>สมัครสมาชิกคนขับ</title>
</head>

<body>
    <div class="register-container">
        <div class="register-title">สมัครสมาชิกคนขับ</div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="mb-3 mt-3">
                <label for="dr_email">อีเมล</label>
                <input type="text" class="form-control" id="dr_email" placeholder="เพิ่มอีเมล" name="dr_email" required>
                <div id="emailError" style="color: red;"></div>
            </div>
            <div class="mb-3">
                <label for="dr_password">รหัสผ่าน</label>
                <input type="password" class="form-control" id="dr_password" placeholder="กรอกรหัสผ่าน" name="dr_password" required>
                <div id="passwordError" style="color: red;"></div>
            </div>
            <div class="mb-3">
                <label for="dr_confirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" id="dr_confirmPassword" placeholder="ยืนยันรหัสผ่าน" name="dr_confirmPassword">
                <div id="passwordConfirmError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_name">ชื่อ</label>
                <input type="text" class="form-control" id="dr_name" placeholder="กรอกชื่อ" name="dr_name" required>
                <div id="nameError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_surname">นามสกุล</label>
                <input type="text" class="form-control" id="dr_surname" placeholder="กรอกนามสกุล" name="dr_surname" required>
                <div id="surnameError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_address">ที่อยู่</label>
                <input type="text" class="form-control" id="dr_address" placeholder="เพิ่มที่อยู่" name="dr_address" required>
                <div id="addressError" style="color: red;"></div>
            </div>
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
                <div id="zipCodeError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_age">อายุ</label>
                <input type="text" class="form-control" id="dr_age" placeholder="กรอกอายุ" name="dr_age" required>
                <div id="ageError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_phone">หมายเลขโทรศัพท์</label>
                <input type="text" class="form-control" id="dr_phone" placeholder="กรอกหมายเลขโทรศัพท์" name="dr_phone" required>
                <div id="phoneError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_id_card">หมายเลขบัตรประชาชน</label>
                <input type="text" class="form-control" id="dr_id_card" placeholder="กรอกหมายเลขบัตรประชาชน" name="dr_id_card" required>
                <div id="idCardError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_id_driving_license">หมายเลขใบขับขี่</label>
                <input type="text" class="form-control" id="dr_id_driving_license" placeholder="กรอกหมายเลขใบขับขี่" name="dr_id_driving_license" required>
                <div id="drivingLicenseError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_date_issued">วันที่ออกบัตร</label>
                <input type="date" class="form-control" id="dr_date_issued" name="dr_date_issued" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_expiry_date">วันที่บัตรหมดอายุ</label>
                <input type="date" class="form-control" id="dr_expiry_date" name="dr_expiry_date" required>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_type_drive">ประเภทรถ</label>
                <select class="form-control" id="dr_type_drive" name="dr_type_drive" required>
                    <option value="" selected disabled>-- เลือกประเภทรถ --</option>
                    <?php foreach ($carTypes as $carType) { ?>
                        <option value="<?= $carType['name_type_car'] ?>"><?= $carType['name_type_car'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3 mt-3">
                <label for="car_number_plate">หมายเลขทะเบียนรถ</label>
                <input type="text" class="form-control" id="car_number_plate" placeholder="กรอกหมายเลขทะเบียนรถ" name="car_number_plate" required>
                <div id="carNumberPlateError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_image_car">รูปภาพรถ</label>
                <input type="file" class="form-control" id="dr_image_car" name="dr_image_car" accept="image/*" required>
                <div id="imageCarError" style="color: red;"></div>
            </div>
            <div class="mb-3 mt-3">
                <label for="dr_image">รูปภาพโปรไฟล์</label>
                <input type="file" class="form-control" id="dr_image" name="dr_image" accept="image/*" required>
                <div id="imageError" style="color: red;"></div>
            </div>
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        </form>

    </div>
    <script src="./script/register_dr.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        type = "text/javascript"
        $('#provinces').change(function () {
            var id_province = $(this).val();

            $.ajax({
                type: "POST",
                url: "./ajax_db.php",
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
                url: "./ajax_db.php",
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
                url: "./ajax_db.php",
                data: { id: id_districts, function: 'districts' },
                success: function (data) {
                    $('#zip_code').val(data)
                }
            });

        });

    </script>
</body>

</html>
