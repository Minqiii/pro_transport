<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<?php if ($current_page != 'login_option.php' && $current_page != 'register_user.php' && $current_page != 'login_user.php' && $current_page != 'login_driver.php'): ?>
                <?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/nav.css">
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>
    <header class="header">
        <a href="index.php" class="Deliver"> Deliver</a>

        <nav class="navbar">
            <a href="#">Transport</a>
            <a href="#">Order</a>
            <a href="#">History</a>
            <a href="#">Typecar</a>
            <a href="#">Contact</a>
            <a href="login_option.php">
            <button type="button" class="btn btn-primary" id="loginBtn">Login</button></a>

        </nav>
    </header>
    
</body>
</html>
