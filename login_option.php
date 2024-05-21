<!DOCTYPE html>
<html>

<head>
    <title>Popup with Link to Another Page</title>
    <style>
        @import url('https://fonts.cdnfonts.com/css/poppins');

        * {
            margin: 0%;
            padding: 0%;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .popup {
            display: none;
            position: relative;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 700px;
            width: 300%;
            height: 70%;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .popup-content {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .popup h2 {
            color: #333;
            margin-bottom: 50px;

        }

        .popup-option {
            display: flex;
            padding: 10px 20px;
            margin: 5px;
            background-color: #4e73df;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            flex-direction: column;
            align-items: flex-end;
            border: black solid;
        }

        #option1Btn,
        #option2Btn {
            display: block;
            margin-bottom: 10px;
            /* ระยะห่างระหว่างปุ่ม */
            background-color: #4e73df;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-align: center;
            width: fit-content;
            /* ปรับขนาดปุ่มให้พอดีกับข้อความ */
            padding: 10px 20px;
            /* ปรับระยะห่างของข้อความในปุ่ม */
        }

        #option1Btn:hover,
        #option2Btn:hover {
            background-color: #9edcae;
            /* สีเมื่อโฮเวอร์ */
        }

        #option1Btn {
            position: absolute;
            top: 160px;
            right: 50px;
            width: 200px;
            height: 80px;
            font-weight: 700;
            font-size: 25px;
            background: rgba(35, 93, 49, 0.4);;
            cursor: pointer;
            border-radius: 40px;

        }

        #option2Btn {
            position: absolute;
            top: calc(200px + 50px);
            right: 50px;
            width: 200px;
            height: 80px;
            font-size: 25px;
            font-weight: 700;
            background-color: rgba(35, 93, 49, 0.4);;
            cursor: pointer;
            border-radius: 40px;
        }

        .btn-close-popup {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-close-popup:hover {
            background-color: #ccc;
            border-radius: 50%;
        }

        .close-icon {
            font-size: 24px;
            color: #333;
        }

        .popup img {
            width: 350px;
            height: 70%;
            margin-right: 20px;
            border-radius: 15px;
        }
    </style>
</head>

<body>

    <div id="popup" class="popup">
        <button id="closePopupBtn" class="btn-close-popup"><span class="close-icon">&#10005;</span></button>
        <div class="popup-content">
            <h2>Log In</h2>
        </div>
        <div class="popup-content">
            <img src="img/transprt.jpg" alt="Image">
        </div>
        <div class="popup-content">
            <button id="option1Btn" class="popup-option">User</button>
            <button id="option2Btn" class="popup-option">Driver</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const popup = document.getElementById('popup');
            popup.style.display = 'block';

            const closePopupBtn = document.getElementById('closePopupBtn');
            const option1Btn = document.getElementById('option1Btn');
            const option2Btn = document.getElementById('option2Btn');

            closePopupBtn.addEventListener('click', () => {
                popup.style.display = 'none';
            });

            option1Btn.addEventListener('click', () => {
                popup.style.display = 'none';
                window.location.href = 'login_user.php'; // ลิงก์ไปยัง login_user.php
            });

            option2Btn.addEventListener('click', () => {
                popup.style.display = 'none';
                window.location.href = 'login_driver.php'; // ลิงก์ไปยัง login_driver.php
            });
        });
    </script>

</body>

</html>