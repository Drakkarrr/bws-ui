<?php
// Include database connection
include '../../db_connection/db_connection.php';

// Enable error reporting for debugging (optional)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user ID is set in the URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // Ensure it's an integer

    // Fetch the current status of the user
    $query = "SELECT is_active FROM tbl_users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($is_active);
    $stmt->fetch();
    $stmt->close();

    // Toggle the status
    $new_status = $is_active ? 0 : 1;

    // Prepare an UPDATE statement
    $sql = "UPDATE tbl_users SET is_active = ? WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $new_status, $user_id); // Bind the parameters
        if ($stmt->execute()) {
            // Redirect back to the user data page with a success message
            $message = $new_status ? 'User activated successfully.' : 'User deactivated temporarily.';
            header("Location: view_all_users.php?message=" . urlencode($message));
            exit();
        } else {
            echo "Error updating user status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No user ID specified.";
}

// Close the database connection
$conn->close();
?>
<!-- Navbar Section -->
<header class="navbar" shadow-sm>
    <div class="container d-flex align-items-center justify-content-between">
        <!-- Sidebar Toggle Button -->
        <button class="sidebar-toggle btn btn-outline-danger me-3" onclick="toggleSidebar()"
            aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Brand -->
        <a href="add_category.php" class="navbar-brand text-dark">Bernadette Wellness Spa Admin Panel</a>

        <!-- Date and Time Display -->
        <div class="ms-auto">
            <span id="currentDateTime" class="text-dark"></span>
        </div>
    </div>
</header>

<!-- Sidebar Section -->
<div class="sidebar bg-light shadow-lg p-3 mb-5 rounded" id="sidebar" style="width: 250px;">
    <button class="close-btn btn btn-outline-purple mb-4" onclick="toggleSidebar()"
        style="font-size: 1.5rem; position: absolute; right: 15px; top: 15px;">&times;</button>

    <!-- Admin Image Section -->
    <div class="text-center mb-3">
        <img src="./images/user_profile/admin1.jpg" alt="Admin Image" class="img-fluid rounded-circle"
            style="width: 100px; height: 100px;">
            <h5 style="color: black;" class="mt-2">Admin</h5>
    </div>
    <ul class="list-unstyled">
        <li class="mb-3">
            <a href="dashboard.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-tachometer-alt fa-lg me-3 icon"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/login/process/view_all_users.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Registered Users Icon -->
                <span>Registered Users</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="../bws_ui/booking/admin_approve_bookings.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-calendar-alt fa-lg me-3 icon"></i>
                <span>Manage Bookings</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="add_category.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Service Category</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="add_services.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Services</span>
            </a>
        </li>
        <li style="text-align: center;">
            <h3 style="color: black;">Inventory</h3>
        </li>
        <li class="mb-3">
            <a href="add_product.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Items</span>
            </a>
        </li>
        <li style="text-align: center; padding: 10px;">
            <h5 style="color: #333; font-weight: 600; margin: 0; font-size: 1.2rem;">Discount Services</h5>
        </li>
        <li class="mb-3">
            <a href="discount.php"
                class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                <i class="fas fa-plus-circle fa-lg me-3 icon"></i>
                <span>Add Discount</span>
            </a>
        </li>
        <li class="mb-3">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item"
                id="logoutLink">
                <i class="fas fa-sign-out-alt fa-lg me-3 icon"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</div>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Data</title>

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../login/login_style/table_style.css">

    <style>
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
        /* Semi-transparent black */
    }

    /* Adjust Modal Dialog Width */
    .modal-dialog {
        max-width: 500px;
        /* Reduces the modal width */
        margin: 1.75rem auto;
    }

    /* Reduce Modal Body Padding */
    .modal-body {
        padding: 1.5rem;
        /* Adjust padding for a more compact look */
    }

    /* Keep Modal Content Style */
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Modal Header and Button Styles */
    .modal-header {
        background-color: #6a1b9a;
        color: #ffffff;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-title {
        font-weight: 600;
    }

    /* Form Controls and Buttons */
    .input-group-text,
    .form-label {
        color: black;
        border: none;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .form-control:focus {
        box-shadow: none;
        border-color: #6a1b9a;
    }

    .btn-primary {
        background-color: #6a1b9a;
        border: none;
        border-radius: 8px;
        font-weight: 600;
    }

    .btn-primary:hover {
        background-color: #8e24aa;
    }

    .table-wrapper {
        max-height: 400px;
        overflow-y: auto;
    }

    .footer-spacing {
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .bg-light-purple {
        background-color: #f0eaff;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: -250px;
        width: 250px;
        height: 100%;
        background-color: #f8f9fa;
        transition: left 0.3s ease;
    }

    .sidebar.open {
        left: 0;
    }

    .footer-spacing {
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .bg-light-purple {
        background-color: #f0eaff;
    }

    .card-fixed-height {
        min-height: 500px;
    }

    /* Toggle Button Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #ADD8E6; /* Light blue */
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
</style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">User Data</h3>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <?php
                    // Fetch data from tbl_users
                    $sql = "SELECT user_id, username, firstname, middlename, lastname, phone, address, age, dob, gender, created_at, is_active FROM tbl_users";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table id='usersTable' class='table table-striped table-bordered'>
                        <thead>
                        <tr>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Age</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>";

                        // Output data for each row
                        while ($row = $result->fetch_assoc()) {
                            $status = $row['is_active'] ? 'Active' : 'Inactive';
                            $toggleStatus = $row['is_active'] ? 'Deactivate' : 'Activate';
                            echo "<tr>
                                <td>" . $row['username'] . "</td>
                                <td>" . $row['firstname'] . "</td>
                                <td>" . $row['middlename'] . "</td>
                                <td>" . $row['lastname'] . "</td>
                                <td>" . $row['phone'] . "</td>
                                <td>" . $row['address'] . "</td>
                                <td>" . $row['age'] . "</td>
                                <td>" . $row['dob'] . "</td>
                                <td>" . $row['gender'] . "</td>
                                <td>" . $row['created_at'] . "</td>
                                <td>" . $status . "</td>
                                <td class='text-center'>
                                    <label class='toggle-switch'>
                                        <input type='checkbox' class='toggle-status' data-user-id='" . $row['user_id'] . "' " . ($row['is_active'] ? 'checked' : '') . ">
                                        <span class='slider'></span>
                                    </label>
                                </td>
                            </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<div class='alert alert-warning' role='alert'>No users found.</div>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function () {
            $('#usersTable').DataTable();
        });

        // Handle toggle switch change
        $(document).on('change', '.toggle-status', function () {
            var userId = $(this).data('user-id');
            var isActive = $(this).is(':checked') ? 1 : 0;
            var action = isActive ? 'activate' : 'deactivate';
            var message = isActive ? 'User activated successfully.' : 'User deactivated successfully.';

            // Show confirmation modal
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to ' + action + ' this user?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, ' + action + ' it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request to toggle user status
                    $.ajax({
                        url: 'toggle_user_status.php',
                        type: 'GET',
                        data: { id: userId },
                        success: function (response) {
                            Swal.fire(
                                'Success!',
                                message,
                                'success'
                            ).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'An error occurred while updating the user status.',
                                'error'
                            );
                        }
                    });
                } else {
                    // Revert the toggle switch state if action is cancelled
                    $(this).prop('checked', !isActive);
                }
            });
        });
    </script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
    </script>
    <script>
        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>

</body>

</html>
<?php include "../../includes/footer.php"; ?>