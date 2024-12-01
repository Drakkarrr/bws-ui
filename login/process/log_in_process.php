<?php
session_start();
include '../../db_connection/db_connection.php'; // Include your database connection script

$invalid_login = ""; // Initialize the error message variable
$successful_login = false; // Initialize success flag

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check in tbl_admin first
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if an admin exists
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Password is correct, set session variables
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin'; // Set role as admin

            $successful_login = true; // Set success flag
        } else {
            // Invalid password
            $invalid_login = "Invalid username or password.";
        }
    } else {
        // No admin found, check in tbl_users
        $stmt->close(); // Close the previous statement
        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if a user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Check if the user is active
            if ($user['is_active'] == 0) {
                $invalid_login = "Your account is deactivated. Please contact support.";
            } else {
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $user['user_id']; // Store user ID for later use
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'user'; // Set role as user
                    $_SESSION['firstname'] = $user['firstname']; // Store first name
                    $_SESSION['lastname'] = $user['lastname']; // Store last name

                    $successful_login = true; // Set success flag
                } else {
                    // Invalid password
                    $invalid_login = "Invalid username or password.";
                }
            }
        } else {
            // No user found
            $invalid_login = "Invalid username or password.";
        }
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <?php if ($successful_login): ?>
        <script>
            // Show success alert
            Swal.fire({
                title: 'Success!',
                text: 'Login successful. Redirecting...',
                icon: 'success',
                timer: 2000, // Automatically close the alert after 2 seconds
                willClose: () => {
                    const role = '<?php echo $_SESSION["role"]; ?>'; // Get the user role
                    if (role === 'admin') {
                        window.location.href = '../../dashboard.php';
                    } else {
                        window.location.href = '../../index.php';
                    }
                }
            });
        </script>
    <?php endif; ?>

    <!-- Show error if login failed -->
    <?php if (!empty($invalid_login)): ?>
        <script>
            // Use SweetAlert to show an error message
            Swal.fire({
                title: 'Error!',
                text: '<?php echo $invalid_login; ?>',
                icon: 'error',
                confirmButtonText: 'Okay'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to log_in.php
                    window.location.href = '../log_in.php';
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>