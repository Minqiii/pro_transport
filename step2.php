<?php
session_start();
require 'conDB.php';

function calculateOrderPrice($type_trans, $distance, $id_type_car, $id_unit, $order_weight, $quantity, $con) {
    $order_price = 0;
    $stmt = $con->prepare("SELECT price_carce FROM type_car WHERE id_type_car = ?");
    $stmt->bind_param("i", $id_type_car);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $price_carce = $row['price_carce'] ?? 0;

    if ($type_trans == 'การขนส่งแบบเหมาทั้งคัน') {
        $order_price = $price_carce + ($distance * 5);
    } elseif ($type_trans == 'การขนส่งแบบนับชิ้น') {
        $stmt = $con->prepare("SELECT name_unit FROM units WHERE id_unit = ?");
        $stmt->bind_param("i", $id_unit);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $unit_type = $row['name_unit'] ?? '';

        if ($unit_type == 'ซองเอกสาร') {
            $order_price = 50 * $distance;
        } elseif ($unit_type == 'กล่อง') {
            if ($order_weight <= 10) {
                $order_price = 150;
            } elseif ($order_weight <= 20) {
                $order_price = 350;
            } elseif ($order_weight <= 30) {
                $order_price = 450;
            }
            $order_price *= $quantity;
        }
    }

    return $order_price;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $requiredFields = [
        'pickup_location', 'pickup_street', 'pickup_city', 'pickup_provinces', 'pickup_code', 'pickup_county',
        'dropoff_location', 'dropoff_street', 'dropoff_city', 'dropoff_provinces', 'dropoff_code', 'dropoff_county',
        'type_trans', 'tel', 'distance', 'duration', 'pickup_time', 'id_type_product', 'quantity', 'id_type_car', 'id_unit', 'order_weight'
    ];

    $missing_fields = [];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        } else {
            $_SESSION[$field] = htmlspecialchars($_POST[$field]);
        }
    }

    if (!empty($missing_fields)) {
        $error_message = "Missing fields: " . implode(', ', $missing_fields) . "<br>All fields are required.";
    } else {
        if (isset($_FILES["img_product"]) && $_FILES["img_product"]["error"] == 0) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["img_product"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["img_product"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $error_message .= "File is not an image.<br>";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                $error_message .= "Sorry, file already exists.<br>";
                $uploadOk = 0;
            }

            if ($_FILES["img_product"]["size"] > 500000) {
                $error_message .= "Sorry, your file is too large.<br>";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $error_message .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                $error_message .= "Sorry, your file was not uploaded.<br>";
            } else {
                if (move_uploaded_file($_FILES["img_product"]["tmp_name"], $target_file)) {
                    $_SESSION['img_product'] = $target_file;
                } else {
                    $error_message .= "Sorry, there was an error uploading your file.<br>";
                }
            }
        }

        if (empty($_SESSION['img_product'])) {
            $missing_fields[] = 'img_product';
        }

        $optionalFields = ['box_wide', 'box_long', 'box_high'];

        foreach ($optionalFields as $field) {
            $_SESSION[$field] = isset($_POST[$field]) && $_POST[$field] !== '' ? htmlspecialchars($_POST[$field]) : '0';
        }

        $order_price = calculateOrderPrice($_SESSION['type_trans'], $_SESSION['distance'], $_SESSION['id_type_car'], $_SESSION['id_unit'], $_SESSION['order_weight'], $_SESSION['quantity'], $con);
        $_SESSION['order_price'] = $order_price;

        // Insert the order into the database
        $stmt = $con->prepare("INSERT INTO orders (user_id, pickup_location, pickup_street, pickup_city, pickup_provinces, pickup_code, pickup_county, dropoff_location, dropoff_street, dropoff_city, dropoff_provinces, dropoff_code, dropoff_county, type_trans, tel, img_product, distance, duration, pickup_time, id_type_product, quantity, id_type_car, id_unit, order_weight, box_wide, box_long, box_high, order_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssssssssssdsiiiidddd", $_SESSION['user_id'], $_SESSION['pickup_location'], $_SESSION['pickup_street'], $_SESSION['pickup_city'], $_SESSION['pickup_provinces'], $_SESSION['pickup_code'], $_SESSION['pickup_county'], $_SESSION['dropoff_location'], $_SESSION['dropoff_street'], $_SESSION['dropoff_city'], $_SESSION['dropoff_provinces'], $_SESSION['dropoff_code'], $_SESSION['dropoff_county'], $_SESSION['type_trans'], $_SESSION['tel'], $_SESSION['img_product'], $_SESSION['distance'], $_SESSION['duration'], $_SESSION['pickup_time'], $_SESSION['id_type_product'], $_SESSION['quantity'], $_SESSION['id_type_car'], $_SESSION['id_unit'], $_SESSION['order_weight'], $_SESSION['box_wide'], $_SESSION['box_long'], $_SESSION['box_high'], $_SESSION['order_price']);
        $stmt->execute();

        header("Location: step3.php");
        exit();
    }
}

$select_data = "SELECT * FROM type_product" or die("Error: " . mysqli_error($con));
$rs_select = mysqli_query($con, $select_data);

$select_data = "SELECT * FROM units" or die("Error: " . mysqli_error($con));
$un_select = mysqli_query($con, $select_data);

$select_data = "SELECT * FROM type_car" or die("Error: " . mysqli_error($con));
$cr_select = mysqli_query($con, $select_data);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Select2 -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/transport.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdzN1yX1RIwyB3ASi9WUuOw-n52OcW4Tc&callback=initAutocomplete&libraries=places"></script>
    <title>Step 2</title>
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
        form select,
        form input[type="number"],
        form input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        form button {
            width: 100%;
            padding: 10px;
            background-color: rgb(41, 88, 155);
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>

    <form id="step2Form" action="step3.php" method="POST">
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div id="outputDistance">distance: </div>
        <div id="outputDuration">duration: </div>
        <input type="hidden" id="hiddenDistance" name="distance"
            value="<?php echo isset($_SESSION['distance']) ? htmlspecialchars($_SESSION['distance']) : ''; ?>">
        <input type="hidden" id="hiddenDuration" name="duration"
            value="<?php echo isset($_SESSION['duration']) ? htmlspecialchars($_SESSION['duration']) : ''; ?>">
        <div>
            <label for="time">เวลารับสินค้า</label>
            <input type="datetime-local" id="time" name="pickup_time"
                value="<?php echo isset($_SESSION['pickup_time']) ? htmlspecialchars($_SESSION['pickup_time']) : ''; ?>">
        </div>
        <div class="box">
            <label for="typeproduct">ประเภทสินค้า</label>
            <select id="typeproduct" name="id_type_product" class="form-control select2">
                <option value="">เลือกประเภทสินค้า</option>
                <?php foreach ($rs_select as $rs) { ?>
                    <option value="<?php echo $rs['id_type_product']; ?>" <?php echo (isset($_SESSION['id_type_product']) && $_SESSION['id_type_product'] == $rs['id_type_product']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($rs['name_type_product']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="quantity">จำนวนสินค้า</label>
            <input type="number" id="quantity" name="quantity" min="1"
                value="<?php echo isset($_SESSION['quantity']) ? htmlspecialchars($_SESSION['quantity']) : ''; ?>">
        </div>
        <div class="box">
            <label for="typecar">ประเภทรถ</label>
            <select class="form-control select2" id="typecar" name="id_type_car">
                <option value="">Select Car Type</option>
                <?php foreach ($cr_select as $cr) { ?>
                    <option value="<?php echo $cr['id_type_car']; ?>" <?php echo (isset($_SESSION['id_type_car']) && $_SESSION['id_type_car'] == $cr['id_type_car']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cr['name_type_car']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label>ปรเภทบรรจุภัณฑ์</label>
            <select class="form-control select2" id="unit" name="id_unit">
                <option value="">Select Unit</option>
                <?php foreach ($un_select as $un) { ?>
                    <option value="<?php echo $un['id_unit']; ?>" <?php echo (isset($_SESSION['id_unit']) && $_SESSION['id_unit'] == $un['id_unit']) ? 'selected' : ''; ?>
                        data-box="<?php echo ($un['name_unit'] == 'กล่อง') ? 'true' : 'false'; ?>">
                        <?php echo htmlspecialchars($un['name_unit']); ?>
                    </option>
                <?php } ?>
            </select>
            <div class="box" id="dimensions" style="display: none;">
                <label for="box_wide">ความกว้าง</label>
                <input type="number" id="box_wide" name="box_wide" placeholder="ความกว้างของกล่อง"
                    value="<?php echo isset($_SESSION['box_wide']) ? htmlspecialchars($_SESSION['box_wide']) : '0'; ?>">
                <label for="box_long">ความยาว</label>
                <input type="number" id="box_long" name="box_long" placeholder="ความยาวของกล่อง"
                    value="<?php echo isset($_SESSION['box_long']) ? htmlspecialchars($_SESSION['box_long']) : '0'; ?>">
                <label for="box_high">ความสูง</label>
                <input type="number" id="box_high" name="box_high" placeholder="ความสูงของกล่อง"
                    value="<?php echo isset($_SESSION['box_high']) ? htmlspecialchars($_SESSION['box_high']) : '0'; ?>">
                <label for="box_weight">น้ำหนักต่อกล่อง</label>
                <input type="number" id="box_weight" name="box_weight" placeholder="น้ำหนักต่อกล่อง"
                    value="<?php echo isset($_SESSION['box_weight']) ? htmlspecialchars($_SESSION['box_weight']) : '0'; ?>">
            </div>
        </div>
        <div class="box">
            <label for="tel">เบอร์โทร:</label>
            <input type="text" id="tel" name="tel" placeholder="0xxxxxxxxx" pattern="[0-9]{10}" required>
        </div>
        <div class="box">
            <label for="imgproduct">ใส่รูปภาพสินค้า หรือรูปผลิตภัณฑ์:</label>
            <input type="file" id="img_product" name="img_product" value="Upload Image" required>
        </div>
        <script>
            $(document).ready(function () {
                $('.select2').select2();
                $('#unit').on('change', function () {
                    var selectedOption = $(this).find('option:selected');
                    var isBox = selectedOption.data('box') === 'true';
                    if (isBox) {
                        $('#dimensions').show();
                    } else {
                        $('#dimensions').hide();
                    }
                });

                // Check initial state if the page is loaded with 'box' selected
                var initialUnit = $('#unit').find('option:selected').data('box');
                if (initialUnit) {
                    $('#dimensions').show();
                } else {
                    $('#dimensions').hide();
                }
            });

            // Calculate the order price and update the form before submission
            $('#calculate').click(function (e) {
                e.preventDefault(); // Prevent the form from submitting

                var formData = $('#step2Form').serialize();
                $.post('calculate_price.php', formData, function (data) {
                    // Update the session variable with the calculated price
                    alert('Order price calculated: ' + data.order_price);
                    $('#hiddenOrderPrice').val(data.order_price);

                    // Now submit the form
                    $('#step2Form').submit();
                }, 'json');
            });
        </script>
        <button type="submit">ยืนยัน</button>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </form>
</body>

</html>