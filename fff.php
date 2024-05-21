<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> login </title>
    <style>
        /* Google Fonts - Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            font-family: 'Sarabun', sans-serif;
        }
        .container {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color:#96d6e3;
            column-gap: 30px;
        }

        .form {
            position: absolute;
            max-width: 430px;
            width: 100%;
            padding: 30px;
            border-radius: 6px;
            background: #FFF;
        }

        .form.signup {
            opacity: 0;
            pointer-events: none;
        }

        .forms.show-signup .form.signup {
            opacity: 1;
            pointer-events: auto;
        }

        .forms.show-signup .form.login {
            opacity: 0;
            pointer-events: none;
        }

        header {
            font-size: 28px;
            font-weight: 600;
            color: #232836;
            text-align: center;
        }

        form {
            margin-top: 30px;
        }

        .form .field {
            position: relative;
            height: 50px;
            width: 100%;
            margin-top: 20px;
            border-radius: 6px;
        }

        .field input,
        .field button {
            height: 100%;
            width: 100%;
            border: none;
            font-size: 16px;
            font-weight: 400;
            border-radius: 6px;
        }

        .field input {
            outline: none;
            padding: 0 15px;
            border: 1px solid#CACACA;
        }

        .field input:focus {
            border-bottom-width: 2px;
        }

        .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 18px;
            color: #8b8b8b;
            cursor: pointer;
            padding: 5px;
        }

        .field button {
            color: #fff;
            background-color: #0171d3;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .field button:hover {
            background-color: #016dcb;
        }

        .form-link {
            text-align: center;
            margin-top: 10px;
        }

        .form-link span,
        .form-link a {
            font-size: 14px;
            font-weight: 400;
            color: #232836;
        }

        .form a {
            color: #0171d3;
            text-decoration: none;
        }

        .form-content a:hover {
            text-decoration: underline;
        }

        .line {
            position: relative;
            height: 1px;
            width: 100%;
            margin: 36px 0;
            background-color: #d4d4d4;
        }

        .line::before {
            content: 'Or';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #FFF;
            color: #8b8b8b;
            padding: 0 15px;
        }

        .media-options a {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .facebook-icon,
        img.google-img {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
        }

        img.google-img {
            height: 20px;
            width: 20px;
            object-fit: cover;
        }

        a.google {
            border: 1px solid #CACACA;
        }

        a.google span {
            font-weight: 500;
            opacity: 0.6;
            color: #232836;
        }

        @media screen and (max-width: 400px) {
            .form {
                padding: 20px 10px;
            }

        }

        .form-link span {
            font-family: 'Sarabun', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="./style/login.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>

</head>

<body>
    <section class="container forms">
        <div class="form login">
            <div class="form-content" id="LoginDriver">
                <header>Login Driver</header>
                <form action="login_driver.php" method="POST">
                    <div class="field input-field">
                    <input type="email" class="form-control" id="email" name="dr_email" placeholder="Enter Email"
            aria-describedby="emailHelp">
                    </div>
                    <div class="field input-field">
                    <input type="password" class="form-control" id="password" name="dr_password">
                </div>
                        <i class='bx bx-hide eye-icon'></i>
                    </div>
                    <div class="form-link">
                        <a href="#" class="forgot-pass">Forgot password?</a>
                    </div>
                    <div class="field button-field">
                    <button type="submit" class="btn btn-primary" name="loginDriver" id="loginDriver">login</button>
                    </div>
                </form>
                <div class="form-link">
                    <span>คุณยังไม่ได้สมัครสมาชิกใช่หรือไม่?<a href="user_register.php"
                            class="link signup-link">Signup</a></span>
                </div>
            </div>
            <div class="line"></div>
            <div class="media-options">
                <a href="#" class="field google">
                    <img src="./img/google.png" alt="" class="google-img">
                    <span>Login with Google</span>
                </a>
            </div>
        </div>
    </section>
    <?php
    require 'conDB.php';

    if (isset($_POST['loginDriver'])) {
        // รับค่าอีเมลและรหัสผ่านจากฟอร์ม
        $email = $_POST["dr_email"];
        $password = $_POST["dr_password"];

        // ตรวจสอบค่าอีเมลและรหัสผ่าน
        if (isset($_POST["dr_email"]) && isset($_POST["dr_password"])) {
            // เพิ่มเงื่อนไขตรวจสอบว่าเป็น admin หรือไม่
            if ($email == "admin@admin.com" && $password == "admin1234.") {
                $_SESSION['admin'] = true;
                // หากค่าตรงกัน ให้เปลี่ยนเส้นทางไปยังหน้าที่คุณต้องการ (Admin)
                header("Location: ./admin/admin.php");
                exit;
            } else {
                // หากค่าไม่ตรงกัน สามารถทำอะไรต่อได้ตามต้องการ เช่น แสดงข้อความผิดพลาด
                $query = "SELECT * FROM delivery_driver WHERE dr_email = '$email' AND dr_password = '$password'";
                $result = mysqli_query($con, $query);

                $row = mysqli_fetch_array($result);

                if ($row) {
                    $_SESSION['dr_id'] = $row['dr_id'];
                    $_SESSION['dr_name'] = $row['dr_name'];
                    $_SESSION['dr_surname'] = $row['dr_surname'];

                    header("location: index1.php");
                    exit;
                } else {
                    // หากไม่พบผู้ใช้ในฐานข้อมูล
                    
                    echo "<script>alert('รหัสผ่านหรืออีเมลไม่ถูกต้อง');</script>";
                }
            }
        }
    }
    ?>
    <script src="./script/login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>


</html>