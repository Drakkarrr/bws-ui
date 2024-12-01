<?php
session_start();
include '../db_connection/db_connection.php'; // Update the path to your database connection script

// Ensure the user is authenticated to reset the password
if (!isset($_SESSION['phone'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_SESSION['phone'];

    // Validate the new password
    if (strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $error_message = "Password must be at least 8 characters long and contain both letters and numbers.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the user's password in the database
        $stmt = $conn->prepare("UPDATE tbl_users SET password = ? WHERE phone = ?");
        if ($stmt === false) {
            error_log('Prepare failed: ' . $conn->error);
            echo "<script>
                    alert('Database error. Please try again later.');
                    window.location.href = 'reset_password.php';
                  </script>";
            exit();
        }
        $stmt->bind_param("ss", $hashed_password, $phone);
        if (!$stmt->execute()) {
            error_log('Execute failed: ' . $stmt->error);
            echo "<script>
                    alert('Database error. Please try again later.');
                    window.location.href = 'reset_password.php';
                  </script>";
            exit();
        }
        $stmt->close();

        // Clear the session
        session_unset();
        session_destroy();

        // Redirect to index page
        header('Location: ../index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
        .match-message {
            font-size: 0.875em;
            margin-top: 0.5em;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h2>Reset Password</h2>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="reset_password.php" method="POST">
                <div class="form-group">
                    <label for="new_password">Enter your new password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm your new password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    <div id="matchMessage" class="match-message"></div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchMessage = document.getElementById('matchMessage');

            if (newPassword === confirmPassword) {
                matchMessage.textContent = 'Passwords match.';
                matchMessage.style.color = 'green';
            } else {
                matchMessage.textContent = 'Passwords do not match.';
                matchMessage.style.color = 'red';
            }
        });
    </script>
</body>
</html>