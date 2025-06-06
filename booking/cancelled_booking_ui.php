<?php
session_start();
include '../../bws_ui/db_connection/db_connection.php'; // Include your database connection
include '../../bws_ui/includes/header.php'; // Include your header file
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../booking//booking style//booking_style.css">
</head>

<body>
    <!-- Navbar Section -->
    <header class="navbar bg-light shadow-sm">
        <div class="container d-flex align-items-center">
            <!-- Sidebar Toggle Button -->
            <button class="sidebar-toggle btn btn-outline-danger me-3" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Logo (Centered) -->
            <a href="dashboard.php" class="navbar-brand mx-auto text-dark">Bernadette Wellness Spa Admin Panel</a>
        </div>
    </header>

    <!-- Sidebar Section -->
    <div class="sidebar bg-light shadow-lg p-3 mb-5 rounded" id="sidebar">
        <button class="close-btn btn btn-outline-purple mb-4" onclick="toggleSidebar()" style="font-size: 1.5rem; position: absolute; right: 15px; top: 15px;">&times;</button>
        <ul class="list-unstyled">
            <li class="mb-3">
                <a href="../dashboard.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-tachometer-alt fa-lg me-3 icon"></i> <!-- Dashboard Icon -->
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../bws_ui/login/process/view_all_users.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Registered Users Icon -->
                    <span>Registered Users</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="#" id="manageBookingsLink" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item shadow rounded p-3 bg-white" role="button" aria-expanded="false">
                    <i class="fas fa-calendar-alt fa-lg me-3 icon text-primary"></i>
                    <span class="fw-bold">Manage Bookings</span>
                    <i class="fas fa-chevron-down ms-auto toggle-icon text-secondary"></i>
                </a>
                <ul class="list-unstyled ms-4 mt-2 collapse" id="manageBookingsDropdown">
                    <li class="mb-2">
                        <a href="../booking/pending_booking.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-clock me-2 text-warning"></i> Pending Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/cancelled_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-times-circle me-2 text-danger"></i> Cancelled Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/no-show_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-user-slash me-2 text-muted"></i> No-Show Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/approved_booking_ui.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-check-circle me-2 text-success"></i> Approved Bookings
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/sales.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-dollar-sign me-2 text-info"></i> Sales
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="../booking/complete_bookings.php" class="text-dark text-decoration-none d-flex align-items-center">
                            <i class="fas fa-calendar-check me-2 text-primary"></i> Complete Bookings
                        </a>
                    </li>
                </ul>
            </li>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const manageBookingsLink = document.getElementById('manageBookingsLink');
                    const manageBookingsDropdown = document.getElementById('manageBookingsDropdown');
                    const toggleIcon = manageBookingsLink.querySelector('.toggle-icon');

                    manageBookingsLink.addEventListener('click', function(event) {
                        event.preventDefault(); // Prevent default anchor click behavior

                        // Toggle the 'show' class to open/close the dropdown
                        const isExpanded = manageBookingsDropdown.classList.toggle('show');
                        manageBookingsLink.setAttribute('aria-expanded', isExpanded);

                        // Toggle the icon between chevron-down and chevron-up
                        toggleIcon.classList.toggle('fa-chevron-down', !isExpanded);
                        toggleIcon.classList.toggle('fa-chevron-up', isExpanded);
                    });
                });
            </script>


            <li class="mb-3">
                <a href="add_category.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-tags fa-lg me-3 icon"></i> <!-- Add Service Category Icon -->
                    <span>Add Service Category</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="add_services.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-plus-circle fa-lg me-3 icon"></i> <!-- Add Services Icon -->
                    <span>Add Services</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../bws_ui/reminders/reminders.php " class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-bell fa-lg me-3 icon"></i> <!-- Reminder Icon -->
                    <span>Appointment Reminders</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="../bws_ui/staff/manage_staff.php" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item">
                    <i class="fas fa-users fa-lg me-3 icon"></i> <!-- Staff Icon -->
                    <span>Manage Staff</span>
                </a>
            </li>
            <li class="mb-3">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none icon-hover list-item" id="logoutLink">
                    <i class="fas fa-sign-out-alt fa-lg me-3 icon"></i> <!-- Log Out Icon -->
                    <span>Log Out</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Cancelled Bookings Section -->
    <div class="container mt-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center text-danger">Cancelled Bookings</h2>
                <table id="cancelledBookingsTable" class="table table-striped table-hover table-bordered bookingTable" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>Full Name</th>
                            <th>Service Names</th>
                            <th>Appointment Date</th>
                            <th>Appointment Time</th>
                            <th>Payment Method</th>
                            <th>Total Price</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch only cancelled bookings
                        $sql = "SELECT appointment_id, full_name, service_names, appointment_date, appointment_time, payment_method, total_price, reason, 'cancelled' AS status 
                                FROM cancelled_bookings
                                ORDER BY cancelled_at DESC";
                        $result = $conn->query($sql);
                    
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>" . htmlspecialchars($row['full_name']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['service_names']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
                                echo "<td>" . ucfirst(htmlspecialchars($row['payment_method'])) . "</td>";
                                echo "<td>₱" . number_format($row['total_price'], 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No cancelled bookings found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php $conn->close(); ?>

    <!-- Include jQuery and DataTables JavaScript library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        $(document).ready(function() {
            $('#cancelledBookingsTable').DataTable({
                responsive: true,
                "autoWidth": false,
                "language": {
                    "paginate": {
                        "previous": "<i class='fas fa-chevron-left'></i>",
                        "next": "<i class='fas fa-chevron-right'></i>"
                    }
                }
            });
        });
    </script>
</body>
</html>

<style>
    #sidebar {
        position: fixed;
        left: -250px;
        top: 0;
        height: 100%;
        width: 250px;
        background: #f8f9fa;
        transition: left 0.3s;
        z-index: 1000;
    }

    #sidebar.active {
        left: 0;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .sidebar-overlay.active {
        display: block;
    }
</style>

<?php include '../../bws_ui/includes/footer.php' ?>