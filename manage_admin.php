<?php
session_start();
include 'db_connection/db_connection.php'; // Adjust the path as needed

// Fetch current admin details
$sql = "SELECT * FROM tbl_admin WHERE username = 'admin123'"; // Replace with dynamic username if needed
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

$showSuccessModal = false;
$disableAddAdmin = isset($_GET['disable_add_admin']) && $_GET['disable_add_admin'] === 'true';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle updating the current admin
    $username = $_POST['username'];
    $password = $_POST['password'];
    $secret_key = $_POST['secret_key'];

    // Hash the password if it is provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $hashed_password = $admin['password']; // Keep the current password if not changed
    }

    // Prepare and execute the SQL statement to update the admin details
    $stmt = $conn->prepare("UPDATE tbl_admin SET username = ?, password = ?, secret_key = ? WHERE username = 'admin123'");
    $stmt->bind_param("sss", $username, $hashed_password, $secret_key);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Admin account updated successfully.";
        $showSuccessModal = true;
    } else {
        $_SESSION['error'] = "Error updating admin account: " . $conn->error;
        // Redirect to the same page to show the error message
        header("Location: manage_admin.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        .center-form {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Manage Admin Account</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" id="successMessage">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form action="manage_admin.php" method="POST" class="center-form">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="secret_key" class="form-label">Secret Key</label>
                <input type="password" class="form-control" id="secret_key" name="secret_key" value="" required>
                <div id="secretKeyError" class="text-danger mt-2"></div>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update Account</button>
                <button type="button" class="btn btn-success" id="addAdminBtn" disabled>Add Admin</button>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Admin account updated successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Secret Key Modal -->
    <div class="modal fade" id="secretKeyModal" tabindex="-1" aria-labelledby="secretKeyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="secretKeyModalLabel">Input Secret Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Please input the secret key first!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($showSuccessModal): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000);
        });
    </script>
    <?php endif; ?>
    <script>
        // Hide the success message after 3 seconds
        setTimeout(function() {
            const successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 3000);

        document.addEventListener('DOMContentLoaded', function() {
            const addAdminBtn = document.getElementById('addAdminBtn');
            addAdminBtn.disabled = true; // Ensure the button is disabled initially

            document.getElementById('secret_key').addEventListener('input', function() {
                const secretKey = this.value;
                const secretKeyError = document.getElementById('secretKeyError');

                if (secretKey === '') {
                    secretKeyError.textContent = 'Secret key cannot be empty.';
                    addAdminBtn.disabled = true;
                    return;
                }

                // Check the secret key in the database in real-time
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'check_secret_key.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        if (xhr.responseText === 'valid') {
                            secretKeyError.textContent = '';
                            addAdminBtn.disabled = false;
                        } else {
                            secretKeyError.textContent = 'Invalid secret key.';
                            addAdminBtn.disabled = true;
                        }
                    }
                };
                xhr.send('secret_key=' + encodeURIComponent(secretKey));
            });

            addAdminBtn.addEventListener('click', function() {
                const secretKey = document.getElementById('secret_key').value;
                const secretKeyError = document.getElementById('secretKeyError');

                if (secretKey === '' || secretKeyError.textContent !== '') {
                    const secretKeyModal = new bootstrap.Modal(document.getElementById('secretKeyModal'));
                    secretKeyModal.show();
                } else {
                    window.location.href = '../../bws_ui/login/process/add_admin.php';
                }
            });
        });
    </script>
</body>
</html>