<?php
session_start();
include 'db_connection.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];

    // Generate a random 6-digit OTP
    $otp = rand(100000, 999999);

    // Store the OTP and its expiry time (60 seconds from now) in the session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 60; // 60 seconds from now
    $_SESSION['phone'] = $phone;

    // Send the OTP to the user's phone number using Semaphore API
    $api_key = 'ed49311fb8620f4e674df7cd10181e95'; // Replace with your Semaphore API key
    $message = 'Your OTP is: ' . $otp;
    $sender_name = 'Bws'; // Replace with your sender name

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'apikey' => $api_key,
        'number' => $phone,
        'message' => $message,
        'sendername' => $sender_name
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Redirect to the OTP verification page
    header('Location: verify_code.php');
    exit();
}
?>