<?php
session_start();
include '../../db_connection/db_connection.php'; // Adjust the path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];

    // Generate a random 6-digit OTP
    $otp = rand(100000, 999999);

    // Store the OTP and its expiry time (60 seconds from now) in the session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 60; // 60 seconds from now
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['username'] = $username;
    $_SESSION['password'] = $password;

    // Send the OTP to the user's phone number using Semaphore API
    $api_key = 'ed49311fb8620f4e674df7cd10181e95'; // Replace with your Semaphore API key
    $message = 'Your OTP is: ' . $otp;
    $sender_name = 'Bws'; // Replace with your sender name

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'apikey' => $api_key,
        'number' => $phone_number,
        'message' => $message,
        'sendername' => $sender_name
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Redirect to the OTP verification page
    header('Location: ../../login/process/verify_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center-form {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Add New Admin</h2>
        <div class="card center-form">
            <div class="card-body">
                <form action="add_admin.php" method="POST" onsubmit="return validatePhoneNumber()">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required pattern="\d{10}" maxlength="10">
                        <div id="phoneError" class="text-danger mt-2"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Verification Code</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function validatePhoneNumber() {
            const phoneInput = document.getElementById('phone_number');
            const phoneError = document.getElementById('phoneError');
            const phonePattern = /^\d{10}$/;

            if (!phonePattern.test(phoneInput.value)) {
                phoneError.textContent = 'Please enter a valid 10-digit phone number.';
                return false;
            }

            phoneError.textContent = '';
            return true;
        }
    </script>
</body>
</html>