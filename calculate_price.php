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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $distance = $_POST['distance'];
    $id_type_car = $_POST['id_type_car'];
    $id_unit = $_POST['id_unit'];
    $order_weight = $_POST['order_weight'];
    $quantity = $_POST['quantity'];
    $type_trans = $_POST['type_trans'];

    $order_price = calculateOrderPrice($type_trans, $distance, $id_type_car, $id_unit, $order_weight, $quantity, $con);

    echo json_encode(['order_price' => $order_price]);
}
