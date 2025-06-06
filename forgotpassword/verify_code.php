<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resend'])) {
        // Resend OTP logic
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = time() + 60; // 60 seconds from now

        // Send the OTP to the user's phone number using Semaphore API
        $api_key = 'ed49311fb8620f4e674df7cd10181e95'; // Replace with your Semaphore API key
        $message = 'Your new OTP is: ' . $otp;
        $sender_name = 'Bws'; // Replace with your sender name
        $phone = $_SESSION['phone'];

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

        $message = "A new OTP has been sent to your phone.";
    } else {
        $entered_code = implode('', $_POST['verification_code']); // Combine the 6 input fields into one string

        if (!is_numeric($entered_code)) {
            $error = "Invalid verification code. Please enter a numeric code.";
        } elseif (time() > $_SESSION['otp_expiry']) {
            $error = "OTP has expired. Please request a new one.";
        } elseif ($entered_code == $_SESSION['otp']) {
            // Code is correct, redirect to reset password page
            header('Location: reset_password.php');
            exit();
        } else {
            $error = "Invalid verification code. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Code</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .card-header {
            background-color: #d1c4e9;
            color: #4a148c;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
        }
        .btn-primary {
            background-color: #7e57c2;
            border: none;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #5e35b1;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: 0.5em;
        }
        .otp-input {
            width: 40px;
            height: 40px;
            text-align: center;
            font-size: 1.5em;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Verify Code</h2>
        </div>
        <div class="card-body">
            <?php if (isset($error)) { echo "<div class='error-message'>$error</div>"; } ?>
            <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>
            <form action="verify_code.php" method="POST">
                <div class="form-group">
                    <label for="verification_code">Enter the verification code sent to your phone:</label>
                    <div class="d-flex justify-content-center">
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                        <input type="text" name="verification_code[]" maxlength="1" class="otp-input" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Verify Code</button>
            </form>
            <form action="verify_code.php" method="POST" class="mt-3">
                <button type="submit" name="resend" class="btn btn-secondary btn-block">Resend OTP</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && index > 0 && e.target.value === '') {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>