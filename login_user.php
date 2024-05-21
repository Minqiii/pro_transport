<?php
session_start();
require 'conDB.php';

if (isset($_POST['loginuser'])) {
    $email = $_POST["email"];
    $password = $_POST["user_password"];

    if (isset($email) && isset($password)) {
        // Check for admin credentials
        if ($email == "admin@admin.com" && $password == "admin1234.") {
            $_SESSION['admin'] = true;
            header("Location: ./admin/admin.php");
            exit;
        } else {
            // Check user credentials
            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['user_password'])) {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['surname'] = $row['surname'];
                    header("location: index.php");
                    exit;
                } else {
                    $error_message = "รหัสผ่านหรืออีเมลไม่ถูกต้อง";
                }
            } else {
                $error_message = "ไม่พบผู้ใช้ในระบบ";
            }
        }
    } else {
        $error_message = "กรุณากรอกอีเมลและรหัสผ่าน";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./style/login.css">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <section class="container forms">
        <div class="form login">
            <div class="form-content" id="LoginUser">
                <header>Login</header>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="field input-field">
                        <input type="email" id="email" name="email" placeholder="Enter email"
                            aria-describedby="emailHelp" required>
                    </div>
                    <div class="field input-field">
                        <input type="password" id="password" placeholder="Enter password" name="user_password" required>
                        <i class='bx bx-hide eye-icon'></i>
                    </div>
                    <div class="form-link">
                        <a href="#" class="forgot-pass">Forgot password?</a>
                    </div>
                    <div class="field button-field">
                        <button type="submit" class="btn btn-primary" name="loginuser" id="loginuser">login</button>
                    </div>
                </form>
                <div class="form-link">
                    <span>คุณยังไม่ได้สมัครสมาชิกใช่หรือไม่?<a href="user_register.php" class="link signup-link">Signup</a></span>
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
    <script src="./script/login.js"></script>
    <script>
        const pwShowHide = document.querySelectorAll(".eye-icon"),
              signupLink = document.querySelector(".signup-link");

        pwShowHide.forEach(eyeIcon => {
            eyeIcon.addEventListener("click", () => {
                let pwFields = eyeIcon.parentElement.querySelectorAll("input[type='password']");

                pwFields.forEach(password => {
                    if (password.type === "password") {
                        password.type = "text";
                        eyeIcon.classList.replace("bx-hide", "bx-show");
                    } else {
                        password.type = "password";
                        eyeIcon.classList.replace("bx-show", "bx-hide");
                    }
                });
            });
        });

        signupLink.addEventListener("click", () => {
            window.location.href = "user_register.php";
        });
    </script>
</body>
</html>
