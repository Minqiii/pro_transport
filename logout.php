<?php
session_start(); // เริ่มต้น session

// ล้างข้อมูล session ทั้งหมด
$_SESSION = array();

// ถ่ายโอนคุณสมบัติของ session จากเซิร์ฟเวอร์ให้กับเบราว์เซอร์
// โดยทำให้คุณสามารถลบ session ออกจากเซิร์ฟเวอร์ได้
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย session
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้า login
header("Location: index.php");
exit;
?>
