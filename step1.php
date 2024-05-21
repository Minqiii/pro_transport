<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $fields = [
        'pickup_location', 'pickup_street', 'pickup_city', 'pickup_provinces', 
        'pickup_code', 'pickup_county', 'dropoff_location', 'dropoff_street', 
        'dropoff_city', 'dropoff_provinces', 'dropoff_code', 'dropoff_county', 
        'type_trans'
    ];

    foreach ($fields as $key) {
        if (isset($_POST[$key])) {
            $_SESSION[$key] = htmlspecialchars($_POST[$key]);
        }
    }

    header("Location: step2.php");
    exit();
}
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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBdzN1yX1RIwyB3ASi9WUuOw-n52OcW4Tc&callback=initAutocomplete&libraries=places"></script>
    <title>Delivery Tracking</title>

    <title>Step 1</title>
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
        form select {
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
    </style>
</head>
<body >
    <form id="step1Form" action="step2.php" method="POST">
        
        <div>
            <label >เลือกประเภทการขนส่ง:</label>
            <select id="type_trans" name="type_trans" required>
                <option value="" disabled selected>เลือกประเภทการขนส่ง</option>
                <option value="การขนส่งแบบเหมาทั้งคัน">การขนส่งแบบเหมาทั้งคัน</option>
                <option value="การขนส่งแบบนับชิ้น">การขนส่งแบบนับชิ้น</option>
                
            </select>
        </div>
        <div>
        <label >จุดรับสินค้า</label>
                <input id="start" placeholder="Enter start location" onFocus="geolocate()" type="text" name="pickup_location">
                <input id="start_street" onFocus="geolocate()" placeholder="ถนน" readonly="true" type="text" name="pickup_street">
                <input id="start_city" onFocus="geolocate()" placeholder="เมือง" readonly="true" type="text" name="pickup_city">
                <input id="start_state" onFocus="geolocate()" placeholder="จังหวัด" readonly="true" type="text" name="pickup_provinces">
                <input id="start_zip" onFocus="geolocate()" placeholder="รหัสไปรษณีย์" readonly="true" type="text" name="pickup_code">
                <input id="start_country" onFocus="geolocate()" placeholder="ประเทศ" readonly="true" type="text" name="pickup_county">
        </div>
        <div>
        <label >จุดส่งสินค้า</label>
                <input id="end" placeholder="Enter destination location" onFocus="geolocate()" type="text"name="dropoff_location">
                <input id="end_street" onFocus="geolocate()"  placeholder="Street Address" readonly="true" type="text" name="dropoff_street">
                <input id="end_city" onFocus="geolocate()" placeholder="City" readonly="true" type="text" name="dropoff_city">
                <input id="end_state" onFocus="geolocate()"  placeholder="State" readonly="true" type="text" name="dropoff_provinces">
                <input id="end_zip" onFocus="geolocate()" placeholder="Postal Code" readonly="true" type="text" name="dropoff_code">
                <input id="end_country" onFocus="geolocate()" placeholder="Country" readonly="true" type="text" name="dropoff_county">
        </div>
        <button type="submit" onclick="calculateAndDisplayRoute();" >ต่อไป</button>
    </form>

    <script src="./script/transport.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


